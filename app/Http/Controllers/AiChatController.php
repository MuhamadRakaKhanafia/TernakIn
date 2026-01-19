<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\AiChatSession;
use App\Models\AiChatMessage;
use App\Models\AnimalType;
use App\Services\GeminiFreeService;

class AiChatController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiFreeService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Tampilkan halaman chat AI
     */
    public function index()
    {
        try {
            $animalTypes = AnimalType::orderBy('name')->get(['id', 'name', 'category']);
            
            // Test koneksi Gemini
            $healthCheck = $this->geminiService->testConnection();
            
            return view('chat.index', [
                'animalTypes' => $animalTypes,
                'healthCheck' => $healthCheck
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading AI chat page: ' . $e->getMessage());
            
            return view('chat.index', [
                'animalTypes' => AnimalType::orderBy('name')->get(['id', 'name', 'category']) ?? collect(),
                'healthCheck' => [
                    'success' => false, 
                    'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
                    'status' => 'error'
                ]
            ]);
        }
    }

    /**
     * Mulai sesi chat baru
     */
    public function startSession(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'animal_type_id' => 'nullable|exists:animal_types,id'
            ]);

            $userId = Auth::id();
            
            Log::info('Starting new chat session', [
                'user_id' => $userId,
                'animal_type_id' => $request->animal_type_id
            ]);

            // Get animal type name for title
            $animalTypeName = 'Umum';
            $animalTypeId = null;
            
            if ($request->animal_type_id) {
                $animalType = AnimalType::find($request->animal_type_id);
                if ($animalType) {
                    $animalTypeName = $animalType->name;
                    $animalTypeId = $animalType->id;
                }
            }

            // Create session
            $session = AiChatSession::create([
                'user_id' => $userId,
                'animal_type_id' => $animalTypeId,
                'title' => 'Konsultasi ' . $animalTypeName . ' - ' . now()->format('d/m H:i'),
                'last_activity' => now(),
            ]);

            // Create welcome message
            $welcomeMessage = $this->getWelcomeMessage($animalTypeName);
            AiChatMessage::create([
                'session_id' => $session->id,
                'role' => 'assistant',
                'content' => $welcomeMessage,
            ]);

            Log::info('Session created successfully', [
                'session_id' => $session->session_id,
                'animal_type' => $animalTypeName
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'session' => [
                        'session_id' => $session->session_id,
                        'title' => $session->title,
                        'animal_type_id' => $session->animal_type_id,
                        'animal_type_name' => $animalTypeName,
                        'created_at' => $session->created_at->toISOString(),
                        'welcome_message' => $welcomeMessage
                    ]
                ],
                'message' => 'Sesi chat berhasil dibuat'
            ]);

        } catch (\Exception $e) {
            Log::error('Error starting chat session: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Gagal membuat sesi chat. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Kirim pesan ke AI - VERSI DIPERBAIKI
     */
    public function sendMessage(Request $request, $sessionId): JsonResponse
    {
        try {
            $request->validate([
                'message' => 'required|string|max:2000'
            ]);

            $userId = Auth::id();
            
            // Rate limiting: 20 requests per 2 minutes per user
            $cacheKey = "chat_requests_{$userId}";
            $currentCount = Cache::get($cacheKey, 0);

            if ($currentCount >= 20) {
                Log::warning('Rate limit exceeded for user', [
                    'user_id' => $userId,
                    'request_count' => $currentCount
                ]);

                return response()->json([
                    'success' => false,
                    'error' => 'Terlalu banyak permintaan. Tunggu 2 menit sebelum mencoba lagi.'
                ], 429);
            }

            // Increment counter
            Cache::put($cacheKey, $currentCount + 1, now()->addMinutes(2));

            // Find session
            $session = AiChatSession::where('session_id', $sessionId)
                ->where('user_id', $userId)
                ->with('animalType')
                ->first();

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'error' => 'Sesi chat tidak ditemukan'
                ], 404);
            }

            Log::info('Processing chat message', [
                'session_id' => $sessionId,
                'user_id' => $userId,
                'message_length' => strlen($request->message),
                'message_preview' => substr($request->message, 0, 100)
            ]);

            // 1. Save user message
            $userMessage = AiChatMessage::create([
                'session_id' => $session->id,
                'role' => 'user',
                'content' => trim($request->message),
            ]);

            // 2. Get recent messages for context (max 3 messages)
            $recentMessages = AiChatMessage::where('session_id', $session->id)
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get()
                ->reverse()
                ->map(function($msg) {
                    return [
                        'role' => $msg->role,
                        'content' => $msg->content
                    ];
                })
                ->toArray();

            // Pastikan ada pesan user di context
            if (empty($recentMessages) || end($recentMessages)['role'] !== 'user') {
                $recentMessages[] = [
                    'role' => 'user',
                    'content' => trim($request->message)
                ];
            }

            Log::debug('Messages for AI context', [
                'count' => count($recentMessages),
                'messages' => $recentMessages
            ]);

            // 3. Call Gemini Service
            $aiResult = $this->geminiService->chat($recentMessages);

            Log::debug('AI Result from Gemini:', $aiResult);

            // 4. Handle AI response
            if (!$aiResult['success']) {
                $errorMessage = $aiResult['data']['content'] ?? 'Gagal menghubungi AI service. Silakan coba lagi nanti.';
                
                // Save error message
                $aiMessage = AiChatMessage::create([
                    'session_id' => $session->id,
                    'role' => 'assistant',
                    'content' => $errorMessage,
                ]);

                // Update session
                $session->update([
                    'last_activity' => now(),
                    'title' => $this->generateSessionTitle($request->message, $session->title)
                ]);

                Log::warning('AI service returned error', [
                    'session_id' => $sessionId,
                    'error' => substr($errorMessage, 0, 200)
                ]);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'content' => $errorMessage,
                        'message_id' => $aiMessage->id,
                        'session_title' => $session->title,
                        'timestamp' => $aiMessage->created_at->toISOString(),
                        'is_fallback' => $aiResult['data']['is_fallback'] ?? true,
                        'is_error' => true,
                        'model' => $aiResult['data']['model'] ?? 'error'
                    ]
                ]);
            }

            // 5. Save AI response
            $aiContent = $aiResult['data']['content'] ?? 'Tidak ada response dari AI.';
            $aiMessage = AiChatMessage::create([
                'session_id' => $session->id,
                'role' => 'assistant',
                'content' => $aiContent,
            ]);

            // 6. Update session
            $session->update([
                'last_activity' => now(),
                'title' => $this->generateSessionTitle($request->message, $session->title)
            ]);

            Log::info('AI response generated successfully', [
                'session_id' => $sessionId,
                'response_length' => strlen($aiContent),
                'model' => $aiResult['data']['model'] ?? 'unknown',
                'is_fallback' => $aiResult['data']['is_fallback'] ?? false
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'content' => $aiContent,
                    'message_id' => $aiMessage->id,
                    'session_title' => $session->title,
                    'timestamp' => $aiMessage->created_at->toISOString(),
                    'is_fallback' => $aiResult['data']['is_fallback'] ?? false,
                    'model' => $aiResult['data']['model'] ?? 'gemini'
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Pesan tidak valid. Maksimal 2000 karakter.'
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Error in sendMessage: ' . $e->getMessage());
            
            // Fallback response yang lebih baik
            try {
                if (isset($session)) {
                    $question = $request->message ?? '';
                    $fallbackMessage = $this->generateEmergencyFallback($question);
                    
                    $aiMessage = AiChatMessage::create([
                        'session_id' => $session->id,
                        'role' => 'assistant',
                        'content' => $fallbackMessage,
                    ]);
                    
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'content' => $fallbackMessage,
                            'message_id' => $aiMessage->id,
                            'session_title' => $session->title,
                            'timestamp' => now()->toISOString(),
                            'is_fallback' => true,
                            'model' => 'emergency-fallback'
                        ]
                    ]);
                }
            } catch (\Exception $innerException) {
                Log::error('Failed to save fallback message: ' . $innerException->getMessage());
            }
            
            return response()->json([
                'success' => false,
                'error' => 'Gagal mengirim pesan. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Emergency fallback generator
     */
    private function generateEmergencyFallback(string $question): string
    {
        $question = strtolower($question);
        
        if (str_contains($question, 'sapi')) {
            return "**MANAJEMEN TERNAK SAPI (Fallback Mode):**\n\n"
                 . "**Kandang:**\n"
                 . "• Luas: 2.5x1.5 m per ekor\n"
                 . "• Lantai: Miring 2-3%\n"
                 . "• Atap: Tinggi 3-4 meter\n\n"
                 . "**Kesehatan:**\n"
                 . "• Vaksinasi Anthrax: Tahunan\n"
                 . "• Obat cacing: 4x setahun\n"
                 . "• Vitamin: Setiap 3 bulan\n\n"
                 . "**Catatan:** Sistem AI sedang mengalami gangguan. Informasi ini dari database lokal.";
        }
        
        return "⚠️ **Sistem AI sedang gangguan** ⚠️\n\n"
             . "Untuk sementara, berikut tips umum peternakan:\n\n"
             . "1. **Kebersihan kandang** adalah kunci kesehatan ternak\n"
             . "2. **Pakan berkualitas** dan air bersih selalu tersedia\n"
             . "3. **Observasi harian** untuk deteksi dini penyakit\n"
             . "4. **Konsultasi dokter hewan** untuk kondisi serius\n\n"
             . "Silakan coba lagi beberapa menit atau hubungi administrator.";
    }

    /**
     * Dapatkan semua sesi pengguna
     */
    public function getSessions(): JsonResponse 
    { 
        try {
            $sessions = AiChatSession::with(['animalType:id,name'])
                ->withCount('messages')
                ->where('user_id', Auth::id())
                ->orderBy('last_activity', 'desc')
                ->get()
                ->map(function ($session) {
                    return [
                        'session_id' => $session->session_id,
                        'title' => $session->title,
                        'animal_type' => $session->animalType ? [
                            'id' => $session->animalType->id,
                            'name' => $session->animalType->name
                        ] : null,
                        'last_activity' => $session->last_activity->toISOString(),
                        'created_at' => $session->created_at->toISOString(),
                        'message_count' => $session->messages_count,
                        'is_active' => $session->last_activity->diffInMinutes(now()) < 30
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'sessions' => $sessions,
                    'total' => $sessions->count()
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting sessions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Gagal memuat sesi chat'
            ], 500);
        }
    }
    
    /**
     * Dapatkan detail sesi beserta pesannya
     */
    public function getSession($sessionId): JsonResponse 
    { 
        try {
            $session = AiChatSession::with(['animalType:id,name,category'])
                ->with(['messages' => function($query) {
                    $query->orderBy('created_at', 'asc');
                }])
                ->where('session_id', $sessionId)
                ->where('user_id', Auth::id())
                ->first();

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'error' => 'Sesi chat tidak ditemukan'
                ], 404);
            }

            $messages = $session->messages->map(function($message) {
                return [
                    'id' => $message->id,
                    'role' => $message->role,
                    'content' => $message->content,
                    'created_at' => $message->created_at->toISOString(),
                    'time' => $message->created_at->format('H:i'),
                    'is_user' => $message->role === 'user'
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'session' => [
                        'session_id' => $session->session_id,
                        'title' => $session->title,
                        'animal_type' => $session->animalType ? [
                            'id' => $session->animalType->id,
                            'name' => $session->animalType->name,
                            'category' => $session->animalType->category
                        ] : null,
                        'created_at' => $session->created_at->toISOString(),
                        'last_activity' => $session->last_activity->toISOString()
                    ],
                    'messages' => $messages
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting session: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Gagal memuat detail sesi'
            ], 500);
        }
    }
    
    /**
     * Hapus sesi chat
     */
    public function deleteSession($sessionId): JsonResponse 
    { 
        try {
            $session = AiChatSession::where('session_id', $sessionId)
                ->where('user_id', Auth::id())
                ->first();

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'error' => 'Sesi tidak ditemukan'
                ], 404);
            }

            $sessionTitle = $session->title;
            $session->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sesi "' . $sessionTitle . '" berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error deleting session: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Gagal menghapus sesi'
            ], 500);
        }
    }
    
    /**
     * Dapatkan statistik penggunaan
     */
    public function getUsageStats(): JsonResponse 
    { 
        try {
            $userId = Auth::id();
            
            $totalMessages = AiChatMessage::whereHas('session', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })->count();

            $totalSessions = AiChatSession::where('user_id', $userId)->count();
            
            $todayMessages = AiChatMessage::whereHas('session', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })->whereDate('created_at', today())->count();

            $todaySessions = AiChatSession::where('user_id', $userId)
                ->whereDate('created_at', today())
                ->count();

            $animalStats = AiChatSession::where('user_id', $userId)
                ->with('animalType:id,name')
                ->select('animal_type_id', DB::raw('count(*) as count'))
                ->groupBy('animal_type_id')
                ->get()
                ->map(function($stat) {
                    return [
                        'animal_type' => $stat->animalType ? $stat->animalType->name : 'Umum',
                        'count' => $stat->count
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'total_messages' => $totalMessages,
                    'total_sessions' => $totalSessions,
                    'today_messages' => $todayMessages,
                    'today_sessions' => $todaySessions,
                    'animal_stats' => $animalStats,
                    'free_tier' => true
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting usage stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Gagal memuat statistik'
            ], 500);
        }
    }
    
    /**
     * Simpan feedback pengguna
     */
    public function storeFeedback(Request $request): JsonResponse 
    { 
        try {
            $request->validate([
                'session_id' => 'required|string',
                'rating' => 'required|integer|min:1|max:5',
                'feedback' => 'nullable|string|max:1000'
            ]);

            $session = AiChatSession::where('session_id', $request->session_id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'error' => 'Sesi tidak ditemukan'
                ], 404);
            }

            // Update session dengan rating
            $session->update([
                'rating' => $request->rating,
                'feedback' => $request->feedback
            ]);

            Log::info('User feedback received', [
                'user_id' => Auth::id(),
                'session_id' => $request->session_id,
                'rating' => $request->rating
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Terima kasih atas feedback Anda!'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Feedback tidak valid'
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Error storing feedback: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Gagal menyimpan feedback'
            ], 500);
        }
    }

    /**
     * Generate judul sesi dari pesan pertama
     */
    private function generateSessionTitle(string $message, string $currentTitle): string
    {
        if (str_contains($currentTitle, 'Konsultasi') && str_contains($currentTitle, '-')) {
            $words = preg_split('/\s+/', trim($message));
            $firstWords = array_slice($words, 0, 6);
            $title = implode(' ', $firstWords);
            
            $title = preg_replace('/[^\w\s]/u', '', $title);
            
            if (strlen($title) > 35) {
                $title = substr($title, 0, 32) . '...';
            }
            
            return $title ?: 'Konsultasi';
        }
        
        return $currentTitle;
    }

    /**
     * Pesan sambutan berdasarkan jenis ternak
     */
    private function getWelcomeMessage(string $animalType): string
    {
        $messages = [
            'Ayam' => "Selamat datang! Saya Dr. Peternak, ahli kesehatan unggas dengan pengalaman 15 tahun. Saya siap membantu Anda dengan masalah ayam, mulai dari pencegahan penyakit, manajemen pakan, hingga optimasi produksi. Ceritakan saja kondisi ayam Anda.",
            
            'Sapi' => "Halo! Saya Dr. Peternak, spesialis ternak besar. Dari pengalaman menangani peternakan sapi di berbagai daerah, saya paham betul tantangan yang dihadapi peternak Indonesia. Ada keluhan khusus tentang sapi Anda?",
            
            'Kambing' => "Selamat datang! Saya Dr. Peternak, fokus pada ternak kecil seperti kambing dan domba. Banyak peternak sukses dengan pola manajemen yang tepat. Mari kita diskusikan kondisi kambing Anda.",
            
            'Bebek' => "Halo! Saya Dr. Peternak, ahli perunggasan termasuk bebek/itik. Perawatan bebek punya keunikan tersendiri, terutama di musim penghujan. Saya siap membantu masalah bebek Anda.",
            
            'Umum' => "Selamat datang! Saya Dr. Peternak, asisten kesehatan ternak dengan pengalaman 20+ tahun di lapangan. Saya siap membantu berbagai masalah peternakan Anda. Silakan ceritakan keluhan atau pertanyaan Anda."
        ];

        return $messages[$animalType] ?? $messages['Umum'];
    }

    /**
     * Test koneksi Gemini langsung
     */
    public function testGeminiConnection(): JsonResponse
    {
        try {
            $result = $this->geminiService->testConnection();
            
            return response()->json($result);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error testing connection: ' . $e->getMessage()
            ], 500);
        }
    }
}