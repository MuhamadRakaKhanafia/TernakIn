@extends('layouts.app')

@section('title', 'Chat AI Queries')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="content-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Chat AI Queries</h1>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-back">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
            </a>
        </div>
    </div>

    <!-- Chat Queries Table -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>User</th>
                        <th>Query</th>
                        <th>AI Response</th>
                        <th>Timestamp</th>
                        <th>Response Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paginatedQueries as $query)
                    <tr data-user-id="{{ $query->session && $query->session->user ? $query->session->user->id : '' }}" 
                        class="user-chat-row {{ $query->response_status === 'error' ? 'chat-error' : '' }}">
                        <td>
                            <div class="user-profile clickable-user" data-user-id="{{ $query->session && $query->session->user ? $query->session->user->id : '' }}">
                                <div class="user-info">
                                    <div class="user-name">{{ $query->session && $query->session->user ? $query->session->user->name : 'Unknown User' }}</div>
                                    @if($query->session && $query->session->user)
                                    <div class="contact-email">
                                        <i class="fas fa-envelope text-muted me-2"></i>
                                        {{ $query->session->user->email }}
                                    </div>
                                    @endif
                                </div>
                                <div class="chat-indicator">
                                    <i class="fas fa-comment-dots text-primary"></i>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="query-content" title="{{ $query->query }}">
                                {{ Str::limit($query->query, 80) }}
                                @if(strlen($query->query) > 80)
                                    <span class="see-more">...lihat lebih</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($query->response && $query->response !== 'N/A')
                                <div class="ai-response" title="{{ $query->response }}">
                                    <div class="ai-response-text">
                                        <i class="fas fa-robot text-info me-2"></i>
                                        {{ Str::limit($query->response, 80) }}
                                    </div>
                                    @if(strlen($query->response) > 80)
                                        <span class="see-more-ai">...lihat lebih</span>
                                    @endif
                                </div>
                            @else
                                <div class="ai-response empty">
                                    <i class="fas fa-robot text-muted me-2"></i>
                                    <span class="text-muted">Tidak ada respons</span>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="timestamp-info">
                                <i class="fas fa-clock text-muted me-2"></i>
                                <span class="timestamp">{{ $query->created_at->format('M d, Y H:i') }}</span>
                                <div class="time-ago text-muted small">
                                    ({{ $query->created_at->diffForHumans() }})
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="status-container">
                                @if($query->response_status === 'success')
                                    <span class="status-badge status-active">
                                        <i class="fas fa-check-circle me-1"></i>
                                        Success
                                    </span>
                                @elseif($query->response_status === 'error')
                                    <span class="status-badge status-inactive">
                                        <i class="fas fa-times-circle me-1"></i>
                                        Error
                                    </span>
                                @else
                                    <span class="status-badge status-pending">
                                        <i class="fas fa-clock me-1"></i>
                                        Pending
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Hidden Chat History Row -->
                    <tr class="chat-history-row" id="chat-history-{{ $query->session && $query->session->user ? $query->session->user->id : '' }}" style="display: none;">
                        <td colspan="5">
                            <div class="chat-history-container">
                                <div class="chat-history-header">
                                    <h6><i class="fas fa-history me-2"></i>Chat History - Last 10 Messages</h6>
                                    <button class="btn btn-sm btn-close-chat" data-user-id="{{ $query->session && $query->session->user ? $query->session->user->id : '' }}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                
                                <div class="chat-history-content" id="chat-content-{{ $query->session && $query->session->user ? $query->session->user->id : '' }}">
                                    <!-- Chat content will be loaded here via AJAX -->
                                    <div class="loading-chat text-center py-3">
                                        <i class="fas fa-spinner fa-spin me-2"></i>
                                        Loading chat history...
                                    </div>
                                </div>
                                
                                <div class="chat-history-stats">
                                    <div class="stat-item-small">
                                        <i class="fas fa-message me-1"></i>
                                        <span class="stat-label">Total Messages:</span>
                                        <span class="stat-value" id="total-messages-{{ $query->session && $query->session->user ? $query->session->user->id : '' }}">0</span>
                                    </div>
                                    <div class="stat-item-small">
                                        <i class="fas fa-user me-1"></i>
                                        <span class="stat-label">User Messages:</span>
                                        <span class="stat-value" id="user-messages-{{ $query->session && $query->session->user ? $query->session->user->id : '' }}">0</span>
                                    </div>
                                    <div class="stat-item-small">
                                        <i class="fas fa-robot me-1"></i>
                                        <span class="stat-label">AI Responses:</span>
                                        <span class="stat-value" id="ai-messages-{{ $query->session && $query->session->user ? $query->session->user->id : '' }}">0</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="no-data">
                                <i class="fas fa-comments fa-4x text-muted mb-4"></i>
                                <h4 class="text-muted mb-2">No Chat Queries Found</h4>
                                <p class="text-muted mb-0">No users have made queries in the Chat AI system yet.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($paginatedQueries->hasPages())
    <div class="pagination-wrapper">
        <div class="d-flex justify-content-between align-items-center">
            <div class="pagination-info">
                <i class="fas fa-list-ol me-2"></i>
                Showing <strong>{{ $paginatedQueries->firstItem() }}</strong> to <strong>{{ $paginatedQueries->lastItem() }}</strong> of <strong>{{ $paginatedQueries->total() }}</strong> chat queries
            </div>
            {{ $paginatedQueries->appends(request()->query())->links() }}
        </div>
    </div>
    @endif
    

<style>
/* ===== CHAT QUERIES PAGE STYLES ===== */

/* IMPORTANT: NAVBAR FIX */
body {
    padding-top: 0 !important;
}

/* Container utama untuk chat queries */
.container-fluid {
    padding: 20px;
    background-color: #f8fafc;
    min-height: calc(100vh - 70px);
    margin-top: 0 !important;
}

/* Ensure navbar stays on top */
.navbar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1030;
    background: white !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Adjust content to account for fixed navbar */
main {
    margin-top: 56px; /* Height of navbar */
}

/* Header Section */
.content-header {
    background: white;
    padding: 2rem;
    border-radius: 16px;
    margin-bottom: 1.5rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    margin-top: 0;
}

.content-header .d-flex {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 2rem;
}

.page-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0;
    letter-spacing: -0.025em;
}

/* Tombol Kembali */
.btn-back {
    background: #f1f5f9;
    color: #374151;
    border: 1px solid #d1d5db;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
    white-space: nowrap;
    text-decoration: none;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.btn-back:hover {
    background: #e2e8f0;
    border-color: #9ca3af;
    color: #111827;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    text-decoration: none;
}

.btn-back i {
    font-size: 0.9rem;
}

/* Table Container */
.table-container {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 1.5rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.table-responsive {
    max-height: 70vh;
    overflow-y: auto;
    border-radius: 16px;
}

.table-hover {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-bottom: 0;
    font-size: 0.875rem;
}

.table-hover thead th {
    position: sticky;
    top: 0;
    background: linear-gradient(135deg, #1f2937 0%, #374151 100%) !important;
    border: none;
    padding: 1.25rem 1rem;
    font-weight: 600;
    color: white !important;
    border-bottom: 2px solid #4b5563;
    text-align: left;
    white-space: nowrap;
    z-index: 1;
}

.table-hover tbody td {
    padding: 1rem;
    vertical-align: middle;
    border-bottom: 1px solid #f3f4f6;
    background: white;
}

/* User Profile with Clickable Feature */
.user-profile {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.user-profile.clickable-user:hover {
    background-color: #f3f4f6;
    transform: translateY(-1px);
}

.user-info {
    flex: 1;
}

.user-name {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.25rem;
    font-size: 1rem;
    transition: color 0.2s ease;
}

.clickable-user:hover .user-name {
    color: #2563eb;
}

.chat-indicator {
    color: #2563eb;
    opacity: 0.7;
    transition: all 0.2s ease;
}

.clickable-user:hover .chat-indicator {
    opacity: 1;
    transform: scale(1.1);
}

/* Contact Info */
.contact-email {
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #6b7280;
}

.contact-email i {
    color: #9ca3af;
}

/* Query Content */
.query-content {
    font-size: 0.875rem;
    color: #1f2937;
    max-width: 300px;
    word-wrap: break-word;
    line-height: 1.5;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 6px;
    transition: background-color 0.2s ease;
}

.query-content:hover {
    background-color: #f3f4f6;
}

.see-more {
    color: #2563eb;
    font-size: 0.8rem;
    font-weight: 500;
    cursor: pointer;
    margin-left: 0.25rem;
}

.see-more:hover {
    text-decoration: underline;
}

/* AI Response */
.ai-response {
    font-size: 0.875rem;
    color: #374151;
    max-width: 300px;
    word-wrap: break-word;
    line-height: 1.5;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 6px;
    background: #f0f9ff;
    border-left: 3px solid #0ea5e9;
    transition: all 0.2s ease;
}

.ai-response:hover {
    background: #e0f2fe;
    transform: translateX(2px);
}

.ai-response.empty {
    background: #f9fafb;
    border-left: 3px solid #9ca3af;
    color: #6b7280;
    cursor: default;
}

.ai-response.empty:hover {
    background: #f9fafb;
    transform: none;
}

.ai-response-text {
    display: flex;
    align-items: flex-start;
}

.ai-response-text i {
    margin-top: 0.125rem;
}

.see-more-ai {
    color: #0ea5e9;
    font-size: 0.8rem;
    font-weight: 500;
    cursor: pointer;
    margin-left: 0.25rem;
    display: inline-block;
    margin-top: 0.25rem;
}

.see-more-ai:hover {
    text-decoration: underline;
}

/* Timestamp Info */
.timestamp-info {
    font-size: 0.875rem;
    color: #6b7280;
}

.timestamp-info i {
    color: #9ca3af;
    margin-right: 0.5rem;
}

.timestamp {
    display: block;
    font-weight: 500;
}

.time-ago {
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

/* Status Indicators */
.status-container {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.status-badge {
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    white-space: nowrap;
}

.status-active {
    background-color: #d1fae5 !important;
    color: #065f46 !important;
    border: none;
}

.status-inactive {
    background-color: #fee2e2 !important;
    color: #991b1b !important;
    border: none;
}

.status-pending {
    background-color: #fef3c7 !important;
    color: #92400e !important;
    border: none;
}

/* Chat History Row */
.chat-history-row {
    background-color: #f9fafb !important;
    border-left: 4px solid #2563eb;
}

.chat-history-container {
    padding: 1.5rem;
    background: white;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    margin: 0.5rem 0;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.chat-history-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #e5e7eb;
}

.chat-history-header h6 {
    font-weight: 600;
    color: #1f2937;
    margin: 0;
    display: flex;
    align-items: center;
}

.btn-close-chat {
    background: #f1f5f9;
    border: 1px solid #d1d5db;
    color: #6b7280;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.btn-close-chat:hover {
    background: #e2e8f0;
    color: #374151;
}

/* Chat Messages */
.chat-messages-container {
    max-height: 300px;
    overflow-y: auto;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.chat-message {
    margin-bottom: 1rem;
    padding: 0.75rem;
    border-radius: 8px;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}

.user-message {
    background: white;
    border-left: 3px solid #3b82f6;
    margin-left: 2rem;
}

.ai-message {
    background: #f0f9ff;
    border-left: 3px solid #0ea5e9;
    margin-right: 2rem;
}

.message-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
    font-size: 0.8rem;
}

.message-sender {
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.message-time {
    color: #6b7280;
    font-size: 0.75rem;
}

.message-content-text {
    font-size: 0.875rem;
    line-height: 1.5;
    color: #1f2937;
}

.loading-chat {
    color: #6b7280;
    font-size: 0.875rem;
}

/* Chat Stats */
.chat-history-stats {
    display: flex;
    gap: 1.5rem;
    padding: 0.75rem;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.stat-item-small {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8rem;
}

.stat-label {
    color: #6b7280;
}

.stat-value {
    font-weight: 600;
    color: #1f2937;
}

/* Error Row Styling */
.chat-error {
    background-color: #fef2f2 !important;
}

.chat-error:hover {
    background-color: #fee2e2 !important;
}

/* No Data State */
.no-data {
    text-align: center;
    padding: 3rem 1rem;
    color: #6b7280;
}

.no-data i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.3;
}

.no-data h4 {
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #374151;
    font-size: 1.25rem;
}

.no-data p {
    font-size: 0.875rem;
    max-width: 300px;
    margin: 0 auto;
}

/* Pagination */
.pagination-wrapper {
    background: white;
    padding: 1.5rem;
    border-radius: 16px;
    margin-top: 1rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.pagination-info {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Scrollbar Styling */
.table-responsive::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.chat-messages-container::-webkit-scrollbar {
    width: 6px;
}

.chat-messages-container::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.chat-messages-container::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

/* Modal Styling */
.modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.modal-header {
    background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
    color: white;
    border-bottom: none;
    border-radius: 12px 12px 0 0;
    padding: 1.5rem;
}

.modal-title {
    font-weight: 600;
    display: flex;
    align-items: center;
}

.modal-body {
    padding: 1.5rem;
}

.message-type {
    margin-bottom: 1rem;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .table-container {
        margin: 0 10px;
    }
    
    .chat-history-stats {
        flex-wrap: wrap;
        gap: 1rem;
    }
}

@media (max-width: 768px) {
    .container-fluid {
        padding: 15px;
    }

    .content-header {
        padding: 1.5rem;
        margin: 1rem;
    }

    .content-header .d-flex {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .btn-back {
        align-self: flex-start;
        padding: 0.625rem 1.25rem;
        margin-top: 0.5rem;
    }

    .page-title {
        font-size: 1.5rem;
    }

    .table-responsive {
        max-height: 60vh;
        margin: 0 0.5rem;
    }

    .table-hover thead th,
    .table-hover tbody td {
        padding: 0.75rem 0.5rem;
    }

    .query-content, .ai-response {
        max-width: 200px;
    }
    
    .status-badge {
        padding: 0.3rem 0.6rem;
        font-size: 0.7rem;
    }
    
    .chat-history-container {
        padding: 1rem;
    }
    
    .chat-messages-container {
        max-height: 250px;
    }
}

@media (max-width: 576px) {
    .container-fluid {
        padding: 10px 5px;
    }

    .content-header {
        padding: 1.25rem;
        margin: 0.5rem;
    }

    .page-title {
        font-size: 1.375rem;
    }

    .table-responsive {
        max-height: 55vh;
    }

    .table-hover thead th,
    .table-hover tbody td {
        padding: 0.75rem 0.25rem;
        font-size: 0.8rem;
    }

    .user-profile {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .user-name {
        font-size: 0.9rem;
    }
    
    .contact-email {
        font-size: 0.8rem;
    }
    
    .no-data {
        padding: 2rem 1rem;
    }
    
    .no-data i {
        font-size: 2.5rem;
    }
    
    .no-data h4 {
        font-size: 1.1rem;
    }
    
    .chat-history-stats {
        flex-direction: column;
        gap: 0.5rem;
    }
}

/* Fix for table header on mobile */
@media (max-width: 768px) {
    .table-responsive {
        overflow-x: auto;
    }
    
    .table-hover {
        min-width: 700px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cache untuk chat history
    const chatHistoryCache = {};
    
    // Event listener untuk klik user
    document.querySelectorAll('.clickable-user').forEach(userElement => {
        userElement.addEventListener('click', function(e) {
            e.stopPropagation();
            const userId = this.dataset.userId;
            if (!userId) return;
            
            toggleChatHistory(userId);
        });
    });
    
    // Event listener untuk lihat lebih (query)
    document.querySelectorAll('.query-content').forEach(queryElement => {
        queryElement.addEventListener('click', function(e) {
            if (e.target.classList.contains('see-more')) return;
            
            const queryText = this.getAttribute('title') || this.textContent.replace('...lihat lebih', '').trim();
            showFullMessage(queryText, 'User Query');
        });
    });
    
    // Event listener untuk lihat lebih (AI response)
    document.querySelectorAll('.ai-response:not(.empty)').forEach(aiElement => {
        aiElement.addEventListener('click', function(e) {
            if (e.target.classList.contains('see-more-ai')) return;
            
            const aiText = this.getAttribute('title') || this.querySelector('.ai-response-text').textContent.replace('...lihat lebih', '').trim();
            showFullMessage(aiText, 'AI Response');
        });
    });
    
    // Event listener untuk see-more links
    document.querySelectorAll('.see-more, .see-more-ai').forEach(link => {
        link.addEventListener('click', function(e) {
            e.stopPropagation();
            const container = this.closest('.query-content, .ai-response');
            const text = container.getAttribute('title') || container.textContent.replace('...lihat lebih', '').trim();
            const type = container.classList.contains('query-content') ? 'User Query' : 'AI Response';
            showFullMessage(text, type);
        });
    });
    
    // Event listener untuk close chat buttons
    document.querySelectorAll('.btn-close-chat').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const userId = this.dataset.userId;
            if (userId) {
                const chatRow = document.getElementById(`chat-history-${userId}`);
                if (chatRow) {
                    chatRow.style.display = 'none';
                }
            }
        });
    });
    
    // Fungsi untuk toggle chat history
    function toggleChatHistory(userId) {
        const chatRow = document.getElementById(`chat-history-${userId}`);
        const userRow = document.querySelector(`.user-chat-row[data-user-id="${userId}"]`);
        
        if (!chatRow || !userRow) return;
        
        // Tutup semua chat history lainnya
        document.querySelectorAll('.chat-history-row').forEach(row => {
            if (row.id !== `chat-history-${userId}`) {
                row.style.display = 'none';
            }
        });
        
        // Toggle chat history yang diklik
        if (chatRow.style.display === 'none' || !chatRow.style.display) {
            chatRow.style.display = 'table-row';
            
            // Scroll ke chat history
            setTimeout(() => {
                chatRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }, 100);
            
            // Load chat history jika belum di-load
            if (!chatHistoryCache[userId]) {
                loadChatHistory(userId);
            }
        } else {
            chatRow.style.display = 'none';
        }
    }
    
    // Fungsi untuk load chat history via AJAX
    async function loadChatHistory(userId) {
        const chatContent = document.getElementById(`chat-content-${userId}`);
        if (!chatContent) return;
        
        try {
            // Show loading
            chatContent.innerHTML = `
                <div class="loading-chat text-center py-3">
                    <i class="fas fa-spinner fa-spin me-2"></i>
                    Loading chat history...
                </div>
            `;
            
            // Simulasi API call (ganti dengan API call sesungguhnya)
            // const response = await fetch(`/api/admin/chat-history/${userId}`);
            // const data = await response.json();
            
            // Simulasi data untuk demo
            setTimeout(() => {
                const mockData = {
                    messages: [
                        {
                            id: 1,
                            query: "Apa gejala penyakit sapi yang umum?",
                            response: "Gejala umum penyakit sapi antara lain: demam tinggi (39-41°C), nafsu makan menurun, lesu, air liur berlebihan, batuk, dan diare. Jika Anda melihat gejala ini, segera hubungi dokter hewan.",
                            type: 'user',
                            timestamp: '2024-01-15 14:30:00'
                        },
                        {
                            id: 2,
                            query: "Bagaimana cara mencegah penyakit pada sapi?",
                            response: "Cara mencegah penyakit pada sapi: 1. Vaksinasi rutin, 2. Kebersihan kandang, 3. Pakan berkualitas, 4. Isolasi hewan sakit, 5. Pemeriksaan kesehatan berkala, 6. Kontrol parasit, 7. Manajemen stres yang baik.",
                            type: 'user',
                            timestamp: '2024-01-15 14:35:00'
                        },
                        {
                            id: 3,
                            query: "Berapa kebutuhan pakan sapi per hari?",
                            response: "Kebutuhan pakan sapi dewasa: 2-3% dari berat badan per hari. Contoh: sapi 500kg butuh 10-15kg pakan/hari. Komposisi: 70% hijauan, 30% konsentrat. Pastikan air minum cukup 40-60 liter/hari.",
                            type: 'user',
                            timestamp: '2024-01-15 14:40:00'
                        }
                    ],
                    stats: {
                        total: 12,
                        user: 8,
                        ai: 4
                    }
                };
                
                renderChatHistory(userId, mockData);
                chatHistoryCache[userId] = mockData;
            }, 800);
            
        } catch (error) {
            console.error('Error loading chat history:', error);
            chatContent.innerHTML = `
                <div class="text-center py-3 text-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Gagal memuat chat history. Silakan coba lagi.
                </div>
            `;
        }
    }
    
    // Fungsi untuk render chat history
    function renderChatHistory(userId, data) {
        const chatContent = document.getElementById(`chat-content-${userId}`);
        const totalMessages = document.getElementById(`total-messages-${userId}`);
        const userMessages = document.getElementById(`user-messages-${userId}`);
        const aiMessages = document.getElementById(`ai-messages-${userId}`);
        
        if (!chatContent) return;
        
        // Update stats
        if (totalMessages) totalMessages.textContent = data.stats.total;
        if (userMessages) userMessages.textContent = data.stats.user;
        if (aiMessages) aiMessages.textContent = data.stats.ai;
        
        // Render messages
        let html = '<div class="chat-messages-container">';
        
        if (data.messages.length === 0) {
            html += `
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-comment-slash fa-2x mb-3"></i>
                    <p>Tidak ada chat history untuk user ini.</p>
                </div>
            `;
        } else {
            // Show last 5 messages
            const lastMessages = data.messages.slice(0, 5);
            
            lastMessages.forEach(msg => {
                const time = new Date(msg.timestamp).toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
                
                if (msg.type === 'user') {
                    html += `
                        <div class="chat-message user-message">
                            <div class="message-header">
                                <div class="message-sender">
                                    <i class="fas fa-user-circle text-primary"></i>
                                    <span>User</span>
                                </div>
                                <div class="message-time">${time}</div>
                            </div>
                            <div class="message-content-text">${msg.query}</div>
                            ${msg.response ? `
                            <div class="ai-response mt-2" style="background: #f0f9ff; border-left: 2px solid #0ea5e9; padding: 0.5rem; border-radius: 4px;">
                                <div class="message-sender small">
                                    <i class="fas fa-robot text-info"></i>
                                    <span>AI Response</span>
                                </div>
                                <div class="message-content-text small mt-1">${msg.response}</div>
                            </div>
                            ` : ''}
                        </div>
                    `;
                }
            });
        }
        
        html += '</div>';
        
        // Add view all button if there are more messages
        if (data.messages.length > 5) {
            html += `
                <div class="text-center mt-3">
                    <button class="btn btn-outline-primary btn-sm" onclick="viewAllChats('${userId}')">
                        <i class="fas fa-history me-1"></i>
                        Lihat Semua Chat (${data.messages.length} pesan)
                    </button>
                </div>
            `;
        }
        
        chatContent.innerHTML = html;
    }
    
    // Fungsi untuk menampilkan modal dengan pesan lengkap
    function showFullMessage(text, type) {
        const messageType = document.getElementById('messageType');
        const messageContent = document.getElementById('messageContent');
        
        if (messageType && messageContent) {
            messageType.textContent = type;
            messageContent.textContent = text;
            
            const messageModal = new bootstrap.Modal(document.getElementById('messageModal'));
            messageModal.show();
        }
    }
    
    // Fungsi untuk copy text ke clipboard
    function copyToClipboard() {
        const messageContent = document.getElementById('messageContent');
        if (messageContent) {
            navigator.clipboard.writeText(messageContent.textContent)
                .then(() => {
                    // Show success message
                    const copyBtn = document.querySelector('.modal-footer .btn-primary');
                    const originalHtml = copyBtn.innerHTML;
                    copyBtn.innerHTML = '<i class="fas fa-check me-2"></i>Copied!';
                    copyBtn.disabled = true;
                    
                    setTimeout(() => {
                        copyBtn.innerHTML = originalHtml;
                        copyBtn.disabled = false;
                    }, 2000);
                })
                .catch(err => {
                    console.error('Failed to copy:', err);
                    alert('Gagal menyalin teks');
                });
        }
    }
    
    // Fungsi untuk view all chats (dapat diimplementasikan lebih lanjut)
    window.viewAllChats = function(userId) {
        alert(`Fitur "Lihat Semua Chat" untuk user ID: ${userId} akan diimplementasikan lebih lanjut.`);
        // Implementasi: redirect ke halaman detail chat user atau modal yang lebih besar
    };
    
    // Auto-load first user's chat history jika diinginkan
    // const firstUser = document.querySelector('.clickable-user');
    // if (firstUser) {
    //     setTimeout(() => {
    //         toggleChatHistory(firstUser.dataset.userId);
    //     }, 1000);
    // }
});
</script>
@endsection