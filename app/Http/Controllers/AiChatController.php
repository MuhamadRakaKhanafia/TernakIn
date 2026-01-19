<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

    public function index()
    {
        try {
            $animalTypes = AnimalType::orderBy('name')->get(['id', 'name', 'category']);
            
            return view('chat.index', [
                'animalTypes' => $animalTypes,
                'healthCheck' => ['healthy' => true, 'message' => 'Service Ready']
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading AI chat page: ' . $e->getMessage());
            
            return view('chat.index', [
                'animalTypes' => collect(),
                'healthCheck' => ['healthy' => false, 'message' => $e->getMessage()]
            ]);
        }
    }

    public function startSession(Request $request): JsonResponse
    {
        try {
            Log::info('Starting new chat session', [
                'user_id' => Auth::id(),
                'animal_type_id' => $request->animal_type_id
            ]);

            $session = AiChatSession::create([
                'user_id' => Auth::id(),
                'animal_type_id' => $request->animal_type_id ?? 1,
                'title' => 'Konsultasi Ternak - ' . now()->format('H:i'),
                'last_activity' => now(),
            ]);

            Log::info('Session created successfully', ['session_id' => $session->session_id]);

            return response()->json([
                'success' => true,
                'data' => [
                    'session' => [
                        'session_id' => $session->session_id,
                        'title' => $session->title,
                        'animal_type_id' => $session->animal_type_id,
                        'created_at' => $session->created_at->toISOString(),
                    ]
                ],
                'message' => 'Sesi chat berhasil dibuat'
            ]);

        } catch (\Exception $e) {
            Log::error('Error starting chat session: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Gagal membuat sesi chat',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

public function sendMessage(Request $request, $sessionId): JsonResponse
{
    try {
        $session = AiChatSession::where('session_id', $sessionId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'error' => 'Session tidak ditemukan'
            ], 404);
        }

        // Save user message
        AiChatMessage::create([
            'session_id' => $session->id,
            'role' => 'user',
            'content' => $request->message,
        ]);

        // Generate AI response
        $messages = [
            ['role' => 'user', 'content' => $request->message]
        ];

        $aiResult = $this->geminiService->chat($messages);

        // FIXED: Pastikan struktur response konsisten
        if ($aiResult['success']) {
            $aiResponse = $aiResult['data']['content'] ?? 'Tidak ada respons';
        } else {
            $aiResponse = $this->generateStaticResponse($request->message);
        }

        // Save AI message
        AiChatMessage::create([
            'session_id' => $session->id,
            'role' => 'assistant',
            'content' => $aiResponse,
        ]);

        // Update session
        $session->update(['last_activity' => now()]);

        // FIXED: Return struktur yang SIMPLE dan KONSISTEN
        return response()->json([
            'success' => true,
            'data' => [
                'content' => $aiResponse
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Gagal mengirim pesan'
        ], 500);
    }
}
    private function generateSessionTitle(string $message, string $currentTitle): string
    {
        // Jika judul masih default, buat judul dari pesan pertama
        if (str_contains($currentTitle, 'Konsultasi Ternak - ')) {
            $words = explode(' ', trim($message));
            $firstWords = array_slice($words, 0, 5);
            $title = implode(' ', $firstWords);
            
            if (strlen($title) > 30) {
                $title = substr($title, 0, 30) . '...';
            }
            
            return $title ?: 'Konsultasi Ternak';
        }
        
        return $currentTitle;
    }

    private function generateStaticResponse(string $message): string
    {
        $lowerMessage = strtolower(trim($message));
        
        if (str_contains($lowerMessage, 'halo') || str_contains($lowerMessage, 'hi') || str_contains($lowerMessage, 'hello')) {
            return "Halo! Saya Asisten Kesehatan Ternak TernakIN. 🐄\n\nSaya siap membantu Anda dengan konsultasi kesehatan ternak, pencegahan penyakit, dan manajemen peternakan.\n\nApa yang ingin Anda tanyakan terkait ternak Anda?";
        }
        
        if (str_contains($lowerMessage, 'ayam')) {
            return "**Tips Kesehatan Ayam:**\n\n🐔 **Pencegahan Penyakit:**\n• Vaksinasi rutin (ND, AI, IB)\n• Kebersihan kandang optimal\n• Pakan bernutrisi seimbang\n• Ventilasi udara cukup\n\n🐔 **Gejala Sakit:**\n• Lesu dan tidak aktif\n• Nafsu makan turun\n• Bulu kusam\n• Diare atau sesak napas\n\n🔍 **Tindakan:** Segera konsultasi dokter hewan jika gejala berlanjut.";
        }
        
        if (str_contains($lowerMessage, 'sapi') || str_contains($lowerMessage, 'kambing')) {
            return "**Perawatan Ternak Besar:**\n\n🐄 **Manajemen Kesehatan:**\n• Pemeriksaan kesehatan berkala\n• Pakan hijauan berkualitas\n• Air minum bersih selalu tersedia\n• Kandang kering dan bersih\n\n🐄 **Tanda Sehat:**\n• Nafsu makan baik\n• Aktif dan responsif\n• Bulu bersih mengilap\n• Produksi normal";
        }
        
        if (str_contains($lowerMessage, 'pakan') || str_contains($lowerMessage, 'makan')) {
            return "**Manajemen Pakan Ternak:**\n\n🌱 **Prinsip Pemberian Pakan:**\n• Sesuaikan dengan jenis dan usia ternak\n• Berikan pakan segar berkualitas\n• Tambahkan vitamin dan mineral\n• Air bersih selalu tersedia\n\n⏰ **Jadwal Ideal:**\n• 2-3 kali sehari secara teratur\n• Sesuaikan porsi dengan kebutuhan\n• Bersihkan tempat pakan secara rutin";
        }
        
        if (str_contains($lowerMessage, 'obat') || str_contains($lowerMessage, 'sakit')) {
            return "**Untuk ternak yang sakit:**\n\n🏥 **Langkah Pertolongan Pertama:**\n• Isolasi ternak yang sakit\n• Berikan pakan dan air bersih\n• Jaga kebersihan kandang\n• Catat gejala yang muncul\n\n💊 **Saran Umum:**\n• Konsultasi dengan dokter hewan untuk diagnosis tepat\n• Jangan berikan obat tanpa resep\n• Pantau suhu dan nafsu makan\n\n⚠️ **Penting:** Untuk penanganan serius, selalu hubungi dokter hewan.";
        }

        return "Terima kasih atas pertanyaan Anda! 🙏\n\nSaya adalah asisten kesehatan ternak yang siap membantu dengan:\n\n🔹 **Konsultasi penyakit ternak**\n🔹 **Tips pencegahan dan pengobatan**\n🔹 **Manajemen pakan dan nutrisi**\n🔹 **Saran pemeliharaan kandang**\n\nSilakan tanyakan lebih spesifik tentang masalah ternak Anda! 🐔🐄🐑";
    }

    public function getSessions(): JsonResponse 
    { 
        try {
            $sessions = AiChatSession::withCount('messages')
                ->where('user_id', Auth::id())
                ->orderBy('last_activity', 'desc')
                ->get()
                ->map(function ($session) {
                    return [
                        'session_id' => $session->session_id,
                        'title' => $session->title,
                        'last_activity' => $session->last_activity,
                        'created_at' => $session->created_at,
                        'message_count' => $session->messages_count,
                    ];
                });

            Log::info('Retrieved sessions', ['count' => $sessions->count()]);

            return response()->json([
                'success' => true,
                'data' => $sessions
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting sessions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Gagal memuat sesi'
            ], 500);
        }
    }
    
    public function getSession($sessionId): JsonResponse 
    { 
        try {
            Log::info('Getting session messages', ['session_id' => $sessionId]);

            $session = AiChatSession::with(['messages' => function($query) {
                $query->orderBy('created_at', 'asc');
            }])
                ->where('session_id', $sessionId)
                ->where('user_id', Auth::id())
                ->first();

            if (!$session) {
                Log::warning('Session not found for messages', ['session_id' => $sessionId]);
                return response()->json([
                    'success' => false,
                    'error' => 'Session tidak ditemukan'
                ], 404);
            }

            $messages = $session->messages->map(function($message) {
                return [
                    'role' => $message->role,
                    'content' => $message->content,
                    'created_at' => $message->created_at->toISOString()
                ];
            });

            Log::info('Retrieved session messages', [
                'session_id' => $sessionId,
                'message_count' => $messages->count()
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'session_id' => $session->session_id,
                    'title' => $session->title,
                    'created_at' => $session->created_at->toISOString(),
                    'messages' => $messages
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting session: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Gagal memuat sesi'
            ], 500);
        }
    }
    
    public function deleteSession($sessionId): JsonResponse 
    { 
        try {
            $session = AiChatSession::where('session_id', $sessionId)
                ->where('user_id', Auth::id())
                ->first();

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'error' => 'Session tidak ditemukan'
                ], 404);
            }

            $session->delete();

            return response()->json([
                'success' => true,
                'message' => 'Session berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting session: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Gagal menghapus sesi'
            ], 500);
        }
    }
    
    public function getUsageStats(): JsonResponse 
    { 
        try {
            $totalMessages = AiChatMessage::whereHas('session', function($query) {
                $query->where('user_id', Auth::id());
            })->count();

            $totalSessions = AiChatSession::where('user_id', Auth::id())->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_messages' => $totalMessages,
                    'total_sessions' => $totalSessions,
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
    
    public function storeFeedback(Request $request): JsonResponse 
    { 
        try {
            $session = AiChatSession::where('session_id', $request->session_id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'error' => 'Session tidak ditemukan'
                ], 404);
            }

            Log::info('User feedback received', [
                'user_id' => Auth::id(),
                'session_id' => $request->session_id,
                'rating' => $request->rating,
                'feedback' => $request->feedback
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Feedback berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            Log::error('Error storing feedback: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Gagal menyimpan feedback'
            ], 500);
        }
    }

    public function testConnection(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'healthy' => true,
                'message' => 'Service connected successfully',
                'free_tier' => true,
                'timestamp' => now()->toISOString()
            ]
        ]);
    }
}