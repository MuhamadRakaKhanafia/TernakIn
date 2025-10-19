<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konsultasi Kesehatan Ternak - AI Chat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .chat-messages {
            max-height: 500px;
            overflow-y: auto;
        }
        .message-bubble {
            max-width: 70%;
            word-wrap: break-word;
        }
        .typing-indicator {
            display: none;
        }
        .typing-indicator.show {
            display: flex;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            <i class="fas fa-robot text-green-600 mr-2"></i>
                            Konsultasi Kesehatan Ternak AI
                        </h1>
                        <p class="text-gray-600 mt-1">Konsultasikan masalah kesehatan ternak Anda dengan AI</p>
                    </div>
                    <div class="flex space-x-2">
                        <button id="sessionsBtn" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-list mr-1"></i>Sesi Chat
                        </button>
                        <button id="statsBtn" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-chart-bar mr-1"></i>Statistik
                        </button>
                        <button id="logoutBtn" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-sign-out-alt mr-1"></i>Logout
                        </button>
                    </div>
                </div>
            </div>

            <!-- Session Info -->
            <div id="sessionInfo" class="bg-white rounded-lg shadow-md p-4 mb-6" style="display: none;">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="font-semibold text-gray-800" id="sessionTitle">Sesi Chat</h3>
                        <p class="text-sm text-gray-600" id="sessionAnimal">Jenis Ternak: -</p>
                    </div>
                    <div class="flex space-x-2">
                        <button id="newSessionBtn" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                            <i class="fas fa-plus mr-1"></i>Sesi Baru
                        </button>
                        <button id="deleteSessionBtn" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                            <i class="fas fa-trash mr-1"></i>Hapus Sesi
                        </button>
                    </div>
                </div>
            </div>

            <!-- Chat Interface -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <!-- Chat Messages -->
                <div id="chatContainer" class="p-4 chat-messages" style="display: none;">
                    <div id="messagesList" class="space-y-4">
                        <!-- Messages will be loaded here -->
                    </div>

                    <!-- Typing Indicator -->
                    <div id="typingIndicator" class="typing-indicator flex items-center space-x-2 text-gray-500">
                        <div class="flex space-x-1">
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        </div>
                        <span class="text-sm">AI sedang mengetik...</span>
                    </div>
                </div>

                <!-- Start New Session Form -->
                <div id="newSessionForm" class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Mulai Sesi Chat Baru</h3>
                    <form id="startSessionForm">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="animalType">
                                Jenis Ternak (Opsional)
                            </label>
                            <select id="animalType" name="animal_type_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="">Pilih jenis ternak...</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="initialMessage">
                                Pesan Awal (Opsional)
                            </label>
                            <textarea id="initialMessage" name="initial_message" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                placeholder="Jelaskan masalah kesehatan ternak Anda..."></textarea>
                        </div>
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-play mr-1"></i>Mulai Chat
                        </button>
                    </form>
                </div>

                <!-- Message Input -->
                <div id="messageInput" class="p-4 border-t border-gray-200" style="display: none;">
                    <form id="sendMessageForm" class="flex space-x-2">
                        <input type="text" id="messageInputField"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Ketik pesan Anda..." required>
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <!-- Sessions Modal -->
    <div id="sessionsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-96 overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Daftar Sesi Chat</h3>
                        <button id="closeSessionsModal" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div id="sessionsList" class="space-y-2">
                        <!-- Sessions will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Modal -->
    <div id="statsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Statistik Penggunaan AI</h3>
                        <button id="closeStatsModal" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div id="statsContent" class="space-y-4">
                        <!-- Stats will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let currentSessionId = null;
        let authToken = localStorage.getItem('auth_token');

        // API Base URL
        const API_BASE = '/api';

        // DOM Elements
        const chatContainer = document.getElementById('chatContainer');
        const newSessionForm = document.getElementById('newSessionForm');
        const messageInput = document.getElementById('messageInput');
        const messagesList = document.getElementById('messagesList');
        const typingIndicator = document.getElementById('typingIndicator');
        const sessionInfo = document.getElementById('sessionInfo');

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            if (!authToken) {
                window.location.href = '/login';
                return;
            }

            loadAnimalTypes();
            setupEventListeners();
        });

        // Setup event listeners
        function setupEventListeners() {
            // Forms
            document.getElementById('startSessionForm').addEventListener('submit', startNewSession);
            document.getElementById('sendMessageForm').addEventListener('submit', sendMessage);

            // Buttons
            document.getElementById('sessionsBtn').addEventListener('click', showSessionsModal);
            document.getElementById('statsBtn').addEventListener('click', showStatsModal);
            document.getElementById('logoutBtn').addEventListener('click', logout);
            document.getElementById('newSessionBtn').addEventListener('click', showNewSessionForm);
            document.getElementById('deleteSessionBtn').addEventListener('click', deleteCurrentSession);

            // Modal close buttons
            document.getElementById('closeSessionsModal').addEventListener('click', hideSessionsModal);
            document.getElementById('closeStatsModal').addEventListener('click', hideStatsModal);
        }

        // API helper functions
        async function apiRequest(endpoint, options = {}) {
            const defaultOptions = {
                headers: {
                    'Authorization': `Bearer ${authToken}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            };

            const response = await fetch(`${API_BASE}${endpoint}`, {
                ...defaultOptions,
                ...options,
                headers: {
                    ...defaultOptions.headers,
                    ...options.headers
                }
            });

            if (response.status === 401) {
                logout();
                return;
            }

            return response.json();
        }

        // Load animal types
        async function loadAnimalTypes() {
            try {
                const response = await apiRequest('/animal-types');
                if (response.success) {
                    const select = document.getElementById('animalType');
                    response.data.forEach(type => {
                        const option = document.createElement('option');
                        option.value = type.id;
                        option.textContent = type.name;
                        select.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Error loading animal types:', error);
            }
        }

        // Start new session
        async function startNewSession(e) {
            e.preventDefault();

            const formData = new FormData(e.target);
            const data = {
                animal_type_id: formData.get('animal_type_id') || null,
                initial_message: formData.get('initial_message') || null
            };

            try {
                const response = await apiRequest('/ai-chat/sessions/start', {
                    method: 'POST',
                    body: JSON.stringify(data)
                });

                if (response.success) {
                    currentSessionId = response.data.session.session_id;
                    updateSessionInfo(response.data.session);

                    // Show chat interface
                    newSessionForm.style.display = 'none';
                    chatContainer.style.display = 'block';
                    messageInput.style.display = 'block';
                    sessionInfo.style.display = 'block';

                    // Clear and load messages
                    messagesList.innerHTML = '';
                    if (response.data.initial_response) {
                        addMessageToChat('assistant', response.data.initial_response.content, response.data.initial_response.created_at);
                    }

                    // Clear form
                    e.target.reset();
                } else {
                    alert('Error: ' + (response.error || 'Gagal membuat sesi'));
                }
            } catch (error) {
                console.error('Error starting session:', error);
                alert('Terjadi kesalahan saat membuat sesi');
            }
        }

        // Send message
        async function sendMessage(e) {
            e.preventDefault();

            const message = document.getElementById('messageInputField').value.trim();
            if (!message || !currentSessionId) return;

            // Add user message to chat
            addMessageToChat('user', message);

            // Clear input
            document.getElementById('messageInputField').value = '';

            // Show typing indicator
            typingIndicator.classList.add('show');

            try {
                const response = await apiRequest(`/ai-chat/sessions/${currentSessionId}/message`, {
                    method: 'POST',
                    body: JSON.stringify({ message })
                });

                // Hide typing indicator
                typingIndicator.classList.remove('show');

                if (response.success) {
                    // Add AI response
                    addMessageToChat('assistant', response.data.ai_response.content, response.data.ai_response.created_at);
                } else {
                    alert('Error: ' + (response.error || 'Gagal mengirim pesan'));
                }
            } catch (error) {
                console.error('Error sending message:', error);
                typingIndicator.classList.remove('show');
                alert('Terjadi kesalahan saat mengirim pesan');
            }
        }

        // Add message to chat
        function addMessageToChat(role, content, timestamp = null) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `flex ${role === 'user' ? 'justify-end' : 'justify-start'}`;

            const bubbleDiv = document.createElement('div');
            bubbleDiv.className = `message-bubble p-3 rounded-lg ${
                role === 'user'
                    ? 'bg-green-500 text-white'
                    : 'bg-gray-200 text-gray-800'
            }`;

            const contentDiv = document.createElement('div');
            contentDiv.textContent = content;
            bubbleDiv.appendChild(contentDiv);

            if (timestamp) {
                const timeDiv = document.createElement('div');
                timeDiv.className = 'text-xs mt-1 opacity-70';
                timeDiv.textContent = new Date(timestamp).toLocaleString('id-ID');
                bubbleDiv.appendChild(timeDiv);
            }

            messageDiv.appendChild(bubbleDiv);
            messagesList.appendChild(messageDiv);

            // Scroll to bottom
            messagesList.scrollTop = messagesList.scrollHeight;
        }

        // Update session info
        function updateSessionInfo(session) {
            document.getElementById('sessionTitle').textContent = session.title;
            document.getElementById('sessionAnimal').textContent =
                `Jenis Ternak: ${session.animal_type ? session.animal_type.name : 'Tidak ditentukan'}`;
        }

        // Show new session form
        function showNewSessionForm() {
            chatContainer.style.display = 'none';
            messageInput.style.display = 'none';
            sessionInfo.style.display = 'none';
            newSessionForm.style.display = 'block';
            currentSessionId = null;
        }

        // Delete current session
        async function deleteCurrentSession() {
            if (!currentSessionId) return;

            if (!confirm('Apakah Anda yakin ingin menghapus sesi ini?')) return;

            try {
                const response = await apiRequest(`/ai-chat/sessions/${currentSessionId}`, {
                    method: 'DELETE'
                });

                if (response.success) {
                    showNewSessionForm();
                } else {
                    alert('Error: ' + (response.error || 'Gagal menghapus sesi'));
                }
            } catch (error) {
                console.error('Error deleting session:', error);
                alert('Terjadi kesalahan saat menghapus sesi');
            }
        }

        // Show sessions modal
        async function showSessionsModal() {
            try {
                const response = await apiRequest('/ai-chat/sessions');

                if (response.success) {
                    const sessionsList = document.getElementById('sessionsList');
                    sessionsList.innerHTML = '';

                    if (response.data.length === 0) {
                        sessionsList.innerHTML = '<p class="text-gray-500 text-center py-4">Belum ada sesi chat</p>';
                    } else {
                        response.data.forEach(session => {
                            const sessionDiv = document.createElement('div');
                            sessionDiv.className = 'p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50';
                            sessionDiv.onclick = () => loadSession(session.session_id);

                            sessionDiv.innerHTML = `
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-medium text-gray-800">${session.title}</h4>
                                        <p class="text-sm text-gray-600">
                                            ${session.animal_type ? session.animal_type.name : 'Tidak ditentukan'} •
                                            ${new Date(session.last_activity).toLocaleDateString('id-ID')}
                                        </p>
                                    </div>
                                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                        ${session.message_count} pesan
                                    </span>
                                </div>
                            `;

                            sessionsList.appendChild(sessionDiv);
                        });
                    }

                    document.getElementById('sessionsModal').classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error loading sessions:', error);
                alert('Terjadi kesalahan saat memuat sesi');
            }
        }

        // Load session
        async function loadSession(sessionId) {
            try {
                const response = await apiRequest(`/ai-chat/sessions/${sessionId}`);

                if (response.success) {
                    currentSessionId = sessionId;
                    updateSessionInfo(response.data);

                    // Show chat interface
                    newSessionForm.style.display = 'none';
                    chatContainer.style.display = 'block';
                    messageInput.style.display = 'block';
                    sessionInfo.style.display = 'block';

                    // Load messages
                    messagesList.innerHTML = '';
                    response.data.messages.forEach(message => {
                        addMessageToChat(message.role, message.content, message.created_at);
                    });

                    hideSessionsModal();
                }
            } catch (error) {
                console.error('Error loading session:', error);
                alert('Terjadi kesalahan saat memuat sesi');
            }
        }

        // Show stats modal
        async function showStatsModal() {
            try {
                const response = await apiRequest('/ai-chat/usage-stats');

                if (response.success) {
                    const stats = response.data;
                    const statsContent = document.getElementById('statsContent');

                    statsContent.innerHTML = `
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">${stats.total_messages}</div>
                                <div class="text-sm text-blue-800">Total Pesan</div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">${stats.total_tokens.toLocaleString()}</div>
                                <div class="text-sm text-green-800">Total Token</div>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-lg col-span-2">
                                <div class="text-2xl font-bold text-purple-600">Rp ${stats.total_cost.toFixed(2)}</div>
                                <div class="text-sm text-purple-800">Total Biaya</div>
                            </div>
                        </div>
                    `;

                    document.getElementById('statsModal').classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error loading stats:', error);
                alert('Terjadi kesalahan saat memuat statistik');
            }
        }

        // Hide modals
        function hideSessionsModal() {
            document.getElementById('sessionsModal').classList.add('hidden');
        }

        function hideStatsModal() {
            document.getElementById('statsModal').classList.add('hidden');
        }

        // Logout
        function logout() {
            localStorage.removeItem('auth_token');
            window.location.href = '/login';
        }
    </script>
</body>
</html>
