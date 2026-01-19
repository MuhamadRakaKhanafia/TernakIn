<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VaccinationController;
use App\Http\Controllers\LivestockController;
use App\Http\Controllers\DiseaseController;
use App\Http\Controllers\AnimalTypeController;
use App\Http\Controllers\PreventionController;
use App\Http\Controllers\SymptomController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AiChatController;
use App\Http\Controllers\WelcomeController;

// Public routes
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/cities/{province_id}', [AuthController::class, 'getCities'])->name('cities');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard')->middleware(['admin']);

    // Admin routes
    Route::prefix('admin')->name('admin.')->middleware(['admin'])->group(function () {
        // Vaccination management
        Route::get('/vaccinations', [AdminController::class, 'vaccinationsIndex'])->name('vaccinations.index');
        Route::get('/vaccinations/{vaccination}', [AdminController::class, 'vaccinationsShow'])->name('vaccinations.show');
        Route::get('/vaccinations/create', [AdminController::class, 'vaccinationsCreate'])->name('vaccinations.create');
        Route::post('/vaccinations', [AdminController::class, 'vaccinationsStore'])->name('vaccinations.store');
        Route::post('/vaccinations/{vaccination}/validate', [AdminController::class, 'vaccinationsValidate'])->name('vaccinations.validate');
        Route::post('/vaccinations/{vaccination}/approve', [AdminController::class, 'vaccinationsApprove'])->name('vaccinations.approve');
        Route::post('/vaccinations/{vaccination}/reject', [AdminController::class, 'vaccinationsReject'])->name('vaccinations.reject');
        Route::post('/vaccinations/{vaccination}/complete', [AdminController::class, 'vaccinationsComplete'])->name('vaccinations.complete');

        // Form submitters management
        Route::get('/form-submitters', [AdminController::class, 'formSubmittersIndex'])->name('form-submitters');
        Route::get('/form-submitters/{user}', [AdminController::class, 'formSubmittersShow'])->name('form-submitters.user');

        // Chat queries management
        Route::get('/chat-queries', [AdminController::class, 'chatQueriesIndex'])->name('chat-queries');

        // Broadcasts management
        Route::get('/broadcasts', [AdminController::class, 'broadcastsIndex'])->name('broadcasts.index');
        Route::get('/broadcasts/create', [AdminController::class, 'broadcastsCreate'])->name('broadcasts.create');
        Route::post('/broadcasts', [AdminController::class, 'broadcastsStore'])->name('broadcasts.store');
        Route::get('/broadcasts/{broadcast}/edit', [AdminController::class, 'broadcastsEdit'])->name('broadcasts.edit');
        Route::put('/broadcasts/{broadcast}', [AdminController::class, 'broadcastsUpdate'])->name('broadcasts.update');
        Route::delete('/broadcasts/{broadcast}', [AdminController::class, 'broadcastsDestroy'])->name('broadcasts.destroy');

        // Diseases management
        Route::get('/diseases', [AdminController::class, 'diseasesIndex'])->name('diseases.index');
        Route::get('/diseases/create', [AdminController::class, 'diseasesCreate'])->name('diseases.create');
        Route::post('/diseases', [AdminController::class, 'diseasesStore'])->name('diseases.store');
        Route::get('/diseases/{disease}/edit', [AdminController::class, 'diseasesEdit'])->name('diseases.edit');
        Route::put('/diseases/{disease}', [AdminController::class, 'diseasesUpdate'])->name('diseases.update');
        Route::delete('/diseases/{disease}', [AdminController::class, 'diseasesDestroy'])->name('diseases.destroy');

        // Logged-in users
        Route::get('/logged-in-users', [AdminController::class, 'loggedInUsers'])->name('logged-in-users');
    });

    // Vaccination routes
    Route::resource('vaccinations', VaccinationController::class);

    // Livestock routes
    Route::resource('livestocks', LivestockController::class);

    // Disease routes
    Route::resource('diseases', DiseaseController::class);

    // Animal Type routes
    Route::resource('animal-types', AnimalTypeController::class);

    // Prevention routes
    Route::resource('preventions', PreventionController::class);

    // Symptom routes
    Route::resource('symptoms', SymptomController::class);

    // Article routes
    Route::resource('articles', ArticleController::class);

    // Province routes
    Route::resource('provinces', ProvinceController::class);

    // Analytics routes
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // AI Chat routes
    Route::get('/chat', [AiChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/session', [AiChatController::class, 'startSession'])->name('chat.startSession');
    Route::post('/chat/message', [AiChatController::class, 'sendMessage'])->name('chat.sendMessage');

    // API Chat routes
    Route::prefix('api/chat')->group(function () {
        Route::post('/sessions/start', [AiChatController::class, 'startSession'])->name('api.chat.startSession');
        Route::post('/sessions/{sessionId}/message', [AiChatController::class, 'sendMessage'])->name('api.chat.sendMessage');
        Route::get('/sessions', [AiChatController::class, 'getSessions'])->name('api.chat.getSessions');
        Route::get('/sessions/{sessionId}', [AiChatController::class, 'getSession'])->name('api.chat.getSession');
        Route::delete('/sessions/{sessionId}', [AiChatController::class, 'deleteSession'])->name('api.chat.deleteSession');
        Route::get('/usage-stats', [AiChatController::class, 'getUsageStats'])->name('api.chat.usageStats');
        Route::post('/feedback', [AiChatController::class, 'storeFeedback'])->name('api.chat.feedback');
        Route::get('/test-gemini', [AiChatController::class, 'testGeminiConnection'])->name('api.chat.testGemini');
    });
});
