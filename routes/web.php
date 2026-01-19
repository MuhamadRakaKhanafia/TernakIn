<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\AnimalTypeController;
use App\Http\Controllers\DiseaseController;
use App\Http\Controllers\SymptomController;
use App\Http\Controllers\PreventionController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AiChatController;
use App\Http\Controllers\LivestockController;
use App\Http\Controllers\WelcomeController;

// Public routes
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/welcome', [WelcomeController::class, 'index'])->name('welcome.index');

// Auth Routes
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// API untuk dynamic dropdown cities
Route::get('/api/cities/{province_id}', [AuthController::class, 'getCities'])->name('api.cities');

// Social Auth Routes
Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

// Web Routes for Public Views (no auth required)
Route::get('/web/diseases', [DiseaseController::class, 'webIndex'])->name('diseases.public.index');
Route::get('/web/diseases/{id}', [DiseaseController::class, 'webShow'])->name('diseases.public.show');
Route::get('/web/symptoms', [SymptomController::class, 'webIndex'])->name('symptoms.public.index');
Route::get('/web/articles', [ArticleController::class, 'webIndex'])->name('articles.public.index');
Route::get('/web/articles/{slug}', [ArticleController::class, 'webShow'])->name('articles.public.show');
Route::get('/web/animal-types', [AnimalTypeController::class, 'webIndex'])->name('animal-types.public.index');
Route::get('/web/preventions', [PreventionController::class, 'webIndex'])->name('preventions.public.index');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    Route::get('/welcome', function () {
        return view('welcome');
    })->name('welcome.index');

    // Dashboard
    Route::get('/dashboard', [LivestockController::class, 'index'])->name('dashboard');
    Route::post('/livestock', [LivestockController::class, 'store'])->name('livestock.store');

    // ==================== AI CHAT ROUTES ====================
    Route::prefix('chat')->name('chat.')->group(function () {
        // Main Chat Page
        Route::get('/', [AiChatController::class, 'index'])->name('index');
        
        // Session Management
        Route::post('/sessions/start', [AiChatController::class, 'startSession'])->name('sessions.start');
        Route::get('/sessions', [AiChatController::class, 'getSessions'])->name('sessions.list');
        Route::get('/sessions/{sessionId}', [AiChatController::class, 'getSession'])->name('sessions.get');
        Route::delete('/sessions/{sessionId}', [AiChatController::class, 'deleteSession'])->name('sessions.delete');
        
        // Message Handling
        Route::post('/sessions/{sessionId}/send', [AiChatController::class, 'sendMessage'])->name('messages.send');
        
        // Analytics & Stats
        Route::get('/usage-stats', [AiChatController::class, 'getUsageStats'])->name('usage.stats');
        Route::post('/feedback', [AiChatController::class, 'storeFeedback'])->name('feedback.store');
        
        // Connection Test
        Route::get('/test-connection', [AiChatController::class, 'testConnection'])->name('test.connection');
    });

    // Animal Types
    Route::get('/animal-types', [AnimalTypeController::class, 'index'])->name('animal-types.index');
    Route::get('/animal-types/{id}', [AnimalTypeController::class, 'show'])->name('animal-types.show');
    Route::post('/animal-types', [AnimalTypeController::class, 'store'])->name('animal-types.store');
    Route::put('/animal-types/{id}', [AnimalTypeController::class, 'update'])->name('animal-types.update');
    Route::delete('/animal-types/{id}', [AnimalTypeController::class, 'destroy'])->name('animal-types.destroy');

    // Diseases
    Route::get('/diseases', [DiseaseController::class, 'index'])->name('diseases.index');
    Route::get('/diseases/{disease}', [DiseaseController::class, 'show'])->name('diseases.show');
    Route::post('/diseases/quick-diagnosis', [DiseaseController::class, 'quickDiagnosis'])->name('diseases.quick-diagnosis');
    Route::get('/diseases/search', [DiseaseController::class, 'searchDiseases'])->name('diseases.search');

    // Symptoms
    Route::get('/symptoms', [SymptomController::class, 'index'])->name('symptoms.index');
    Route::get('/symptoms/common/{animalTypeId}', [SymptomController::class, 'getCommonSymptoms'])->name('symptoms.common');

    // Prevention
    Route::get('/preventions', [PreventionController::class, 'index'])->name('preventions.index');
    Route::get('/preventions/disease/{diseaseId}', [PreventionController::class, 'getByDisease'])->name('preventions.by-disease');
    Route::get('/preventions/tips', [PreventionController::class, 'getPreventionTips'])->name('preventions.tips');

    // Anallytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/{id}', [AnalyticsController::class, 'show'])->name('analytics.show');
    // Articles
    Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/{slug}', [ArticleController::class, 'show'])->name('articles.show');
    Route::get('/articles/popular', [ArticleController::class, 'popularArticles'])->name('articles.popular');
    Route::get('/articles/recent', [ArticleController::class, 'recentArticles'])->name('articles.recent');

    // Livestock
    Route::get('/livestock', [LivestockController::class, 'index'])->name('livestock.index');
    Route::get('/livestock/create', [LivestockController::class, 'create'])->name('livestock.create');
    Route::post('/livestock', [LivestockController::class, 'store'])->name('livestock.store');
    Route::get('/livestock/{livestock}', [LivestockController::class, 'show'])->name('livestock.show');
    Route::get('/livestock/{livestock}/edit', [LivestockController::class, 'edit'])->name('livestock.edit');
    Route::put('/livestock/{livestock}', [LivestockController::class, 'update'])->name('livestock.update');
    Route::delete('/livestock/{livestock}', [LivestockController::class, 'destroy'])->name('livestock.destroy');
});

// API Routes
Route::middleware(['auth'])->prefix('api')->group(function () {
    // Animal Types untuk dropdown
    Route::get('/animal-types', [AnimalTypeController::class, 'apiIndex'])->name('api.animal-types');
});

// Fallback route
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});