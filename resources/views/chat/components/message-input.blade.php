<div id="messageInput" class="message-input">
    <form id="sendMessageForm" class="message-form">
        @csrf
        <input type="hidden" name="session_id" id="currentSessionId">
        
        <div class="input-group">
            <textarea name="message" id="messageText" class="form-control" 
                      placeholder="Ketik pertanyaan Anda tentang kesehatan ternak..." 
                      rows="1" required></textarea>
            
            <button type="submit" class="btn btn-primary btn-send" id="sendMessageBtn">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
        
        <div class="input-actions">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="attachFileBtn">
                <i class="fas fa-paperclip"></i> Lampirkan File
            </button>
        </div>
    </form>
</div>