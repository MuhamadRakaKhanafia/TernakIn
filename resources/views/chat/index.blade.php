@extends('layouts.app')

@section('title', 'AI Chat - TernakIN')
@section('page-title', 'TernakIN AI Chat')

@section('content')
<div class="container-fluid">
    <div class="row">

        <!-- Main Chat Area -->
        <div class="col-12 col-lg-9 col-xl-10">
            <div class="chat-container bg-white rounded-4 shadow-sm">
                <!-- Header -->
                <div class="chat-header bg-gradient-primary text-white rounded-top-4 py-3 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="chat-avatar me-3">
                                <div class="avatar bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                    <i class="fas fa-robot text-white fs-5"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="mb-0 text-white fw-bold">TernakAI</h5>
                                <small class="text-white text-opacity-80">Asisten TernakIN</small>
                            </div>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-light" onclick="window.chatSystem.startQuickSession()">
                                <i class="fas fa-plus me-1"></i> Sesi Baru
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Chat Messages Area -->
                <div class="chat-messages" style="flex: 1; display: flex; flex-direction: column; background: #f8fafc; min-height: 0; position: relative; overflow: hidden;">
                    <div class="messages-container p-4" id="messagesContainer" style="flex: 1; overflow-y: auto; background: #f8fafc; padding: 1.5rem; min-height: 0;">
                        <div class="text-center text-muted py-5" id="emptyState">
                            <i class="fas fa-comments fa-3x mb-3 opacity-50"></i>
                            <p class="mb-0">Mulai percakapan dengan AI</p>
                            <small class="text-muted">Ketik pesan Anda di bawah untuk memulai konsultasi</small>
                        </div>
                    </div>

                    <!-- Loading Indicator -->
                    <div class="loading-indicator text-center p-4 border-top bg-light" id="loadingIndicator" style="display: none;">
                        <div class="typing-animation d-inline-flex align-items-center">
                            <div class="typing-dots me-3">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                            <span class="text-muted fw-medium">TernakAI sedang mengetik...</span>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div id="errorMessage" class="alert alert-danger alert-dismissible fade show m-4 rounded-3" style="display: none;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle me-3 fs-5"></i>
                            <div class="flex-grow-1">
                                <span id="errorText"></span>
                            </div>
                            <button type="button" class="btn-close" onclick="document.getElementById('errorMessage').style.display='none'"></button>
                        </div>
                    </div>
                </div>

                <!-- Message Input -->
                <div class="message-input-area border-top bg-light rounded-bottom-4 p-3">
                    <form id="sendMessageForm" class="message-form">
                        @csrf
                        <input type="hidden" name="session_id" id="currentSessionId">
                        <input type="hidden" name="animal_type_id" id="selectedAnimalType">

                        <div class="input-container d-flex align-items-end gap-3">
                            <div class="flex-grow-1 position-relative">
                                <textarea name="message" id="messageText" class="form-control message-input"
                                        placeholder="Tulis pertanyaan tentang kesehatan ternak..."
                                        rows="1" style="border: 2px solid #e2e8f0; border-radius: 20px; padding: 16px 20px; font-size: 1rem; background: white; resize: none; min-height: 56px; max-height: 150px; line-height: 1.5; width: 100%;"></textarea>
                            </div>

                            <div class="send-button-container">
                                <button type="submit" class="btn btn-primary send-button" id="sendMessageBtn"
                                        style="width: 56px; height: 56px; border-radius: 50%; border: none; background: #10b981; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-paper-plane text-white"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Message Templates -->
<template id="userMessageTemplate">
    <div class="message user-message" style="margin-bottom: 1.5rem;">
        <div class="d-flex justify-content-end">
            <div class="message-bubble" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border-radius: 18px 18px 4px 18px; padding: 12px 18px; max-width: 70%; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);">
                <div class="message-text" style="line-height: 1.6; font-size: 0.95rem; word-wrap: break-word;"></div>
                <div class="message-time" style="font-size: 0.75rem; margin-top: 6px; opacity: 0.7; text-align: right;">
                    <span class="time-text"></span>
                </div>
            </div>
        </div>
    </div>
</template>

<template id="aiMessageTemplate">
    <div class="message ai-message" style="margin-bottom: 1.5rem;">
        <div class="d-flex justify-content-start align-items-start" style="gap: 0.75rem;">
            <div class="message-avatar">
                <div class="avatar" style="width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <i class="fas fa-robot"></i>
                </div>
            </div>
            <div class="message-content" style="max-width: calc(100% - 50px);">
                <div class="message-bubble" style="background: white; border: 1px solid #e2e8f0; color: #2d3748; border-radius: 18px 18px 18px 4px; padding: 12px 18px; max-width: 70%; box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);">
                    <div class="message-text" style="line-height: 1.6; font-size: 0.95rem; word-wrap: break-word; white-space: pre-wrap;"></div>
                    <div class="message-actions" style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px;">
                        <div class="message-time" style="font-size: 0.75rem; opacity: 0.7; text-align: left;">
                            <span class="time-text"></span>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary reload-btn" style="font-size: 0.7rem; padding: 2px 8px; border-radius: 12px; display: none;" title="Reload response">
                            <i class="fas fa-redo"></i> Reload
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
@endsection

@push('styles')
<style>
:root {
    --primary-green: #10b981;
    --primary-green-dark: #059669;
    --primary-green-light: #34d399;
    --secondary-purple: #8b5cf6;
    --accent-blue: #3b82f6;
    --accent-orange: #f59e0b;
    --danger-red: #ef4444;
    
    --gradient-primary: linear-gradient(135deg, #089e2bff 0%, #006913ff 100%);
    --gradient-green: linear-gradient(135deg, #10b921ff 0%, #059669 100%);
    --gradient-purple: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    
    --user-bubble: linear-gradient(135deg, #10b981 0%, #10b981 100%);
    --ai-bubble: #ffffff;
    --background-primary: #f8fafc;
    --background-secondary: #ffffff;
    --border-light: #e2e8f0;
    --border-medium: #d1d5db;
    --text-primary: #1f2937;
    --text-secondary: #4b5563;
    --text-muted: #6b7280;
    
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    --shadow-glow: 0 0 20px rgba(16, 185, 129, 0.15);
}

* {
    box-sizing: border-box;
}

.container-fluid {
    padding: 0;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    min-height: 100vh;
}

.row {
    margin: 0;
    min-height: 100vh;
}

/* ===========================================
   MAIN CHAT CONTAINER - LEBAR
   =========================================== */

.col-12.col-lg-9.col-xl-10 {
    padding: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.chat-container {
    height: 90vh;
    max-height: 90vh;
    width: 100%;
    max-width: 1400px;
    display: flex;
    flex-direction: column;
    background: var(--background-secondary);
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 
        var(--shadow-xl),
        0 0 0 1px rgba(255, 255, 255, 0.8),
        inset 0 1px 0 0 rgba(255, 255, 255, 0.2);
    border: 1px solid var(--border-light);
    backdrop-filter: blur(20px);
    position: relative;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    margin: 0 auto;
}

.chat-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--gradient-sunset);
    z-index: 10;
}

/* ===========================================
   PERBAIKAN BUBBLE CHAT - FIXED
   =========================================== */

.user-message {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 1.5rem;
}

.user-message .message-bubble {
    background: var(--user-bubble) !important;
    color: white;
    border-radius: 24px 24px 6px 24px !important;
    padding: 1rem 1.5rem;
    max-width: 75%;
    box-shadow: 
        var(--shadow-lg),
        0 4px 20px rgba(16, 185, 129, 0.3);
    position: relative;
    word-wrap: break-word;
    overflow-wrap: break-word;
    border: 1px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    margin-left: auto;
}

.user-message .message-bubble::after {
    content: '';
    position: absolute;
    bottom: -8px;
    right: 20px;
    width: 16px;
    height: 16px;
    background: var(--user-bubble);
    clip-path: polygon(0 0, 100% 0, 100% 100%);
    border-bottom-right-radius: 4px;
}

.ai-message {
    display: flex;
    justify-content: flex-start;
    align-items: flex-start;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}

.ai-message .message-bubble {
    background: var(--ai-bubble) !important;
    border: 1px solid rgba(255, 255, 255, 0.8) !important;
    color: var(--text-primary);
    border-radius: 24px 24px 24px 6px !important;
    padding: 1rem 1.5rem;
    max-width: 75%;
    box-shadow: 
        var(--shadow-lg),
        inset 0 1px 0 0 rgba(255, 255, 255, 0.8);
    position: relative;
    word-wrap: break-word;
    overflow-wrap: break-word;
    backdrop-filter: blur(10px);
    margin-right: auto;
}

.ai-message .message-bubble::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 20px;
    width: 16px;
    height: 16px;
    background: var(--ai-bubble);
    clip-path: polygon(0 0, 0 100%, 100% 0);
    border-bottom-left-radius: 4px;
    border-left: 1px solid rgba(255, 255, 255, 0.8);
    border-top: 1px solid rgba(255, 255, 255, 0.8);
}

/* ===========================================
   PERBAIKAN: GANTI ICON ROBOT DENGAN LOGO
   =========================================== */

.message-avatar .avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--gradient-green);
    box-shadow: var(--shadow-md);
    border: 2px solid white;
    transition: all 0.3s ease;
    flex-shrink: 0;
    position: relative;
    overflow: hidden;
}
/* Gambar logo untuk avatar AI */
.message-avatar .avatar::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url('/images/TernakIn.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    border-radius: 50%;
}

/* Logo untuk Header Chat */
.chat-avatar .avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.3);
    box-shadow: var(--shadow-md);
    position: relative;
    overflow: hidden;
}

/* Gambar logo untuk header */
.chat-avatar .avatar::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url('/images/TernakIn.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    border-radius: 50%;
}

/* Hover effects untuk logo */
.message-avatar .avatar:hover::before,
.chat-avatar .avatar:hover::before {
    transform: scale(1.1);
    transition: transform 0.3s ease;
}

/* Hapus icon Font Awesome yang lama */
.chat-avatar .avatar i,
.message-avatar .avatar i {
    display: none !important;
}

.message-content {
    max-width: calc(100% - 55px);
}

.message-text {
    line-height: 1.6;
    font-size: 0.95rem;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Inter', sans-serif;
    word-wrap: break-word;
    white-space: pre-wrap;
    font-weight: 450;
}

.message-time {
    font-size: 0.75rem;
    margin-top: 0.5rem;
    opacity: 0.7;
    text-align: right;
    font-weight: 500;
}

.ai-message .message-time {
    text-align: left;
}


/* ===========================================
   PERBAIKAN INPUT TEXT - SINGLE LINE HORIZONTAL
   =========================================== */

.message-input-area {
    background: var(--background-secondary);
    flex-shrink: 0;
    border-top: 1px solid var(--border-light);
    padding: 1.5rem 2rem !important;
    position: relative;
}

.message-input-area::before {
    content: '';
    position: absolute;
    top: 0;
    left: 2rem;
    right: 2rem;
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--border-light), transparent);
}

.input-container {
    width: 100%;
    display: flex;
    align-items: center; /* PERUBAHAN: center align untuk single line */
    gap: 1rem;
}

.flex-grow-1 {
    flex: 1 1 auto;
    min-width: 0;
}

/* PERBAIKAN: Input text single line horizontal */
.message-input {
    border: 2px solid var(--border-light) !important;
    border-radius: 25px !important;
    padding: 1rem 1.5rem !important;
    font-size: 1rem !important;
    background: var(--background-secondary) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    
    /* SINGLE LINE - TIDAK ADA MULTI LINE */
    resize: none !important;
    min-height: 56px !important;
    max-height: 56px !important; /* SAMA DENGAN MIN-HEIGHT */
    line-height: 1.5 !important;
    width: 100% !important;
    margin: 0 !important;
    box-sizing: border-box !important;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
    box-shadow: var(--shadow-sm) !important;
    
    /* SINGLE LINE PROPERTIES */
    white-space: nowrap !important; /* Teks tidak wrap ke baris baru */
    overflow-x: auto !important;    /* Scroll horizontal jika perlu */
    overflow-y: hidden !important;  /* Tidak ada scroll vertikal */
    
    /* HAPUS SCROLLBAR */
    scrollbar-width: none !important;
    -ms-overflow-style: none !important;
}

/* Webkit browsers - hapus scrollbar horizontal juga */
.message-input::-webkit-scrollbar {
    display: none !important;
    width: 0 !important;
    height: 0 !important;
}

.message-input::-webkit-scrollbar-track,
.message-input::-webkit-scrollbar-thumb {
    display: none !important;
}

/* Focus state - tetap single line */
.message-input:focus {
    border-color: var(--primary-green) !important;
    box-shadow: 
        var(--shadow-glow),
        0 0 0 4px rgba(16, 185, 129, 0.1) !important;
    outline: none !important;
    transform: translateY(-1px) !important;
    
    /* Tetap single line */
    white-space: nowrap !important;
    overflow-x: auto !important;
    overflow-y: hidden !important;
}

.message-input::placeholder {
    color: var(--text-muted);
    font-size: 0.95rem;
    font-weight: 450;
    white-space: nowrap; /* Placeholder juga single line */
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Smooth scroll horizontal */
.message-input {
    scroll-behavior: smooth;
}

.send-button-container {
    flex-shrink: 0;
    display: flex;
    align-items: center;
    height: 56px;
}

.send-button {
    width: 56px !important;
    height: 56px !important;
    border-radius: 50% !important;
    border: none !important;
    background: var(--gradient-green) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    margin: 0 !important;
    flex-shrink: 0;
    box-shadow: var(--shadow-lg);
    position: relative;
    overflow: hidden;
}

.send-button::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s ease;
}

.send-button:hover {
    background: var(--gradient-green) !important;
    transform: scale(1.05) translateY(-2px);
    box-shadow: 
        var(--shadow-xl),
        0 8px 25px rgba(16, 185, 129, 0.4);
}

.send-button:hover::before {
    left: 100%;
}

.send-button:active {
    transform: scale(0.98) translateY(0);
}

.send-button i {
    font-size: 1.1rem;
    color: white;
    transition: transform 0.3s ease;
}

.send-button:hover i {
    transform: translateX(2px);
}

/* ===========================================
   CHAT HEADER
   =========================================== */

.chat-header {
    background: var(--gradient-primary) !important;
    color: white !important;
    flex-shrink: 0;
    padding: 1.25rem 2rem !important;
    position: relative;
    overflow: hidden;
}

.chat-header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 70%);
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-10px) rotate(180deg); }
}

.chat-messages {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: 
        radial-gradient(circle at 20% 80%, rgba(16, 185, 129, 0.05) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(139, 92, 246, 0.05) 0%, transparent 50%),
        var(--background-primary);
    min-height: 0;
    position: relative;
    overflow: hidden;
}

.messages-container {
    flex: 1;
    overflow-y: auto;
    background: transparent;
    padding: 1.5rem 2rem;
    scroll-behavior: smooth;
    min-height: 0;
}

/* ===========================================
   ANIMATIONS & EFFECTS
   =========================================== */

.message {
    animation: messageSlideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes messageSlideIn {
    from {
        opacity: 0;
        transform: translateY(15px) scale(0.98);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* ===========================================
   RESPONSIVE DESIGN
   =========================================== */

@media (max-width: 1200px) {
    .col-12.col-lg-9.col-xl-10 {
        padding: 1rem;
    }
    
    .chat-container {
        height: 85vh;
        max-width: 100%;
    }
}

@media (max-width: 768px) {
    .container-fluid {
        padding: 0.5rem;
    }
    
    .col-12.col-lg-9.col-xl-10 {
        padding: 0.5rem;
    }
    
    .chat-container {
        height: 80vh;
        border-radius: 20px;
    }

    .user-message .message-bubble,
    .ai-message .message-bubble {
        max-width: 85%;
    }

    .messages-container {
        padding: 1rem 1.25rem;
    }

    .message-bubble {
        padding: 0.875rem 1.25rem;
    }

    .message-text {
        font-size: 0.9rem;
    }
    
    .message-input-area {
        padding: 1.25rem !important;
    }
    
    .input-container {
        gap: 0.75rem;
    }
    
    .message-input {
        padding: 0.875rem 1.25rem !important;
        min-height: 52px !important;
        max-height: 52px !important;
        font-size: 0.95rem !important;
    }
    
    .send-button-container {
        height: 52px;
    }
    
    .send-button {
        width: 52px !important;
        height: 52px !important;
    }
    
    .send-button i {
        font-size: 1rem;
    }
    
    .chat-header {
        padding: 1rem 1.5rem !important;
    }
}

@media (max-width: 576px) {
    .chat-container {
        height: 75vh;
        border-radius: 16px;
    }

    .messages-container {
        padding: 0.75rem 1rem;
    }

    .user-message .message-bubble,
    .ai-message .message-bubble {
        max-width: 90%;
    }

    .message-avatar .avatar {
        width: 36px;
        height: 36px;
    }
    
    .message-avatar .avatar::before {
        font-size: 1rem;
    }
    
    .message-content {
        max-width: calc(100% - 45px);
    }
    
    .message-input-area {
        padding: 1rem !important;
    }
    
    .input-container {
        gap: 0.5rem;
    }
    
    .message-input {
        padding: 0.75rem 1rem !important;
        min-height: 48px !important;
        max-height: 48px !important;
        font-size: 0.9rem !important;
        border-radius: 24px !important;
    }
    
    .send-button-container {
        height: 48px;
    }
    
    .send-button {
        width: 48px !important;
        height: 48px !important;
    }
    
    .send-button i {
        font-size: 0.9rem;
    }
    
    .chat-header {
        padding: 0.875rem 1.25rem !important;
    }
    
    .chat-header h5 {
        font-size: 1.1rem;
    }
}

/* ===========================================
   SCROLLBAR STYLING UNTUK MESSAGES CONTAINER
   =========================================== */

.messages-container::-webkit-scrollbar {
    width: 6px;
}

.messages-container::-webkit-scrollbar-track {
    background: transparent;
    border-radius: 3px;
}

.messages-container::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark));
    border-radius: 3px;
}

.messages-container::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, var(--primary-green-dark), var(--primary-green));
}
</style>
@endpush

@push('scripts')
<script>
class ChatSystem {
    constructor() {
        this.currentSession = null;
        this.isLoading = false;
    }

    init() {
        console.log('🚀 ChatSystem initialized');
        this.bindEvents();
        this.initializeChat();
    }

    bindEvents() {
        console.log('🔗 Binding events...');

        // Send message form
        const sendForm = document.getElementById('sendMessageForm');
        if (sendForm) {
            sendForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.sendMessage();
            });
        }

        // Textarea enter key handling
        const messageInput = document.getElementById('messageText');
        if (messageInput) {
            messageInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }
            });

            // Auto resize
            messageInput.addEventListener('input', () => {
                this.autoResizeTextarea();
            });
        }

        // Reload button event delegation
        const messagesContainer = document.getElementById('messagesContainer');
        if (messagesContainer) {
            messagesContainer.addEventListener('click', (e) => {
                if (e.target.classList.contains('reload-btn')) {
                    e.preventDefault();
                    this.reloadMessage(e.target);
                }
            });
        }
    }

    async sendMessage() {
        console.log('📤 Send message called');
        
        if (this.isLoading) {
            console.log('⏳ Already loading, skip');
            return;
        }

        const messageInput = document.getElementById('messageText');
        if (!messageInput) {
            console.error('❌ Message input not found');
            return;
        }

        const message = messageInput.value.trim();
        console.log('💬 Message to send:', message);
        
        if (!message) {
            this.showError('Pesan tidak boleh kosong');
            return;
        }

        // Jika belum ada session, buat session cepat
        if (!this.currentSession) {
            console.log('🆕 No session, creating quick session...');
            await this.startQuickSession();
            if (!this.currentSession) {
                this.showError('Gagal membuat sesi chat');
                return;
            }
        }

        const sendBtn = document.getElementById('sendMessageBtn');
        if (!sendBtn) return;
        
        try {
            this.isLoading = true;
            sendBtn.disabled = true;
            messageInput.disabled = true;
            sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin fs-6"></i>';

            // Clear empty state
            const emptyState = document.getElementById('emptyState');
            if (emptyState) emptyState.style.display = 'none';

            // Display user message immediately
            console.log('👤 Displaying user message...');
            this.displayMessage('user', message);
            messageInput.value = '';
            this.autoResizeTextarea();

            // Show loading indicator
            this.showLoading(true);
            this.hideError();

            console.log('📤 Sending to API, session:', this.currentSession.session_id);

            // Gunakan FormData untuk menghindari CORS dan content-type issues
            const formData = new FormData();
            formData.append('message', message);
            formData.append('session_id', this.currentSession.session_id);
            formData.append('_token', '{{ csrf_token() }}');

            // API call dengan timeout
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 30000);

            const response = await fetch(
                `/api/chat/sessions/${this.currentSession.session_id}/message`,
                {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData,
                    signal: controller.signal
                }
            );

            clearTimeout(timeoutId);

            console.log('📥 Response status:', response.status);

            if (!response.ok) {
                if (response.status === 404) {
                    // Session tidak ditemukan, buat session baru
                    console.log('🔄 Session not found, creating new session...');
                    this.currentSession = null;
                    await this.startQuickSession();
                    if (this.currentSession) {
                        // Coba kirim pesan lagi dengan session baru
                        await this.retrySendMessage(message);
                        return;
                    }
                }
                
                let errorMessage = `HTTP error! status: ${response.status}`;
                if (response.status === 500) {
                    errorMessage = 'Server sedang mengalami masalah. Silakan coba lagi.';
                }
                throw new Error(errorMessage);
            }

            const responseData = await response.json();
            console.log('📦 Full API Response:', responseData);

            if (responseData && responseData.success) {
                console.log('✅ API Response successful');

                // Extract AI content - FIXED STRUCTURE
                let aiContent = '';

                // Coba semua kemungkinan struktur response
                if (responseData.data?.ai_response?.content) {
                    aiContent = responseData.data.ai_response.content;
                } else if (responseData.data?.content) {
                    aiContent = responseData.data.content;
                } else if (typeof responseData.data === 'string') {
                    aiContent = responseData.data;
                } else if (responseData.ai_response?.content) {
                    aiContent = responseData.ai_response.content;
                } else if (responseData.content) {
                    aiContent = responseData.content;
                } else if (responseData.message) {
                    aiContent = responseData.message;
                } else {
                    aiContent = 'Terima kasih atas pertanyaan Anda!';
                }

                console.log('🤖 Extracted AI Content:', aiContent);

                // Display AI message
                console.log('🤖 Displaying AI message...');
                this.displayMessage('assistant', aiContent);
                this.showLoading(false);

            } else {
                const errorMsg = responseData?.error || responseData?.message || 'Terjadi kesalahan';
                console.error('❌ API Error:', errorMsg);
                this.showError('AI Error: ' + errorMsg);
                // Display the actual error message from API response
                this.displayMessage('assistant', errorMsg);
                this.showLoading(false);
            }
        } catch (error) {
            console.error('💥 Send message error:', error);
            this.showLoading(false);
            
            let errorMsg = error.message;
            if (error.name === 'AbortError') {
                errorMsg = 'Request timeout. Silakan coba lagi.';
            } else if (error.message.includes('Failed to fetch')) {
                errorMsg = 'Koneksi internet terputus. Periksa koneksi Anda.';
            } else if (error.message.includes('404')) {
                errorMsg = 'Sesi tidak ditemukan. Membuat sesi baru...';
                this.currentSession = null;
                await this.startQuickSession();
            }
            
            this.showError(errorMsg);
            this.displayMessage('assistant', 'Maaf, terjadi gangguan: ' + errorMsg);
        } finally {
            this.isLoading = false;
            if (sendBtn) {
                sendBtn.disabled = false;
                sendBtn.innerHTML = '<i class="fas fa-paper-plane fs-6"></i>';
            }
            if (messageInput) {
                messageInput.disabled = false;
                messageInput.focus();
            }
        }
    }

    async retrySendMessage(message) {
        console.log('🔄 Retrying send message with new session...');
        if (!this.currentSession) return;

        try {
            const formData = new FormData();
            formData.append('message', message);
            formData.append('session_id', this.currentSession.session_id);
            formData.append('_token', '{{ csrf_token() }}');

            const response = await fetch(
                `/api/chat/sessions/${this.currentSession.session_id}/message`,
                {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData
                }
            );

            if (response.ok) {
                const responseData = await response.json();
                if (responseData && responseData.success) {
                    let aiContent = responseData.data?.ai_response?.content || 
                                   responseData.data?.content || 
                                   'Terima kasih atas pertanyaan Anda!';
                    
                    this.displayMessage('assistant', aiContent);
                }
            }
        } catch (error) {
            console.error('❌ Retry failed:', error);
            this.displayMessage('assistant', 'Maaf, terjadi kesalahan setelah membuat sesi baru.');
        }
    }

    displayMessage(role, content) {
        console.log(`🎯 Displaying ${role} message:`, content.substring(0, 100) + '...');
        
        const messagesContainer = document.getElementById('messagesContainer');
        if (!messagesContainer) {
            console.error('❌ Messages container not found!');
            return;
        }
        
        // Hide empty state
        const emptyState = document.getElementById('emptyState');
        if (emptyState) emptyState.style.display = 'none';
        
        let template;
        if (role === 'assistant') {
            template = document.getElementById('aiMessageTemplate');
        } else {
            template = document.getElementById(role + 'MessageTemplate');
        }

        if (!template) {
            console.error(`❌ Template not found for role: ${role}`);
            return;
        }
        
        try {
            const messageElement = template.content.cloneNode(true);
            const messageText = messageElement.querySelector('.message-text');
            const timeText = messageElement.querySelector('.time-text');
            
            if (messageText) {
                messageText.textContent = content;
            }
            
            if (timeText) {
                timeText.textContent = this.getCurrentTime();
            }
            
            messagesContainer.appendChild(messageElement);
            this.scrollToBottom();
            
            console.log(`✅ ${role} message displayed successfully`);
            
        } catch (error) {
            console.error('❌ Error displaying message:', error);
        }
    }

    showLoading(show) {
        const loadingIndicator = document.getElementById('loadingIndicator');
        if (!loadingIndicator) return;

        if (show) {
            loadingIndicator.style.display = 'block';
        } else {
            loadingIndicator.style.display = 'none';
        }
        this.scrollToBottom();
    }

    showError(message) {
        console.error('❌ Error:', message);
        
        const errorElement = document.getElementById('errorMessage');
        const errorText = document.getElementById('errorText');
        
        if (!errorElement || !errorText) return;
        
        errorText.textContent = message;
        errorElement.style.display = 'block';

        setTimeout(() => {
            this.hideError();
        }, 5000);
    }

    hideError() {
        const errorElement = document.getElementById('errorMessage');
        if (errorElement) {
            errorElement.style.display = 'none';
        }
    }

    autoResizeTextarea() {
        const textarea = document.getElementById('messageText');
        if (!textarea) return;
        
        textarea.style.height = 'auto';
        const newHeight = Math.min(textarea.scrollHeight, 150);
        textarea.style.height = newHeight + 'px';
    }

    scrollToBottom() {
        const container = document.getElementById('messagesContainer');
        if (container) {
            setTimeout(() => {
                container.scrollTop = container.scrollHeight;
            }, 100);
        }
    }

    getCurrentTime() {
        return new Date().toLocaleTimeString('id-ID', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
    }

    async startQuickSession() {
        console.log('🚀 Starting quick session...');
        try {
            const formData = new FormData();
            formData.append('animal_type_id', 1);
            formData.append('_token', '{{ csrf_token() }}');

            const response = await fetch('/api/chat/sessions/start', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    this.currentSession = data.data.session;
                    console.log('✅ Quick session created:', this.currentSession);
                    localStorage.setItem('currentChatSession', JSON.stringify(this.currentSession));
                    this.displayMessage('assistant', 'Halo! Saya Asisten Kesehatan Ternak TernakIN. Ada yang bisa saya bantu? 🐄');
                } else {
                    throw new Error(data.error || 'Failed to create session');
                }
            } else {
                throw new Error('Network response was not ok: ' + response.status);
            }
        } catch (error) {
            console.error('❌ Failed to start quick session:', error);
            this.showError('Gagal memulai sesi: ' + error.message);
        }
    }

    reloadMessage(buttonElement) {
        console.log('🔄 Reload message called');

        if (this.isLoading) {
            console.log('⏳ Already loading, skip reload');
            return;
        }

        // Find the AI message element that contains this button
        const aiMessageElement = buttonElement.closest('.ai-message');
        if (!aiMessageElement) {
            console.error('❌ AI message element not found');
            return;
        }

        // Find the previous user message
        const messagesContainer = document.getElementById('messagesContainer');
        const allMessages = messagesContainer.querySelectorAll('.message');
        let userMessage = null;

        // Loop backwards to find the last user message before this AI message
        for (let i = Array.from(allMessages).indexOf(aiMessageElement) - 1; i >= 0; i--) {
            const msg = allMessages[i];
            if (msg.classList.contains('user-message')) {
                const messageText = msg.querySelector('.message-text');
                if (messageText) {
                    userMessage = messageText.textContent.trim();
                    break;
                }
            }
        }

        if (!userMessage) {
            console.error('❌ No user message found to reload');
            this.showError('Tidak dapat menemukan pesan untuk di-reload');
            return;
        }

        console.log('📝 Reloading with user message:', userMessage);

        // Remove the current AI message
        aiMessageElement.remove();

        // Resend the message
        this.resendMessage(userMessage);
    }

    async resendMessage(message) {
        console.log('🔄 Resending message:', message);

        if (this.isLoading) {
            return;
        }

        const sendBtn = document.getElementById('sendMessageBtn');
        if (!sendBtn) return;

        try {
            this.isLoading = true;
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin fs-6"></i>';

            // Show loading indicator
            this.showLoading(true);
            this.hideError();

            console.log('📤 Resending to API, session:', this.currentSession.session_id);

            // Gunakan FormData untuk menghindari CORS dan content-type issues
            const formData = new FormData();
            formData.append('message', message);
            formData.append('session_id', this.currentSession.session_id);
            formData.append('_token', '{{ csrf_token() }}');

            // API call dengan timeout
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 30000);

            const response = await fetch(
                `/api/chat/sessions/${this.currentSession.session_id}/message`,
                {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData,
                    signal: controller.signal
                }
            );

            clearTimeout(timeoutId);

            console.log('📥 Reload response status:', response.status);

            if (!response.ok) {
                let errorMessage = `HTTP error! status: ${response.status}`;
                if (response.status === 500) {
                    errorMessage = 'Server sedang mengalami masalah. Silakan coba lagi.';
                }
                throw new Error(errorMessage);
            }

            const responseData = await response.json();
            console.log('📦 Reload API Response:', responseData);

            this.showLoading(false);

            if (responseData && responseData.success) {
                console.log('✅ Reload API Response successful');

                // Extract AI content
                let aiContent = '';

                if (responseData.data?.ai_response?.content) {
                    aiContent = responseData.data.ai_response.content;
                } else if (responseData.data?.content) {
                    aiContent = responseData.data.content;
                } else if (typeof responseData.data === 'string') {
                    aiContent = responseData.data;
                } else if (responseData.ai_response?.content) {
                    aiContent = responseData.ai_response.content;
                } else if (responseData.content) {
                    aiContent = responseData.content;
                } else if (responseData.message) {
                    aiContent = responseData.message;
                } else {
                    aiContent = 'Terima kasih atas pertanyaan Anda!';
                }

                console.log('🤖 Reload extracted AI Content:', aiContent);

                // Display AI message
                console.log('🤖 Displaying reloaded AI message...');
                this.displayMessage('assistant', aiContent);

            } else {
                const errorMsg = responseData?.error || responseData?.message || 'Terjadi kesalahan';
                console.error('❌ Reload API Error:', errorMsg);
                this.showError('Reload Error: ' + errorMsg);
                this.displayMessage('assistant', errorMsg);
            }
        } catch (error) {
            console.error('💥 Reload message error:', error);
            this.showLoading(false);

            let errorMsg = error.message;
            if (error.name === 'AbortError') {
                errorMsg = 'Request timeout. Silakan coba lagi.';
            } else if (error.message.includes('Failed to fetch')) {
                errorMsg = 'Koneksi internet terputus. Periksa koneksi Anda.';
            }

            this.showError(errorMsg);
            this.displayMessage('assistant', 'Maaf, terjadi kesalahan saat reload: ' + errorMsg);
        } finally {
            this.isLoading = false;
            if (sendBtn) {
                sendBtn.disabled = false;
                sendBtn.innerHTML = '<i class="fas fa-paper-plane fs-6"></i>';
            }
        }
    }

    initializeChat() {
        const savedSession = localStorage.getItem('currentChatSession');
        if (savedSession) {
            try {
                const sessionData = JSON.parse(savedSession);
                if (sessionData && sessionData.session_id) {
                    this.currentSession = sessionData;
                    console.log('✅ Loaded session from storage:', this.currentSession);
                }
            } catch (e) {
                console.error('Error loading saved session:', e);
                localStorage.removeItem('currentChatSession');
            }
        }
    }
}

// Initialize chat system
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM Content Loaded - Starting chat system...');
    
    try {
        const chatSystem = new ChatSystem();
        window.chatSystem = chatSystem;
        chatSystem.init();
        
        console.log('🎉 Chat system initialized successfully!');
        
    } catch (error) {
        console.error('💥 Failed to initialize chat system:', error);
    }
});
</script>
@endpush