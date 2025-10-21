@extends('layouts.app')

@section('title', 'TernakIN - Sistem Informasi Kesehatan Hewan Ternak')
@section('page-title', 'Selamat Datang di TernakIN')

@section('content')
<div class="welcome-container">
    <div class="welcome-hero">
        <h1>Selamat Datang di TernakIN</h1>
        <p>Sistem Informasi Kesehatan Hewan Ternak Terintegrasi</p>
        <p class="hero-description">
            TernakIN adalah platform komprehensif untuk memantau, mendiagnosis, dan mengelola kesehatan hewan ternak Anda.
            Dapatkan informasi lengkap tentang penyakit, gejala, obat, vaksin, dan tips pencegahan.
        </p>
        <div class="hero-actions">
            @guest
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-user-plus"></i> Daftar Sekarang
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-home"></i> Ke Dashboard
                </a>
            @endguest
        </div>
    </div>

    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-stethoscope"></i>
            </div>
            <h3>Diagnosa Penyakit</h3>
            <p>Identifikasi penyakit hewan ternak dengan cepat melalui gejala dan informasi lengkap.</p>
            <a href="{{ route('diseases.public.index') }}" class="feature-link">
                Lihat Penyakit <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-pills"></i>
            </div>
            <h3>Obat & Vaksin</h3>
            <p>Temukan informasi lengkap tentang obat-obatan dan vaksin untuk hewan ternak.</p>
            <a href="{{ route('medicines.public.index') }}" class="feature-link">
                Lihat Obat <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-book-medical"></i>
            </div>
            <h3>Artikel & Edukasi</h3>
            <p>Baca artikel terbaru tentang kesehatan hewan dan tips perawatan ternak.</p>
            <a href="{{ route('articles.public.index') }}" class="feature-link">
                Lihat Artikel <i class="fas fa-arrow-right"></i>
            </a>
        </div>

    <div class="stats-section">
        <h2>Statistik TernakIN</h2>
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number">142+</div>
                <div class="stat-label">Penyakit Teridentifikasi</div>
            </div>

            <div class="stat-item">
                <div class="stat-number">87+</div>
                <div class="stat-label">Obat & Vaksin</div>
            </div>

            <div class="stat-item">
                <div class="stat-number">56+</div>
                <div class="stat-label">Artikel Edukasi</div>
            </div>
            
            <div class="stat-item">
                <div class="stat-number">1000+</div>
                <div class="stat-label">Peternak Terbantu</div>
            </div>
        </div>
    </div>
</div>

<style>
.welcome-container {
    padding: 2rem;
}

.welcome-hero {
    text-align: center;
    margin-bottom: 3rem;
    padding: 3rem 0;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
    color: white;
    border-radius: 12px;
}

.welcome-hero h1 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    font-weight: 700;
}

.welcome-hero p {
    font-size: 1.2rem;
    margin-bottom: 1rem;
    opacity: 0.9;
}

.hero-description {
    max-width: 600px;
    margin: 0 auto 2rem;
    font-size: 1rem;
    line-height: 1.6;
}

.hero-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.feature-card {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: transform 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-5px);
}

.feature-icon {
    font-size: 3rem;
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.feature-card h3 {
    font-size: 1.5rem;
    margin-bottom: 1rem;
    color: var(--text-color);
}

.feature-card p {
    color: var(--text-light);
    margin-bottom: 1.5rem;
    line-height: 1.6;
}

.feature-link {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: color 0.3s ease;
}

.feature-link:hover {
    color: var(--primary-light);
}

.stats-section {
    text-align: center;
    margin-top: 3rem;
}

.stats-section h2 {
    font-size: 2rem;
    margin-bottom: 2rem;
    color: var(--text-color);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
}

.stat-item {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
}

.stat-label {
    color: var(--text-light);
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .welcome-hero h1 {
        font-size: 2rem;
    }

    .hero-actions {
        flex-direction: column;
        align-items: center;
    }

    .features-grid {
        grid-template-columns: 1fr;
    }

    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>
@endsection
