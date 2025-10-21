<?php
// app/Http/Controllers/AiChatController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StartChatSessionRequest;
use App\Http\Requests\SendMessageRequest;
use App\Http\Requests\FeedbackRequest;
use App\Models\AiChatSession;
use App\Models\AiChatMessage;
use App\Models\AiUsageAnalytic;
use App\Models\AnimalType;
use App\Services\GeminiService;
use Exception;

class AiChatController extends Controller
{
    private $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Menampilkan halaman chat AI
     */
    public function index()
    {
        try {
            Log::info('User mengakses halaman AI Chat', [
                'user_id' => Auth::id(),
                'ip' => request()->ip()
            ]);

            $animalTypes = AnimalType::orderBy('name')->get(['id', 'name', 'category']);

            return view('chat.index', compact('animalTypes'));

        } catch (Exception $e) {
            Log::error('Error loading AI chat page: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memuat halaman konsultasi AI.');
        }
    }

    /**
     * Memulai sesi chat baru
     */
    public function startSession(StartChatSessionRequest $request): JsonResponse
    {
        // Validasi sudah dilakukan oleh StartChatSessionRequest
        DB::beginTransaction();
        try {
            $session = $this->createChatSession($request);
            $initialResponse = $this->handleInitialMessage($request, $session);

            DB::commit();

            Log::info('Sesi chat baru dibuat', [
                'user_id' => Auth::id(),
                'session_id' => $session->session_id,
                'animal_type_id' => $request->animal_type_id
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'session' => $session->load('animalType'),
                    'initial_response' => $initialResponse
                ]
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error starting chat session: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request_data' => $request->validated()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Gagal membuat sesi chat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengirim pesan ke AI
     */
    public function sendMessage(SendMessageRequest $request, $sessionId): JsonResponse
    {
        // Validasi sudah dilakukan oleh SendMessageRequest
        DB::beginTransaction();
        try {
            // Validasi session ownership
            $session = $this->validateSessionOwnership($sessionId);

            // Simpan pesan user
            $userMessage = $this->saveUserMessage($session->id, $request->message);

            // Generate context dari history chat
            $chatHistory = $this->getChatHistory($session->id);

            // Dapatkan response dari AI
            $aiResponse = $this->getAIResponse($request->message, $chatHistory);

            // Simpan pesan AI
            $assistantMessage = $this->saveAssistantMessage($session->id, $aiResponse['content']);

            // Update last activity
            $session->update(['last_activity' => now()]);

            // Log usage analytics
            $this->logUsageAnalytics($session, $aiResponse['tokens']);

            DB::commit();

            Log::info('Pesan berhasil diproses', [
                'user_id' => Auth::id(),
                'session_id' => $sessionId,
                'message_length' => strlen($request->message)
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'ai_response' => $assistantMessage,
                    'usage' => $aiResponse['tokens']
                ]
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error sending message: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'session_id' => $sessionId
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Gagal mengirim pesan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan daftar sesi chat user
     */
    public function getSessions(): JsonResponse
    {
        try {
            $sessions = AiChatSession::with(['animalType'])
                ->where('user_id', Auth::id())
                ->withCount('messages')
                ->orderBy('last_activity', 'desc')
                ->get()
                ->map(function ($session) {
                    return [
                        'session_id' => $session->session_id,
                        'title' => $session->title,
                        'animal_type' => $session->animalType,
                        'last_activity' => $session->last_activity,
                        'messages_count' => $session->messages_count,
                        'created_at' => $session->created_at
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $sessions
            ]);

        } catch (Exception $e) {
            Log::error('Error getting sessions: ' . $e->getMessage(), [
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Gagal memuat daftar sesi'
            ], 500);
        }
    }

    /**
     * Mendapatkan detail sesi dan pesannya
     */
    public function getSession($sessionId): JsonResponse
    {
        try {
            $session = $this->validateSessionOwnership($sessionId);

            $sessionData = $session->load(['animalType', 'messages' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }]);

            return response()->json([
                'success' => true,
                'data' => $sessionData
            ]);

        } catch (Exception $e) {
            Log::error('Error getting session: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'session_id' => $sessionId
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Gagal memuat sesi'
            ], 500);
        }
    }

    /**
     * Menghapus sesi chat
     */
    public function deleteSession($sessionId): JsonResponse
    {
        DB::beginTransaction();
        try {
            $session = $this->validateSessionOwnership($sessionId);

            // Hapus related records first
            AiUsageAnalytic::where('session_id', $session->id)->delete();
            AiChatMessage::where('session_id', $session->id)->delete();
            
            // Hapus session
            $session->delete();

            DB::commit();

            Log::info('Sesi chat dihapus', [
                'user_id' => Auth::id(),
                'session_id' => $sessionId
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sesi berhasil dihapus'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting session: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'session_id' => $sessionId
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Gagal menghapus sesi'
            ], 500);
        }
    }

    /**
     * Mendapatkan statistik penggunaan AI
     */
    public function getUsageStats(): JsonResponse
    {
        try {
            $stats = AiUsageAnalytic::where('user_id', Auth::id())
                ->select([
                    DB::raw('COUNT(*) as total_messages'),
                    DB::raw('SUM(total_tokens) as total_tokens'),
                    DB::raw('SUM(cost) as total_cost'),
                    DB::raw('COUNT(DISTINCT session_id) as total_sessions')
                ])
                ->first();

            $dailyStats = AiUsageAnalytic::where('user_id', Auth::id())
                ->whereDate('created_at', today())
                ->select([
                    DB::raw('COUNT(*) as today_messages'),
                    DB::raw('SUM(total_tokens) as today_tokens')
                ])
                ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_messages' => $stats->total_messages ?? 0,
                    'total_tokens' => $stats->total_tokens ?? 0,
                    'total_cost' => $stats->total_cost ?? 0,
                    'total_sessions' => $stats->total_sessions ?? 0,
                    'today_messages' => $dailyStats->today_messages ?? 0,
                    'today_tokens' => $dailyStats->today_tokens ?? 0
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Error getting usage stats: ' . $e->getMessage(), [
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Gagal memuat statistik'
            ], 500);
        }
    }

    /**
     * Menyimpan feedback untuk response AI
     */
    public function storeFeedback(FeedbackRequest $request): JsonResponse
    {
        // Validasi sudah dilakukan oleh FeedbackRequest
        try {
            // Validasi ownership
            $session = $this->validateSessionOwnership($request->session_id);
            $message = AiChatMessage::where('id', $request->message_id)
                ->where('session_id', $session->id)
                ->firstOrFail();

            // Simpan feedback (bisa di-extend ke table terpisah)
            Log::info('User memberikan feedback', [
                'user_id' => Auth::id(),
                'message_id' => $request->message_id,
                'rating' => $request->rating,
                'session_id' => $request->session_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Feedback berhasil disimpan'
            ]);

        } catch (Exception $e) {
            Log::error('Error storing feedback: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'feedback_data' => $request->validated()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Gagal menyimpan feedback'
            ], 500);
        }
    }

    /**
     * Melacak kategori pesan untuk analytics
     */
    public function trackMessageCategory(Request $request): JsonResponse
    {
        // Validasi sederhana untuk analytics tracking
        $request->validate([
            'session_id' => 'required|exists:ai_chat_sessions,session_id',
            'categories' => 'required|array',
            'message_length' => 'required|integer|min:1'
        ]);

        try {
            $session = $this->validateSessionOwnership($request->session_id);

            Log::info('Message category tracked', [
                'user_id' => Auth::id(),
                'session_id' => $request->session_id,
                'categories' => $request->categories,
                'message_length' => $request->message_length
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Analytics tracked'
            ]);

        } catch (Exception $e) {
            Log::error('Error tracking message category: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Gagal melacak analytics'
            ], 500);
        }
    }

    // ==================== PRIVATE HELPER METHODS ====================

    /**
     * Membuat session chat baru
     */
    private function createChatSession(StartChatSessionRequest $request): AiChatSession
    {
        $validated = $request->validated();

        return AiChatSession::create([
            'user_id' => Auth::id(),
            'animal_type_id' => $validated['animal_type_id'] ?? null,
            'title' => $this->generateSessionTitle($validated['initial_message'] ?? null),
            'last_activity' => now(),
        ]);
    }

    /**
     * Generate judul session otomatis
     */
    private function generateSessionTitle(?string $message): string
    {
        if (!$message) {
            return 'Konsultasi Kesehatan Ternak';
        }

        // Coba ekstrak kata kunci dari pesan
        $keywords = $this->extractKeywords($message);
        
        if (!empty($keywords)) {
            return 'Konsultasi: ' . implode(', ', array_slice($keywords, 0, 3));
        }

        return substr($message, 0, 50) . (strlen($message) > 50 ? '...' : '');
    }

    /**
     * Ekstrak kata kunci dari pesan
     */
    private function extractKeywords(string $message): array
    {
        $commonWords = ['saya', 'ada', 'yang', 'dengan', 'pada', 'untuk', 'dari', 'ke'];
        $words = array_filter(
            preg_split('/\s+/', strtolower($message)),
            function ($word) use ($commonWords) {
                return strlen($word) > 3 && !in_array($word, $commonWords);
            }
        );

        return array_slice(array_unique($words), 0, 5);
    }

    /**
     * Handle pesan awal jika ada
     */
    private function handleInitialMessage(StartChatSessionRequest $request, AiChatSession $session): ?AiChatMessage
    {
        $validated = $request->validated();
        
        if (!isset($validated['initial_message'])) {
            return null;
        }

        $userMessage = AiChatMessage::create([
            'session_id' => $session->id,
            'role' => 'user',
            'content' => $validated['initial_message'],
        ]);

        $aiResponse = $this->getAIResponse($validated['initial_message'], []);

        return AiChatMessage::create([
            'session_id' => $session->id,
            'role' => 'assistant',
            'content' => $aiResponse['content'],
        ]);
    }

    /**
     * Validasi kepemilikan session
     */
    private function validateSessionOwnership(string $sessionId): AiChatSession
    {
        $session = AiChatSession::where('session_id', $sessionId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$session) {
            throw new Exception('Session tidak ditemukan atau tidak memiliki akses');
        }

        return $session;
    }

    /**
     * Simpan pesan user
     */
    private function saveUserMessage(int $sessionId, string $message): AiChatMessage
    {
        return AiChatMessage::create([
            'session_id' => $sessionId,
            'role' => 'user',
            'content' => $message,
        ]);
    }

    /**
     * Simpan pesan assistant
     */
    private function saveAssistantMessage(int $sessionId, string $content): AiChatMessage
    {
        return AiChatMessage::create([
            'session_id' => $sessionId,
            'role' => 'assistant',
            'content' => $content,
        ]);
    }

    /**
     * Dapatkan history chat untuk context
     */
    private function getChatHistory(int $sessionId, int $limit = 10): array
    {
        return AiChatMessage::where('session_id', $sessionId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->map(function ($message) {
                return [
                    'role' => $message->role,
                    'content' => $message->content
                ];
            })
            ->toArray();
    }

    /**
     * Dapatkan response dari AI service
     */
    private function getAIResponse(string $message, array $history): array
    {
        $result = $this->geminiService->chat(
            $this->buildMessageContext($message, $history),
            ['include_tokens' => true]
        );

        if (!$result['success']) {
            throw new Exception($result['error'] ?? 'AI service error');
        }

        return [
            'content' => $result['content'],
            'tokens' => $result['tokens'] ?? ['prompt_tokens' => 0, 'completion_tokens' => 0, 'total_tokens' => 0]
        ];
    }

    /**
     * Build context messages untuk AI
     */
    private function buildMessageContext(string $currentMessage, array $history): array
    {
        $systemPrompt = [
            'role' => 'user',
            'content' => "Anda adalah asisten kesehatan ternak yang ahli. Berikan saran tentang penyakit ternak, pencegahan, pengobatan, dan manajemen peternakan. Gunakan bahasa Indonesia yang mudah dipahami. Fokus pada solusi praktis untuk peternak. Selalu tekankan pentingnya konsultasi dengan dokter hewan untuk diagnosis yang akurat."
        ];

        $messages = [$systemPrompt];

        // Add history messages
        foreach ($history as $message) {
            $messages[] = $message;
        }

        // Add current message
        $messages[] = [
            'role' => 'user',
            'content' => $currentMessage
        ];

        return $messages;
    }

    /**
     * Log usage analytics
     */
    private function logUsageAnalytics(AiChatSession $session, array $tokens): void
    {
        AiUsageAnalytic::create([
            'user_id' => Auth::id(),
            'session_id' => $session->id,
            'input_tokens' => $tokens['prompt_tokens'] ?? 0,
            'output_tokens' => $tokens['completion_tokens'] ?? 0,
            'total_tokens' => $tokens['total_tokens'] ?? 0,
            'cost' => $this->calculateCost($tokens['total_tokens'] ?? 0),
        ]);
    }

    /**
     * Calculate cost berdasarkan tokens (contoh sederhana)
     */
    private function calculateCost(int $totalTokens): float
    {
        // Contoh calculation: $0.0001 per token
        return $totalTokens * 0.0001;
    }
}