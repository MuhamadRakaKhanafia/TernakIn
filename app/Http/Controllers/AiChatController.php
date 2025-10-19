<?php

namespace App\Http\Controllers;

use App\Models\AiChatSession;
use App\Models\AiChatMessage;
use App\Models\AnimalType;
use App\Models\Diseases;
use App\Services\OpenAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AiChatController extends Controller
{
    private $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    public function startNewSession(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'animal_type_id' => 'nullable|exists:animal_types,id',
            'initial_message' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $session = AiChatSession::create([
                'session_id' => Str::uuid(),
                'user_id' => Auth::id(),
                'animal_type_id' => $request->animal_type_id,
                'title' => 'Konsultasi Kesehatan Ternak',
                'last_activity' => now()
            ]);

            $initialResponse = null;

            // Add initial message if provided
            if ($request->initial_message) {
                $this->addMessageToSession($session, 'user', $request->initial_message);
                
                // Generate AI response
                $response = $this->generateAIResponse($session, $request->initial_message);
                
                if ($response['success']) {
                    $aiMessage = $this->addMessageToSession($session, 'assistant', $response['content'], [
                        'tokens' => $response['tokens'],
                        'model' => $response['model'],
                        'cost' => $this->calculateCost($response['tokens']['total_tokens'], $response['model'])
                    ]);
                    $initialResponse = $aiMessage;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sesi chat berhasil dibuat',
                'data' => [
                    'session' => $session->load(['animalType']),
                    'initial_response' => $initialResponse
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Gagal membuat sesi chat: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sendMessage(Request $request, $sessionId)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:2000',
            'disease_id' => 'nullable|exists:diseases,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $session = AiChatSession::where('session_id', $sessionId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        DB::beginTransaction();
        try {
            // Add user message
            $userMessage = $this->addMessageToSession($session, 'user', $request->message);

            // Prepare context
            $diseaseData = [];
            if ($request->disease_id) {
                $disease = Diseases::with(['symptoms', 'preventionMethods'])->find($request->disease_id);
                $diseaseData = $this->prepareDiseaseData($disease);
            }

            // Get conversation history
            $conversationHistory = $this->getConversationHistory($session);

            // Generate AI response
            $response = $this->generateAIResponse($session, $request->message, $diseaseData, $conversationHistory);

            if ($response['success']) {
                $aiMessage = $this->addMessageToSession($session, 'assistant', $response['content'], [
                    'tokens' => $response['tokens'],
                    'model' => $response['model'],
                    'cost' => $this->calculateCost($response['tokens']['total_tokens'], $response['model'])
                ]);

                // Update session stats
                $session->update([
                    'message_count' => $session->messages()->count(),
                    'token_count' => $session->messages()->sum('token_count'),
                    'last_activity' => now(),
                    'title' => $this->generateSessionTitle($session, $request->message)
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Pesan berhasil dikirim',
                    'data' => [
                        'user_message' => $userMessage,
                        'ai_response' => $aiMessage,
                        'usage' => $response['tokens']
                    ]
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'error' => $response['error']
                ], 500);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Gagal mengirim pesan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSessionHistory($sessionId)
    {
        $session = AiChatSession::with(['messages', 'animalType'])
            ->where('session_id', $sessionId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $session
        ]);
    }

    public function getUserSessions()
    {
        $sessions = AiChatSession::with(['animalType'])
            ->where('user_id', Auth::id())
            ->orderBy('last_activity', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sessions
        ]);
    }

    public function deleteSession($sessionId)
    {
        $session = AiChatSession::where('session_id', $sessionId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        DB::beginTransaction();
        try {
            // Delete all messages first
            $session->messages()->delete();
            // Then delete session
            $session->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sesi chat berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Gagal menghapus sesi chat: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAiUsageStats()
    {
        $stats = AiChatMessage::whereHas('session', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->selectRaw('SUM(token_count) as total_tokens, SUM(cost) as total_cost, COUNT(*) as total_messages')
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'total_tokens' => $stats->total_tokens ?? 0,
                'total_cost' => $stats->total_cost ?? 0,
                'total_messages' => $stats->total_messages ?? 0
            ]
        ]);
    }

    public function index()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Return the chat interface view
        return view('chat.index');
    }

    // Private helper methods
    private function addMessageToSession(AiChatSession $session, string $role, string $content, array $metadata = []): AiChatMessage
    {
        return $session->messages()->create([
            'role' => $role,
            'content' => $content,
            'metadata' => $metadata,
            'token_count' => $metadata['tokens']['total_tokens'] ?? 0,
            'cost' => $metadata['cost'] ?? 0,
            'model_used' => $metadata['model'] ?? 'gpt-3.5-turbo'
        ]);
    }

    private function generateAIResponse(AiChatSession $session, string $userMessage, array $diseaseData = [], array $history = []): array
    {
        // Prepare messages array
        $messages = $this->openAIService->generateVeterinaryContext($diseaseData);

        // Add conversation history (last 10 messages to manage token limit)
        foreach ($history as $msg) {
            $messages[] = [
                'role' => $msg['role'],
                'content' => $msg['content']
            ];
        }

        // Add current user message
        $messages[] = [
            'role' => 'user',
            'content' => $userMessage
        ];

        return $this->openAIService->chat($messages);
    }

    private function getConversationHistory(AiChatSession $session, int $limit = 10): array
    {
        return $session->messages()
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

    private function prepareDiseaseData(?Diseases $disease): array
    {
        if (!$disease) {
            return [];
        }

        return [
            'name' => $disease->name,
            'causative_agent' => $disease->causative_agent,
            'symptoms' => $disease->symptoms->pluck('name')->implode(', '),
            'prevention' => $disease->preventionMethods->pluck('title')->implode(', '),
            'treatment' => $disease->general_treatment,
            'is_zoonotic' => $disease->is_zoonotic
        ];
    }

    private function calculateCost(int $tokens, string $model): float
    {
        $costPerThousand = match($model) {
            'gpt-4' => 0.03,
            'gpt-4-32k' => 0.06,
            default => 0.002 // gpt-3.5-turbo
        };

        return ($tokens / 1000) * $costPerThousand;
    }

    private function generateSessionTitle(AiChatSession $session, string $latestMessage): string
    {
        // Simple title generation based on the first few words of the latest message
        $words = explode(' ', trim($latestMessage));
        $firstWords = array_slice($words, 0, 5);
        $title = implode(' ', $firstWords);
        
        return strlen($title) > 50 ? substr($title, 0, 47) . '...' : $title;
    }
}