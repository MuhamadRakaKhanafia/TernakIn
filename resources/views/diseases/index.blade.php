@extends('layouts.app')

@section('title', 'Penyakit Hewan - TernakIN')
@section('page-title', 'Penyakit Hewan')

@section('content')
<div class="container-fluid">
    <!-- Statistik Penyakit -->
    <div class="row mb-4 justify-content-center">
        <div class="col-auto">
            <div class="stats-container">
                <div class="stat-item">
                    <span class="stat-number">{{ $diseases->total() }}</span>
                    <span class="stat-label">Total Penyakit</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ $diseases->where('is_zoonotic', 1)->count() }}</span>
                    <span class="stat-label">Zoonosis</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ $diseases->where('is_viral', 1)->count() }}</span>
                    <span class="stat-label">Penyakit Viral</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ $diseases->where('is_bakterial', 1)->count() }}</span>
                    <span class="stat-label">Penyakit Bakterial</span>
                </div>
                 <div class="stat-item">
                    <span class="stat-number">{{ $diseases->where('is_parasite', 1)->count() }}</span>
                    <span class="stat-label">Penyakit Parasite</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid Penyakit -->
    <div class="diseases-grid">
        @forelse($diseases as $disease)
        <div class="disease-card">
            <div class="disease-header">
                <div class="disease-code {{ $disease->is_zoonotic ? 'zoonotic' : 'non-zoonotic' }}">
                    {{ $disease->disease_code }}
                    @if($disease->is_zoonotic)
                    <span class="zoonotic-badge" title="Dapat menular ke manusia">
                        <i class="fas fa-exclamation-triangle"></i> Zoonosis
                    </span>
                    @endif
                </div>
                <h3 class="disease-title">{{ $disease->name }}</h3>
                @if($disease->other_names)
                <p class="disease-alias"><small>Juga dikenal sebagai: {{ $disease->other_names }}</small></p>
                @endif
            </div>

<div class="disease-content">
    <div class="disease-info">
        <div class="info-item">
            <i class="fas fa-bug"></i>
            <div>
                <strong>Agen Penyebab:</strong>
                @if($disease->causative_agent)
                    <span class="causative-agent">{{ $disease->causative_agent }}</span>
                @else
                    <span class="causative-agent text-muted">Sedang diteliti</span>
                @endif
            </div>
        </div>
        
        <div class="info-item">
            <i class="fas fa-dove"></i>
            <div>
                <strong>Menyerang:</strong>
                <div class="animal-types">
                    @if($disease->animalTypes->count() > 0)
                        @foreach($disease->animalTypes as $animalType)
                            <span class="animal-badge">{{ $animalType->name }}</span>
                        @endforeach
                    @else
                        <span class="animal-badge text-muted">Semua jenis hewan</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="info-item">
            <i class="fas fa-share-alt"></i>
            <div>
                <strong>Penularan:</strong>
                @if($disease->transmission_method)
                    <span class="transmission-method">{{ Str::limit($disease->transmission_method, 80) }}</span>
                @else
                    <span class="transmission-method text-muted">Belum tersedia</span>
                @endif
            </div>
        </div>
    </div>

    <div class="disease-description">
        <p>{{ Str::limit($disease->description, 120) }}</p>
    </div>

    @if($disease->symptoms->count() > 0)
    <div class="disease-symptoms">
        <h6><i class="fas fa-stethoscope me-2"></i>Gejala Utama:</h6>
        <div class="symptoms-list">
            @foreach($disease->symptoms->take(3) as $symptom)
            <span class="symptom-tag">
                {{ $symptom->name }}
                @if($symptom->pivot->is_primary)
                <i class="fas fa-star text-warning ms-1" title="Gejala Primer"></i>
                @endif
            </span>
            @endforeach
            @if($disease->symptoms->count() > 3)
            <span class="symptom-tag more-symptoms">
                +{{ $disease->symptoms->count() - 3 }} gejala lainnya
            </span>
            @endif
        </div>
    </div>
    @endif

    <div class="disease-treatment">
        <div class="treatment-header">
            <i class="fas fa-medkit treatment-icon"></i>
            <h6>Penanganan Umum:</h6>
        </div>
        <div class="treatment-content">
            <p class="treatment-text">{{ Str::limit($disease->general_treatment, 100) }}</p>
        </div>
    </div>
</div>

            <div class="disease-actions">
                <a href="{{ route('diseases.show', $disease->id) }}" class="btn btn-primary btn-detail">
                    <i class="fas fa-eye me-2"></i>Detail Lengkap
                </a>
                @if($disease->is_zoonotic)
                <span class="zoonotic-warning">
                    <i class="fas fa-exclamation-circle"></i> Berisiko ke manusia
                </span>
                @endif
            </div>
        </div>
        @empty
        <div class="no-data text-center py-5">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h3 class="text-muted">Tidak ada penyakit ditemukan</h3>
            <p class="text-muted">Coba ubah kriteria pencarian atau filter</p>
            <a href="{{ route('diseases.index') }}" class="btn btn-primary mt-2">
                <i class="fas fa-refresh me-2"></i>Tampilkan Semua
            </a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($diseases->hasPages())
    <div class="pagination-wrapper mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="pagination-info text-muted">
                Menampilkan {{ $diseases->firstItem() }} - {{ $diseases->lastItem() }} dari {{ $diseases->total() }} penyakit
            </div>
            {{ $diseases->appends(request()->query())->links() }}
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
:root {
    --primary-color: #10b981;
    --primary-light: #34d399;
    --primary-dark: #059669;
    --secondary-color: #8b5cf6;
    --accent-color: #f59e0b;
    --danger-color: #ef4444;
    --warning-color: #f59e0b;
    --info-color: #3b82f6;
    --text-dark: #1f2937;
    --text-medium: #4b5563;
    --text-light: #6b7280;
    --background-light: #f8fafc;
    --border-light: #e2e8f0;
    --border-medium: #d1d5db;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
/* Styles untuk data kosong */
.causative-agent.text-muted {
    background: #f3f4f6;
    color: #6b7280;
    border-color: #e5e7eb;
    font-style: italic;
}

.animal-badge.text-muted {
    background: #f3f4f6;
    color: #6b7280;
    border-color: #e5e7eb;
    font-style: italic;
}

.transmission-method.text-muted {
    color: #6b7280;
    font-style: italic;
}

.container-fluid {
    padding: 0;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    min-height: 100vh;
}

/* Header dengan Pencarian */
.content-header {
    background: white;
    padding: 2rem;
    border-radius: 20px;
    box-shadow: var(--shadow-lg);
    margin-bottom: 2rem;
    border: 1px solid var(--border-light);
    backdrop-filter: blur(10px);
}

.search-box {
    position: relative;
    max-width: 500px;
    margin: 0 auto;
}

.search-box input {
    border-radius: 50px;
    padding: 1rem 1.5rem 1rem 3rem;
    border: 2px solid var(--border-light);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    width: 100%;
    font-size: 1rem;
    background: #f8fafc;
    box-shadow: var(--shadow-sm);
}

.search-box input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1), var(--shadow-md);
    background: white;
    transform: translateY(-1px);
}

.search-box::before {
    content: '\f002';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    position: absolute;
    left: 1.25rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-light);
    z-index: 2;
}

/* Stats Container Modern */
.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    background: white;
    padding: 2rem;
    border-radius: 20px;
    box-shadow: var(--shadow-lg);
    margin-bottom: 2rem;
    border: 1px solid var(--border-light);
    margin-left: auto;
    margin-right: auto;
    max-width: 1200px;
    backdrop-filter: blur(10px);
}

.stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 1.5rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 16px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid var(--border-light);
    position: relative;
    overflow: hidden;
}

.stat-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
}

.stat-item:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-xl);
    border-color: var(--primary-light);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--primary-color);
    line-height: 1;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.stat-label {
    font-size: 0.9rem;
    color: var(--text-medium);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Diseases Grid Modern */
.diseases-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(420px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
    padding: 0 1rem;
}

.disease-card {
    background: white;
    border-radius: 24px;
    box-shadow: var(--shadow-lg);
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid var(--border-light);
    display: flex;
    flex-direction: column;
    height: 100%;
    position: relative;
    backdrop-filter: blur(10px);
}

.disease-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.disease-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: var(--shadow-xl);
}

.disease-card:hover::before {
    transform: scaleX(1);
}

.disease-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    position: relative;
    overflow: hidden;
}

.disease-header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-10px) rotate(180deg); }
}

.disease-code {
    font-size: 0.85rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    z-index: 2;
}

.zoonotic-badge {
    background: rgba(255,255,255,0.25);
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
}

.disease-title {
    font-size: 1.5rem;
    font-weight: 800;
    margin: 0;
    line-height: 1.3;
    position: relative;
    z-index: 2;
    letter-spacing: -0.025em;
}

.disease-alias {
    opacity: 0.9;
    margin: 0.5rem 0 0 0;
    font-style: italic;
    position: relative;
    z-index: 2;
    font-size: 0.9rem;
}

.disease-content {
    padding: 2rem;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.disease-info {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 12px;
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.info-item:hover {
    background: white;
    border-color: var(--primary-light);
    transform: translateX(5px);
    box-shadow: var(--shadow-sm);
}

.info-item i {
    color: var(--primary-color);
    margin-top: 0.1rem;
    flex-shrink: 0;
    font-size: 1.1rem;
    width: 20px;
    text-align: center;
}

.causative-agent {
    background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%);
    color: #1d4ed8;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    border: 1px solid #dbeafe;
    display: inline-block;
}

.animal-types {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.animal-badge {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    color: #166534;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    border: 1px solid #bbf7d0;
    transition: all 0.3s ease;
}

.animal-badge:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-sm);
}

.transmission-method {
    font-size: 0.95rem;
    color: var(--text-medium);
    line-height: 1.5;
    font-weight: 500;
}

.disease-description p {
    color: var(--text-medium);
    line-height: 1.6;
    margin: 0;
    font-size: 0.95rem;
}

.disease-symptoms, .disease-treatment {
    border-top: 2px solid var(--border-light);
    padding-top: 1.5rem;
    margin-top: 0.5rem;
}

.disease-symptoms h6 {
    color: var(--text-dark);
    font-weight: 700;
    margin-bottom: 1rem;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.disease-treatment {
    background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
    border-radius: 12px;
    padding: 1.5rem;
    margin-top: 1rem;
    border: 1px solid #dcfce7;
}

.treatment-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.treatment-icon {
    color: var(--primary-color);
    font-size: 1.1rem;
    flex-shrink: 0;
}

.treatment-header h6 {
    color: var(--text-dark);
    font-weight: 700;
    font-size: 1rem;
    margin: 0;
}

.treatment-content {
    margin-left: 0;
}

.treatment-text {
    font-size: 0.95rem;
    color: var(--text-medium);
    line-height: 1.6;
    margin: 0;
    font-weight: 500;
}

.symptoms-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.symptom-tag {
    background: linear-gradient(135deg, #f3f4f6 0%, #f9fafb 100%);
    color: var(--text-dark);
    padding: 8px 14px;
    border-radius: 10px;
    font-size: 0.85rem;
    font-weight: 600;
    border: 1px solid #e5e7eb;
    line-height: 1.3;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.symptom-tag::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 3px;
    height: 100%;
    background: var(--primary-color);
    transform: scaleY(0);
    transition: transform 0.3s ease;
}

.symptom-tag:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    background: white;
}

.symptom-tag:hover::before {
    transform: scaleY(1);
}

.more-symptoms {
    background: linear-gradient(135deg, #fef3c7 0%, #fef9c3 100%);
    color: #92400e;
    border-color: #fcd34d;
}

.disease-actions {
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-top: 1px solid var(--border-light);
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
}

.btn-detail {
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    border: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    color: white;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: var(--shadow-md);
}

.btn-detail:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-xl);
    background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary-color) 100%);
    color: white;
}

.zoonotic-warning {
    color: var(--danger-color);
    font-size: 0.85rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 8px 12px;
    background: rgba(239, 68, 68, 0.1);
    border-radius: 8px;
    border: 1px solid rgba(239, 68, 68, 0.2);
}

.no-data {
    grid-column: 1 / -1;
    padding: 4rem 2rem;
    text-align: center;
    background: white;
    border-radius: 20px;
    box-shadow: var(--shadow-lg);
    margin: 2rem 0;
}

.no-data i {
    opacity: 0.3;
    margin-bottom: 1.5rem;
    font-size: 4rem;
}

.no-data h3 {
    color: var(--text-medium);
    margin-bottom: 1rem;
    font-weight: 600;
}

.no-data p {
    color: var(--text-light);
    margin-bottom: 2rem;
}

.pagination-wrapper {
    background: white;
    padding: 2rem;
    border-radius: 20px;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--border-light);
    margin: 0 auto;
    max-width: 1200px;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .diseases-grid {
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 1.5rem;
    }
}

@media (max-width: 768px) {
    .container-fluid {
        padding: 0.5rem;
    }
    
    .content-header {
        padding: 1.5rem;
        border-radius: 16px;
        margin-bottom: 1.5rem;
    }
    
    .stats-container {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        padding: 1.5rem;
        border-radius: 16px;
        margin-bottom: 1.5rem;
    }
    
    .diseases-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
        padding: 0;
    }
    
    .disease-card {
        border-radius: 16px;
    }
    
    .disease-header {
        padding: 1.5rem;
    }
    
    .disease-content {
        padding: 1.5rem;
        gap: 1.25rem;
    }
    
    .stat-number {
        font-size: 2rem;
    }
    
    .disease-actions {
        padding: 1.25rem 1.5rem;
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .pagination-wrapper {
        padding: 1.5rem;
        border-radius: 16px;
    }
}

@media (max-width: 576px) {
    .content-header {
        padding: 1.25rem;
    }
    
    .stats-container {
        grid-template-columns: 1fr;
        padding: 1.25rem;
    }
    
    .stat-item {
        padding: 1.25rem;
    }
    
    .disease-header {
        padding: 1.25rem;
    }
    
    .disease-title {
        font-size: 1.3rem;
    }
    
    .disease-content {
        padding: 1.25rem;
        gap: 1rem;
    }
    
    .info-item {
        padding: 0.875rem;
        gap: 0.75rem;
    }
    
    .treatment-header {
        gap: 0.625rem;
    }
    
    .disease-treatment {
        padding: 1.25rem;
    }
    
    .btn-detail {
        padding: 10px 20px;
        width: 100%;
        justify-content: center;
    }
    
    .no-data {
        padding: 3rem 1.5rem;
    }
    
    .no-data i {
        font-size: 3rem;
    }
}

/* Smooth Animations */
.disease-card,
.stat-item,
.btn-detail,
.info-item,
.symptom-tag {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Loading States */
.btn-detail:disabled {
    opacity: 0.7;
    transform: none !important;
    box-shadow: none !important;
}

/* Custom Scrollbar */
.diseases-grid::-webkit-scrollbar {
    width: 6px;
}

.diseases-grid::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.diseases-grid::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.diseases-grid::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');

    function performSearch() {
        const search = searchInput.value;
        
        const url = new URL(window.location);

        if (search) {
            url.searchParams.set('search', search);
        } else {
            url.searchParams.delete('search');
        }

        url.searchParams.delete('page'); // Reset to first page on new search

        window.location.href = url.toString();
    }

    // Search input with debounce
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 500);
    });

    // Enter key in search input
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });
});
</script>
@endpush