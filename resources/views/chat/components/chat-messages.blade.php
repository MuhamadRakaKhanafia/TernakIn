<div id="chatMessages" class="chat-messages" style="display: none;">
    <div class="messages-container" id="messagesContainer">
        <!-- Messages will be loaded here dynamically -->
    </div>
    <div class="loading-indicator" id="loadingIndicator" style="display: none;">
        <div class="typing-animation">
            <span>AI sedang mengetik</span>
            <div class="typing-dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
</div>

<!-- Template for user message -->
<template id="userMessageTemplate">
    <div class="message user-message">
        <div class="message-avatar">
            <i class="fas fa-user"></i>
        </div>
        <div class="message-content">
            <div class="message-text"></div>
            <div class="message-time"></div>
        </div>
    </div>
</template>

<!-- Template for AI message -->
<template id="aiMessageTemplate">
    <div class="message ai-message">
        <div class="message-avatar">
            <i class="fas fa-robot"></i>
        </div>
        <div class="message-content">
            <div class="message-text"></div>
            <div class="message-time"></div>
            <div class="message-actions">
                <button class="btn-copy" title="Salin pesan">
                    <i class="fas fa-copy"></i>
                </button>
                <button class="btn-regenerate" title="Regenerasi respons">
                    <i class="fas fa-redo"></i>
                </button>
            </div>
        </div>
    </div>
</template>