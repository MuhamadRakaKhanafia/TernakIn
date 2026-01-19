<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TernakIN - Dashboard')</title>
    <!-- Memuat Font Awesome 5.15.3 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Memuat Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen">
    @if(!in_array(Route::currentRouteName(), ['login', 'register']))
    <!-- Navbar -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo atau Nama Aplikasi -->
                <div class="flex items-center">
                    <div class="logo-container flex items-center">
                        <img src="{{ asset('images/TernakIn.jpg') }}" 
                             alt="TernakIN Logo" 
                             class="h-8 w-8 mr-2 rounded-lg logo-img"
                             onerror="this.style.display='none'; document.getElementById('logo-icon').style.display='flex';">
                        <div id="logo-icon" class="logo-icon h-8 w-8 mr-2 rounded-lg bg-gradient-to-r from-green-500 to-green-600 flex items-center justify-center text-white shadow-md" style="display: none;">
                            <i class="fas fa-cow text-sm"></i>
                        </div>
                    </div>
                    <a href="/" class="flex-shrink-0 text-2xl font-bold text-green-600 logo-text">
                        TernakIN
                    </a>
                </div>

                <!-- Tautan Navigasi Desktop -->
                <div class="hidden md:flex md:space-x-4">
                    <a href="{{ route('welcome') }}" class="text-gray-600 hover:bg-green-50 hover:text-green-600 px-3 py-2 rounded-lg text-sm font-medium transition duration-150 ease-in-out flex items-center">
                        <i class="fas fa-home mr-2"></i> Beranda
                    </a>
                    @auth
                        <a href="{{ route('livestocks.index') }}" class="text-gray-600 hover:bg-green-50 hover:text-green-600 px-3 py-2 rounded-lg text-sm font-medium transition duration-150 ease-in-out flex items-center">
                            <i class="fas fa-newspaper mr-2"></i> Form
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:bg-green-50 hover:text-green-600 px-3 py-2 rounded-lg text-sm font-medium transition duration-150 ease-in-out flex items-center">
                            <i class="fas fa-newspaper mr-2"></i> Form
                        </a>
                    @endauth
                    <a href="{{ route('diseases.index') }}" class="text-gray-600 hover:bg-green-50 hover:text-green-600 px-3 py-2 rounded-lg text-sm font-medium transition duration-150 ease-in-out flex items-center">
                        <i class="fas fa-stethoscope mr-2"></i> Penyakit
                    </a>
                    @auth
                        <a href="{{ route('chat.index') }}" class="text-gray-600 hover:bg-green-50 hover:text-green-600 px-3 py-2 rounded-lg text-sm font-medium transition duration-150 ease-in-out flex items-center">
                            <i class="fas fa-robot mr-2"></i> AI Chat
                        </a>
                        <a href="{{ route('vaccinations.index') }}" class="text-gray-600 hover:bg-green-50 hover:text-green-600 px-3 py-2 rounded-lg text-sm font-medium transition duration-150 ease-in-out flex items-center">
                            <i class="fas fa-syringe mr-2"></i> Vaksin
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:bg-green-50 hover:text-green-600 px-3 py-2 rounded-lg text-sm font-medium transition duration-150 ease-in-out flex items-center">
                            <i class="fas fa-robot mr-2"></i> AI Chat
                        </a>
                    @endauth
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:bg-green-50 hover:text-green-600 px-3 py-2 rounded-lg text-sm font-medium transition duration-150 ease-in-out flex items-center">
                                <i class="fas fa-tachometer-alt mr-2"></i> Admin Dashboard
                            </a>
                            <a href="{{ route('analytics.index') }}" class="text-gray-600 hover:bg-green-50 hover:text-green-600 px-3 py-2 rounded-lg text-sm font-medium transition duration-150 ease-in-out flex items-center">
                                <i class="fas fa-chart-bar mr-2"></i> Analitik
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:bg-green-50 hover:text-green-600 px-3 py-2 rounded-lg text-sm font-medium transition duration-150 ease-in-out flex items-center">
                            <i class="fas fa-chart-bar mr-2"></i> Analitik
                        </a>
                    @endauth
                    @auth
                        <!-- Profile Dropdown -->
                        <div class="relative">
                            <button id="profile-button" type="button" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" aria-expanded="false" aria-haspopup="true">
                                <span class="sr-only">Open user menu</span>
                                <img class="h-8 w-8 rounded-full object-cover border-2 border-white shadow-sm" src="{{ auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : asset('images/default-avatar.svg') }}" alt="Profile Picture">
                                <i class="fas fa-chevron-down ml-2 text-gray-400"></i>
                            </button>

                            <!-- Dropdown menu -->
                            <div id="profile-menu" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden z-50" role="menu" aria-orientation="vertical" aria-labelledby="profile-button" tabindex="-1">
                                <div class="py-1" role="none">
                                    <!-- Profile info -->
                                    <div class="px-4 py-3 border-b border-gray-200">
                                        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                                        <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                                    </div>

                                    <!-- Profile link -->
                                    <a href="{{ route('profile') }}" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100 hover:text-gray-900 flex items-center" role="menuitem" tabindex="-1">
                                        <i class="fas fa-user mr-2"></i> Profil
                                    </a>

                                    <!-- Logout -->
                                    <form method="POST" action="{{ route('logout') }}" class="block">
                                        @csrf
                                        <button type="submit" class="text-red-600 w-full text-left block px-4 py-2 text-sm hover:bg-red-50 hover:text-red-700 flex items-center" role="menuitem" tabindex="-1">
                                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="bg-green-600 text-white px-3 py-2 rounded-lg text-sm font-medium shadow-md hover:bg-green-700 transition duration-150 ease-in-out flex items-center">
                            <i class="fas fa-sign-in-alt mr-2"></i> Login
                        </a>
                        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-3 py-2 rounded-lg text-sm font-medium shadow-md hover:bg-blue-700 transition duration-150 ease-in-out flex items-center">
                            <i class="fas fa-user-plus mr-2"></i> Register
                        </a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-button" type="button" class="text-gray-600 hover:text-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2" aria-expanded="false" aria-controls="mobile-menu">
                        <span class="sr-only">Buka menu utama</span>
                        <i id="menu-icon-open" class="fas fa-bars text-xl"></i>
                        <i id="menu-icon-close" class="fas fa-times text-xl hidden"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div class="md:hidden hidden" id="mobile-menu">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 border-t border-gray-100">
                    <a href="{{ route('welcome') }}" class="text-gray-600 hover:bg-green-50 hover:text-green-600 block px-3 py-2 rounded-md text-base font-medium transition duration-150 ease-in-out flex items-center">
                        <i class="fas fa-home mr-2"></i> Beranda
                    </a>
                    @auth
                        <a href="{{ route('livestocks.index') }}" class="text-gray-600 hover:bg-green-50 hover:text-green-600 block px-3 py-2 rounded-md text-base font-medium transition duration-150 ease-in-out flex items-center">
                            <i class="fas fa-newspaper mr-2"></i> Form
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:bg-green-50 hover:text-green-600 block px-3 py-2 rounded-md text-base font-medium transition duration-150 ease-in-out flex items-center">
                            <i class="fas fa-newspaper mr-2"></i> Form
                        </a>
                    @endauth
                    <a href="{{ route('diseases.index') }}" class="text-gray-600 hover:bg-green-50 hover:text-green-600 block px-3 py-2 rounded-md text-base font-medium transition duration-150 ease-in-out flex items-center">
                        <i class="fas fa-stethoscope mr-2"></i> Penyakit
                    </a>
                    @auth
                        <a href="{{ route('chat.index') }}" class="text-gray-600 hover:bg-green-50 hover:text-green-600 block px-3 py-2 rounded-md text-base font-medium transition duration-150 ease-in-out flex items-center">
                            <i class="fas fa-robot mr-2"></i> AI Chat
                        </a>
                        <a href="{{ route('vaccinations.index') }}" class="text-gray-600 hover:bg-green-50 hover:text-green-600 block px-3 py-2 rounded-md text-base font-medium transition duration-150 ease-in-out flex items-center">
                            <i class="fas fa-syringe mr-2"></i> Vaksin
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:bg-green-50 hover:text-green-600 block px-3 py-2 rounded-md text-base font-medium transition duration-150 ease-in-out flex items-center">
                            <i class="fas fa-robot mr-2"></i> AI Chat
                        </a>
                    @endauth
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:bg-green-50 hover:text-green-600 block px-3 py-2 rounded-md text-base font-medium transition duration-150 ease-in-out flex items-center">
                                <i class="fas fa-tachometer-alt mr-2"></i> Admin Dashboard
                            </a>
                            <a href="{{ route('analytics.index') }}" class="text-gray-600 hover:bg-green-50 hover:text-green-600 block px-3 py-2 rounded-md text-base font-medium transition duration-150 ease-in-out flex items-center">
                                <i class="fas fa-chart-bar mr-2"></i> Analitik
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:bg-green-50 hover:text-green-600 block px-3 py-2 rounded-md text-base font-medium transition duration-150 ease-in-out flex items-center">
                            <i class="fas fa-chart-bar mr-2"></i> Analitik
                        </a>
                    @endauth
                    @auth
                        <!-- Mobile Profile Section -->
                        <div class="border-t border-gray-200 pt-4 pb-3">
                            <div class="flex items-center px-3">
                                <img src="{{ auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : asset('images/default-avatar.svg') }}"
                                     alt="Profile Picture"
                                     class="h-10 w-10 rounded-full object-cover border-2 border-white shadow-sm mr-3">
                                <div class="flex-1">
                                    <p class="text-base font-medium text-gray-800">{{ auth()->user()->name }}</p>
                                    <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                                </div>
                            </div>
                            <div class="mt-3 space-y-1">
                                <a href="{{ route('profile') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:bg-green-50 hover:text-green-600 transition duration-150 ease-in-out flex items-center">
                                    <i class="fas fa-user mr-2"></i> Profil
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="block">
                                    @csrf
                                    <button type="submit" class="w-full text-left text-red-600 hover:bg-red-50 hover:text-red-700 block px-3 py-2 rounded-md text-base font-medium transition duration-150 ease-in-out flex items-center">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="bg-green-600 text-white block px-3 py-2 rounded-md text-base font-medium transition duration-150 ease-in-out mt-1 flex items-center">
                            <i class="fas fa-sign-in-alt mr-2"></i> Masuk
                        </a>
                        <a href="{{ route('register') }}" class="bg-blue-600 text-white block px-3 py-2 rounded-md text-base font-medium transition duration-150 ease-in-out mt-1 flex items-center">
                            <i class="fas fa-user-plus mr-2"></i> Daftar
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Broadcast Messages -->
    @php
        try {
            $activeBroadcasts = \App\Models\Broadcast::where('is_active', true)
                ->where(function($query) {
                    $query->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                })
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            $activeBroadcasts = collect();
        }
    @endphp

    @if($activeBroadcasts->count() > 0)
    <div id="broadcast-container" class="broadcast-container">
        @foreach($activeBroadcasts as $broadcast)
        <div class="broadcast-message" data-broadcast-id="{{ $broadcast->id }}">
            <div class="broadcast-content">
                <div class="broadcast-text">
                    {!! $broadcast->message !!}
                    @if($broadcast->link_url)
                        <a href="{{ $broadcast->link_url }}" class="broadcast-link" target="_blank">
                            {{ $broadcast->link_text ?: 'Pelajari Lebih Lanjut' }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
    @endif

    <!-- Main Content -->
    <div class="main-content">

        @yield('content')
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Navbar Mobile Logic
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const iconOpen = document.getElementById('menu-icon-open');
            const iconClose = document.getElementById('menu-icon-close');

            if (mobileMenuButton && mobileMenu && iconOpen && iconClose) {
                mobileMenuButton.addEventListener('click', () => {
                    const isExpanded = mobileMenuButton.getAttribute('aria-expanded') === 'true' || false;

                    mobileMenu.classList.toggle('hidden');
                    iconOpen.classList.toggle('hidden');
                    iconClose.classList.toggle('hidden');

                    mobileMenuButton.setAttribute('aria-expanded', !isExpanded);
                });
            }

            // Profile Dropdown Logic
            const profileButton = document.getElementById('profile-button');
            const profileMenu = document.getElementById('profile-menu');

            if (profileButton && profileMenu) {
                profileButton.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isExpanded = profileButton.getAttribute('aria-expanded') === 'true';
                    profileButton.setAttribute('aria-expanded', !isExpanded);
                    profileMenu.classList.toggle('hidden');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', (e) => {
                    if (!profileButton.contains(e.target) && !profileMenu.contains(e.target)) {
                        profileButton.setAttribute('aria-expanded', 'false');
                        profileMenu.classList.add('hidden');
                    }
                });

                // Close dropdown on escape key
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        profileButton.setAttribute('aria-expanded', 'false');
                        profileMenu.classList.add('hidden');
                    }
                });
            }

            // Broadcast Display Logic
            const broadcastContainer = document.getElementById('broadcast-container');
            if (broadcastContainer) {
                const broadcastMessages = broadcastContainer.querySelectorAll('.broadcast-message');
                const displayDuration = 20000; // 20 seconds per display (extremely slow for readability)
                const hideDuration = 25000; // Hide for 25 seconds
                let currentBroadcastIndex = 0;
                let displayInterval;
                let cycleCount = 0; // Track how many complete cycles have been shown

                // Hide all messages initially
                broadcastMessages.forEach(msg => msg.style.display = 'none');

                function showNextBroadcast() {
                    if (broadcastMessages.length === 0) return;

                    // Hide previous message
                    broadcastMessages.forEach(msg => msg.style.display = 'none');

                    // Show current message
                    broadcastMessages[currentBroadcastIndex].style.display = 'block';

                    // Move to next broadcast
                    currentBroadcastIndex++;

                    // If we've gone through all broadcasts, check cycle count
                    if (currentBroadcastIndex >= broadcastMessages.length) {
                        cycleCount++;
                        currentBroadcastIndex = 0;

                        // If we've shown 2 complete cycles, hide container and restart after 1 minute
                        if (cycleCount >= 2) {
                            clearInterval(displayInterval);
                            broadcastContainer.style.display = 'none'; // Hide immediately
                            setTimeout(() => {
                                cycleCount = 0; // Reset cycle count
                                // Reset animation state for all messages
                                broadcastMessages.forEach(msg => {
                                    msg.style.display = 'none';
                                    msg.style.animation = 'none';
                                    msg.offsetHeight; // Trigger reflow
                                    msg.style.animation = '';
                                });
                                broadcastContainer.style.display = 'block';
                                startBroadcastCycle();
                            }, hideDuration);
                        }
                        // If not yet 2 cycles, continue immediately with next cycle
                    }
                }

                function startBroadcastCycle() {
                    showNextBroadcast();
                    displayInterval = setInterval(showNextBroadcast, displayDuration);
                }

                // Start the broadcast cycle
                startBroadcastCycle();
            }
        });
    </script>

    <script src="{{ asset('js/script.js') }}"></script>
    @stack('scripts')

    <style>
        /* Broadcast Messages Styles */
        .broadcast-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 0; /* Increased padding for better visibility */
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            width: 100%;
            min-height: 40px; /* Ensure minimum height */
        }

        .broadcast-message {
            position: absolute;
            width: 100%;
            padding: 0 20px;
            animation: slideInFromRight 20s ease-in-out forwards; /* Match display duration */
        }

        .broadcast-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            font-size: 16px; /* Larger font size */
            font-weight: 500;
        }

        .broadcast-icon {
            color: #ffd700;
            font-size: 18px; /* Larger icon */
        }

        .broadcast-text {
            flex: 1;
            text-align: center;
        }

        .broadcast-link {
            color: #ffffff;
            text-decoration: underline;
            font-weight: 600;
            margin-left: 8px;
            transition: color 0.3s ease;
        }

        .broadcast-link:hover {
            color: #ffd700;
            text-decoration: none;
        }

        @keyframes slideInFromRight {
            0% {
                transform: translateX(100%);
                opacity: 0;
            }
            15% { /* Slower transition in */
                opacity: 1;
            }
            85% { /* Stay visible longer */
                opacity: 1;
            }
            100% {
                transform: translateX(-100%);
                opacity: 0;
            }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .broadcast-content {
                font-size: 12px;
                gap: 8px;
            }

            .broadcast-icon {
                font-size: 14px;
            }
        }
    </style>
</body>
</html>
