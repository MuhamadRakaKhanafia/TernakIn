<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TernakIN - Dashboard')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <i class="fas fa-paw"></i>
                <h1>TernakIN</h1>
            </div>
            <ul class="menu">
                <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('diseases.*') ? 'active' : '' }}">
                    <a href="{{ route('diseases.index') }}">
                        <i class="fas fa-stethoscope"></i>
                        <span>Penyakit Hewan</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('symptoms.*') ? 'active' : '' }}">
                    <a href="{{ route('symptoms.index') }}">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Gejala</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('medicines.*') ? 'active' : '' }}">
                    <a href="{{ route('medicines.index') }}">
                        <i class="fas fa-pills"></i>
                        <span>Obat & Vaksin</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('preventions.*') ? 'active' : '' }}">
                    <a href="{{ route('preventions.index') }}">
                        <i class="fas fa-shield-alt"></i>
                        <span>Pencegahan</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('articles.*') ? 'active' : '' }}">
                    <a href="{{ route('articles.index') }}">
                        <i class="fas fa-book-medical"></i>
                        <span>Artikel</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('animal-types.*') ? 'active' : '' }}">
                    <a href="{{ route('animal-types.index') }}">
                        <i class="fas fa-dove"></i>
                        <span>Jenis Hewan</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('chat.index') ? 'active' : '' }}">
                    <a href="{{ route('chat.index') }}">
                        <i class="fas fa-robot"></i>
                        <span>AI Chat</span>
                    </a>
                </li>
                <li class="menu-item">
                    <i class="fas fa-search"></i>
                    <span>Pencarian</span>
                </li>
                <li class="menu-item">
                    <i class="fas fa-chart-bar"></i>
                    <span>Analitik</span>
                </li>
                <li class="menu-item">
                    <i class="fas fa-cog"></i>
                    <span>Pengaturan</span>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h2>@yield('page-title', 'Dashboard TernakIN')</h2>
                <div class="header-actions">
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-outline-primary">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    @else
                        <div class="user-info">
                            <div class="user-avatar">{{ substr(Auth::user()->name, 0, 2) }}</div>
                            <span>{{ Auth::user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </button>
                            </form>
                        </div>
                    @endguest
                </div>
            </div>

            @yield('content')
        </div>
    </div>

    <script src="{{ asset('js/script.js') }}"></script>
    @stack('scripts')
</body>
</html>
