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
            typingIndicator: document.getElementById('typingIndicator'),
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

    // ... (methods lainnya untuk UI management)
}