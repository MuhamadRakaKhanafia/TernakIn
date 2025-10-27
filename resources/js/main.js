// Inisialisasi chat system
document.addEventListener('DOMContentLoaded', function() {
    const chatSystem = new ChatSystem();
    chatSystem.init();
});

class ChatSystem {
    constructor() {
        this.currentSession = null;
        this.apiService = new ApiService();
        this.uiManager = new UIManager();
        this.sessionManager = new SessionManager();
    }

    init() {
        this.bindEvents();
        this.loadSessions();
    }

    bindEvents() {
        // Form mulai sesi baru
        document.getElementById('startSessionForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.startNewSession();
        });

        // Form kirim pesan
        document.getElementById('sendMessageForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.sendMessage();
        });

        // Tombol sesi baru
        document.getElementById('newSessionBtn').addEventListener('click', () => {
            this.showNewSessionForm();
        });
    }

    async startNewSession() {
        const formData = new FormData(document.getElementById('startSessionForm'));
        
        try {
            const response = await this.apiService.startSession({
                animal_type_id: formData.get('animal_type_id'),
                initial_message: formData.get('initial_message')
            });

            if (response.success) {
                this.currentSession = response.data.session;
                this.uiManager.showChatInterface();
                this.uiManager.displayInitialResponse(response.data.initial_response);
            } else {
                this.uiManager.showError(response.error);
            }
        } catch (error) {
            this.uiManager.showError('Gagal memulai sesi chat');
        }
    }

    async sendMessage() {
        const messageInput = document.getElementById('messageText');
        const message = messageInput.value.trim();

        if (!message || !this.currentSession) return;

        try {
            const response = await this.apiService.sendMessage(
                this.currentSession.session_id, 
                message
            );

            if (response.success) {
                this.uiManager.displayAiResponse(response.data.ai_response);
                messageInput.value = '';
            } else {
                this.uiManager.showError(response.error);
            }
        } catch (error) {
            this.uiManager.showError('Gagal mengirim pesan');
        }
    }

    showNewSessionForm() {
        this.uiManager.showNewSessionForm();
        this.currentSession = null;
    }

    async loadSessions() {
        try {
            const response = await this.apiService.getSessions();
            if (response.success) {
                this.sessionManager.displaySessions(response.data);
            }
        } catch (error) {
            console.error('Gagal memuat sesi:', error);
        }
    }
}