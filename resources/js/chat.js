// Main chat entry point
import ApiService from './ApiService.js';
import ChatSystem from './ChatSystem.js';
import MessageHandler from './MessageHandler.js';
import SessionManager from './SessionManager.js';
import UIManager from './UIManager.js';
import EventManager from './EventManager.js';
import AnalyticsTracker from './AnalyticsTracker.js';

// Initialize chat system when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    try {
        // Create global instances
        window.apiService = new ApiService();
        window.chatSystem = new ChatSystem();
        window.messageHandler = new MessageHandler(window.chatSystem);
        window.sessionManager = new SessionManager(window.chatSystem);
        window.uiManager = new UIManager(window.chatSystem);

        // Initialize the system
        window.chatSystem.init();

        console.log('Chat system initialized successfully');
    } catch (error) {
        console.error('Failed to initialize chat system:', error);
        // Fallback to basic functionality
        console.warn('Using fallback chat functionality');
    }
});
