<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Services\GeminiFreeService;

echo "Testing GeminiFreeService...\n";

try {
    $service = new GeminiFreeService();

    echo "Service created successfully\n";

    $result = $service->chat([
        ['role' => 'user', 'content' => 'Apa gejala ayam yang sakit?']
    ], ['max_tokens' => 200]);

    echo "Success: " . ($result['success'] ? 'true' : 'false') . "\n";
    echo "Error: " . ($result['error'] ?? 'none') . "\n";
    echo "Content length: " . strlen($result['content'] ?? '') . "\n";

    if ($result['content']) {
        echo "Content preview: " . substr($result['content'], 0, 100) . "...\n";
    }

} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
