<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use App\Models\User;

// Get the user we created earlier
$user = User::where('email', 'test4@example.com')->first();
if (!$user) {
    echo json_encode(['error' => 'User not found']);
    exit;
}

// Create a request with user authentication
$request = new Request();
$request->setUserResolver(function () use ($user) {
    return $user;
});

$controller = new AuthController();
$response = $controller->profile($request);

echo $response->getContent();
