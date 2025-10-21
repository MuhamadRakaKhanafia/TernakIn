<?php
// app/Services/GeminiService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\AiUsageAnalytic;
use Exception;

class GeminiService 
{
    private $apiKey;
    private $baseUrl;
    private $defaultModel = 'gemini-pro';

    public function __construct() 
    {
        $this->apiKey = config('services.google.gemini.api_key');
        $this->baseUrl = config('services.google.gemini.url');
        
        if (!$this->apiKey) {
            throw new Exception('Gemini API key not configured');
        }
    }

    /**
     * Main chat method
     */
    public function chat(array $messages, array $options = []): array 
    {
        try {
            $formattedMessages = $this->formatMessagesForGemini($messages);
            
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->baseUrl . '?key=' . $this->apiKey, [
                'contents' => $formattedMessages,
                'generationConfig' => [
                    'temperature' => $options['temperature'] ?? 0.7,
                    'maxOutputTokens' => $options['max_tokens'] ?? 1000,
                    'topP' => 0.8,
                    'topK' => 40,
                ],
                'safetySettings' => [
                    [
                        'category' => 'HARM_CATEGORY_HARASSMENT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_HATE_SPEECH',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $this->handleSuccessResponse($data, $messages, $options);
            } else {
                return $this->handleErrorResponse($response);
            }

        } catch (Exception $e) {
            Log::error('Gemini Service Exception: ' . $e->getMessage());
            return [
                'success' => false, 
                'error' => 'Service unavailable: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Handle successful API response
     */
    private function handleSuccessResponse(array $data, array $messages, array $options): array
    {
        if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            return [
                'success' => false, 
                'error' => 'Invalid response format from Gemini API'
            ];
        }

        $content = $data['candidates'][0]['content']['parts'][0]['text'];
        
        // Clean response
        $content = $this->cleanResponse($content);

        $result = [
            'success' => true,
            'content' => $content,
            'model' => $this->defaultModel
        ];

        // Add token usage if requested
        if ($options['include_tokens'] ?? false) {
            $estimatedTokens = $this->estimateTokenUsage($messages, $content);
            $result['tokens'] = $estimatedTokens;
            $this->logUsage($estimatedTokens);
        }

        return $result;
    }

    /**
     * Handle API error response
     */
    private function handleErrorResponse($response): array
    {
        $errorData = $response->json();
        $errorMessage = $errorData['error']['message'] ?? 'API request failed';
        
        Log::error('Gemini API Error: ' . $errorMessage, [
            'status' => $response->status(),
            'response' => $errorData
        ]);

        return [
            'success' => false, 
            'error' => $this->formatErrorMessage($errorMessage)
        ];
    }

    /**
     * Format error message untuk user
     */
    private function formatErrorMessage(string $error): string
    {
        if (str_contains($error, 'quota')) {
            return 'Kuota API telah habis. Silakan coba lagi nanti.';
        }
        
        if (str_contains($error, 'timeout')) {
            return 'Timeout terhubung ke layanan AI. Silakan coba lagi.';
        }

        return 'Terjadi kesalahan pada layanan AI. Silakan coba lagi.';
    }

    /**
     * Format messages untuk Gemini API
     */
    private function formatMessagesForGemini(array $messages): array 
    {
        $formatted = [];

        foreach ($messages as $message) {
            $role = $message['role'] === 'assistant' ? 'model' : 'user';
            
            $formatted[] = [
                'role' => $role,
                'parts' => [
                    ['text' => $message['content']]
                ]
            ];
        }

        return $formatted;
    }

    /**
     * Clean AI response
     */
    private function cleanResponse(string $content): string
    {
        // Remove excessive newlines
        $content = preg_replace('/\n{3,}/', "\n\n", $content);
        
        // Trim whitespace
        $content = trim($content);
        
        return $content;
    }

    /**
     * Estimate token usage (approximation)
     */
    private function estimateTokenUsage(array $inputMessages, string $outputContent): array 
    {
        $inputText = '';
        foreach ($inputMessages as $msg) {
            $inputText .= $msg['content'] . ' ';
        }

        // Rough estimation: 1 token ≈ 4 characters for English, 2 for Indonesian
        $inputTokens = ceil(mb_strlen($inputText) / 2);
        $outputTokens = ceil(mb_strlen($outputContent) / 2);
        $totalTokens = $inputTokens + $outputTokens;

        return [
            'prompt_tokens' => $inputTokens,
            'completion_tokens' => $outputTokens,
            'total_tokens' => $totalTokens
        ];
    }

    /**
     * Log usage to database
     */
    private function logUsage(array $usage): void 
    {
        try {
            if (Auth::check()) {
                AiUsageAnalytic::create([
                    'user_id' => Auth::id(),
                    'input_tokens' => $usage['prompt_tokens'],
                    'output_tokens' => $usage['completion_tokens'],
                    'total_tokens' => $usage['total_tokens'],
                    'cost' => $this->calculateCost($usage['total_tokens']),
                    'model_used' => $this->defaultModel
                ]);
            }
        } catch (Exception $e) {
            Log::error('Failed to log Gemini usage: ' . $e->getMessage());
        }
    }

    /**
     * Calculate cost based on tokens
     */
    private function calculateCost(int $totalTokens): float
    {
        // Gemini pricing approximation (per 1K tokens)
        $costPerThousandTokens = 0.00025; // $0.00025 per 1K tokens
        return ($totalTokens / 1000) * $costPerThousandTokens;
    }

    /**
     * Generate veterinary-specific context
     */
    public function generateVeterinaryContext(array $diseaseData = []): array 
    {
        $baseContext = "Anda adalah asisten AI ahli kesehatan hewan ternak dengan spesialisasi dalam:

1. Diagnosis awal gejala penyakit ternak
2. Rekomendasi pencegahan penyakit
3. Manajemen kesehatan ternak
4. Nutrisi dan pakan ternak
5. Biosecurity dan sanitasi kandang

PETUNJUK:
- Berikan informasi yang akurat dan praktis
- Tekankan pentingnya KONSULTASI dengan DOKTER HEWAN untuk diagnosis pasti
- Fokus pada pencegahan dan penanganan awal
- Gunakan bahasa Indonesia yang mudah dipahami peternak
- Sertakan tindakan darurat jika diperlukan
- Berikan estimasi waktu penanganan";

        if (!empty($diseaseData)) {
            $diseaseInfo = "\n\nINFORMASI PENYAKIT TERKAIT:\n";
            foreach ($diseaseData as $key => $value) {
                $diseaseInfo .= "- " . ucfirst(str_replace('_', ' ', $key)) . ": {$value}\n";
            }
            $baseContext .= $diseaseInfo;
        }

        return [[
            'role' => 'user',
            'content' => $baseContext
        ]];
    }

    /**
     * Validate API connection
     */
    public function validateConnection(): bool
    {
        try {
            $response = Http::timeout(10)
                ->get($this->baseUrl . '?key=' . $this->apiKey);
                
            return $response->successful();
        } catch (Exception $e) {
            return false;
        }
    }
}