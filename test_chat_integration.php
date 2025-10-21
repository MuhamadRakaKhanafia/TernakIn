<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use App\Services\GeminiService;
use App\Http\Controllers\AiChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

$app = require_once 'bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

echo "Testing AI Chat Integration with Gemini\n";
echo "========================================\n\n";

// Test Gemini Service
echo "1. Testing Gemini Service...\n";
$service = app(GeminiService::class);
$result = $service->chat([['role' => 'user', 'content' => 'Halo, saya punya masalah dengan sapi yang batuk']]);
echo "   Success: " . ($result['success'] ? 'Yes' : 'No') . "\n";
if ($result['success']) {
    echo "   Response: " . substr($result['content'], 0, 100) . "...\n";
} else {
    echo "   Error: " . ($result['error'] ?? 'Unknown error') . "\n";
}
echo "\n";

// Test Controller Methods (simulated)
echo "2. Testing AiChatController methods...\n";

// Simulate startSession
echo "   Testing startSession method...\n";
$controller = app(AiChatController::class);

// Create a mock request
$request = new Request();
$request->merge([
    'animal_type_id' => null,
    'initial_message' => 'Sapi saya batuk terus'
]);

// We can't fully test controller methods without authentication, but we can check if the service is properly injected
echo "   Controller instantiated successfully\n";
echo "   Gemini service injected: " . (isset($controller->geminiService) ? 'Yes' : 'No') . "\n";

echo "\n3. Database Tables Check...\n";
try {
    $tables = [
        'ai_chat_sessions',
        'ai_chat_messages',
        'ai_usage_analytics'
    ];

    foreach ($tables as $table) {
        $count = \Illuminate\Support\Facades\DB::table($table)->count();
        echo "   $table: $count records\n";
    }
} catch (Exception $e) {
    echo "   Error checking tables: " . $e->getMessage() . "\n";
}

echo "\n4. Configuration Check...\n";
$apiKey = config('services.google.gemini.api_key');
echo "   Gemini API Key configured: " . (!empty($apiKey) ? 'Yes' : 'No') . "\n";

$url = config('services.google.gemini.url');
echo "   Gemini URL configured: " . (!empty($url) ? 'Yes' : 'No') . "\n";

echo "\nTesting completed!\n";
