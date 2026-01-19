@extends('layouts.app')

@section('title', 'Hewan Ternak - TernakIN')
@section('page-title', 'Hewan Ternak')

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
            <select id="typeFilter" class="form-select">
                <option value="">Semua Jenis Hewan</option>
                @foreach($animalTypes as $type)
                <option value="{{ $type->id }}" {{ request('animal_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
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
            <a href="{{ route('livestocks.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Hewan Ternak
            </a>
        </div>
    </div>

    <!-- Statistik Hewan Ternak -->
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
                        
                        // Calculate age based on category
                        $ageDisplay = '-';
                        if ($isPoultry && $livestock->age_weeks) {
                            $ageDisplay = $livestock->age_weeks . ' minggu';
                        } elseif (!$isPoultry && $livestock->age_months) {
                            $ageDisplay = $livestock->age_months . ' bulan';
                        }
                    @endphp
                    <tr data-id="{{ $livestock->id }}" class="livestock-row {{ $livestock->health_status == 'sakit' ? 'table-danger' : '' }}">
                        <td>{{ $livestocks->firstItem() + $index }}</td>
                        <td>
                            {{ $livestock->name ?: '-' }}
                            @if($livestock->identification_number)
                            <small class="text-muted d-block">ID: {{ $livestock->identification_number }}</small>
                            @endif
                        </td>
                        <td>
                            @if($animalType)
                                <span class="animal-type-badge">{{ $animalType->name }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            {{ $livestock->sex == 'jantan' ? 'Jantan' : 'Betina' }}
                        </td>
                        <td>
                            {{ $ageDisplay }}
                        </td>
                        <td>
                            <div class="editable-cell" data-field="weight_kg" data-value="{{ $livestock->weight_kg ?? '' }}">
                                <span class="display-value">
                                    {{ $livestock->weight_kg ? number_format($livestock->weight_kg, 1) : '-' }}
                                </span>
                                <input type="number" step="0.01" class="edit-input" value="{{ $livestock->weight_kg }}" min="0.1" style="display: none;" placeholder="0.0">
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
                                <input type="text" class="edit-input" value="{{ $livestock->feed_type }}" style="display: none;" placeholder="Jenis pakan">
                            </div>
                        </td>
                        <td>
                            <div class="editable-cell" data-field="daily_feed_kg" data-value="{{ $livestock->daily_feed_kg ?? '' }}">
                                <span class="display-value">
                                    {{ $livestock->daily_feed_kg ? number_format($livestock->daily_feed_kg, 2) : '-' }}
                                </span>
                                <input type="number" step="0.01" class="edit-input" value="{{ $livestock->daily_feed_kg }}" min="0.01" style="display: none;" placeholder="0.00">
                            </div>
                        </td>
                        <td>
                            <div class="editable-cell" data-field="housing_type" data-value="{{ $livestock->housing_type ?? '' }}">
                                <span class="display-value">{{ $livestock->housing_type ?: '-' }}</span>
                                <input type="text" class="edit-input" value="{{ $livestock->housing_type }}" style="display: none;" placeholder="Tipe kandang">
                            </div>
                        </td>
                        <td>
                            <div class="editable-cell" data-field="notes" data-value="{{ $livestock->notes ?? '' }}">
                                <span class="display-value">
                                    {{ $livestock->notes ? Str::limit($livestock->notes, 30) : '-' }}
                                </span>
                                <textarea class="edit-input" rows="2" style="display: none;" placeholder="Catatan tambahan">{{ $livestock->notes }}</textarea>
                            </div>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $livestock->id }}" title="Hapus">
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
.livestock-table td:nth-child(13) { min-width: 140px; max-width: 160px; }

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
    flex-direction: column;
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
    padding: 0.5rem;
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

/* Animal Type Badge */
.animal-type-badge {
    background: #e0f2fe;
    color: #0369a1;
    padding: 0.25rem 0.75rem;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
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

/* Message Box System */
.message-box {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    min-width: 300px;
    max-width: 400px;
    border-radius: 8px;
    padding: 1rem 1.5rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    animation: slideIn 0.3s ease;
    border-left: 4px solid;
}

.message-box.success {
    background: #f0fdf4;
    border-left-color: #10b981;
    color: #065f46;
}

.message-box.error {
    background: #fef2f2;
    border-left-color: #ef4444;
    color: #991b1b;
}

.message-box.info {
    background: #eff6ff;
    border-left-color: #3b82f6;
    color: #1e40af;
}

.message-box.warning {
    background: #fffbeb;
    border-left-color: #f59e0b;
    color: #92400e;
}

.message-box.hide {
    animation: slideOut 0.3s ease;
    opacity: 0;
    transform: translateX(100%);
}

.message-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.message-text {
    flex: 1;
    font-weight: 500;
}

.close-btn {
    background: none;
    border: none;
    color: inherit;
    opacity: 0.7;
    cursor: pointer;
    padding: 0.25rem;
    margin-left: 1rem;
}

.close-btn:hover {
    opacity: 1;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(100%);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideOut {
    from {
        opacity: 1;
        transform: translateX(0);
    }
    to {
        opacity: 0;
        transform: translateX(100%);
    }
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
    // Initialize CSRF token for AJAX
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Filter functionality
    const typeFilter = document.getElementById('typeFilter');
    const healthFilter = document.getElementById('healthFilter');
    const vaccinationFilter = document.getElementById('vaccinationFilter');

    function applyFilters() {
        const params = new URLSearchParams(window.location.search);

        if (typeFilter.value) params.set('animal_type_id', typeFilter.value);
        else params.delete('animal_type_id');

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

    // Edit functionality
    function enableEditMode(row) {
        // Exit edit mode for other rows
        document.querySelectorAll('.livestock-row.edit-mode').forEach(otherRow => {
            if (otherRow !== row) disableEditMode(otherRow);
        });

        row.classList.add('edit-mode');
        
        // Store original values and show inputs
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
                
                // Adjust input width
                if (input.tagName === 'INPUT' || input.tagName === 'SELECT') {
                    input.style.width = '100%';
                    input.style.padding = '0.25rem';
                    input.style.minHeight = '2rem';
                }
                
                if (input.tagName === 'TEXTAREA') {
                    input.style.width = '100%';
                    input.style.minHeight = '3rem';
                }
                
                // Focus on first input
                if (input.tagName === 'INPUT' && input.type !== 'hidden') {
                    setTimeout(() => {
                        input.focus();
                        input.select();
                    }, 10);
                }
            }
        });

        // Update button visibility
        updateButtonVisibility(row);
    }

    function disableEditMode(row) {
        row.classList.remove('edit-mode');
        
        // Restore original values and hide inputs
        row.querySelectorAll('.editable-cell').forEach(cell => {
            const display = cell.querySelector('.display-value');
            const input = cell.querySelector('.edit-input');
            
            if (display && input && cell._originalDisplay !== undefined) {
                // Restore original values
                display.innerHTML = cell._originalDisplay;
                
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
        
        // Toggle buttons based on edit mode
        const editBtn = row.querySelector('.edit-btn');
        const saveBtn = row.querySelector('.save-btn');
        const cancelBtn = row.querySelector('.cancel-btn');
        const deleteBtn = row.querySelector('.delete-btn');
        
        if (editBtn) editBtn.style.display = isEditMode ? 'none' : 'inline-block';
        if (saveBtn) saveBtn.style.display = isEditMode ? 'inline-block' : 'none';
        if (cancelBtn) cancelBtn.style.display = isEditMode ? 'inline-block' : 'none';
        if (deleteBtn) deleteBtn.style.display = isEditMode ? 'none' : 'inline-block';
    }

    function saveChanges(row, livestockId) {
        const formData = new FormData();
        formData.append('_method', 'PUT');
        formData.append('_token', csrfToken);

        let hasChanges = false;
        const changes = {};

        // Collect all changed data
        row.querySelectorAll('.editable-cell').forEach(cell => {
            const field = cell.dataset.field;
            const input = cell.querySelector('.edit-input');
            
            if (input) {
                let value = input.value;
                
                // Handle empty values
                if (value === '') {
                    value = null;
                }
                
                // Handle number inputs
                if (input.type === 'number') {
                    value = value ? parseFloat(value) : null;
                }
                
                // Only add if value changed
                if (JSON.stringify(value) !== JSON.stringify(cell._originalValue)) {
                    formData.append(field, value);
                    changes[field] = value;
                    hasChanges = true;
                }
            }
        });

        if (!hasChanges) {
            disableEditMode(row);
            showMessage('Tidak ada perubahan data', 'info');
            return;
        }

        // Show loading state
        const saveBtn = row.querySelector('.save-btn');
        const originalHtml = saveBtn.innerHTML;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        saveBtn.disabled = true;

        // Send AJAX request
        fetch(`/livestock/${livestockId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.status === 403) {
                throw new Error('Anda tidak memiliki izin untuk mengubah data ini');
            }
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || `HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Update display with new values
                updateRowDisplay(row, changes);
                disableEditMode(row);
                showMessage('Data berhasil diperbarui!', 'success');
                
                // Update stats
                updateStats();
            } else {
                throw new Error(data.message || 'Gagal menyimpan data');
            }
        })
        .catch(error => {
            console.error('Save error:', error);
            showMessage('Error: ' + error.message, 'error');
            disableEditMode(row);
        })
        .finally(() => {
            saveBtn.innerHTML = originalHtml;
            saveBtn.disabled = false;
        });
    }

    function updateRowDisplay(row, changes) {
        row.querySelectorAll('.editable-cell').forEach(cell => {
            const field = cell.dataset.field;
            const display = cell.querySelector('.display-value');
            const input = cell.querySelector('.edit-input');
            
            if (!display || !input) return;
            
            const value = changes[field] !== undefined ? changes[field] : input.value;
            
            // Update display based on field type
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
                case 'feed_type':
                case 'housing_type':
                    display.textContent = value || '-';
                    break;
                default:
                    display.textContent = value || '-';
            }
            
            // Update stored original values
            cell._originalDisplay = display.innerHTML;
            cell._originalValue = value;
            input.value = value;
        });
    }

    function updateStats() {
        fetch('/livestock/stats', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const stats = document.querySelectorAll('.stat-number');
                if (stats.length >= 4) {
                    stats[0].textContent = data.data.total;
                    stats[1].textContent = data.data.healthy;
                    stats[2].textContent = data.data.needVaccination;
                    stats[3].textContent = data.data.sick;
                }
            }
        })
        .catch(error => console.error('Stats update error:', error));
    }

    function deleteLivestock(livestockId) {
        if (!confirm('Apakah Anda yakin ingin menghapus data hewan ternak ini?')) return;

        const formData = new FormData();
        formData.append('_method', 'DELETE');
        formData.append('_token', csrfToken);

        const row = document.querySelector(`tr[data-id="${livestockId}"]`);
        if (row) {
            row.style.opacity = '0.5';
            row.style.transition = 'opacity 0.3s ease';
        }

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
                if (row) {
                    row.remove();
                    showMessage('Data berhasil dihapus!', 'success');
                    updateStats();
                    
                    // Check if table is empty
                    if (!document.querySelector('.livestock-table tbody tr')) {
                        setTimeout(() => location.reload(), 1000);
                    }
                }
            } else {
                throw new Error(data.message || 'Gagal menghapus data');
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            showMessage('Error: ' + error.message, 'error');
            if (row) {
                row.style.opacity = '1';
            }
        });
    }

    // Message Box System
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

    // Event delegation for table actions
    document.addEventListener('click', function(e) {
        // Edit button
        if (e.target.closest('.edit-btn')) {
            e.preventDefault();
            const row = e.target.closest('.livestock-row');
            enableEditMode(row);
        }
        
        // Save button
        if (e.target.closest('.save-btn')) {
            e.preventDefault();
            const row = e.target.closest('.livestock-row');
            const livestockId = row.dataset.id;
            saveChanges(row, livestockId);
        }
        
        // Cancel button
        if (e.target.closest('.cancel-btn')) {
            e.preventDefault();
            const row = e.target.closest('.livestock-row');
            disableEditMode(row);
        }
        
        // Delete button
        if (e.target.closest('.delete-btn')) {
            e.preventDefault();
            const livestockId = e.target.closest('.delete-btn').dataset.id;
            deleteLivestock(livestockId);
        }
    });

    // Initialize table on load
    function initializeTable() {
        document.querySelectorAll('.livestock-row').forEach(row => {
            // Store original values
            row.querySelectorAll('.editable-cell').forEach(cell => {
                const display = cell.querySelector('.display-value');
                const input = cell.querySelector('.edit-input');
                
                if (display && input) {
                    cell._originalDisplay = display.innerHTML;
                    cell._originalValue = input.value;
                }
            });
            
            // Set initial button states
            updateButtonVisibility(row);
        });
    }

    // Initialize on page load
    initializeTable();
});
</script>
@endpush