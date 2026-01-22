<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Client\Response;
use Exception;

class GeminiFreeService
{
    private $apiKey;
    private $baseUrl;
    private $model;
    
    public function __construct()
    {
        $this->apiKey = env('GEMINI_FREE_API_KEY');
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1/models/';

        // Model yang akan dicoba (prioritas Gemini 2.5 Flash)
        $this->model = 'gemini-2.5-flash';
    }

    /**
     * Chat dengan Gemini AI - VERSI DIPERBAIKI
     */
    public function chat(array $messages): array
    {
        try {
            // 1. Validasi API key
            if (empty($this->apiKey)) {
                Log::error('GEMINI_FREE_API_KEY is empty in .env');
                return [
                    'success' => false,
                    'data' => [
                        'content' => '❌ API Key Gemini tidak ditemukan di .env file.',
                        'model' => 'error',
                        'is_fallback' => true,
                        'is_ai' => false,
                        'error_code' => 'API_KEY_MISSING'
                    ]
                ];
            }

            // 2. Log untuk debugging
            Log::info('Gemini Service - API Key found: ' . substr($this->apiKey, 0, 10) . '...');

            // 3. Daftar model yang akan dicoba (dari Gemini 2.5 ke versi lebih lama)
            $modelsToTry = [
                'gemini-2.5-flash',        // Gemini 2.5 Flash (latest)
                'gemini-2.5-pro',          // Gemini 2.5 Pro
                'gemini-2.0-flash',        // Gemini 2.0 Flash
                'gemini-2.0-flash-001',    // Gemini 2.0 Flash 001
                'gemini-2.0-flash-lite',   // Gemini 2.0 Flash-Lite
                'gemini-2.5-flash-lite'    // Gemini 2.5 Flash-Lite
            ];

            $lastError = null;
            $workingModel = null;
            $aiResponse = null;

            // 4. Coba semua model sampai satu berhasil
            foreach ($modelsToTry as $model) {
                try {
                    Log::info('Trying Gemini model: ' . $model);
                    
                    $result = $this->callGeminiWithModel($messages, $model);
                    
                    if ($result['success']) {
                        $workingModel = $model;
                        $aiResponse = $result['content'];
                        break; // Stop setelah menemukan model yang berhasil
                    } else {
                        $lastError = $result['error'] ?? 'Unknown error';
                        Log::warning('Model ' . $model . ' failed: ' . $lastError);
                    }
                    
                } catch (Exception $e) {
                    Log::warning('Exception with model ' . $model . ': ' . $e->getMessage());
                    continue;
                }
            }

            // 5. Jika ada model yang berhasil
            if ($workingModel && $aiResponse) {
                Log::info('✅ Gemini response successful with model: ' . $workingModel);
                
                return [
                    'success' => true,
                    'data' => [
                        'content' => trim($aiResponse),
                        'model' => $workingModel,
                        'is_fallback' => false,
                        'is_ai' => true,
                        'response_time' => now()->format('H:i:s')
                    ]
                ];
            }

            // 6. Jika semua model gagal, gunakan fallback dengan AI knowledge
            Log::error('All Gemini models failed. Last error: ' . ($lastError ?? 'No error info'));
            
            return $this->getSmartFallbackResponse($messages, $lastError);

        } catch (Exception $e) {
            Log::error('Gemini Service Exception: ' . $e->getMessage());
            
            return [
                'success' => false,
                'data' => [
                    'content' => '⚠️ Terjadi kesalahan sistem. ' . $this->generateExpertResponse($messages),
                    'model' => 'error',
                    'is_fallback' => true,
                    'is_ai' => false
                ]
            ];
        }
    }

    /**
     * Call Gemini API dengan model tertentu
     */
    private function callGeminiWithModel(array $messages, string $model): array
    {
        $url = $this->baseUrl . $model . ':generateContent?key=' . $this->apiKey;

        // Ambil pesan terakhir dari user
        $userMessage = $this->getLastUserMessage($messages);
        
        // System prompt untuk Dr. Peternak
        $systemPrompt = "Anda adalah \"Dr. Peternak\" - ahli kesehatan hewan ternak dengan 25 tahun pengalaman di Indonesia.
        
SPESIALISASI: sapi, ayam, kambing, domba, bebek/itik
SIFAT: Ramah, sabar, memahami peternak Indonesia
BAHASA: Indonesia sederhana, praktis, mudah dipahami

FORMAT JAWABAN:
1. Identifikasi masalah
2. Penyebab yang mungkin
3. Solusi praktis bertahap
4. Tips pencegahan
5. Tandai situasi darurat

Sekarang bantu peternak dengan pertanyaan berikut:";

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $systemPrompt . "\n\nPERTANYAAN PETERNAK:\n" . $userMessage]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 4096,
                'topP' => 0.8,
                'topK' => 40
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
        ];

        /** @var Response $response */
        $response = Http::timeout(30)
            ->retry(2, 1000)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])
            ->post($url, $payload);

        if ($response->successful()) {
            /** @var array $data */
            $data = $response->json();

            // Extract content dari berbagai format response
            $content = $this->extractContentFromResponse($data);

            if ($content) {
                return [
                    'success' => true,
                    'content' => $content
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Invalid response format from Gemini API'
                ];
            }
        } else {
            /** @var string $errorBody */
            $errorBody = $response->body();
            /** @var array|null $errorData */
            $errorData = json_decode($errorBody, true);

            return [
                'success' => false,
                'error' => $errorData['error']['message'] ?? 'API Error: ' . $response->status()
            ];
        }
    }

    /**
     * Extract content dari response Gemini
     */
    private function extractContentFromResponse(array $data): ?string
    {
        // Format 1: candidates[0].content.parts[0].text
        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            return $data['candidates'][0]['content']['parts'][0]['text'];
        }
        
        // Format 2: candidates[0].content.text
        if (isset($data['candidates'][0]['content']['text'])) {
            return $data['candidates'][0]['content']['text'];
        }
        
        // Format 3: candidates[0].text
        if (isset($data['candidates'][0]['text'])) {
            return $data['candidates'][0]['text'];
        }
        
        // Format 4: Ada error message
        if (isset($data['error']['message'])) {
            return 'Error from Gemini: ' . $data['error']['message'];
        }
        
        return null;
    }

    /**
     * Get last user message dari array messages
     */
    private function getLastUserMessage(array $messages): string
    {
        // Cari pesan terakhir dari user
        foreach (array_reverse($messages) as $message) {
            if (isset($message['role']) && $message['role'] === 'user' && isset($message['content'])) {
                return trim($message['content']);
            }
        }
        
        return 'Halo, ada yang bisa saya bantu?';
    }

    /**
     * Smart fallback response dengan pengetahuan peternakan
     */
    private function getSmartFallbackResponse(array $messages, ?string $error = null): array
    {
        $userMessage = $this->getLastUserMessage($messages);
        $expertResponse = $this->generateExpertResponseBasedOnQuestion($userMessage);
        
        $prefix = '';
        if ($error) {
            $prefix = "⚠️ **Catatan:** Gemini API mengalami masalah (" . substr($error, 0, 80) . ").\n\n";
        }
        
        Log::info('Using expert fallback for: ' . substr($userMessage, 0, 50));
        
        return [
            'success' => true,
            'data' => [
                'content' => $prefix . $expertResponse,
                'model' => 'expert-knowledge-base',
                'is_fallback' => true,
                'is_ai' => false,
                'source' => 'Pengetahuan ahli peternakan'
            ]
        ];
    }

    /**
     * Generate expert response berdasarkan pertanyaan
     */
    private function generateExpertResponseBasedOnQuestion(string $question): string
    {
        $question = strtolower(trim($question));
        
        if (empty($question) || str_contains($question, 'halo') || str_contains($question, 'hai')) {
            return "👨‍⚕️ **Halo! Saya Dr. Peternak**\n\n"
                 . "Saya ahli kesehatan hewan ternak dengan pengalaman 25 tahun. Ada yang bisa saya bantu terkait peternakan Anda?\n\n"
                 . "**Silakan tanyakan tentang:**\n"
                 . "• Kesehatan ternak (sapi, ayam, kambing, dll)\n"
                 . "• Pencegahan penyakit\n"
                 . "• Manajemen pakan\n"
                 . "• Desain kandang\n"
                 . "• Reproduksi ternak";
        }
        
        // Deteksi berdasarkan keyword
        if (str_contains($question, 'sapi')) {
            if (str_contains($question, 'kandang')) {
                return "🏠 **KANDANG SAPI IDEAL:**\n\n"
                     . "**Ukuran:** 2.5 × 1.5 m per ekor dewasa\n"
                     . "**Lantai:** Beton miring 2-3% ke belakang\n"
                     . "**Atap:** Tinggi 3-4 meter\n"
                     . "**Ventilasi:** Cross ventilation penting\n"
                     . "**Drainase:** Saluran di belakang kandang\n"
                     . "**Orientasi:** Timur-Barat untuk hindari matahari sore\n\n"
                     . "**Tips:** Bersihkan kotoran harian, sediakan tempat makan/minum terpisah.";
            }
            
            if (str_contains($question, 'pakan') || str_contains($question, 'makan')) {
                return "🌱 **PAKAN SAPI OPTIMAL:**\n\n"
                     . "**Kebutuhan harian (sapi 400kg):**\n"
                     . "• Hijauan: 40-50 kg (rumput gajah, odot, lamtoro)\n"
                     . "• Konsentrat: 4-8 kg (jagung, dedak, bungkil kelapa)\n"
                     . "• Air minum: 40-60 liter bersih\n"
                     . "• Mineral block: selalu tersedia\n\n"
                     . "**Waktu pemberian:**\n"
                     . "• Pagi (06:00): 40% hijauan\n"
                     . "• Siang (12:00): Konsentrat\n"
                     . "• Sore (16:00): 60% hijauan";
            }
            
            if (str_contains($question, 'sakit') || str_contains($question, 'kesehatan')) {
                return "🏥 **PROGRAM KESEHATAN SAPI:**\n\n"
                     . "**Vaksinasi wajib:**\n"
                     . "✅ Anthrax - Tahunan\n"
                     . "✅ Brucellosis - 2 tahun sekali (betina)\n"
                     . "✅ HS - Tahunan (daerah endemik)\n\n"
                     . "**Pengobatan rutin:**\n"
                     . "• Obat cacing: 4x setahun\n"
                     . "• Vitamin AD3E: 3 bulan sekali\n"
                     . "• Pemeriksaan: Suhu, nafsu makan harian\n\n"
                     . "**Darurat:** Hubungi dokter hewan jika demam >40°C, tidak makan >24 jam, sesak napas.";
            }
            
            return "🐄 **MANAJEMEN TERNAK SAPI - Panduan Dr. Peternak:**\n\n"
                 . "**Kandang:** 2.5×1.5 m/ekor, lantai miring 2-3%\n"
                 . "**Kesehatan:** Vaksin Anthrax tahunan, obat cacing 4x/tahun\n"
                 . "**Pakan:** Hijauan 10% BB + konsentrat 1-2% BB\n"
                 . "**Reproduksi:** Dewasa 15-18 bulan, bunting 9 bulan\n"
                 . "**Produksi:** Susu 10-15 liter/hari (sapi perah)";
        }
        
        if (str_contains($question, 'ayam')) {
            return "🐔 **MANAJEMEN AYAM PROFESIONAL:**\n\n"
                 . "**Kandang:** 8-10 ekor/m², suhu 24-28°C\n"
                 . "**Ventilasi:** 1 m³/kg BB/menit\n"
                 . "**Pakan:**\n"
                 . "• Starter (0-1 bulan): 21-23% protein\n"
                 . "• Grower (1-2 bulan): 18-20% protein\n"
                 . "• Layer: 16-18% protein + kalsium 3.5%\n"
                 . "**Vaksinasi wajib:**\n"
                 . "• Hari 4: ND-IB\n"
                 . "• Minggu 4: Gumboro\n"
                 . "• Minggu 8: ND booster\n"
                 . "• Minggu 12: AI (flu burung)";
        }
        
        if (str_contains($question, 'kambing') || str_contains($question, 'domba')) {
            return "🐐 **PERAWATAN KAMBING/DOMBA:**\n\n"
                 . "**Kandang panggung:** Tinggi 1-1.5 m, lantai renggang 2 cm\n"
                 . "**Pakan:** Hijauan 3-4% BB + konsentrat 0.5-1% BB\n"
                 . "**Kesehatan:**\n"
                 . "• Vaksin PPR: Tahunan\n"
                 . "• Obat cacing: 3 bulan sekali\n"
                 . "• Vitamin: 6 bulan sekali\n"
                 . "• Periksa kuku: 2 bulan sekali\n"
                 . "**Reproduksi:** Dewasa 6-8 bulan, bunting 5 bulan";
        }

        if (str_contains($question, 'ikan') || str_contains($question, 'lele') || str_contains($question, 'nila') || str_contains($question, 'gurame') || str_contains($question, 'bandeng')) {
            return $this->getFishFarmingExpert($question);
        }

        return "👨‍⚕️ **Dr. Peternak - Konsultasi Ternak**\n\n"
             . "Untuk memberikan jawaban yang tepat tentang **\"" . $question . "\"**, saya perlu informasi:\n\n"
             . "1. **Jenis ternak** (sapi, ayam, kambing, dll)\n"
             . "2. **Umur ternak**\n"
             . "3. **Jumlah** yang terkena\n"
             . "4. **Gejala spesifik** yang diamati\n"
             . "5. **Sudah berapa lama** kondisi ini\n"
             . "6. **Sudah dicoba** apa saja\n\n"
             . "**Contoh pertanyaan lengkap:**\n"
             . "\"Sapi umur 2 tahun, 3 ekor, tidak mau makan sejak 2 hari, perut kiri membesar, sudah coba minyak kelapa.\"";
    }

    /**
     * Expert advice untuk budidaya ikan
     */
    private function getFishFarmingExpert(string $question): string
    {
        if (str_contains($question, 'lele')) {
            return "🐱 **BUDIDAYA IKAN LELE - Panduan Lengkap Dr. Peternak**\n\n"
                 . "**1. PEMILIHAN LOKASI:**\n"
                 . "• Kolam tanah atau terpal\n"
                 . "• Luas: 50-100 m² per 1000 ekor\n"
                 . "• Kedalaman: 1-1.5 meter\n"
                 . "• Sumber air bersih melimpah\n\n"
                 . "**2. PERSIAPAN KOLAM:**\n"
                 . "• Kapur dolomit: 100-200 kg/ha\n"
                 . "• Pupuk kandang: 2000-3000 kg/ha\n"
                 . "• Pupuk urea: 50-100 kg/ha\n"
                 . "• Pupuk SP-36: 50 kg/ha\n"
                 . "• Diamkan 7-10 hari\n\n"
                 . "**3. PEMBENIHAN:**\n"
                 . "• Bibit lele: 5-7 cm (umur 21-30 hari)\n"
                 . "• Padat tebar: 200-300 ekor/m²\n"
                 . "• Bibit sehat: aktif berenang, warna cerah\n\n"
                 . "**4. PAKAN:**\n"
                 . "• Pelet komersial: 28-32% protein\n"
                 . "• Frekuensi: 3-4x sehari\n"
                 . "• Porsi: 3-5% dari bobot tubuh\n"
                 . "• Jenis pakan: starter, grower, finisher\n\n"
                 . "**5. PENGELOLAAN KOLAM:**\n"
                 . "• Ganti air: 10-20% per hari\n"
                 . "• Suhu optimal: 26-30°C\n"
                 . "• pH: 6.5-8.5\n"
                 . "• Oksigen terlarut: >3 mg/L\n\n"
                 . "**6. PANEN:**\n"
                 . "• Umur: 3-4 bulan\n"
                 . "• Bobot: 150-250 gram/ekor\n"
                 . "• Produktivitas: 10-15 ton/ha/tahun\n\n"
                 . "**7. PENYAKIT UMUM:**\n"
                 . "• Motile Aeromonas Septicemia (MAS)\n"
                 . "• Columnaris\n"
                 . "• White spot disease\n"
                 . "• Pencegahan: vaksin, probiotik, kualitas air";
        }

        if (str_contains($question, 'nila')) {
            return "🐠 **BUDIDAYA IKAN NILA - Panduan Dr. Peternak**\n\n"
                 . "**1. JENIS NILA:**\n"
                 . "• Nila hitam (Oreochromis niloticus)\n"
                 . "• Nila merah (gift tilapia)\n"
                 . "• Nila galunggung\n\n"
                 . "**2. KOLAM:**\n"
                 . "• Sistem: Terpal, beton, tanah\n"
                 . "• Ukuran: 100-500 m²\n"
                 . "• Kedalaman: 1-2 meter\n"
                 . "• Padat tebar: 50-100 ekor/m²\n\n"
                 . "**3. PAKAN:**\n"
                 . "• Pelet: 25-30% protein\n"
                 . "• Pakan alami: plankton, azolla\n"
                 . "• Suplementasi: vitamin C\n\n"
                 . "**4. KONDISI AIR:**\n"
                 . "• Suhu: 25-32°C\n"
                 . "• pH: 6.5-9.0\n"
                 . "• Oksigen: >3 mg/L\n"
                 . "• Amonia: <0.1 mg/L\n\n"
                 . "**5. REPRODUKSI:**\n"
                 . "• Sex reversal: methyltestosterone\n"
                 . "• Rasio jantan: 1:3 (betina:jantan)\n"
                 . "• Siklus: 24-28 hari\n\n"
                 . "**6. PANEN:**\n"
                 . "• Umur: 4-5 bulan\n"
                 . "• Bobot: 200-400 gram\n"
                 . "• Produktivitas: 5-10 ton/ha";
        }

        return "🐟 **BUDIDAYA IKAN - Konsultasi Dr. Peternak**\n\n"
             . "**JENIS IKAN YANG BISA DIBANTU:**\n"
             . "• Ikan lele (clarias catfish)\n"
             . "• Ikan nila (tilapia)\n"
             . "• Ikan gurame (giant gourami)\n"
             . "• Ikan bandeng (milkfish)\n\n"
             . "**ASPEK YANG BISA DIKONSULTASIKAN:**\n"
             . "• Persiapan kolam dan lokasi\n"
             . "• Pemilihan bibit berkualitas\n"
             . "• Manajemen pakan optimal\n"
             . "• Pengelolaan kualitas air\n"
             . "• Pencegahan dan pengobatan penyakit\n"
             . "• Teknik panen dan pasca panen\n\n"
             . "**Silakan tanyakan lebih spesifik, contoh:**\n"
             . "\"Bagaimana cara membuat kolam lele yang baik?\"\n"
             . "\"Apa saja penyakit ikan nila dan cara mengobatinya?\"";
    }

    /**
     * Generate expert response (alias untuk kompatibilitas)
     */
    private function generateExpertResponse(array $messages): string
    {
        $userMessage = $this->getLastUserMessage($messages);
        return $this->generateExpertResponseBasedOnQuestion($userMessage);
    }

    /**
     * Test koneksi Gemini
     */
    public function testConnection(): array
    {
        try {
            if (empty($this->apiKey)) {
                return [
                    'success' => false,
                    'message' => '❌ GEMINI_FREE_API_KEY tidak ditemukan di .env',
                    'status' => 'missing_api_key'
                ];
            }

            // Test dengan model yang paling mungkin berhasil
            $testModel = 'gemini-2.5-flash';
            $testUrl = $this->baseUrl . $testModel . ':generateContent?key=' . $this->apiKey;
            
            $payload = [
                'contents' => [
                    [
                        'parts' => [['text' => 'Test connection. Reply with "OK".']]
                    ]
                ],
                'generationConfig' => [
                    'maxOutputTokens' => 5
                ]
            ];

            /** @var Response $response */
            $response = Http::timeout(10)->post($testUrl, $payload);

            if ($response->successful()) {
                /** @var array $data */
                $data = $response->json();
                $content = $this->extractContentFromResponse($data);

                return [
                    'success' => true,
                    'message' => '✅ Gemini API CONNECTED',
                    'status' => 'connected',
                    'model' => $testModel,
                    'response' => $content,
                    'api_key_preview' => substr($this->apiKey, 0, 10) . '...'
                ];
            } else {
                /** @var array|null $error */
                $error = $response->json();
                return [
                    'success' => false,
                    'message' => '❌ Gemini Connection FAILED',
                    'status' => 'failed',
                    'error' => $error['error']['message'] ?? 'HTTP ' . $response->status(),
                    'api_key_exists' => true
                ];
            }

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => '❌ Test Exception: ' . $e->getMessage(),
                'status' => 'exception'
            ];
        }
    }
}