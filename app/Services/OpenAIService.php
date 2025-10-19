<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\AiUsageAnalytic;

class OpenAIService {
    private $apiKey;
    private $baseUrl = 'https://api.openai.com/v1';
    private $defaultModel = 'gpt-3.5-turbo';

    public function __construct() {
        $this->apiKey = config('services.openai.api_key');
    }

    public function chat(array $messages, string $model = null, array $options = []): array {
        $model = $model ?: $this->defaultModel;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post("{$this->baseUrl}/chat/completions", [
                'model' => $model,
                'messages' => $messages,
                'max_tokens' => $options['max_tokens'] ?? 1500,
                'temperature' => $options['temperature'] ?? 0.7,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->logUsage($data['usage'], $model);

                return [
                    'success' => true,
                    'content' => $data['choices'][0]['message']['content'],
                    'tokens' => $data['usage'],
                    'model' => $model
                ];
            }

            return ['success' => false, 'error' => 'API request failed'];
        } catch (\Exception $e) {
            Log::error('OpenAI Service Exception: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Service unavailable'];
        }
    }

    private function logUsage(array $usage, string $model): void {
        try {
            $cost = $this->calculateCost($usage['total_tokens'], $model);

            AiUsageAnalytic::updateOrCreate(
                [
                    'usage_date' => now()->toDateString(),
                    'user_id' => Auth::id(),
                    'model_used' => $model
                ],
                [
                    'total_requests' => DB::raw('total_requests + 1'),
                    'total_tokens' => DB::raw('total_tokens + ' . $usage['total_tokens']),
                    'total_cost' => DB::raw('total_cost + ' . $cost)
                ]
            );
        } catch (\Exception $e) {
            Log::error('Failed to log AI usage: ' . $e->getMessage());
        }
    }

    private function calculateCost(int $tokens, string $model): float {
        $costPerThousand = match($model) {
            'gpt-4' => 0.03,
            'gpt-4-32k' => 0.06,
            default => 0.002 // gpt-3.5-turbo
        };
        return ($tokens / 1000) * $costPerThousand;
    }

    public function generateVeterinaryContext(array $diseaseData = []): array {
        $baseContent = "Anda adalah asisten AI ahli kesehatan hewan ternak. Berikan informasi akurat dan tekankan KONSULTASI dengan DOKTER HEWAN untuk diagnosis. Fokus pada pencegahan, gejala umum, dan tindakan darurat. Gunakan bahasa Indonesia yang mudah dipahami.";

        if (!empty($diseaseData)) {
            $diseaseInfo = "Informasi penyakit terkait:\n";
            $diseaseInfo .= "- Nama: " . ($diseaseData['name'] ?? 'Tidak diketahui') . "\n";
            $diseaseInfo .= "- Agen penyebab: " . ($diseaseData['causative_agent'] ?? 'Tidak diketahui') . "\n";
            $diseaseInfo .= "- Gejala: " . ($diseaseData['symptoms'] ?? 'Tidak diketahui') . "\n";
            $diseaseInfo .= "- Pencegahan: " . ($diseaseData['prevention'] ?? 'Tidak diketahui') . "\n";
            $diseaseInfo .= "- Pengobatan: " . ($diseaseData['treatment'] ?? 'Tidak diketahui') . "\n";
            $diseaseInfo .= "- Zoonosis: " . (($diseaseData['is_zoonotic'] ?? false) ? 'Ya' : 'Tidak') . "\n";

            $baseContent .= "\n\n" . $diseaseInfo;
        }

        return [
            [
                'role' => 'system',
                'content' => $baseContent
            ]
        ];
    }
}
