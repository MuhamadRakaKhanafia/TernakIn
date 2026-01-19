// js/chat/components/SessionManager.js
class SessionManager {
    constructor(chatSystem) {
        this.chatSystem = chatSystem;
        this.sessions = [];
    }

    async startSession(formData) {
        if (this.chatSystem.isLoading) return;

        try {
            this.chatSystem.isLoading = true;
            this.chatSystem.uiManager.showLoading('Membuat sesi baru...');

            const response = await this.chatSystem.apiService.startSession(formData);

            if (response.success) {
                this.chatSystem.currentSessionId = response.data.session.session_id;
                this.chatSystem.uiManager.updateSessionInfo(response.data.session);
                this.chatSystem.uiManager.showChatInterface();

                if (response.data.initial_response) {
                    this.chatSystem.messageHandler.addMessage(
                        'assistant',
                        response.data.initial_response.content,
                        response.data.initial_response.created_at
                    );
                }

                this.chatSystem.analyticsTracker.trackSessionStart(formData);
                this.chatSystem.uiManager.showNotification('Sesi berhasil dibuat', 'success');
            }
        } catch (error) {
            this.chatSystem.uiManager.showError('Gagal membuat sesi: ' + error.message);
        } finally {
            this.chatSystem.isLoading = false;
            this.chatSystem.uiManager.hideLoading();
        }
    }

    async loadSession(sessionId) {
        try {
            this.chatSystem.uiManager.showLoading('Memuat sesi...');

            const response = await this.chatSystem.apiService.getSession(sessionId);

            if (response.success) {
                this.chatSystem.currentSessionId = sessionId;
                this.chatSystem.uiManager.updateSessionInfo(response.data);
                this.chatSystem.uiManager.showChatInterface();
                this.chatSystem.uiManager.loadMessages(response.data.messages);
                this.chatSystem.uiManager.hideSessionsModal();
            }
        } catch (error) {
            this.chatSystem.uiManager.showError('Gagal memuat sesi: ' + error.message);
        } finally {
            this.chatSystem.uiManager.hideLoading();
        }
    }

    async deleteSession(sessionId) {
        if (!confirm('Apakah Anda yakin ingin menghapus sesi ini?')) return;

        try {
            await this.chatSystem.apiService.deleteSession(sessionId);

            if (sessionId === this.chatSystem.currentSessionId) {
                this.chatSystem.uiManager.showNewSessionForm();
            }

            this.chatSystem.uiManager.showNotification('Sesi berhasil dihapus', 'success');
        } catch (error) {
            this.chatSystem.uiManager.showError('Gagal menghapus sesi: ' + error.message);
        }
    }

    async loadAnimalTypes() {
        try {
            // Implementation for loading animal types
            const response = await fetch('/api/animal-types');
            const data = await response.json();
            this.chatSystem.uiManager.populateAnimalTypes(data.data || data);
        } catch (error) {
            console.error('Error loading animal types:', error);
            this.chatSystem.uiManager.populateAnimalTypes(this.getDefaultAnimalTypes());
        }
    }

    getDefaultAnimalTypes() {
        return [
            { id: 1, name: 'Ayam Broiler' },
            { id: 2, name: 'Ayam Petelur' },
            { id: 3, name: 'Sapi Potong' },
            { id: 4, name: 'Sapi Perah' },
            { id: 5, name: 'Kambing' },
            { id: 6, name: 'Domba' }
        ];
    }
}

export default SessionManager;
