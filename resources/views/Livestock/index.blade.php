@extends('layouts.app')

@section('title', 'Hewan Ternak - TernakIN')
@section('page-title', 'Hewan Ternak')

@section('content')
<div class="container-fluid">
    <!-- Flash Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Header dengan Filter - STRUCTURE DIPERBAIKI -->
    <div class="content-header">
        <div class="filter-section">
            <select id="typeFilter" class="form-select">
                <option value="">Semua Jenis Hewan</option>
                @foreach($animalTypes as $type)
                <option value="{{ $type->name }}" {{ request('type') == $type->name ? 'selected' : '' }}>{{ $type->name }}</option>
                @endforeach
            </select>
            <select id="healthFilter" class="form-select">
                <option value="">Semua Status Kesehatan</option>
                <option value="sehat" {{ request('health_status') == 'sehat' ? 'selected' : '' }}>Sehat</option>
                <option value="sakit" {{ request('health_status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
            </select>
            <select id="vaccinationFilter" class="form-select">
                <option value="">Semua Status Vaksinasi</option>
                <option value="up_to_date" {{ request('vaccination_status') == 'up_to_date' ? 'selected' : '' }}>Terkini</option>
                <option value="need_update" {{ request('vaccination_status') == 'need_update' ? 'selected' : '' }}>Perlu Update</option>
                <option value="not_vaccinated" {{ request('vaccination_status') == 'not_vaccinated' ? 'selected' : '' }}>Belum Vaksin</option>
            </select>
            <a href="{{ route('livestock.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Hewan Ternak
            </a>
        </div>
    </div>

    <!-- Statistik Hewan Ternak - STRUCTURE DIPERBAIKI -->
    <div class="stats-container">
        <div class="stat-item">
            <span class="stat-number">{{ $totalLivestock }}</span>
            <span class="stat-label">Total Hewan</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">{{ $healthyLivestock }}</span>
            <span class="stat-label">Hewan Sehat</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">{{ $needVaccination }}</span>
            <span class="stat-label">Perlu Vaksin</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">{{ $sickLivestock }}</span>
            <span class="stat-label">Hewan Sakit</span>
        </div>
    </div>

    <!-- Tabel Hewan Ternak -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="table livestock-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama/ID</th>
                        <th>Jenis Hewan</th>
                        <th>Jenis Kelamin</th>
                        <th>Umur</th>
                        <th>Berat (kg)</th>
                        <th>Status Kesehatan</th>
                        <th>Status Vaksinasi</th>
                        <th>Jenis Pakan</th>
                        <th>Pakan Harian (kg)</th>
                        <th>Tipe Kandang</th>
                        <th>Catatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($livestocks as $index => $livestock)
                    @php
                        $animalType = $livestock->animalType;
                        $isPoultry = $animalType && $animalType->category === 'poultry';
                    @endphp
                    <tr data-id="{{ $livestock->id }}" class="livestock-row {{ $livestock->health_status == 'sakit' ? 'table-danger' : '' }}">
                        <td>{{ $livestocks->firstItem() + $index }}</td>
                        <td>
                            <div class="editable-cell" data-field="name" data-value="{{ $livestock->name }}">
                                <span class="display-value">{{ $livestock->name ?: '-' }}</span>
                                <input type="text" class="edit-input" value="{{ $livestock->name }}" style="display: none;">
                            </div>
                            @if($livestock->identification_number)
                            <small class="text-muted">ID: {{ $livestock->identification_number }}</small>
                            @endif
                        </td>
                        <td>
                            @if($animalType)
                                {{ $animalType->name }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="editable-cell" data-field="sex" data-value="{{ $livestock->sex }}">
                                <span class="display-value">
                                    {{ $livestock->sex == 'jantan' ? 'Jantan' : 'Betina' }}
                                </span>
                                <select class="edit-input" style="display: none;">
                                    <option value="jantan" {{ $livestock->sex == 'jantan' ? 'selected' : '' }}>Jantan</option>
                                    <option value="betina" {{ $livestock->sex == 'betina' ? 'selected' : '' }}>Betina</option>
                                </select>
                            </div>
                        </td>
                        <td>
                            @if($isPoultry)
                                <div class="editable-cell" data-field="age_weeks" data-value="{{ $livestock->age_weeks ?? '' }}">
                                    <span class="display-value">
                                        {{ $livestock->age_weeks ? $livestock->age_weeks . ' minggu' : '-' }}
                                    </span>
                                    <input type="number" class="edit-input" value="{{ $livestock->age_weeks }}" min="1" max="104" style="display: none;">
                                </div>
                            @else
                                <div class="editable-cell" data-field="age_months" data-value="{{ $livestock->age_months ?? '' }}">
                                    <span class="display-value">
                                        {{ $livestock->age_months ? $livestock->age_months . ' bulan' : '-' }}
                                    </span>
                                    <input type="number" class="edit-input" value="{{ $livestock->age_months }}" min="1" max="240" style="display: none;">
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="editable-cell" data-field="weight_kg" data-value="{{ $livestock->weight_kg ?? '' }}">
                                <span class="display-value">
                                    {{ $livestock->weight_kg ? number_format($livestock->weight_kg, 1) : '-' }}
                                </span>
                                <input type="number" step="0.01" class="edit-input" value="{{ $livestock->weight_kg }}" min="0.1" style="display: none;">
                            </div>
                        </td>
                        <td>
                            <div class="editable-cell" data-field="health_status" data-value="{{ $livestock->health_status }}">
                                <span class="display-value">
                                    @if($livestock->health_status == 'sehat')
                                        <span class="badge bg-success">Sehat</span>
                                    @else
                                        <span class="badge bg-danger">Sakit</span>
                                    @endif
                                </span>
                                <select class="edit-input" style="display: none;">
                                    <option value="sehat" {{ $livestock->health_status == 'sehat' ? 'selected' : '' }}>Sehat</option>
                                    <option value="sakit" {{ $livestock->health_status == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                </select>
                            </div>
                        </td>
                        <td>
                            <div class="editable-cell" data-field="vaccination_status" data-value="{{ $livestock->vaccination_status }}">
                                <span class="display-value">
                                    @switch($livestock->vaccination_status)
                                        @case('up_to_date')
                                            <span class="badge vaccination-badge up_to_date">Terkini</span>
                                            @break
                                        @case('need_update')
                                            <span class="badge vaccination-badge need_update">Perlu Update</span>
                                            @break
                                        @case('not_vaccinated')
                                            <span class="badge vaccination-badge not_vaccinated">Belum Vaksin</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">-</span>
                                    @endswitch
                                </span>
                                <select class="edit-input" style="display: none;">
                                    <option value="up_to_date" {{ $livestock->vaccination_status == 'up_to_date' ? 'selected' : '' }}>Terkini</option>
                                    <option value="need_update" {{ $livestock->vaccination_status == 'need_update' ? 'selected' : '' }}>Perlu Update</option>
                                    <option value="not_vaccinated" {{ $livestock->vaccination_status == 'not_vaccinated' ? 'selected' : '' }}>Belum Vaksin</option>
                                </select>
                            </div>
                        </td>
                        <td>
                            <div class="editable-cell" data-field="feed_type" data-value="{{ $livestock->feed_type ?? '' }}">
                                <span class="display-value">{{ $livestock->feed_type ?: '-' }}</span>
                                <input type="text" class="edit-input" value="{{ $livestock->feed_type }}" style="display: none;">
                            </div>
                        </td>
                        <td>
                            <div class="editable-cell" data-field="daily_feed_kg" data-value="{{ $livestock->daily_feed_kg ?? '' }}">
                                <span class="display-value">
                                    {{ $livestock->daily_feed_kg ? number_format($livestock->daily_feed_kg, 2) : '-' }}
                                </span>
                                <input type="number" step="0.01" class="edit-input" value="{{ $livestock->daily_feed_kg }}" min="0.01" style="display: none;">
                            </div>
                        </td>
                        <td>
                            <div class="editable-cell" data-field="housing_type" data-value="{{ $livestock->housing_type ?? '' }}">
                                <span class="display-value">{{ $livestock->housing_type ?: '-' }}</span>
                                <input type="text" class="edit-input" value="{{ $livestock->housing_type }}" style="display: none;">
                            </div>
                        </td>
                        <td>
                            <div class="editable-cell" data-field="notes" data-value="{{ $livestock->notes ?? '' }}">
                                <span class="display-value">
                                    {{ $livestock->notes ? Str::limit($livestock->notes, 30) : '-' }}
                                </span>
                                <textarea class="edit-input" rows="2" style="display: none;">{{ $livestock->notes }}</textarea>
                            </div>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-warning edit-btn" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-success save-btn" title="Simpan" style="display: none;">
                                    <i class="fas fa-save"></i>
                                </button>
                                <button class="btn btn-secondary cancel-btn" title="Batal" style="display: none;">
                                    <i class="fas fa-times"></i>
                                </button>
                                <button class="btn btn-danger delete-btn" data-id="{{ $livestock->id }}" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13">
                            <div class="no-data">
                                <i class="fas fa-inbox"></i>
                                <h5>Belum ada data hewan ternak</h5>
                                <p>Klik tombol "Tambah Hewan Ternak" untuk menambah data baru</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($livestocks->hasPages())
    <div class="pagination-wrapper">
        <div class="d-flex justify-content-between align-items-center">
            <div class="pagination-info">
                Menampilkan {{ $livestocks->firstItem() }} - {{ $livestocks->lastItem() }} dari {{ $livestocks->total() }} hewan ternak
            </div>
            {{ $livestocks->appends(request()->query())->links() }}
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

/* Header dengan Filter Modern - DIPERBAIKI */

.content-header {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    margin: 0 auto 30px auto; /* Pusatkan dengan margin auto */
    border: 1px solid #e5e7eb;
    display: flex;
    justify-content: center;
    align-items: center;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    max-width: 1400px;
    width: calc(100% - 40px); /* Beri spacing 20px di kiri-kanan */
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
    margin-left: auto;
    margin-right: auto;
    max-width: 1400px;
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
    margin-left: auto;
    margin-right: auto;
    max-width: 1400px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    width: 95%;
}

.table-responsive {
    max-height: 70vh;
    overflow-y: auto;
    border-radius: 8px;
}

.livestock-table {
    margin-bottom: 0;
    font-size: 0.9rem;
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    min-width: 1300px;
}

.livestock-table th {
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

.livestock-table th:first-child {
    border-top-left-radius: 8px;
}

.livestock-table th:last-child {
    border-top-right-radius: 8px;
}

.livestock-table td {
    padding: 1.25rem 1rem;
    vertical-align: middle;
    border-bottom: 1px solid #f3f4f6;
    background: white;
    transition: all 0.2s ease;
    text-align: center;
    min-width: 100px;
}

/* Kolom spesifik dengan width yang lebih optimal */
.livestock-table td:nth-child(1) { min-width: 60px; max-width: 80px; }
.livestock-table td:nth-child(2) { min-width: 150px; max-width: 200px; }
.livestock-table td:nth-child(3) { min-width: 120px; max-width: 150px; }
.livestock-table td:nth-child(4) { min-width: 100px; max-width: 120px; }
.livestock-table td:nth-child(5) { min-width: 100px; max-width: 120px; }
.livestock-table td:nth-child(6) { min-width: 100px; max-width: 120px; }
.livestock-table td:nth-child(7) { min-width: 120px; max-width: 150px; }
.livestock-table td:nth-child(8) { min-width: 130px; max-width: 160px; }
.livestock-table td:nth-child(9) { min-width: 120px; max-width: 150px; }
.livestock-table td:nth-child(10) { min-width: 120px; max-width: 150px; }
.livestock-table td:nth-child(11) { min-width: 120px; max-width: 150px; }
.livestock-table td:nth-child(12) { min-width: 150px; max-width: 200px; }
.livestock-table td:nth-child(13) { min-width: 120px; max-width: 140px; }

.livestock-table tbody tr {
    transition: all 0.2s ease;
}

.livestock-table tbody tr:hover {
    background-color: #f8fafc;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.livestock-table tbody tr:last-child td {
    border-bottom: none;
}

.livestock-table tbody tr:last-child td:first-child {
    border-bottom-left-radius: 8px;
}

.livestock-table tbody tr:last-child td:last-child {
    border-bottom-right-radius: 8px;
}

/* Editable Cells Modern */
.editable-cell {
    position: relative;
    min-height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.display-value {
    width: 100%;
    padding: 0.5rem 0;
    font-weight: 500;
    color: var(--text-color);
    text-align: center;
    word-wrap: break-word;
}

.edit-input {
    width: 100%;
    font-size: 0.85rem;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    background: white;
    transition: all 0.2s ease;
    font-weight: 500;
    text-align: center;
}

.edit-input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px rgba(5, 150, 105, 0.1);
    outline: none;
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

.vaccination-badge.up_to_date {
    background: #10b981 !important;
    color: white;
}

.vaccination-badge.need_update {
    background: #f59e0b !important;
    color: white;
}

.vaccination-badge.not_vaccinated {
    background: #ef4444 !important;
    color: white;
}

.bg-success {
    background: #10b981 !important;
}

.bg-danger {
    background: #ef4444 !important;
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

.btn-warning {
    background: #f59e0b;
    color: white;
}

.btn-warning:hover {
    background: #d97706;
    transform: translateY(-1px);
}

.btn-success {
    background: #10b981;
    color: white;
}

.btn-success:hover {
    background: #059669;
    transform: translateY(-1px);
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
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
    margin-left: auto;
    margin-right: auto;
    max-width: 1400px;
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

/* Table Row States */
.livestock-row.table-danger {
    background: #fef2f2 !important;
    border-left: 2px solid var(--danger-high);
}

.livestock-row.table-danger:hover {
    background: #fee2e2 !important;
}

/* Edit Mode Styles */
.livestock-row.edit-mode {
    background: #f0fdf4 !important;
    border-left: 2px solid var(--primary-color);
}

/* Responsive Design */
@media (max-width: 1024px) {
    .stats-container {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .table-container {
        max-width: 95%;
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
    
    .table-container,
    .stats-container,
    .pagination-wrapper {
        margin-left: auto;
        margin-right: auto;
        max-width: calc(100% - 20px);
    }
}

@media (max-width: 576px) {
    .container-fluid {
        padding: 0.5rem;
    }
    
    .content-header,
    .table-container,
    .stats-container,
    .pagination-wrapper {
        margin-left: 0;
        margin-right: 0;
        max-width: 100%;
    }
    
    .stats-container {
        grid-template-columns: 1fr;
        gap: 1rem;
        padding: 1rem;
    }
    
    .filter-section {
        flex-direction: column;
        align-items: stretch;
    }
    
    .form-select {
        min-width: auto;
        width: 100%;
    }
}

/* Smooth Animations */
.livestock-table tbody tr,
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
    const typeFilter = document.getElementById('typeFilter');
    const healthFilter = document.getElementById('healthFilter');
    const vaccinationFilter = document.getElementById('vaccinationFilter');

    function applyFilters() {
        const params = new URLSearchParams(window.location.search);

        if (typeFilter.value) params.set('type', typeFilter.value);
        else params.delete('type');

        if (healthFilter.value) params.set('health_status', healthFilter.value);
        else params.delete('health_status');

        if (vaccinationFilter.value) params.set('vaccination_status', vaccinationFilter.value);
        else params.delete('vaccination_status');

        params.delete('page');
        window.location.href = `${window.location.pathname}?${params.toString()}`;
    }

    if (typeFilter) typeFilter.addEventListener('change', applyFilters);
    if (healthFilter) healthFilter.addEventListener('change', applyFilters);
    if (vaccinationFilter) vaccinationFilter.addEventListener('change', applyFilters);

    // Simple editing functionality
    const table = document.querySelector('.livestock-table');
    
    if (!table) return;

    table.addEventListener('click', function(e) {
        const target = e.target;
        const button = target.closest('button');
        
        if (!button) return;

        const row = button.closest('tr');
        const livestockId = row.dataset.id;

        if (button.classList.contains('edit-btn')) {
            e.preventDefault();
            enableEditMode(row);
        }
        else if (button.classList.contains('save-btn')) {
            e.preventDefault();
            saveChanges(row, livestockId);
        }
        else if (button.classList.contains('cancel-btn')) {
            e.preventDefault();
            disableEditMode(row);
        }
        else if (button.classList.contains('delete-btn')) {
            e.preventDefault();
            deleteLivestock(livestockId);
        }
    });

    function enableEditMode(row) {
        // Disable all other edit modes first
        document.querySelectorAll('.livestock-row.edit-mode').forEach(otherRow => {
            if (otherRow !== row) disableEditMode(otherRow);
        });

        row.classList.add('edit-mode');
        
        // Show inputs, hide display values
        row.querySelectorAll('.editable-cell').forEach(cell => {
            const display = cell.querySelector('.display-value');
            const input = cell.querySelector('.edit-input');
            
            if (display && input) {
                // Store original values
                cell._originalDisplay = display.innerHTML;
                cell._originalValue = input.value;
                
                // Switch visibility
                display.style.display = 'none';
                input.style.display = 'block';
            }
        });

        // Update button visibility
        updateButtonVisibility(row);
    }

    function disableEditMode(row) {
        row.classList.remove('edit-mode');
        
        // Show display values, hide inputs
        row.querySelectorAll('.editable-cell').forEach(cell => {
            const display = cell.querySelector('.display-value');
            const input = cell.querySelector('.edit-input');
            
            if (display && input && cell._originalDisplay !== undefined) {
                // Restore original values
                display.innerHTML = cell._originalDisplay;
                input.value = cell._originalValue;
                
                // Switch visibility
                display.style.display = 'block';
                input.style.display = 'none';
            }
        });

        // Update button visibility
        updateButtonVisibility(row);
    }

    function updateButtonVisibility(row) {
        const isEditMode = row.classList.contains('edit-mode');
        
        // Toggle edit button
        const editBtn = row.querySelector('.edit-btn');
        if (editBtn) {
            editBtn.style.display = isEditMode ? 'none' : 'inline-block';
        }
        
        // Toggle save button
        const saveBtn = row.querySelector('.save-btn');
        if (saveBtn) {
            saveBtn.style.display = isEditMode ? 'inline-block' : 'none';
        }
        
        // Toggle cancel button
        const cancelBtn = row.querySelector('.cancel-btn');
        if (cancelBtn) {
            cancelBtn.style.display = isEditMode ? 'inline-block' : 'none';
        }
        
        // Toggle delete button
        const deleteBtn = row.querySelector('.delete-btn');
        if (deleteBtn) {
            deleteBtn.style.display = isEditMode ? 'none' : 'inline-block';
        }
    }

    function saveChanges(row, livestockId) {
        const formData = new FormData();
        formData.append('_method', 'PUT');
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        let hasChanges = false;

        row.querySelectorAll('.editable-cell').forEach(cell => {
            const field = cell.dataset.field;
            const input = cell.querySelector('.edit-input');
            
            if (input && input.value !== cell._originalValue) {
                formData.append(field, input.value);
                hasChanges = true;
            }
        });

        if (!hasChanges) {
            disableEditMode(row);
            showMessage('Tidak ada perubahan data', 'info');
            return;
        }

        // Show loading
        const saveBtn = row.querySelector('.save-btn');
        const originalHtml = saveBtn.innerHTML;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        saveBtn.disabled = true;

        fetch(`/livestock/${livestockId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update display with new values
                updateRowDisplay(row);
                disableEditMode(row);
                showMessage('Data berhasil diperbarui!', 'success');
            } else {
                throw new Error(data.message || 'Gagal menyimpan data');
            }
        })
        .catch(error => {
            console.error('Save error:', error);
            showMessage('Error: ' + error.message, 'error');
        })
        .finally(() => {
            saveBtn.innerHTML = originalHtml;
            saveBtn.disabled = false;
        });
    }

    function updateRowDisplay(row) {
        row.querySelectorAll('.editable-cell').forEach(cell => {
            const field = cell.dataset.field;
            const display = cell.querySelector('.display-value');
            const input = cell.querySelector('.edit-input');
            const value = input ? input.value : '';

            if (!display) return;

            switch(field) {
                case 'sex':
                    display.textContent = value === 'jantan' ? 'Jantan' : 'Betina';
                    break;
                case 'health_status':
                    const healthBadge = value === 'sehat' ? 'bg-success' : 'bg-danger';
                    const healthText = value === 'sehat' ? 'Sehat' : 'Sakit';
                    display.innerHTML = `<span class="badge ${healthBadge}">${healthText}</span>`;
                    
                    // Update row color
                    if (value === 'sakit') {
                        row.classList.add('table-danger');
                    } else {
                        row.classList.remove('table-danger');
                    }
                    break;
                case 'vaccination_status':
                    let vaxClass = '', vaxText = '';
                    switch(value) {
                        case 'up_to_date': vaxClass = 'up_to_date'; vaxText = 'Terkini'; break;
                        case 'need_update': vaxClass = 'need_update'; vaxText = 'Perlu Update'; break;
                        case 'not_vaccinated': vaxClass = 'not_vaccinated'; vaxText = 'Belum Vaksin'; break;
                        default: vaxClass = 'bg-secondary'; vaxText = '-';
                    }
                    display.innerHTML = `<span class="badge vaccination-badge ${vaxClass}">${vaxText}</span>`;
                    break;
                case 'age_weeks':
                    display.textContent = value ? `${value} minggu` : '-';
                    break;
                case 'age_months':
                    display.textContent = value ? `${value} bulan` : '-';
                    break;
                case 'weight_kg':
                    display.textContent = value ? parseFloat(value).toFixed(1) : '-';
                    break;
                case 'daily_feed_kg':
                    display.textContent = value ? parseFloat(value).toFixed(2) : '-';
                    break;
                case 'notes':
                    display.textContent = value ? (value.length > 30 ? value.substring(0, 30) + '...' : value) : '-';
                    break;
                default:
                    display.textContent = value || '-';
            }

            // Update stored original values
            cell._originalDisplay = display.innerHTML;
            cell._originalValue = value;
        });
    }

    function deleteLivestock(livestockId) {
        if (!confirm('Hapus data hewan ternak ini?')) return;

        const formData = new FormData();
        formData.append('_method', 'DELETE');
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        fetch(`/livestock/${livestockId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelector(`tr[data-id="${livestockId}"]`)?.remove();
                showMessage('Data berhasil dihapus!', 'success');
                
                // Reload if no data left
                if (!document.querySelector('.livestock-table tbody tr')) {
                    setTimeout(() => location.reload(), 1000);
                }
            } else {
                showMessage(data.message || 'Gagal menghapus', 'error');
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            showMessage('Error menghapus data', 'error');
        });
    }

    // New Message Box System
    function showMessage(message, type = 'info') {
        // Remove existing message boxes
        document.querySelectorAll('.message-box').forEach(box => {
            box.classList.add('hide');
            setTimeout(() => box.remove(), 300);
        });

        const messageBox = document.createElement('div');
        messageBox.className = `message-box ${type}`;
        
        const icons = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-circle',
            warning: 'fas fa-exclamation-triangle',
            info: 'fas fa-info-circle'
        };

        messageBox.innerHTML = `
            <div class="message-content">
                <div class="message-text">
                    <i class="${icons[type] || icons.info} me-2"></i>
                    ${message}
                </div>
                <button class="close-btn" type="button">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        document.body.appendChild(messageBox);

        // Auto remove after 5 seconds
        const autoRemove = setTimeout(() => {
            messageBox.classList.add('hide');
            setTimeout(() => messageBox.remove(), 300);
        }, 5000);

        // Close button event
        messageBox.querySelector('.close-btn').addEventListener('click', () => {
            clearTimeout(autoRemove);
            messageBox.classList.add('hide');
            setTimeout(() => messageBox.remove(), 300);
        });
    }

    // Initialize - ensure all inputs are hidden and buttons are in correct state
    function initializeTable() {
        document.querySelectorAll('.livestock-row').forEach(row => {
            row.querySelectorAll('.edit-input').forEach(input => {
                input.style.display = 'none';
            });
            row.querySelectorAll('.display-value').forEach(display => {
                display.style.display = 'block';
            });
            
            // Ensure buttons are in correct initial state
            updateButtonVisibility(row);
        });
    }

    // Prevent accidental edits
    document.addEventListener('dblclick', function(e) {
        if (e.target.closest('.editable-cell') && !e.target.closest('button')) {
            e.preventDefault();
        }
    });

    // Initialize on load
    initializeTable();
});
</script>
@endpush