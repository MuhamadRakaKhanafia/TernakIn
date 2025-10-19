<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;

$data = [
    'email' => 'test4@example.com',
    'password' => 'password123'
];

$request = new Request();
$request->merge($data);

$controller = new AuthController();
$response = $controller->login($request);

echo $response->getContent();
