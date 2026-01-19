// js/chat/components/UIManager.js
class UIManager {
    constructor(chatSystem) {
        this.chatSystem = chatSystem;
        this.elements = this.initializeElements();
    }

    initializeElements() {
        return {
            chatContainer: document.getElementById('chatContainer'),
            newSessionForm: document.getElementById('newSessionForm'),
            messageInput: document.getElementById('messageInput'),
            messagesList: document.getElementById('messagesList'),
            loadingIndicator: document.getElementById('loadingIndicator'),
            sessionInfo: document.getElementById('sessionInfo'),
            quickQuestions: document.getElementById('quickQuestions'),
            messageInputField: document.getElementById('messageInputField'),
            sessionsModal: document.getElementById('sessionsModal'),
            statsModal: document.getElementById('statsModal'),
            sessionTitle: document.getElementById('sessionTitle'),
            sessionAnimal: document.getElementById('sessionAnimal')
        };
    }

    showWelcomeScreen() {
        this.elements.newSessionForm.style.display = 'flex';
        this.elements.chatContainer.style.display = 'none';
        this.elements.messageInput.style.display = 'none';
        this.elements.sessionInfo.style.display = 'none';
    }

    showChatInterface() {
        this.elements.newSessionForm.style.display = 'none';
        this.elements.chatContainer.style.display = 'flex';
        this.elements.messageInput.style.display = 'block';
        this.elements.sessionInfo.style.display = 'block';
        this.elements.quickQuestions.style.display = 'block';
    }

    showNewSessionForm() {
        this.chatSystem.currentSessionId = null;
        this.chatSystem.isFirstMessage = true;
        this.showWelcomeScreen();
    }

    updateSessionInfo(session) {
        this.elements.sessionTitle.textContent = session.title;
        this.elements.sessionAnimal.textContent = `Jenis Ternak: ${session.animal_type?.name || 'Tidak ditentukan'}`;
    }

    showTypingIndicator() {
        if (this.elements.loadingIndicator) {
            this.elements.loadingIndicator.style.display = 'block';
            this.scrollToBottom();
        }
    }

    hideTypingIndicator() {
        if (this.elements.loadingIndicator) {
            this.elements.loadingIndicator.style.display = 'none';
        }
    }

    createMessageElement(role, content, timestamp = null) {
        const template = document.getElementById(role === 'user' ? 'userMessageTemplate' : 'aiMessageTemplate');
        const messageElement = template.content.cloneNode(true);

        const messageText = messageElement.querySelector('.message-text');
        messageText.innerHTML = this.formatMessage(content);

        if (timestamp) {
            const messageTime = messageElement.querySelector('.message-time');
            messageTime.textContent = this.formatTimestamp(timestamp);
        }

        return messageElement;
    }

    appendMessage(messageElement) {
        const messagesContainer = document.getElementById('messagesContainer');
        if (messagesContainer) {
            messagesContainer.appendChild(messageElement);
        }
    }

    scrollToBottom() {
        const chatMessages = document.getElementById('chatMessages');
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    }

    clearMessageInput() {
        if (this.elements.messageInputField) {
            this.elements.messageInputField.value = '';
        }
    }

    formatMessage(content) {
        return content
            .replace(/\n/g, '<br>')
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>');
    }

    formatTimestamp(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    showError(message) {
        // Implement error display
        console.error(message);
    }

    showWarning(message) {
        // Implement warning display
        console.warn(message);
    }

    hideQuickQuestions() {
        if (this.elements.quickQuestions) {
            this.elements.quickQuestions.style.display = 'none';
        }
    }

    // ... (methods lainnya untuk UI management)
}

export default UIManager;
