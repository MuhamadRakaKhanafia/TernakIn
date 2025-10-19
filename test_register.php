<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;

$data = [
    'name' => 'Test User',
    'email' => 'test4@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'phone' => '08123456789',
    'province_id' => '1',
    'city_id' => '1',
    'district' => 'Test District',
    'village' => 'Test Village',
    'detailed_address' => 'Test Address'
];

$request = new Request();
$request->merge($data);

$controller = new AuthController();
$response = $controller->register($request);

echo $response->getContent();
