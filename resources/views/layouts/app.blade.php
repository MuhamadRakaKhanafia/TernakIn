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
                        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:bg-green-50 hover:text-green-600 px-3 py-2 rounded-lg text-sm font-medium transition duration-150 ease-in-out flex items-center">
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
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:bg-green-50 hover:text-green-600 px-3 py-2 rounded-lg text-sm font-medium transition duration-150 ease-in-out flex items-center">
                            <i class="fas fa-robot mr-2"></i> AI Chat
                        </a>
                    @endauth
                    @auth
                        <a href="{{ route('analytics.index') }}" class="text-gray-600 hover:bg-green-50 hover:text-green-600 px-3 py-2 rounded-lg text-sm font-medium transition duration-150 ease-in-out flex items-center">
                            <i class="fas fa-chart-bar mr-2"></i> Analitik
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:bg-green-50 hover:text-green-600 px-3 py-2 rounded-lg text-sm font-medium transition duration-150 ease-in-out flex items-center">
                            <i class="fas fa-chart-bar mr-2"></i> Analitik
                        </a>
                    @endauth
                    @auth
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-red-600 text-white px-3 py-2 rounded-lg text-sm font-medium shadow-md hover:bg-red-700 transition duration-150 ease-in-out flex items-center">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>
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
                        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:bg-green-50 hover:text-green-600 block px-3 py-2 rounded-md text-base font-medium transition duration-150 ease-in-out flex items-center">
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
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:bg-green-50 hover:text-green-600 block px-3 py-2 rounded-md text-base font-medium transition duration-150 ease-in-out flex items-center">
                            <i class="fas fa-robot mr-2"></i> AI Chat
                        </a>
                    @endauth
                    @auth
                        <a href="{{ route('analytics.index') }}" class="text-gray-600 hover:bg-green-50 hover:text-green-600 block px-3 py-2 rounded-md text-base font-medium transition duration-150 ease-in-out flex items-center">
                            <i class="fas fa-chart-bar mr-2"></i> Analitik
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:bg-green-50 hover:text-green-600 block px-3 py-2 rounded-md text-base font-medium transition duration-150 ease-in-out flex items-center">
                            <i class="fas fa-chart-bar mr-2"></i> Analitik
                        </a>
                    @endauth
                    @auth
                        <form method="POST" action="{{ route('logout') }}" class="block">
                            @csrf
                            <button type="submit" class="w-full text-left text-red-600 hover:bg-red-50 hover:text-red-700 block px-3 py-2 rounded-md text-base font-medium transition duration-150 ease-in-out flex items-center">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>
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
        });
    </script>

    <script src="{{ asset('js/script.js') }}"></script>
    @stack('scripts')
</body>
</html>