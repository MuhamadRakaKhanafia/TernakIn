// js/chat/core/ApiService.js
class ApiService {
    constructor() {
        this.baseUrl = '/api/chat';
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    }

    async request(endpoint, options = {}) {
        const config = {
            headers: {
                'X-CSRF-TOKEN': this.csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            credentials: 'same-origin',
            ...options
        };

        try {
            const response = await fetch(`${this.baseUrl}${endpoint}`, config);
            return await this.handleResponse(response);
        } catch (error) {
            throw this.handleError(error);
        }
    }

    async handleResponse(response) {
        if (response.status === 401) {
            throw new Error('UNAUTHORIZED');
        }

        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new Error(errorData.error || `HTTP ${response.status}`);
        }

        return await response.json();
    }

    handleError(error) {
        if (error.message === 'UNAUTHORIZED') {
            this.showLoginModal();
        }
        return error;
    }

    showLoginModal() {
        // Implementation for login modal
        console.warn('User needs to login');
    }

    // Specific API methods
    async startSession(data) {
        return this.request('/sessions/start', {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }

    async sendMessage(sessionId, message) {
        return this.request(`/sessions/${sessionId}/message`, {
            method: 'POST',
            body: JSON.stringify({ message })
        });
    }

    async getSessions() {
        return this.request('/sessions');
    }

    async getSession(sessionId) {
        return this.request(`/sessions/${sessionId}`);
    }

    async deleteSession(sessionId) {
        return this.request(`/sessions/${sessionId}`, {
            method: 'DELETE'
        });
    }

    async getUsageStats() {
        return this.request('/usage-stats');
    }

    async validateConnection() {
        try {
            await this.request('/sessions');
            return true;
        } catch (error) {
            throw new Error('API connection failed');
        }
    }
}