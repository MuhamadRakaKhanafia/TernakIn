@extends('layouts.app')

@section('title', 'TernakIN - Sistem Informasi Kesehatan Hewan Ternak')

@push('styles')
<style>
    /* CSS Khusus untuk Welcome Page */
    :root {
        --primary-color: #059669;
        --primary-light: #34d399;
        --primary-dark: #047857;
        --secondary-color: #4f46e5;
        --text-color: #1f2937;
        --text-light: #4b5563;
        --background-light: #f9fafb;
        --danger-high: #dc2626;
        --danger-medium: #f59e0b;
        --danger-low: #3b82f6;
    }

    .welcome-container {
        padding: 0;
    }

    .section-title {
        text-align: center;
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--text-color);
        margin-bottom: 1rem;
    }

    .section-subtitle {
        text-align: center;
        font-size: 1.2rem;
        color: var(--text-light);
        margin-bottom: 3rem;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    /* Button Styling */
    .btn {
        border-radius: 9999px;
        padding: 0.75rem 1.75rem;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 2px solid transparent;
    }

    .btn-lg {
        font-size: 1.125rem;
        padding: 1rem 2.25rem;
    }

    .me-2 { margin-right: 0.5rem; }

    .btn-primary {
        background-color: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
        box-shadow: 0 4px 10px rgba(5, 150, 105, 0.3);
    }

    .btn-primary:hover {
        background-color: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(5, 150, 105, 0.4);
    }

    .btn-outline-light {
        background: transparent;
        color: white;
        border-color: white;
    }

    .btn-outline-light:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: translateY(-2px);
        box-shadow: 0 0 15px rgba(255, 255, 255, 0.2);
    }

    /* Hero Slider Styles */
    .welcome-hero {
        position: relative;
        background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary-dark) 100%);
        color: white;
        overflow: hidden;
        min-height: 85vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .hero-slider {
        position: relative;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .slider-container {
        position: relative;
        height: 450px;
        overflow: hidden;
    }

    .slide {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 6rem;
        opacity: 0;
        transition: opacity 1s ease;
    }

    .slide.active {
        opacity: 1;
    }

    .slide-content {
        flex: 1;
        max-width: 500px;
        z-index: 2;
        padding: 1rem 0;
    }

    .slide-content h1 {
        font-size: 3rem;
        font-weight: 800;
        color: white;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .slide-content p {
        font-size: 1.4rem;
        margin-bottom: 1rem;
        opacity: 0.95;
        font-weight: 300;
    }

    .hero-description {
        font-size: 1.05rem !important;
        line-height: 1.7;
        opacity: 0.8 !important;
    }

    .slide-image {
        flex: 1;
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }

    .slide-image img {
        max-width: 550px;
        max-height: 350px;
        border-radius: 16px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
        object-fit: cover;
        border: 4px solid rgba(255, 255, 255, 0.2);
    }

    .slider-controls {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 2rem;
        padding: 2rem;
    }

    .slider-prev,
    .slider-next {
        background: rgba(255, 255, 255, 0.15);
        border: none;
        color: white;
        width: 55px;
        height: 55px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        backdrop-filter: blur(5px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .slider-prev:hover,
    .slider-next:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.05);
    }

    .dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.5);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .dot.active {
        background: white;
        transform: scale(1.4);
        box-shadow: 0 0 5px rgba(255, 255, 255, 0.8);
    }

    .hero-actions {
        display: flex;
        gap: 1.5rem;
        justify-content: center;
        padding: 3rem 2rem;
        background: rgba(0, 0, 0, 0.1);
    }

    /* Features Section */
    .features-section {
        padding: 6rem 2rem;
        background: var(--background-light);
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }

    .feature-card {
        background: white;
        padding: 2.5rem 2rem;
        border-radius: 16px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        text-align: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #e5e7eb;
    }

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .feature-icon {
        font-size: 2.5rem;
        margin: 0 auto 1.5rem;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.15);
    }

    .feature-icon-green {
        background: var(--primary-color);
    }
    .feature-icon-indigo {
        background: var(--secondary-color);
    }
    .feature-icon-orange {
        background: var(--danger-medium);
    }

    .feature-card h3 {
        font-size: 1.6rem;
        margin-bottom: 0.75rem;
        color: var(--text-color);
    }

    .feature-card p {
        color: var(--text-light);
        margin-bottom: 1.5rem;
    }

    .feature-link {
        color: var(--primary-color);
        font-weight: 700;
    }

    .feature-link:hover {
        color: var(--primary-dark);
        text-decoration: underline;
    }

    /* Dangers Section */
    .dangers-section {
        padding: 6rem 2rem;
        background: #f5f5f5;
    }

    .dangers-header {
        text-align: center;
        margin-bottom: 3rem;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
    }

    .dangers-header h2 {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--danger-high);
        margin-bottom: 0.5rem;
    }

    .dangers-header p {
        font-size: 1.2rem;
        color: var(--text-light);
    }

    .dangers-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        max-width: 1000px;
        margin: 0 auto;
    }

    .danger-card {
        text-align: center;
        padding: 2.5rem 1.5rem;
        border-radius: 16px;
        background: white;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border: 3px solid transparent;
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        border-top: 8px solid transparent;
    }

    .danger-card:nth-child(1) { border-top-color: var(--danger-high); }
    .danger-card:nth-child(2) { border-top-color: var(--danger-medium); }
    .danger-card:nth-child(3) { border-top-color: var(--danger-low); }

    .danger-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
    }

    .danger-icon {
        width: 65px;
        height: 65px;
        border-radius: 50%;
        margin: 0 auto 1.5rem;
        font-size: 2rem;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
    }

    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(220, 38, 38, 0); }
        100% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0); }
    }

    .danger-high {
        background: var(--danger-high);
        animation: pulse 2s infinite ease-out;
    }

    .danger-medium {
        background: var(--danger-medium);
        background-color: #f97316;
    }

    .danger-low {
        background: var(--danger-low);
    }

    .danger-card h4 {
        font-size: 1.5rem;
        font-weight: 800;
        margin-bottom: 0.75rem;
    }

    .danger-card p {
        color: var(--text-light);
    }

    /* Stats Section */
    .stats-section {
        padding: 6rem 2rem;
        background: linear-gradient(to right, #4c51bf, #667eea);
        color: white;
    }

    .stats-section .section-title {
        color: white;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 2rem;
        max-width: 1100px;
        margin: 0 auto;
    }

    .stat-item {
        text-align: center;
        padding: 2rem;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 16px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .stat-number {
        font-size: 4rem;
        font-weight: 800;
        margin-bottom: 0.25rem;
        line-height: 1;
        color: #fff;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .stat-label {
        font-size: 1.1rem;
        font-weight: 500;
        opacity: 0.95;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* CTA Section */
    .cta-section {
        padding: 5rem 2rem;
        background: var(--text-color);
        color: white;
        text-align: center;
        background: linear-gradient(135deg, var(--text-color) 0%, #374151 100%);
    }

    .cta-content h2 {
        font-size: 3rem;
        font-weight: 800;
        color: white;
    }

    .cta-content p {
        font-size: 1.3rem;
        margin-bottom: 2.5rem;
        opacity: 0.9;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .slide {
            padding: 0 3rem;
        }
        .slide-image img {
            max-width: 400px;
            max-height: 280px;
        }
    }

    @media (max-width: 768px) {
        .slide {
            flex-direction: column;
            text-align: center;
            padding: 2rem;
            justify-content: center;
        }
        
        .slide-content {
            max-width: 100%;
            margin-bottom: 1.5rem;
        }
        
        .slide-content h1 {
            font-size: 2.5rem;
        }
        
        .slide-image {
            justify-content: center;
            margin-bottom: 1rem;
        }
        
        .slide-image img {
            max-width: 90%;
            max-height: 250px;
        }
        
        .slider-container {
            height: auto;
            min-height: 550px;
        }

        .dangers-header h2 {
            font-size: 2rem;
        }
        
        .section-title, .cta-content h2 {
            font-size: 2rem;
        }
        
        .stats-grid {
            grid-template-columns: 1fr 1fr;
        }
        
        .hero-actions,
        .cta-actions {
            flex-direction: column;
            align-items: center;
        }
        
        .btn-lg {
            width: 100%;
            max-width: 300px;
        }
    }

    @media (max-width: 480px) {
        .welcome-hero {
            min-height: 60vh;
        }
        
        .slider-container {
            min-height: 480px;
        }
        
        .slide-content h1 {
            font-size: 2rem;
        }
        
        .slide-content p {
            font-size: 1.1rem;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .stat-number {
            font-size: 3rem;
        }
        
        .stat-label {
            font-size: 0.9rem;
        }
        
        .hero-actions, .cta-actions {
            gap: 0.75rem;
        }
    }
</style>
@endpush

@section('page-title')

@section('content')
<div class="welcome-container">
    <!-- Hero Section dengan Slider -->
    <div class="welcome-hero">
        <div class="hero-slider">
            <div class="slider-container">
                <!-- Slide 1 -->
                <div class="slide active">
                    <div class="slide-content">
                        <h1>Selamat Datang di TernakIN</h1>
                        <p>Sistem Informasi Kesehatan Hewan Ternak Terintegrasi</p>
                        <p class="hero-description">
                            Kelola kesehatan ternak Anda dengan mudah dan efisien. Pantau, diagnosa, dan cegah penyakit dengan sistem yang terpercaya.
                        </p>
                    </div>
                    <div class="slide-image">
                        <img src="images/Domba.jpg" alt="Peternakan Modern">
                    </div>
                </div>
                
                <!-- Slide 2 -->
                <div class="slide">
                    <div class="slide-content">
                        <h1>Kelola Data Hewan Ternak</h1>
                        <p>Sistem Pencatatan yang Terorganisir</p>
                        <p class="hero-description">
                            Input dan kelola data hewan ternak dengan sistem yang mudah digunakan. Pantau perkembangan kesehatan setiap hewan.
                        </p>
                    </div>
                    <div class="slide-image">
                        <img src="images/Ayam.jpg" alt="Kelola Data Ternak">
                    </div>
                </div>
                
                <!-- Slide 3 -->
                <div class="slide">
                    <div class="slide-content">
                        <h1>Waspada Penyakit Hewan</h1>
                        <p>Deteksi Dini dan Pencegahan</p>
                        <p class="hero-description">
                            Kenali gejala penyakit berbahaya pada hewan ternak. Dapatkan informasi lengkap tentang pencegahan dan penanganan.
                        </p>
                    </div>
                    <div class="slide-image">
                         <img src="images/Sapi.jpg" alt="Kelola Data Ternak">
                    </div>
                </div>
            </div>
             <!-- Slide 4 -->
                <div class="slide">
                    <div class="slide-content">
                        <h1>TernakIn</h1>
                        <p>Aplikasi Yang Menginovasi Para Peternak</p>
                        <p class="hero-description">
                            TernakIn hadir untuk memudahkan peternak dalam mengelola kesehatan hewan ternak mereka dengan teknologi terkini, dan juga memberikan informasi penting mengenai penyakit hewan ternak.
                        </p>
                    </div>
                    <div class="slide-image">
                         <img src="images/Kambing.jpg" alt="Kami di TernakIn">
                    </div>
                </div>
            </div>

            <div class="slider-controls">
                <button class="slider-prev">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="slider-dots">
                    <span class="dot active" data-slide="0"></span>
                    <span class="dot" data-slide="1"></span>
                    <span class="dot" data-slide="2"></span>
                </div>
                <button class="slider-next">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
        
        <!-- Hero Actions -->
        <div class="hero-actions">
            @auth
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard Admin
                    </a>
                    <a href="{{ route('livestocks.index') }}" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-cow me-2"></i> Kelola Hewan Ternak
                    </a>
                @else
                    <a href="{{ route('livestocks.index') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-cow me-2"></i> Kelola Hewan Ternak
                    </a>
                    <a href="{{ route('livestocks.create') }}" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-plus me-2"></i> Tambah Hewan Ternak
                    </a>
                @endif
            @else
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-user-plus me-2"></i> Daftar Sekarang
                </a>
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-sign-in-alt me-2"></i> Masuk
                </a>
            @endauth
        </div>
    </div>

    <!-- Fitur Utama -->
    <div class="features-section">
        <h2 class="section-title">Mengapa Memilih TernakIN?</h2>
        <p class="section-subtitle">Solusi lengkap untuk manajemen kesehatan hewan ternak Anda</p>
        
        <!-- Features Grid -->
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon feature-icon-green">
                    <i class="fas fa-paw"></i>
                </div>
                <h3>Manajemen Data Ternak</h3>
                <p>Kelola data hewan ternak secara terpusat. Catat riwayat kesehatan, vaksinasi, dan perkembangan setiap hewan dengan sistem yang terorganisir.</p>
                @auth
                    <a href="{{ route('livestocks.index') }}" class="feature-link">
                        Kelola Ternak <i class="fas fa-arrow-right"></i>
                    </a>
                @else
                    <a href="{{ route('register') }}" class="feature-link">
                        Mulai Sekarang <i class="fas fa-arrow-right"></i>
                    </a>
                @endauth
            </div>
            
            <div class="feature-card">
                <div class="feature-icon feature-icon-indigo">
                    <i class="fas fa-stethoscope"></i>
                </div>
                <h3>Deteksi Penyakit</h3>
                <p>Identifikasi gejala penyakit secara dini. Akses database lengkap penyakit hewan ternak beserta cara penanganan yang tepat.</p>
                <a href="{{ route('diseases.index') }}" class="feature-link">
                    Pelajari Penyakit <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon feature-icon-orange">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>Analisis & Laporan</h3>
                <p>Dapatkan insight tentang kesehatan ternak melalui dashboard analitik. Buat laporan kesehatan secara otomatis.</p>
                @auth
                    <a href="{{ route('analytics.index') }}" class="feature-link">
                        Lihat Analitik <i class="fas fa-arrow-right"></i>
                    </a>
                @else
                    <a href="{{ route('register') }}" class="feature-link">
                        Lihat Demo <i class="fas fa-arrow-right"></i>
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Bahaya Penyakit Ternak -->
    <div class="dangers-section">
        <div class="dangers-header">
            <h2>Waspadai Bahaya Penyakit Hewan Ternak</h2>
            <p>Penyakit ternak tidak hanya merugikan secara ekonomi, tetapi juga dapat membahayakan kesehatan manusia</p>
        </div>
        
        <div class="dangers-grid">
            <div class="danger-card">
                <div class="danger-icon danger-high">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h4>Zoonosis (Bahaya Tinggi)</h4>
                <p>Penyakit yang dapat menular dari hewan ke manusia seperti Anthrax, Rabies, dan Flu Burung. Perlu penanganan cepat dan serius.</p>
            </div>
            
            <div class="danger-card">
                <div class="danger-icon danger-medium">
                    <i class="fas fa-skull-crossbones"></i>
                </div>
                <h4>Wabah Penyakit (Bahaya Sedang)</h4>
                <p>Penyakit menular dengan penyebaran cepat seperti PMK (Penyakit Mulut dan Kuku) yang merugikan peternak secara masif.</p>
            </div>
            
            <div class="danger-card">
                <div class="danger-icon danger-low">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <h4>Kerugian Ekonomi (Bahaya Lain)</h4>
                <p>Kematian hewan ternak dan penurunan produktivitas menyebabkan kerugian finansial yang signifikan bagi bisnis peternakan Anda.</p>
            </div>
        </div>
    </div>

    <!-- Statistik -->
    <div class="stats-section">
        <h2 class="section-title">TernakIN dalam Angka</h2>
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number" data-target="{{ $totalLivestock }}">0</div>
                <div class="stat-label">Hewan Ternak Terdaftar</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" data-target="{{ $totalDiseases }}">0</div>
                <div class="stat-label">Penyakit Terdata</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" data-target="{{ $totalUsers }}">0</div>
                <div class="stat-label">Peternak Bergabung</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" data-target="{{ $healthyPercentage }}">0</div>
                <div class="stat-label">Tingkat Kesehatan (%)</div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="cta-section">
        <div class="cta-content">
            @auth
                @if(Auth::user()->isAdmin())
            <h2>Kelola Sistem TernakIN</h2>
            <p>Akses dashboard admin untuk mengelola pengguna, vaksinasi, dan data sistem</p>
            <div class="cta-actions">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard Admin
                </a>
            </div>
                @else
            <h2>Siap Mengelola Ternak dengan Lebih Baik?</h2>
            <p>Tambahkan hewan ternak baru atau lihat analitik kesehatan ternak Anda</p>
            <div class="cta-actions">
                <a href="{{ route('livestocks.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i> Tambah Hewan Ternak
                </a>
            </div>
                @endif
            @else
            <h2>Siap Mengelola Ternak dengan Lebih Baik?</h2>
            <p>Bergabunglah dengan ratusan peternak yang telah mempercayakan manajemen kesehatan ternak mereka kepada TernakIN</p>
            <div class="cta-actions">
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-user-plus me-2"></i> Mulai Sekarang
                </a>
            </div>
            @endauth
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Slider Logic
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.dot');
        const prevBtn = document.querySelector('.slider-prev');
        const nextBtn = document.querySelector('.slider-next');
        let currentSlide = 0;
        let slideInterval;

        function showSlide(n) {
            slides.forEach(slide => slide.style.transition = 'opacity 1s ease');
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));
            
            currentSlide = (n + slides.length) % slides.length;
            slides[currentSlide].classList.add('active');
            dots[currentSlide].classList.add('active');
        }

        function nextSlide() {
            showSlide(currentSlide + 1);
        }

        function prevSlide() {
            showSlide(currentSlide - 1);
        }

        function startSlider() {
            stopSlider();
            slideInterval = setInterval(nextSlide, 5000);
        }

        function stopSlider() {
            clearInterval(slideInterval);
        }

        if(slides.length > 0) {
            showSlide(0);
            startSlider();
        }

        // Event listeners
        nextBtn.addEventListener('click', (e) => {
            e.preventDefault();
            stopSlider();
            nextSlide();
            startSlider();
        });

        prevBtn.addEventListener('click', (e) => {
            e.preventDefault();
            stopSlider();
            prevSlide();
            startSlider();
        });

        dots.forEach(dot => {
            dot.addEventListener('click', function(e) {
                e.preventDefault();
                stopSlider();
                showSlide(parseInt(this.dataset.slide));
                startSlider();
            });
        });

        // Pause slider on hover
        const slider = document.querySelector('.hero-slider');
        if (slider) {
            slider.addEventListener('mouseenter', stopSlider);
            slider.addEventListener('mouseleave', startSlider);
        }

        // Animated counter for stats
        const statNumbers = document.querySelectorAll('.stat-number');
        
        function animateValue(element, start, end, duration) {
            if (element.animated) return; 
            element.animated = true;
            
            let startTimestamp = null;
            const labelText = element.parentElement.querySelector('.stat-label').textContent;
            const isPercentage = labelText.includes('(') && labelText.includes(')'); 
            const suffix = labelText.includes('(%)') ? '%' : '';

            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                let value = Math.floor(progress * (end - start) + start);

                if (isPercentage) {
                     element.textContent = `${value}${suffix}`;
                } else {
                     element.textContent = value.toLocaleString('id-ID');
                }
                
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }

        // Intersection Observer for stats animation
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    statNumbers.forEach(stat => {
                        const target = parseInt(stat.dataset.target) || 0; 
                        animateValue(stat, 0, target, 2500);
                    });
                    statsObserver.disconnect();
                }
            });
        }, { threshold: 0.4 });

        const statsSection = document.querySelector('.stats-section');
        if (statsSection) {
            statsObserver.observe(statsSection);
        }
    });
</script>
@endpush