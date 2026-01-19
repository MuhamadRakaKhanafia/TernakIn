import ApiService from './ApiService.js';
import EventManager from './EventManager.js';
import SessionManager from './SessionManager.js';
import MessageHandler from './MessageHandler.js';
import UIManager from './UIManager.js';
import AnalyticsTracker from './AnalyticsTracker.js';

class ChatSystem {
    constructor() {
        this.currentSessionId = null;
        this.isFirstMessage = true;
        this.isLoading = false;

        this.apiService = new ApiService();
        this.eventManager = new EventManager();
        this.sessionManager = new SessionManager(this);
        this.messageHandler = new MessageHandler(this);
        this.uiManager = new UIManager(this);
        this.analyticsTracker = new AnalyticsTracker(this);

        this.init();
    }

    async init() {
        try {
            await this.loadDependencies();
            this.setupEventListeners();
            await this.loadInitialData();
            this.uiManager.showChatInterface();
        } catch (error) {
            console.error('Failed to initialize chat system:', error);
            this.uiManager.showError('Gagal memuat sistem chat');
        }
    }

    async loadDependencies() {
        // Load any required dependencies
        await this.apiService.validateConnection();
    }

    setupEventListeners() {
        this.eventManager.on('session:start', (data) => this.sessionManager.startSession(data));
        this.eventManager.on('session:load', (sessionId) => this.sessionManager.loadSession(sessionId));
        this.eventManager.on('session:delete', (sessionId) => this.sessionManager.deleteSession(sessionId));
        this.eventManager.on('message:send', (message) => this.messageHandler.sendMessage(message));
        this.eventManager.on('ui:showSessions', () => this.uiManager.showSessionsModal());
        this.eventManager.on('ui:showStats', () => this.uiManager.showStatsModal());
    }

    async loadInitialData() {
        await this.sessionManager.loadAnimalTypes();
    }

    // Public methods for external access
    startNewSession(formData) {
        this.eventManager.emit('session:start', formData);
    }

    sendUserMessage(message) {
        this.eventManager.emit('message:send', message);
    }

    loadChatSession(sessionId) {
        this.eventManager.emit('session:load', sessionId);
    }
}

export default ChatSystem;
