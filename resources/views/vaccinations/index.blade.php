@extends('layouts.app')

@section('title', 'Jadwal Vaksinasi - TernakIN')
@section('page-title', 'Jadwal Vaksinasi')

@section('content')
<div class="container-fluid">
    <!-- Flash Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Header dengan Filter -->
    <div class="content-header">
        <div class="filter-section">
            <select id="animalTypeFilter" class="form-select">
                <option value="">Semua Jenis Hewan</option>
                @foreach($animalTypes ?? [] as $animalType)
                <option value="{{ $animalType->id }}" {{ request('animal_type_id') == $animalType->id ? 'selected' : '' }}>
                    {{ $animalType->name }}
                </option>
                @endforeach
            </select>
            <select id="statusFilter" class="form-select">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Validasi</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
            </select>
            <a href="{{ route('vaccinations.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Vaksinasi
            </a>
        </div>
    </div>

    <!-- Statistik Vaksinasi -->
    <div class="stats-container">
        <div class="stat-item">
            <span class="stat-number">{{ $vaccinations->total() }}</span>
            <span class="stat-label">Total Vaksinasi</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">{{ $vaccinations->where('status', 'pending')->count() }}</span>
            <span class="stat-label">Menunggu Validasi</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">{{ $vaccinations->where('status', 'approved')->count() }}</span>
            <span class="stat-label">Disetujui</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">{{ $vaccinations->where('status', 'completed')->count() }}</span>
            <span class="stat-label">Selesai</span>
        </div>
    </div>

    <!-- Tabel Vaksinasi -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="table vaccination-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Hewan Ternak</th>
                        <th>Nama Vaksin</th>
                        <th>Tanggal Vaksinasi</th>
                        <th>Vaksinasi Selanjutnya</th>
                        <th>Status</th>
                        <th>Catatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vaccinations as $index => $vaccination)
                    <tr data-id="{{ $vaccination->id }}">
                        <td>{{ $vaccinations->firstItem() + $index }}</td>
                        <td>
                            @if($vaccination->animalType)
                                <div>
                                    <strong>{{ $vaccination->animalType->name }}</strong>
                                </div>
                            @else
                                <span class="text-muted">Jenis hewan tidak ditemukan</span>
                            @endif
                        </td>
                        <td>{{ $vaccination->vaccine_name }}</td>
                        <td>{{ $vaccination->vaccination_date ? $vaccination->vaccination_date->format('d/m/Y') : '-' }}</td>
                        <td>
                            @if($vaccination->next_vaccination_date)
                                {{ $vaccination->next_vaccination_date->format('d/m/Y') }}
                                @if($vaccination->next_vaccination_date->isPast())
                                    <small class="text-danger d-block">Terlewat</small>
                                @elseif($vaccination->next_vaccination_date->diffInDays() <= 7)
                                    <small class="text-warning d-block">Mendekati</small>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @switch($vaccination->status)
                                @case('pending')
                                    <span class="badge bg-warning">Menunggu</span>
                                    @break
                                @case('approved')
                                    <span class="badge bg-success">Disetujui</span>
                                    @break
                                @case('rejected')
                                    <span class="badge bg-danger">Ditolak</span>
                                    @break
                                @case('completed')
                                    <span class="badge bg-primary">Selesai</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">-</span>
                            @endswitch
                        </td>
                        <td>
                            @if($vaccination->notes)
                                <span title="{{ $vaccination->notes }}">
                                    {{ Str::limit($vaccination->notes, 30) }}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <form method="POST" action="{{ route('vaccinations.destroy', $vaccination) }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm delete-btn" title="Hapus"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus data vaksinasi ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="no-data">
                                <i class="fas fa-syringe"></i>
                                <h5>Belum ada data vaksinasi</h5>
                                <p>Klik tombol "Tambah Vaksinasi" untuk menambah jadwal vaksinasi baru</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($vaccinations->hasPages())
    <div class="pagination-wrapper">
        <div class="d-flex justify-content-between align-items-center">
            <div class="pagination-info">
                Menampilkan {{ $vaccinations->firstItem() }} - {{ $vaccinations->lastItem() }} dari {{ $vaccinations->total() }} vaksinasi
            </div>
            {{ $vaccinations->appends(request()->query())->links() }}
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
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

.container-fluid {
    padding: 20px;
    background-color: #f8fafc;
    min-height: 100vh;
    max-width: 1400px;
    margin: 0 auto;
}

/* Flash Messages Modern */
.alert {
    border: none;
    border-radius: 8px;
    padding: 1rem 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    border-left: 4px solid transparent;
}

.alert-success {
    background: #f0fdf4;
    border-left-color: var(--primary-color);
    color: #065f46;
}

.alert-danger {
    background: #fef2f2;
    border-left-color: var(--danger-high);
    color: #991b1b;
}

.alert .btn-close {
    padding: 0.75rem;
}

/* Header dengan Filter Modern */
.content-header {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 30px;
    border: 1px solid #e5e7eb;
    display: flex;
    justify-content: center;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.filter-section {
    display: flex;
    gap: 1rem;
    align-items: center;
    flex-wrap: wrap;
    justify-content: center;
    width: 100%;
}

.form-select {
    padding: 0.75rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    background: white;
    transition: all 0.2s ease;
    min-width: 200px;
    font-size: 0.9rem;
    font-weight: 500;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.75rem center;
    background-repeat: no-repeat;
    background-size: 16px 12px;
    border: 1px solid #d1d5db !important;
}

.form-select:focus {
    border-color: var(--primary-color) !important;
    box-shadow: 0 0 0 2px rgba(5, 150, 105, 0.1) !important;
    outline: none !important;
}

.btn-primary {
    background: var(--primary-color);
    border: none;
    border-radius: 6px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.2s ease;
    color: white;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(5, 150, 105, 0.2);
    color: white;
}

/* Stats Container Modern */
.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    background: white;
    padding: 2rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 1.5rem;
    background: #f8fafc;
    border-radius: 6px;
    transition: all 0.2s ease;
    border: 1px solid #e2e8f0;
}

.stat-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary-color);
    line-height: 1;
    margin-bottom: 0.75rem;
}

.stat-label {
    font-size: 0.9rem;
    color: var(--text-light);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Table Container Modern */
.table-container {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
    margin-bottom: 2rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.table-responsive {
    max-height: 70vh;
    overflow-y: auto;
    overflow-x: auto;
    border-radius: 8px;
}

.vaccination-table {
    margin-bottom: 0;
    font-size: 0.9rem;
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    min-width: 1000px;
}

.vaccination-table th {
    position: sticky;
    top: 0;
    background: #374151 !important;
    border: none;
    padding: 1.25rem 1rem;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
    color: white !important;
    border-bottom: 1px solid #4b5563;
    text-align: center;
    white-space: nowrap;
}

.vaccination-table th:first-child {
    border-top-left-radius: 8px;
}

.vaccination-table th:last-child {
    border-top-right-radius: 8px;
}

.vaccination-table td {
    padding: 1.25rem 1rem;
    vertical-align: middle;
    border-bottom: 1px solid #f3f4f6;
    background: white;
    transition: all 0.2s ease;
    text-align: center;
    min-width: 120px;
}

/* Kolom spesifik dengan width yang lebih optimal */
.vaccination-table td:nth-child(1) { min-width: 60px; max-width: 80px; }
.vaccination-table td:nth-child(2) { min-width: 200px; max-width: 250px; }
.vaccination-table td:nth-child(3) { min-width: 150px; max-width: 200px; }
.vaccination-table td:nth-child(4) { min-width: 120px; max-width: 140px; }
.vaccination-table td:nth-child(5) { min-width: 120px; max-width: 140px; }
.vaccination-table td:nth-child(6) { min-width: 130px; max-width: 160px; }
.vaccination-table td:nth-child(7) { min-width: 150px; max-width: 200px; }
.vaccination-table td:nth-child(8) { min-width: 140px; max-width: 160px; }

.vaccination-table tbody tr {
    transition: all 0.2s ease;
}

.vaccination-table tbody tr:hover {
    background-color: #f8fafc;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.vaccination-table tbody tr:last-child td {
    border-bottom: none;
}

.vaccination-table tbody tr:last-child td:first-child {
    border-bottom-left-radius: 8px;
}

.vaccination-table tbody tr:last-child td:last-child {
    border-bottom-right-radius: 8px;
}

/* Badges Modern */
.badge {
    padding: 0.5rem 0.75rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none;
}

.bg-warning {
    background: #f59e0b !important;
    color: white;
}

.bg-success {
    background: #10b981 !important;
    color: white;
}

.bg-danger {
    background: #ef4444 !important;
    color: white;
}

.bg-primary {
    background: #3b82f6 !important;
    color: white;
}

.bg-secondary {
    background: #6b7280 !important;
    color: white;
}

/* Action Buttons Modern */
.action-buttons {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.action-buttons .btn {
    padding: 0.5rem;
    font-size: 0.8rem;
    font-weight: 600;
    border-radius: 4px !important;
    border: none;
    transition: all 0.2s ease;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-info {
    background: #3b82f6;
    color: white;
}

.btn-info:hover {
    background: #2563eb;
    transform: translateY(-1px);
}

.btn-warning {
    background: #f59e0b;
    color: white;
}

.btn-warning:hover {
    background: #d97706;
    transform: translateY(-1px);
}

.btn-danger {
    background: #ef4444;
    color: white;
}

.btn-danger:hover {
    background: #dc2626;
    transform: translateY(-1px);
}

/* No Data State Modern */
.no-data {
    color: #6b7280;
    padding: 3rem 1rem;
    text-align: center;
}

.no-data i {
    opacity: 0.3;
    margin-bottom: 1rem;
    font-size: 3rem;
}

.no-data h5 {
    font-weight: 600;
    margin-bottom: 0.75rem;
    font-size: 1.25rem;
}

.no-data p {
    font-size: 0.9rem;
    opacity: 0.7;
}

/* Pagination Modern */
.pagination-wrapper {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.pagination-info {
    font-size: 0.9rem;
    font-weight: 500;
    color: var(--text-light);
}

.pagination {
    margin-bottom: 0;
}

.page-link {
    border: 1px solid #d1d5db;
    border-radius: 4px;
    padding: 0.5rem 0.875rem;
    margin: 0 0.125rem;
    color: var(--text-color);
    font-weight: 600;
    transition: all 0.2s ease;
    font-size: 0.9rem;
    text-decoration: none;
}

.page-link:hover {
    border-color: var(--primary-color);
    background-color: #f0fdf4;
    color: var(--primary-color);
    transform: translateY(-1px);
}

.page-item.active .page-link {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .stats-container {
        grid-template-columns: repeat(2, 1fr);
    }

    .table-container {
        margin: 0 10px;
    }

    .container-fluid {
        padding: 15px;
    }
}

@media (max-width: 768px) {
    .container-fluid {
        padding: 10px;
    }

    .content-header {
        padding: 1.25rem;
        justify-content: center;
    }

    .filter-section {
        justify-content: center;
        gap: 0.75rem;
    }

    .form-select {
        min-width: 150px;
    }

    .stats-container {
        padding: 1.5rem;
    }

    .stat-number {
        font-size: 2rem;
    }

    .action-buttons {
        flex-direction: column;
        gap: 0.25rem;
    }

    .action-buttons .btn {
        width: 32px;
        height: 32px;
        font-size: 0.75rem;
    }
}

@media (max-width: 576px) {
    .container-fluid {
        padding: 0.5rem;
    }

    .content-header {
        flex-direction: column;
        gap: 0.75rem;
    }

    .filter-section {
        flex-direction: column;
        width: 100%;
    }

    .form-select {
        min-width: auto;
        width: 100%;
    }

    .btn-primary {
        width: 100%;
        justify-content: center;
    }

    .stats-container {
        grid-template-columns: 1fr;
        gap: 0.75rem;
        padding: 1rem;
    }

    .stat-item {
        padding: 1rem;
    }

    .pagination-wrapper {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }

    .table-responsive {
        max-height: 65vh;
    }
}

/* Smooth Animations */
.vaccination-table tbody tr,
.stat-item,
.btn,
.form-select,
.alert {
    transition: all 0.2s ease;
}

/* Loading States */
.btn:disabled {
    opacity: 0.7;
    transform: none !important;
}

.fa-spin {
    animation: fa-spin 1s infinite linear;
}

@keyframes fa-spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
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
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const animalTypeFilter = document.getElementById('animalTypeFilter');
    const statusFilter = document.getElementById('statusFilter');

    function applyFilters() {
        const params = new URLSearchParams(window.location.search);

        if (animalTypeFilter.value) params.set('animal_type_id', animalTypeFilter.value);
        else params.delete('animal_type_id');

        if (statusFilter.value) params.set('status', statusFilter.value);
        else params.delete('status');

        params.delete('page');
        window.location.href = `${window.location.pathname}?${params.toString()}`;
    }

    if (animalTypeFilter) animalTypeFilter.addEventListener('change', applyFilters);
    if (statusFilter) statusFilter.addEventListener('change', applyFilters);
});
</script>
@endpush
