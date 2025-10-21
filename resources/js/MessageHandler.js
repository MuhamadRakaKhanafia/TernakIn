// js/chat/components/MessageHandler.js
class MessageHandler {
    constructor(chatSystem) {
        this.chatSystem = chatSystem;
    }

    async sendMessage(messageContent) {
        if (!messageContent.trim() || !this.chatSystem.currentSessionId) {
            this.chatSystem.uiManager.showWarning('Silakan mulai sesi chat terlebih dahulu');
            return;
        }

        // Add user message to UI immediately
        this.addMessage('user', messageContent);
        this.chatSystem.uiManager.clearMessageInput();

        if (this.chatSystem.isFirstMessage) {
            this.chatSystem.uiManager.hideQuickQuestions();
            this.chatSystem.isFirstMessage = false;
        }

        // Show typing indicator
        this.chatSystem.uiManager.showTypingIndicator();

        try {
            const response = await this.chatSystem.apiService.sendMessage(
                this.chatSystem.currentSessionId, 
                messageContent
            );

            this.chatSystem.uiManager.hideTypingIndicator();

            if (response.success) {
                this.addMessage(
                    'assistant',
                    response.data.ai_response.content,
                    response.data.ai_response.created_at
                );
                
                this.chatSystem.analyticsTracker.trackMessageSent(messageContent, 'success');
            }
        } catch (error) {
            this.chatSystem.uiManager.hideTypingIndicator();
            this.addMessage(
                'assistant',
                `Maaf, terjadi kesalahan: ${error.message}. Silakan coba lagi.`
            );
            this.chatSystem.analyticsTracker.trackMessageSent(messageContent, 'error');
        }
    }

    addMessage(role, content, timestamp = null) {
        const messageElement = this.chatSystem.uiManager.createMessageElement(role, content, timestamp);
        this.chatSystem.uiManager.appendMessage(messageElement);
        this.chatSystem.uiManager.scrollToBottom();
    }

    formatMessage(content) {
        return content
            .replace(/\n/g, '<br>')
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>');
    }
}