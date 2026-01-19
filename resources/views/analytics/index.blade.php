@extends('layouts.app')

@section('title', 'Analytics - TernakIN')
@section('page-title', 'Analytics')

@section('content')
<div class="container-fluid">
    <!-- Flash Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Header -->
    <div class="content-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Analytics Semua User</h1>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-back">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
            </a>
        </div>
    </div>
    <!-- Statistik Utama - Horizontal Layout dengan Spacing -->
    <div class="row justify-content-center mb-4">
        <div class="col-xxl-8 col-xl-10">
            <div class="stats-row-horizontal">
                <div class="stat-item-horizontal">
                    <div class="stat-icon-horizontal bg-primary">
                        <i class="fas fa-paw"></i>
                    </div>
                    <div class="stat-info-horizontal">
                        <h3>{{ $totalLivestock }}</h3>
                        <p>Total Hewan Ternak</p>
                    </div>
                </div>
                <div class="stat-item-horizontal">
                    <div class="stat-icon-horizontal bg-success">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <div class="stat-info-horizontal">
                        <h3>{{ $healthyLivestock }}</h3>
                        <p>Hewan Sehat</p>
                    </div>
                </div>
                <div class="stat-item-horizontal">
                    <div class="stat-icon-horizontal bg-warning">
                        <i class="fas fa-syringe"></i>
                    </div>
                    <div class="stat-info-horizontal">
                        <h3>{{ $needVaccination }}</h3>
                        <p>Perlu Vaksinasi</p>
                    </div>
                </div>
                <div class="stat-item-horizontal">
                    <div class="stat-icon-horizontal bg-danger">
                        <i class="fas fa-bug"></i>
                    </div>
                    <div class="stat-info-horizontal">
                        <h3>{{ $totalDiseases }}</h3>
                        <p>Jenis Penyakit</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 - Lebih Kecil dan Rapih -->
    <div class="row justify-content-center mb-4">
        <div class="col-xxl-8 col-xl-10">
            <div class="row">
                <!-- Distribusi Jenis Hewan -->
                <div class="col-xl-6 col-lg-12 mb-4">
                    <div class="chart-card-sm">
                        <div class="chart-header-sm">
                            <h5><i class="fas fa-chart-pie me-2"></i>Jenis Hewan Ternak</h5>
                        </div>
                        <div class="chart-container-sm">
                            <canvas id="animalTypeChart" height="220"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Status Kesehatan -->
                <div class="col-xl-6 col-lg-12 mb-4">
                    <div class="chart-card-sm">
                        <div class="chart-header-sm">
                            <h5><i class="fas fa-heartbeat me-2"></i>Status Kesehatan</h5>
                        </div>
                        <div class="chart-container-sm">
                            <canvas id="healthStatusChart" height="220"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 - Lebih Kecil dan Rapih -->
    <div class="row justify-content-center mb-4">
        <div class="col-xxl-8 col-xl-10">
            <div class="row">
                <!-- Status Vaksinasi -->
                <div class="col-xl-6 col-lg-12 mb-4">
                    <div class="chart-card-sm">
                        <div class="chart-header-sm">
                            <h5><i class="fas fa-syringe me-2"></i>Status Vaksinasi</h5>
                        </div>
                        <div class="chart-container-sm">
                            <canvas id="vaccinationChart" height="220"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Penyakit Terbanyak -->
                <div class="col-xl-6 col-lg-12 mb-4">
                    <div class="chart-card-sm">
                        <div class="chart-header-sm">
                            <h5><i class="fas fa-viruses me-2"></i>Penyakit Terbanyak</h5>
                        </div>
                        <div class="chart-container-sm">
                            <canvas id="diseasesChart" height="220"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 3 - Lebih Kecil dan Rapih -->
    <div class="row justify-content-center mb-4">
        <div class="col-xxl-8 col-xl-10">
            <div class="row">
                <!-- Trend Bulanan -->
                <div class="col-xl-8 col-lg-12 mb-4">
                    <div class="chart-card-sm">
                        <div class="chart-header-sm">
                            <h5><i class="fas fa-chart-line me-2"></i>Trend Kesehatan Bulanan</h5>
                        </div>
                        <div class="chart-container-sm">
                            <canvas id="monthlyTrendChart" height="240"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Jenis Kelamin -->
                <div class="col-xl-4 col-lg-12 mb-4">
                    <div class="chart-card-sm">
                        <div class="chart-header-sm">
                            <h5><i class="fas fa-venus-mars me-2"></i>Jenis Kelamin</h5>
                        </div>
                        <div class="chart-container-sm">
                            <canvas id="genderChart" height="240"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables - Center -->
    <div class="row justify-content-center">
        <div class="col-xxl-8 col-xl-10">
            <div class="row">
                <!-- Hewan Sakit -->
                <div class="col-xl-6 col-lg-12 mb-4">
                    <div class="data-card-sm">
                        <div class="card-header-sm">
                            <h5><i class="fas fa-exclamation-triangle me-2"></i>Hewan Sakit</h5>
                        </div>
                        <div class="card-body-sm">
                            <div class="table-responsive-sm">
                                <table class="table table-hover table-sm table-borderless">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nama</th>
                                            <th>Jenis</th>
                                            <th>Status</th>
                                            <th>Umur</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($sickLivestocks as $livestock)
                                        <tr class="table-danger">
                                            <td><strong>{{ $livestock->name ?: 'Tidak ada nama' }}</strong></td>
                                            <td>{{ $livestock->animalType->name ?? '-' }}</td>
                                            <td><span class="badge bg-danger">Sakit</span></td>
                                            <td>
                                                @if($livestock->animalType && $livestock->animalType->category === 'poultry')
                                                    {{ $livestock->age_weeks ? $livestock->age_weeks . ' minggu' : '-' }}
                                                @else
                                                    {{ $livestock->age_months ? $livestock->age_months . ' bulan' : '-' }}
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-3">
                                                <i class="fas fa-check-circle fa-lg mb-2 text-success"></i>
                                                <br><small>Tidak ada hewan yang sakit</small>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Perlu Vaksinasi -->
                <div class="col-xl-6 col-lg-12 mb-4">
                    <div class="data-card-sm">
                        <div class="card-header-sm">
                            <h5><i class="fas fa-clock me-2"></i>Perlu Vaksinasi</h5>
                        </div>
                        <div class="card-body-sm">
                            <div class="table-responsive-sm">
                                <table class="table table-hover table-sm table-borderless">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nama</th>
                                            <th>Jenis</th>
                                            <th>Status</th>
                                            <th>Terakhir Vaksin</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($needVaccinationLivestocks as $livestock)
                                        <tr class="table-warning">
                                            <td><strong>{{ $livestock->name ?: 'Tidak ada nama' }}</strong></td>
                                            <td>{{ $livestock->animalType->name ?? '-' }}</td>
                                            <td><span class="badge bg-warning text-dark">Perlu Vaksin</span></td>
                                            <td>{{ $livestock->last_vaccination_date ? \Carbon\Carbon::parse($livestock->last_vaccination_date)->format('d M Y') : 'Belum pernah' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-3">
                                                <i class="fas fa-check-circle fa-lg mb-2 text-success"></i>
                                                <br><small>Semua hewan sudah divaksin</small>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
:root {
    --primary-color: #10b981;
    --primary-dark: #059669;
    --primary-light: #34d399;
    --secondary-color: #3b82f6;
    --accent-color: #8b5cf6;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --info-color: #06b6d4;
    
    --gradient-primary: linear-gradient(135deg, #10b981 0%, #059669 100%);
    --gradient-secondary: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    --gradient-accent: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    --gradient-warning: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    --gradient-danger: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    
    --text-dark: #1f2937;
    --text-medium: #4b5563;
    --text-light: #6b7280;
    --background-light: #f8fafc;
    --background-white: #ffffff;
    --border-light: #e5e7eb;
    --border-medium: #d1d5db;
    
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    --shadow-glow: 0 0 20px rgba(16, 185, 129, 0.15);
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


/* Container & Layout */
.container-fluid {
    padding: 1.5rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    min-height: 100vh;
}

/* Flash Messages Modern */
.alert {
    border: none;
    border-radius: 12px;
    padding: 1rem 1.5rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow-md);
    border-left: 4px solid transparent;
    backdrop-filter: blur(10px);
}

.alert-success {
    background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
    border-left-color: var(--primary-color);
    color: #065f46;
}

.alert .btn-close {
    padding: 0.75rem;
}

/* ===========================================
   HORIZONTAL STATS ROW - MODERN DESIGN
   =========================================== */

.stats-row-horizontal {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
    background: var(--background-white);
    border-radius: 20px;
    padding: 2rem;
    box-shadow: var(--shadow-xl);
    border: 1px solid var(--border-light);
    backdrop-filter: blur(10px);
    position: relative;
    overflow: hidden;
}

.stats-row-horizontal::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--gradient-primary);
}

.stat-item-horizontal {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    padding: 1rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 16px;
    position: relative;
    overflow: hidden;
}

.stat-item-horizontal::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.stat-item-horizontal:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
    background: var(--background-light);
}

.stat-item-horizontal:hover::before {
    opacity: 1;
}

.stat-icon-horizontal {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    flex-shrink: 0;
    box-shadow: var(--shadow-lg);
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

/* PERBAIKAN: Pastikan icon Font Awesome terlihat */
.stat-icon-horizontal i {
    position: relative;
    z-index: 2;
    font-size: 1.3rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.stat-icon-horizontal::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: inherit;
    border-radius: inherit;
    z-index: 1;
}

.stat-icon-horizontal::after {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, rgba(255, 255, 255, 0) 70%);
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 1;
}

.stat-item-horizontal:hover .stat-icon-horizontal {
    transform: scale(1.1) rotate(5deg);
}

.stat-item-horizontal:hover .stat-icon-horizontal::after {
    opacity: 1;
}

.stat-info-horizontal {
    flex: 1;
    min-width: 0;
}

.stat-info-horizontal h3 {
    font-size: 2rem;
    font-weight: 800;
    margin: 0 0 0.5rem 0;
    color: var(--text-dark);
    line-height: 1;
    letter-spacing: -0.025em;
    background: linear-gradient(135deg, var(--text-dark), var(--text-medium));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stat-info-horizontal p {
    margin: 0;
    color: var(--text-light);
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Background colors untuk stat icons - PERBAIKAN: Pastikan warna terlihat */
.bg-primary { background: var(--gradient-secondary) !important; }
.bg-success { background: var(--gradient-primary) !important; }
.bg-warning { background: var(--gradient-warning) !important; }
.bg-danger { background: var(--gradient-danger) !important; }

/* ===========================================
   CHART CARDS - MODERN DESIGN
   =========================================== */

.chart-card-sm, .data-card-sm {
    background: var(--background-white);
    border-radius: 20px;
    box-shadow: var(--shadow-lg);
    margin-bottom: 1.5rem;
    height: 100%;
    border: 1px solid var(--border-light);
    backdrop-filter: blur(10px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.chart-card-sm::before, .data-card-sm::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--gradient-primary);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.chart-card-sm:hover, .data-card-sm:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-xl);
}

.chart-card-sm:hover::before, .data-card-sm:hover::before {
    transform: scaleX(1);
}

.chart-header-sm, .card-header-sm {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--border-light);
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    position: relative;
}

.chart-header-sm h5, .card-header-sm h5 {
    margin: 0;
    font-size: 1rem;
    font-weight: 700;
    color: var(--text-dark);
    letter-spacing: -0.025em;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* PERBAIKAN: Icon di header chart cards */
.chart-header-sm h5 i, .card-header-sm h5 i {
    color: var(--primary-color);
    font-size: 1.1rem;
    width: 24px;
    height: 24px;
    display: flex !important; /* Pastikan icon terlihat */
    align-items: center;
    justify-content: center;
    background: rgba(16, 185, 129, 0.1);
    border-radius: 8px;
    padding: 0.25rem;
}

.chart-container-sm {
    padding: 1.5rem;
    position: relative;
}

.card-body-sm {
    padding: 0;
}

/* ===========================================
   TABLES - MODERN DESIGN
   =========================================== */

.table-responsive-sm {
    max-height: 300px;
    overflow-y: auto;
    border-radius: 0 0 20px 20px;
}

.table {
    margin-bottom: 0;
    font-family: 'Inter', sans-serif;
}

.table th {
    font-weight: 700;
    font-size: 0.75rem;
    text-transform: uppercase;
    color: var(--text-light);
    border-bottom: 2px solid var(--border-light);
    background-color: #f8fafc;
    padding: 1rem 1.25rem;
    letter-spacing: 0.5px;
    position: sticky;
    top: 0;
    z-index: 10;
}

.table td {
    font-size: 0.85rem;
    vertical-align: middle;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #f8fafc;
    color: var(--text-medium);
    font-weight: 500;
}

.table-borderless td, .table-borderless th {
    border: none;
}

.table-hover tbody tr {
    transition: all 0.2s ease;
}

.table-hover tbody tr:hover {
    background-color: #f8fafc;
    transform: translateX(4px);
    box-shadow: var(--shadow-sm);
}

/* Table Row States */
.table-danger {
    background: linear-gradient(135deg, #fef2f2 0%, #fef2f2 100%);
    border-left: 4px solid var(--danger-color);
}

.table-warning {
    background: linear-gradient(135deg, #fffbeb 0%, #fffbeb 100%);
    border-left: 4px solid var(--warning-color);
}

/* Badges Modern */
.badge {
    font-size: 0.7rem;
    font-weight: 700;
    padding: 0.4em 0.8em;
    border-radius: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: 1px solid transparent;
}

.bg-danger {
    background: var(--gradient-danger) !important;
    color: white;
    border-color: rgba(220, 38, 38, 0.2);
}

.bg-warning {
    background: var(--gradient-warning) !important;
    color: white;
    border-color: rgba(217, 119, 6, 0.2);
}

.bg-success {
    background: var(--gradient-primary) !important;
    color: white;
    border-color: rgba(5, 150, 105, 0.2);
}

/* ===========================================
   ANIMATIONS & EFFECTS
   =========================================== */

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.stats-row-horizontal {
    animation: fadeInUp 0.6s ease-out;
}

.chart-card-sm, .data-card-sm {
    animation: slideInLeft 0.5s ease-out;
}

/* Stagger animation for chart cards */
.chart-card-sm:nth-child(1) { animation-delay: 0.1s; }
.chart-card-sm:nth-child(2) { animation-delay: 0.2s; }
.chart-card-sm:nth-child(3) { animation-delay: 0.3s; }
.chart-card-sm:nth-child(4) { animation-delay: 0.4s; }

/* ===========================================
   CUSTOM SCROLLBARS
   =========================================== */

.table-responsive-sm::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

.table-responsive-sm::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.table-responsive-sm::-webkit-scrollbar-thumb {
    background: var(--gradient-primary);
    border-radius: 3px;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.table-responsive-sm::-webkit-scrollbar-thumb:hover {
    background: var(--primary-dark);
}

/* ===========================================
   RESPONSIVE DESIGN
   =========================================== */

@media (max-width: 1200px) {
    .stats-row-horizontal {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.25rem;
        padding: 1.75rem;
    }
    
    .stat-item-horizontal {
        gap: 1rem;
    }
    
    .stat-icon-horizontal {
        width: 55px;
        height: 55px;
        font-size: 1.3rem;
    }
    
    .stat-info-horizontal h3 {
        font-size: 1.8rem;
    }
}

@media (max-width: 992px) {
    .container-fluid {
        padding: 1rem;
    }
    
    .stats-row-horizontal {
        padding: 1.5rem;
    }
    
    .chart-header-sm, .card-header-sm {
        padding: 1rem 1.25rem;
    }
    
    .chart-container-sm {
        padding: 1.25rem;
    }
    
    .table th,
    .table td {
        padding: 0.875rem 1rem;
    }
}

@media (max-width: 768px) {
    .stats-row-horizontal {
        grid-template-columns: 1fr;
        gap: 1rem;
        padding: 1.25rem;
    }
    
    .stat-item-horizontal {
        justify-content: flex-start;
        text-align: left;
        padding: 1rem;
    }
    
    .stat-icon-horizontal {
        width: 50px;
        height: 50px;
        font-size: 1.2rem;
    }
    
    .stat-info-horizontal h3 {
        font-size: 1.6rem;
    }
    
    .stat-info-horizontal p {
        font-size: 0.85rem;
    }

    .chart-container-sm {
        padding: 1rem;
    }

    .table th,
    .table td {
        padding: 0.75rem 0.875rem;
        font-size: 0.8rem;
    }
}

@media (max-width: 576px) {
    .container-fluid {
        padding: 0.75rem;
    }
    
    .stats-row-horizontal {
        padding: 1rem;
        border-radius: 16px;
    }
    
    .stat-item-horizontal {
        gap: 0.875rem;
        padding: 0.875rem;
    }
    
    .stat-icon-horizontal {
        width: 45px;
        height: 45px;
        font-size: 1.1rem;
    }
    
    .stat-info-horizontal h3 {
        font-size: 1.4rem;
    }
    
    .stat-info-horizontal p {
        font-size: 0.8rem;
    }

    .chart-card-sm, .data-card-sm {
        border-radius: 16px;
    }
    
    .chart-header-sm, .card-header-sm {
        padding: 0.875rem 1rem;
    }
    
    .chart-container-sm {
        padding: 0.875rem;
    }
    
    .table th,
    .table td {
        padding: 0.625rem 0.75rem;
        font-size: 0.75rem;
    }
    
    .badge {
        font-size: 0.65rem;
        padding: 0.3em 0.6em;
    }
}

/* ===========================================
   UTILITY CLASSES
   =========================================== */

.mb-4 {
    margin-bottom: 2rem !important;
}

.justify-content-center .row {
    justify-content: center;
}

/* Empty state styling */
.text-muted {
    color: var(--text-light) !important;
}

.text-center .fas {
    opacity: 0.5;
}

/* Smooth transitions */
.stats-row-horizontal,
.chart-card-sm,
.data-card-sm,
.stat-item-horizontal,
.table-hover tbody tr {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Focus states for accessibility */
.stat-item-horizontal:focus-within {
    outline: 2px solid var(--primary-color);
    outline-offset: 2px;
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .chart-card-sm,
    .data-card-sm,
    .stats-row-horizontal {
        border: 2px solid var(--text-dark);
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .stats-row-horizontal,
    .chart-card-sm,
    .data-card-sm,
    .stat-item-horizontal,
    .table-hover tbody tr {
        transition: none;
        animation: none;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data dari controller
    const analyticsData = @json($analyticsData);

    // Common chart options for smaller charts
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: {
                    font: {
                        family: "'Inter', sans-serif",
                        size: 10,
                        weight: '500'
                    },
                    padding: 10,
                    usePointStyle: true,
                    boxWidth: 8
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleFont: {
                    family: "'Inter', sans-serif",
                    size: 10,
                    weight: '600'
                },
                bodyFont: {
                    family: "'Inter', sans-serif",
                    size: 9
                },
                padding: 8,
                cornerRadius: 4,
                displayColors: true
            }
        }
    };

    // Chart Distribusi Jenis Hewan
    const animalTypeCtx = document.getElementById('animalTypeChart').getContext('2d');
    new Chart(animalTypeCtx, {
        type: 'doughnut',
        data: {
            labels: analyticsData.animal_types.labels,
            datasets: [{
                data: analyticsData.animal_types.data,
                backgroundColor: [
                    '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6',
                    '#06b6d4', '#84cc16', '#f97316', '#ec4899', '#6366f1'
                ],
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 12
            }]
        },
        options: {
            ...commonOptions,
            plugins: {
                ...commonOptions.plugins,
                legend: {
                    position: 'right',
                    labels: {
                        ...commonOptions.plugins.legend.labels,
                        generateLabels: function(chart) {
                            const data = chart.data;
                            if (data.labels.length && data.datasets.length) {
                                return data.labels.map((label, i) => {
                                    const value = data.datasets[0].data[i];
                                    const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : '0';
                                    return {
                                        text: `${label} (${percentage}%)`,
                                        fillStyle: data.datasets[0].backgroundColor[i],
                                        strokeStyle: data.datasets[0].borderColor,
                                        lineWidth: data.datasets[0].borderWidth,
                                        pointStyle: 'circle'
                                    };
                                });
                            }
                            return [];
                        }
                    }
                }
            },
            cutout: '55%'
        }
    });

    // Chart Status Kesehatan
    const healthStatusCtx = document.getElementById('healthStatusChart').getContext('2d');
    new Chart(healthStatusCtx, {
        type: 'pie',
        data: {
            labels: ['Sehat', 'Sakit'],
            datasets: [{
                data: [analyticsData.health_status.sehat, analyticsData.health_status.sakit],
                backgroundColor: ['#10b981', '#ef4444'],
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 12
            }]
        },
        options: {
            ...commonOptions,
            plugins: {
                ...commonOptions.plugins,
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Chart Status Vaksinasi
    const vaccinationCtx = document.getElementById('vaccinationChart').getContext('2d');
    new Chart(vaccinationCtx, {
        type: 'bar',
        data: {
            labels: ['Terkini', 'Perlu Update', 'Belum Vaksin'],
            datasets: [{
                label: 'Jumlah Hewan',
                data: [
                    analyticsData.vaccination_status.up_to_date,
                    analyticsData.vaccination_status.need_update,
                    analyticsData.vaccination_status.not_vaccinated
                ],
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                borderWidth: 0,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            ...commonOptions,
            plugins: {
                ...commonOptions.plugins,
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.03)'
                    },
                    ticks: {
                        font: {
                            family: "'Inter', sans-serif",
                            size: 9
                        },
                        stepSize: 1
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            family: "'Inter', sans-serif",
                            size: 9,
                            weight: '600'
                        }
                    }
                }
            }
        }
    });

    // Chart Penyakit Terbanyak
    const diseasesCtx = document.getElementById('diseasesChart').getContext('2d');
    new Chart(diseasesCtx, {
        type: 'bar',
        data: {
            labels: analyticsData.top_diseases.labels,
            datasets: [{
                label: 'Jumlah Kasus',
                data: analyticsData.top_diseases.data,
                backgroundColor: '#3b82f6',
                borderWidth: 0,
                borderRadius: 4,
            }]
        },
        options: {
            ...commonOptions,
            indexAxis: 'y',
            plugins: {
                ...commonOptions.plugins,
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.03)'
                    },
                    ticks: {
                        font: {
                            family: "'Inter', sans-serif",
                            size: 9
                        },
                        stepSize: 1
                    }
                },
                y: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            family: "'Inter', sans-serif",
                            size: 9,
                            weight: '500'
                        }
                    }
                }
            }
        }
    });

    // Chart Trend Bulanan
    const monthlyTrendCtx = document.getElementById('monthlyTrendChart').getContext('2d');
    new Chart(monthlyTrendCtx, {
        type: 'line',
        data: {
            labels: analyticsData.monthly_trend.labels,
            datasets: [
                {
                    label: 'Hewan Sehat',
                    data: analyticsData.monthly_trend.healthy,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.08)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 1.5,
                    pointRadius: 3,
                    pointHoverRadius: 5
                },
                {
                    label: 'Hewan Sakit',
                    data: analyticsData.monthly_trend.sick,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.08)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#ef4444',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 1.5,
                    pointRadius: 3,
                    pointHoverRadius: 5
                }
            ]
        },
        options: {
            ...commonOptions,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.03)'
                    },
                    ticks: {
                        font: {
                            family: "'Inter', sans-serif",
                            size: 9
                        }
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.03)'
                    },
                    ticks: {
                        font: {
                            family: "'Inter', sans-serif",
                            size: 9
                        }
                    }
                }
            }
        }
    });

    // Chart Jenis Kelamin
    const genderCtx = document.getElementById('genderChart').getContext('2d');
    new Chart(genderCtx, {
        type: 'doughnut',
        data: {
            labels: ['Jantan', 'Betina'],
            datasets: [{
                data: [analyticsData.gender_distribution.jantan, analyticsData.gender_distribution.betina],
                backgroundColor: ['#3b82f6', '#ec4899'],
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 12
            }]
        },
        options: {
            ...commonOptions,
            plugins: {
                ...commonOptions.plugins,
                legend: {
                    position: 'bottom'
                }
            },
            cutout: '60%'
        }
    });

    // Smooth loading animation
    const charts = document.querySelectorAll('canvas');
    charts.forEach((chart, index) => {
        chart.style.opacity = '0';
        chart.style.transition = 'opacity 0.5s ease';
        
        setTimeout(() => {
            chart.style.opacity = '1';
        }, 100 + (index * 100));
    });
});
</script>
@endpush