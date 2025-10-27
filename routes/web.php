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
    return view('welcome');
})->name('home');

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
Route::get('/auth/facebook', [SocialAuthController::class, 'redirectToFacebook'])->name('auth.facebook');
Route::get('/auth/facebook/callback', [SocialAuthController::class, 'handleFacebookCallback']);

// Web Routes for Public Views (no auth required)
Route::get('/web/diseases', [DiseaseController::class, 'webIndex'])->name('diseases.public.index');
Route::get('/web/diseases/{id}', [DiseaseController::class, 'webShow'])->name('diseases.public.show');
Route::get('/web/symptoms', [SymptomController::class, 'webIndex'])->name('symptoms.public.index');
Route::get('/web/medicines', [MedicineController::class, 'webIndex'])->name('medicines.public.index');
Route::get('/web/medicines/{id}', [MedicineController::class, 'webShow'])->name('medicines.public.show');
Route::get('/web/articles', [ArticleController::class, 'webIndex'])->name('articles.public.index');
Route::get('/web/articles/{slug}', [ArticleController::class, 'webShow'])->name('articles.public.show');
Route::get('/web/animal-types', [AnimalTypeController::class, 'webIndex'])->name('animal-types.public.index');
Route::get('/web/preventions', [PreventionController::class, 'webIndex'])->name('preventions.public.index');

// Protected routes - Web Session Based
Route::middleware(['auth'])->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Dashboard
    Route::get('/dashboard', function () {
        return view('index');
    })->name('dashboard');

    // AI Chat Route
    Route::get('/chat', [AiChatController::class, 'index'])->name('chat.index');

    // Animal Types
    Route::get('/animal-types', [AnimalTypeController::class, 'index'])->name('animal-types.index');
    Route::get('/animal-types/{id}', [AnimalTypeController::class, 'show'])->name('animal-types.show');
    Route::post('/animal-types', [AnimalTypeController::class, 'store'])->name('animal-types.store');
    Route::put('/animal-types/{id}', [AnimalTypeController::class, 'update'])->name('animal-types.update');
    Route::delete('/animal-types/{id}', [AnimalTypeController::class, 'destroy'])->name('animal-types.destroy');

    // Diseases
    Route::get('/diseases', [DiseaseController::class, 'index'])->name('diseases.index');
    Route::get('/diseases/{id}', [DiseaseController::class, 'show'])->name('diseases.show');
    Route::post('/diseases/quick-diagnosis', [DiseaseController::class, 'quickDiagnosis'])->name('diseases.quick-diagnosis');
    Route::get('/diseases/search', [DiseaseController::class, 'searchDiseases'])->name('diseases.search');

    // Symptoms
    Route::get('/symptoms', [SymptomController::class, 'index'])->name('symptoms.index');
    Route::get('/symptoms/common/{animalTypeId}', [SymptomController::class, 'getCommonSymptoms'])->name('symptoms.common');

    // Prevention
    Route::get('/preventions', [PreventionController::class, 'index'])->name('preventions.index');
    Route::get('/preventions/disease/{diseaseId}', [PreventionController::class, 'getByDisease'])->name('preventions.by-disease');
    Route::get('/preventions/tips', [PreventionController::class, 'getPreventionTips'])->name('preventions.tips');

    // Medicines
    Route::get('/medicines', [MedicineController::class, 'index'])->name('medicines.index');
    Route::get('/medicines/{id}', [MedicineController::class, 'show'])->name('medicines.show');
    Route::get('/medicines/disease/{diseaseId}', [MedicineController::class, 'getMedicinesByDisease'])->name('medicines.by-disease');

    // Articles
    Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/{slug}', [ArticleController::class, 'show'])->name('articles.show');
    Route::get('/articles/popular', [ArticleController::class, 'popularArticles'])->name('articles.popular');
    Route::get('/articles/recent', [ArticleController::class, 'recentArticles'])->name('articles.recent');
});

// API Routes untuk AI Chat (Sanctum protected)
Route::middleware(['auth:sanctum'])->prefix('api/chat')->group(function () {
    // Session Management
    Route::post('/sessions/start', [AiChatController::class, 'startSession']);
    Route::get('/sessions', [AiChatController::class, 'getSessions']);
    Route::get('/sessions/{sessionId}', [AiChatController::class, 'getSession']);
    Route::delete('/sessions/{sessionId}', [AiChatController::class, 'deleteSession']);
    
    // Message Handling
    Route::post('/sessions/{sessionId}/message', [AiChatController::class, 'sendMessage']);
    
    // Analytics & Feedback
    Route::get('/usage-stats', [AiChatController::class, 'getUsageStats']);
    Route::post('/feedback', [AiChatController::class, 'storeFeedback']);
    Route::post('/analytics/message-category', [AiChatController::class, 'trackMessageCategory']);
});

// API untuk Animal Types (untuk dropdown di AI Chat)
Route::middleware(['auth:sanctum'])->get('/api/animal-types', [AnimalTypeController::class, 'apiIndex']);