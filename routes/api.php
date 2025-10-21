<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnimalTypeController;
use App\Http\Controllers\DiseaseController;
use App\Http\Controllers\SymptomController;
use App\Http\Controllers\PreventionController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AiChatController;
use App\Http\Controllers\ProvinceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Provinces and Cities
Route::get('/provinces', [ProvinceController::class, 'index']);
Route::get('/provinces/{id}', [ProvinceController::class, 'show']);
Route::get('/provinces/{provinceId}/cities', [ProvinceController::class, 'getCities']);

// Animal Types (moved outside sanctum middleware for AJAX calls from web pages)
Route::middleware('auth:web')->group(function () {
    Route::get('/animal-types', [AnimalTypeController::class, 'index']);
    Route::get('/animal-types/{id}', [AnimalTypeController::class, 'show']);
    Route::post('/animal-types', [AnimalTypeController::class, 'store']);
    Route::put('/animal-types/{id}', [AnimalTypeController::class, 'update']);
    Route::delete('/animal-types/{id}', [AnimalTypeController::class, 'destroy']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    // Diseases
    Route::get('/diseases', [DiseaseController::class, 'index']);
    Route::get('/diseases/{id}', [DiseaseController::class, 'show']);
    Route::post('/diseases/quick-diagnosis', [DiseaseController::class, 'quickDiagnosis']);
    Route::get('/diseases/search', [DiseaseController::class, 'searchDiseases']);

    // Symptoms
    Route::get('/symptoms', [SymptomController::class, 'index']);
    Route::get('/symptoms/common/{animalTypeId}', [SymptomController::class, 'getCommonSymptoms']);

    // Prevention
    Route::get('/preventions/disease/{diseaseId}', [PreventionController::class, 'getByDisease']);
    Route::get('/preventions/tips', [PreventionController::class, 'getPreventionTips']);

    // Medicines
    Route::get('/medicines', [MedicineController::class, 'index']);
    Route::get('/medicines/{id}', [MedicineController::class, 'show']);
    Route::get('/medicines/disease/{diseaseId}', [MedicineController::class, 'getMedicinesByDisease']);

    // Articles
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/{slug}', [ArticleController::class, 'show']);
    Route::get('/articles/popular', [ArticleController::class, 'popularArticles']);
    Route::get('/articles/recent', [ArticleController::class, 'recentArticles']);
});


