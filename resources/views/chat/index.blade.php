@extends('layouts.app')

@section('title', 'AI Chat - TernakIN')
@section('page-title', 'Konsultasi Kesehatan Ternak AI')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">
            <div class="ai-chat-container" id="aiChatApp">
                <!-- Header -->
                @include('chat.components.header')
                
                <!-- Session Info -->
                @include('chat.components.session-info')
                
                <!-- Chat Interface -->
                <div class="chat-interface">
                    <!-- Chat Messages -->
                    @include('chat.components.chat-messages')
                    
                    <!-- Start New Session Form -->
                    <div id="newSessionForm" class="new-session-form">
                        <div class="session-form-container">
                            <div class="form-header">
                                <h3>Mulai Konsultasi Baru</h3>
                                <p>Pilih jenis ternak untuk memulai konsultasi dengan AI</p>
                            </div>
                            
                            <form id="startSessionForm">
                                @csrf
                                <div class="form-group">
                                    <label for="animalType">Jenis Ternak *</label>
                                    <select name="animal_type_id" id="animalType" class="form-control" required>
                                        <option value="">Pilih Jenis Ternak</option>
                                        @foreach($animalTypes as $type)
                                            <option value="{{ $type->id }}">
                                                {{ $type->name }} ({{ $type->category }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="sessionTitle">Judul Sesi (Opsional)</label>
                                    <input type="text" name="title" id="sessionTitle" class="form-control" 
                                           placeholder="Contoh: Masalah pencernaan sapi">
                                </div>
                                
                                <div class="form-group">
                                    <label for="initialMessage">Pesan Awal (Opsional)</label>
                                    <textarea name="initial_message" id="initialMessage" class="form-control" 
                                              rows="3" placeholder="Jelaskan masalah yang dialami ternak Anda..."></textarea>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary btn-start">
                                        <i class="fas fa-comment-medical"></i> Mulai Konsultasi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Message Input -->
                    @include('chat.components.message-input')
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
@include('chat.components.modals.sessions-modal')
@include('chat.components.modals.stats-modal')
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/chat/main.css') }}">
<link rel="stylesheet" href="{{ asset('css/chat/components.css') }}">
<link rel="stylesheet" href="{{ asset('css/chat/responsive.css') }}">
@endpush

@push('scripts')
<script>
    // Pass data dari Laravel ke JavaScript
    window.chatConfig = {
        csrfToken: '{{ csrf_token() }}',
        routes: {
            startSession: '{{ route("chat.sessions.start") }}',
            getSessions: '{{ route("chat.sessions.list") }}',
            getSession: '{{ route("chat.sessions.get", ":sessionId") }}',
            deleteSession: '{{ route("chat.sessions.delete", ":sessionId") }}',
            sendMessage: '{{ route("chat.messages.send", ":sessionId") }}',
            usageStats: '{{ route("chat.usage.stats") }}',
            feedback: '{{ route("chat.feedback.store") }}'
        },
        user: {
            id: {{ Auth::id() }},
            name: '{{ Auth::user()->name }}'
        }
    };
</script>
<script src="{{ asset('js/chat/utils/helpers.js') }}"></script>
<script src="{{ asset('js/chat/utils/formatters.js') }}"></script>
<script src="{{ asset('js/chat/utils/validators.js') }}"></script>
<script src="{{ asset('js/chat/core/ApiService.js') }}"></script>
<script src="{{ asset('js/chat/core/EventManager.js') }}"></script>
<script src="{{ asset('js/chat/components/SessionManager.js') }}"></script>
<script src="{{ asset('js/chat/components/MessageHandler.js') }}"></script>
<script src="{{ asset('js/chat/components/UIManager.js') }}"></script>
<script src="{{ asset('js/chat/components/AnalyticsTracker.js') }}"></script>
<script src="{{ asset('js/chat/core/ChatSystem.js') }}"></script>
<script src="{{ asset('js/chat/main.js') }}"></script>
@endpush