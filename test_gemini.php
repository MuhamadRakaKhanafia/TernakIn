<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use App\Services\GeminiService;

$app = require_once 'bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

echo "Testing Gemini Service (without tokens)\n";

$service = app(GeminiService::class);
$result = $service->chat([['role' => 'user', 'content' => 'Halo, apa kabar?']], ['max_tokens' => 50]);

print_r($result);

echo "\nTesting Gemini Service (with tokens)\n";

$resultWithTokens = $service->chat([['role' => 'user', 'content' => 'Halo, apa kabar?']], ['max_tokens' => 50, 'include_tokens' => true]);

print_r($resultWithTokens);
