<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GeminiFreeService
{
    private $apiKey;
    private $baseUrl;
    
    public function __construct()
    {
        $this->apiKey = env('GEMINI_FREE_API_KEY');
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/';
    }

   public function chat(array $messages, array $options = []): array
{
    try {
        if (empty($this->apiKey)) {
            return $this->getDummyResponse($messages);
        }

        $model = 'gemini-2.0-flash-exp';
        $url = $this->baseUrl . $model . ':generateContent?key=' . $this->apiKey;

        $lastMessage = end($messages);
        $userMessage = $lastMessage['content'] ?? '';

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => "Anda asisten kesehatan ternak. Jawab: " . $userMessage]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 1000,
            ]
        ];

        $response = Http::timeout(30)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, $payload);

        if ($response->successful()) {
            $data = $response->json();
            
            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                $content = $data['candidates'][0]['content']['parts'][0]['text'];
                
                // FIXED: Return struktur yang SIMPLE
                return [
                    'success' => true,
                    'data' => [
                        'content' => trim($content)
                    ]
                ];
            }
        }

        // Fallback ke dummy response
        return $this->getDummyResponse($messages);

    } catch (\Exception $e) {
        return $this->getDummyResponse($messages);
    }
}

private function getDummyResponse(array $messages): array
{
    $lastMessage = end($messages);
    $userMessage = $lastMessage['content'] ?? '';
    
    $response = "Halo! Saya Asisten Kesehatan Ternak TernakIN. ";
    
    if (str_contains(strtolower($userMessage), 'ayam')) {
        $response .= "Untuk ayam: vaksinasi rutin, kebersihan kandang, pakan bernutrisi.";
    } else if (str_contains(strtolower($userMessage), 'sapi')) {
        $response .= "Untuk sapi: pemeriksaan berkala, pakan hijauan, air bersih.";
    } else {
        $response .= "Ada yang bisa saya bantu mengenai ternak Anda?";
    }
    
    // FIXED: Struktur konsisten
    return [
        'success' => true,
        'data' => [
            'content' => $response
        ]
    ];
}

    private function callGeminiAPI(array $messages): array
    {
        $model = 'gemini-2.0-flash-exp';
        $url = $this->baseUrl . $model . ':generateContent?key=' . $this->apiKey;

        $lastMessage = end($messages);
        $userMessage = $lastMessage['content'] ?? '';

        $systemPrompt = "Anda adalah asisten kesehatan ternak profesional di TernakIN. Berikan jawaban yang informatif tentang kesehatan ternak, pencegahan penyakit, manajemen pakan, dan peternakan.";

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $systemPrompt . "\n\nPertanyaan: " . $userMessage]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 1024,
                'topP' => 0.8,
                'topK' => 40
            ]
        ];

        $response = Http::timeout(30)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post($url, $payload);

        if ($response->successful()) {
            $data = $response->json();
            
            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                $content = $data['candidates'][0]['content']['parts'][0]['text'];
                $content = trim($content);
                
                return [
                    'success' => true,
                    'content' => $content
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Invalid response format'
                ];
            }
        } else {
            return [
                'success' => false,
                'error' => 'API error'
            ];
        }
    }

    private function generateDummyResponse(string $userMessage): string
    {
        $message = strtolower(trim($userMessage));
        
        if (empty($message)) {
            return "Halo! Saya Asisten Kesehatan Ternak TernakIN. Ada yang bisa saya bantu?";
        }
        
        if (str_contains($message, 'halo')) {
            return "Halo! Saya Asisten Kesehatan Ternak TernakIN. Saya siap membantu dengan konsultasi kesehatan ternak, pencegahan penyakit, dan manajemen peternakan.";
        }
        
        if (str_contains($message, 'ayam')) {
            return "Untuk kesehatan ayam:\n• Vaksinasi rutin\n• Kebersihan kandang\n• Pakan bernutrisi\n• Ventilasi udara cukup";
        }
        
        if (str_contains($message, 'sapi')) {
            return "Untuk perawatan sapi:\n• Pemeriksaan kesehatan berkala\n• Pakan hijauan berkualitas\n• Air minum bersih\n• Kandang kering dan bersih";
        }
        
        return "Terima kasih atas pertanyaan Anda! Saya siap membantu dengan konsultasi kesehatan ternak.";
    }
}