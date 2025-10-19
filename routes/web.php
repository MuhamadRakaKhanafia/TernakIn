<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\AnimalTypeController;
use App\Http\Controllers\DiseaseController;
use App\Http\Controllers\SymptomController;
use App\Http\Controllers\PreventionController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AiChatController;

// Public routes
Route::get('/', function () {
    return view('index');
})->name('dashboard');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/profile', [AuthController::class, 'profile'])->middleware('auth:sanctum');
Route::put('/profile', [AuthController::class, 'updateProfile'])->middleware('auth:sanctum');

// Social Auth Routes
Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);
Route::get('/auth/facebook', [SocialAuthController::class, 'redirectToFacebook'])->name('auth.facebook');
Route::get('/auth/facebook/callback', [SocialAuthController::class, 'handleFacebookCallback']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    // Animal Types
    Route::get('/animal-types', [AnimalTypeController::class, 'index']);
    Route::get('/animal-types/{id}', [AnimalTypeController::class, 'show']);
    Route::post('/animal-types', [AnimalTypeController::class, 'store']);
    Route::put('/animal-types/{id}', [AnimalTypeController::class, 'update']);
    Route::delete('/animal-types/{id}', [AnimalTypeController::class, 'destroy']);

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

    // AI Chat Routes - Keep only the view route in web.php
    Route::get('/ai-chat', [AiChatController::class, 'index'])->name('ai-chat.index');
});

// Web Routes for Views (no auth required for demo)
Route::get('/web/diseases', [DiseaseController::class, 'webIndex'])->name('diseases.index');
Route::get('/web/diseases/{id}', [DiseaseController::class, 'webShow'])->name('diseases.show');
Route::get('/web/symptoms', [SymptomController::class, 'webIndex'])->name('symptoms.index');
Route::get('/web/medicines', [MedicineController::class, 'webIndex'])->name('medicines.index');
Route::get('/web/medicines/{id}', [MedicineController::class, 'webShow'])->name('medicines.show');
Route::get('/web/articles', [ArticleController::class, 'webIndex'])->name('articles.index');
Route::get('/web/articles/{slug}', [ArticleController::class, 'webShow'])->name('articles.show');
Route::get('/web/animal-types', [AnimalTypeController::class, 'webIndex'])->name('animal-types.index');
Route::get('/web/preventions', [PreventionController::class, 'webIndex'])->name('preventions.index');

// Additional web routes for missing functionality
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/dashboard', function () {
    return view('index');
})->name('dashboard');
