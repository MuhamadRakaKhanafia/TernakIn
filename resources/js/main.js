// js/chat/main.js
document.addEventListener('DOMContentLoaded', function() {
    try {
        // Initialize the main chat system
        window.chatSystem = new ChatSystem();
        
        // Make chat system globally available for HTML onclick handlers
        window.copyMessage = (button) => chatSystem.uiManager.copyMessage(button);
        window.rateResponse = (button, rating) => chatSystem.uiManager.rateResponse(button, rating);
        
        console.log('AI Chat System initialized successfully');
    } catch (error) {
        console.error('Failed to initialize AI Chat System:', error);
        // Show fallback UI or error message
        document.body.innerHTML = `
            <div class="error-container">
                <h2>Terjadi Kesalahan</h2>
                <p>Sistem chat sedang tidak tersedia. Silakan refresh halaman.</p>
                <button onclick="location.reload()">Refresh Halaman</button>
            </div>
        `;
    }
});