@extends('layouts.app')

@section('title', 'AI Chat - TernakIN')
@section('page-title', 'Konsultasi Kesehatan Ternak AI')
@section('content')
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
        @include('chat.components.new-session-form')
        
        <!-- Message Input -->
        @include('chat.components.message-input')
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