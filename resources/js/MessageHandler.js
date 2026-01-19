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

        // Create AI message element immediately (like Gemini)
        const aiMessageElement = this.chatSystem.uiManager.createMessageElement('assistant', '', new Date().toISOString());
        this.chatSystem.uiManager.appendMessage(aiMessageElement);
        this.chatSystem.uiManager.scrollToBottom();

        const messageText = aiMessageElement.querySelector('.message-text');

        try {
            const response = await this.chatSystem.apiService.sendMessage(
                this.chatSystem.currentSessionId,
                messageContent
            );

            if (response.success) {
                // Stream the response like Gemini
                await this.streamText(messageText, response.data.ai_response.content);

                // Update timestamp after streaming
                const messageTime = aiMessageElement.querySelector('.message-time');
                messageTime.textContent = this.chatSystem.uiManager.formatTimestamp(new Date().toISOString());

                this.chatSystem.analyticsTracker.trackMessageSent(messageContent, 'success');
            } else {
                await this.streamText(messageText, `Maaf, terjadi kesalahan: ${response.error || 'Unknown error'}. Silakan coba lagi.`);
                this.chatSystem.analyticsTracker.trackMessageSent(messageContent, 'error');
            }
        } catch (error) {
            await this.streamText(messageText, `Maaf, terjadi kesalahan: ${error.message}. Silakan coba lagi.`);
            this.chatSystem.analyticsTracker.trackMessageSent(messageContent, 'error');
        }
    }

    addMessage(role, content, timestamp = null) {
        const messageElement = this.chatSystem.uiManager.createMessageElement(role, content, timestamp);
        this.chatSystem.uiManager.appendMessage(messageElement);
        this.chatSystem.uiManager.scrollToBottom();
    }

    formatMessage(content) {
        // Clean up excessive newlines first
        content = content.replace(/\n{3,}/g, '\n\n');

        // Convert markdown-style formatting
        content = content
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')  // Bold
            .replace(/\*(.*?)\*/g, '<em>$1</em>')            // Italic
            .replace(/__(.*?)__/g, '<u>$1</u>')              // Underline
            .replace(/`(.*?)`/g, '<code>$1</code>');         // Inline code

        // Convert line breaks to HTML
        content = content.replace(/\n/g, '<br>');

        // Handle bullet points and numbered lists
        content = content
            .replace(/^\* (.+)$/gm, '<li>$1</li>')           // Bullet points
            .replace(/^\d+\. (.+)$/gm, '<li>$1</li>');       // Numbered lists

        // Wrap consecutive list items in ul/ol tags
        content = content.replace(/(<li>.*<\/li>\s*)+/g, '<ul>$&</ul>');

        // Handle headers
        content = content
            .replace(/^### (.*$)/gm, '<h4>$1</h4>')
            .replace(/^## (.*$)/gm, '<h3>$1</h3>')
            .replace(/^# (.*$)/gm, '<h2>$1</h2>');

        // Clean up any remaining formatting issues
        content = content.replace(/<br><br><br>/g, '<br><br>');

        return content;
    }

    async streamText(element, text) {
        const words = text.split(' ');
        let currentText = '';

        for (let i = 0; i < words.length; i++) {
            currentText += words[i] + ' ';
            element.innerHTML = this.formatMessage(currentText.trim());

            // Scroll to bottom as text streams
            this.chatSystem.uiManager.scrollToBottom();

            // Delay between words (adjust speed as needed)
            await new Promise(resolve => setTimeout(resolve, 50));
        }

        // Final format after streaming
        element.innerHTML = this.formatMessage(text);
    }
}

export default MessageHandler;
