@extends('layouts.app')

@section('title', 'Kelola Vaksinasi - Admin TernakIN')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="content-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Kelola Vaksinasi</h1>
                <p class="page-subtitle">Kelola dan validasi jadwal vaksinasi pengguna</p>
            </div>
            <div class="btn-group">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>

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

    <!-- Stats Cards -->
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
            <span class="stat-number">{{ $vaccinations->where('status', 'rejected')->count() }}</span>
            <span class="stat-label">Ditolak</span>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="filter-section-card">
        <div class="filter-section">
            <form method="GET" action="{{ route('admin.vaccinations.index') }}" class="d-flex gap-3 flex-wrap">
                <div class="search-box">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari vaksinasi..." class="form-control">
                </div>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Validasi</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                </select>
                <select name="user_id" class="form-select">
                    <option value="">Semua User</option>
                    @foreach(\App\Models\User::where('user_type', 'peternak')->get() as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                    @endforeach
                </select>
                <a href="{{ route('admin.vaccinations.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Clear
                </a>
            </form>
        </div>
    </div>

    <!-- Vaccinations Table -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Hewan</th>
                        <th>Vaksin</th>
                        <th>Tanggal Vaksinasi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vaccinations as $index => $vaccination)
                    <tr>
                        <td>{{ $vaccinations->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-container me-2">
                                    @if($vaccination->user->profile_picture)
                                        <img class="avatar-img" src="{{ asset('storage/' . $vaccination->user->profile_picture) }}" alt="{{ $vaccination->user->name }}">
                                    @else
                                        <div class="avatar-placeholder">
                                            <span class="avatar-initial">{{ substr($vaccination->user->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $vaccination->user->name }}</div>
                                    <small class="text-muted">{{ $vaccination->user->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <div class="fw-bold">{{ $vaccination->animalType->name }}</div>
                                <small class="text-muted">
                                    {{ $vaccination->animalType->category == 'poultry' ? 'Unggas' : ($vaccination->animalType->category == 'large_animal' ? 'Ternak Besar' : 'Lainnya') }}
                                </small>
                            </div>
                        </td>
                        <td>{{ $vaccination->vaccine_name }}</td>
                        <td>{{ \Carbon\Carbon::parse($vaccination->vaccination_date)->format('d/m/Y') }}</td>
                        <td>
                            @if($vaccination->status === 'pending')
                                <span class="badge bg-warning">Menunggu Validasi</span>
                            @elseif($vaccination->status === 'approved')
                                <span class="badge bg-success">Disetujui</span>
                            @elseif($vaccination->status === 'rejected')
                                <span class="badge bg-danger">Ditolak</span>
                            @elseif($vaccination->status === 'completed')
                                <span class="badge bg-info">Selesai</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.vaccinations.show', $vaccination) }}" class="action-btn view" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="no-data">
                                <i class="fas fa-syringe fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Belum ada data vaksinasi</h5>
                                <p class="text-muted">Data vaksinasi akan muncul di sini setelah pengguna menambahkan vaksinasi baru.</p>
                                <a href="{{ route('admin.vaccinations.create') }}" class="btn btn-primary mt-2">
                                    <i class="fas fa-plus me-2"></i>Tambah Vaksinasi Pertama
                                </a>
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
                Showing {{ $vaccinations->firstItem() }} to {{ $vaccinations->lastItem() }} of {{ $vaccinations->total() }} vaccinations
            </div>
            {{ $vaccinations->appends(request()->query())->links() }}
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
/* ===== BASE STYLES ===== */
.container-fluid {
    padding: 20px;
    background-color: #f8fafc;
    min-height: calc(100vh - 70px);
}

/* ===== HEADER SECTION ===== */
.content-header {
    background: white;
    padding: 2rem;
    border-radius: 16px;
    margin-bottom: 1.5rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.content-header .d-flex {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1.5rem;
}

@media (max-width: 768px) {
    .content-header .d-flex {
        flex-direction: column;
        gap: 1rem;
    }
}

.page-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0.5rem;
    letter-spacing: -0.025em;
}

.page-subtitle {
    color: #6b7280;
    font-size: 0.95rem;
    line-height: 1.5;
}

/* ===== BUTTON STYLES ===== */
.btn {
    padding: 0.625rem 1.25rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.875rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none;
    white-space: nowrap;
}

.btn-primary {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border: none;
    box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
}

.btn-primary:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
}

.btn-secondary {
    background: #f1f5f9;
    color: #374151;
    border: 1px solid #d1d5db;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.btn-secondary:hover {
    background: #e2e8f0;
    border-color: #9ca3af;
    color: #111827;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.btn-outline-primary {
    background: white;
    color: #10b981;
    border: 1px solid #10b981;
}

.btn-outline-primary:hover {
    background: #10b981;
    color: white;
    transform: translateY(-1px);
}

.btn-outline-secondary {
    background: white;
    color: #6b7280;
    border: 1px solid #d1d5db;
}

.btn-outline-secondary:hover {
    background: #f9fafb;
    border-color: #9ca3af;
    color: #374151;
    transform: translateY(-1px);
}

/* Header button container */
.content-header .btn-group {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    flex-wrap: wrap;
}

@media (max-width: 768px) {
    .content-header .btn-group {
        width: 100%;
        justify-content: flex-start;
    }
}

/* ===== STATS SECTION ===== */
.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.stat-item {
    background: white;
    padding: 1.25rem;
    border-radius: 12px;
    text-align: center;
    border: 1px solid #e5e7eb;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    transition: all 0.2s ease;
}

.stat-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
    margin-bottom: 0.5rem;
    display: block;
}

.stat-label {
    font-size: 0.8125rem;
    color: #6b7280;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* ===== FILTER SECTION ===== */
.filter-section-card {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.filter-section form {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    flex-wrap: wrap;
}

@media (max-width: 768px) {
    .filter-section form {
        flex-direction: column;
        align-items: stretch;
    }

    .filter-section form .btn {
        width: 100%;
        justify-content: center;
    }
}

.search-box {
    position: relative;
    flex: 1;
    min-width: 250px;
}

.search-box input {
    width: 100%;
    padding: 0.625rem 1rem 0.625rem 2.5rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.search-box input:focus {
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    outline: none;
}

.search-box::before {
    content: '\f002';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    position: absolute;
    left: 0.875rem;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
    font-size: 0.875rem;
}

.form-select {
    padding: 0.625rem 2.5rem 0.625rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.875rem;
    background-color: white;
    min-width: 160px;
    cursor: pointer;
    transition: all 0.2s ease;
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.75rem center;
    background-repeat: no-repeat;
    background-size: 16px 12px;
}

.form-select:focus {
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    outline: none;
}

/* ===== TABLE SECTION ===== */
.table-container {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 1.5rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.table-responsive {
    max-height: 60vh;
    overflow-y: auto;
    border-radius: 12px;
}

.table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-bottom: 0;
    font-size: 0.875rem;
}

.table thead th {
    position: sticky;
    top: 0;
    background: linear-gradient(135deg, #1f2937 0%, #374151 100%) !important;
    border: none;
    padding: 1rem;
    font-weight: 600;
    color: white !important;
    border-bottom: 2px solid #4b5563;
    text-align: left;
    white-space: nowrap;
    z-index: 1;
}

.table tbody td {
    padding: 1rem;
    vertical-align: middle;
    border-bottom: 1px solid #f3f4f6;
    background: white;
}

.table tbody tr:hover {
    background-color: #f9fafb;
}

.table tbody tr:last-child td {
    border-bottom: none;
}

/* ===== BADGES ===== */
.badge {
    padding: 0.25rem 0.625rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-block;
}

/* ===== ACTION BUTTONS (SIMPLIFIED) ===== */
.action-buttons {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.action-btn {
    width: 36px;
    height: 36px;
    padding: 0;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid transparent;
    transition: all 0.2s ease;
    text-decoration: none;
    cursor: pointer;
    font-size: 0.875rem;
}

.action-btn.view {
    background: #f0f9ff;
    color: #0ea5e9;
    border-color: #bae6fd;
}

.action-btn.view:hover {
    background: #e0f2fe;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(14, 165, 233, 0.2);
}

/* ===== NO DATA STATE ===== */
.no-data {
    text-align: center;
    padding: 3rem 1rem;
    color: #6b7280;
}

.no-data i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.3;
}

.no-data h5 {
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #374151;
    font-size: 1.125rem;
}

.no-data p {
    font-size: 0.875rem;
    margin-bottom: 1.5rem;
    max-width: 300px;
    margin-left: auto;
    margin-right: auto;
}

/* ===== PAGINATION ===== */
.pagination-wrapper {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    margin-top: 1rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.pagination-info {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
}

/* ===== SCROLLBAR STYLING ===== */
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

/* ===== ALERTS ===== */
.alert {
    border-radius: 10px;
    border: none;
    padding: 1rem 1.5rem;
    margin-bottom: 1.5rem;
    border-left: 4px solid transparent;
}

.alert-success {
    background-color: #d1fae5;
    color: #065f46;
    border-left-color: #10b981;
}

.alert-danger {
    background-color: #fee2e2;
    color: #991b1b;
    border-left-color: #ef4444;
}

.btn-close {
    padding: 0.75rem;
    opacity: 0.5;
}

.btn-close:hover {
    opacity: 0.75;
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 1024px) {
    .stats-container {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .container-fluid {
        padding: 15px;
    }

    .content-header {
        padding: 1.5rem;
    }

    .page-title {
        font-size: 1.5rem;
    }

    .table-responsive {
        max-height: 55vh;
    }

    .table thead th,
    .table tbody td {
        padding: 0.75rem 0.5rem;
    }

    .action-buttons {
        flex-wrap: wrap;
        justify-content: center;
    }

    .form-select {
        width: 100%;
        min-width: auto;
    }

    .search-box {
        min-width: auto;
    }
}

@media (max-width: 576px) {
    .container-fluid {
        padding: 10px;
    }

    .content-header {
        padding: 1.25rem;
    }

    .page-title {
        font-size: 1.375rem;
    }

    .stats-container {
        grid-template-columns: 1fr;
    }

    .stat-item {
        padding: 1rem;
    }

    .stat-number {
        font-size: 1.75rem;
    }

    .table-responsive {
        max-height: 50vh;
        font-size: 0.8125rem;
    }

    .badge {
        font-size: 0.7rem;
        padding: 0.2rem 0.5rem;
    }

    .action-btn {
        width: 32px;
        height: 32px;
        font-size: 0.8125rem;
    }
}

/* Fix for table header on mobile */
@media (max-width: 768px) {
    .table-responsive {
        overflow-x: auto;
    }

    .table {
        min-width: 800px;
    }
}

/* Additional styling for table content */
.fw-bold {
    font-weight: 600 !important;
}

.text-muted {
    color: #6b7280 !important;
}

.d-flex.flex-wrap.gap-1 {
    gap: 0.25rem !important;
}

/* Ensure form buttons don't have default button styling */
button.action-btn {
    background: transparent;
    border: none;
}

/* Tooltip-like hover titles */
[title] {
    position: relative;
}

[title]:hover::after {
    content: attr(title);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: #1f2937;
    color: white;
    padding: 0.375rem 0.75rem;
    border-radius: 4px;
    font-size: 0.75rem;
    white-space: nowrap;
    z-index: 1000;
    margin-bottom: 0.5rem;
}

[title]:hover::before {
    content: '';
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    border: 4px solid transparent;
    border-top-color: #1f2937;
    margin-bottom: -0.25rem;
    z-index: 1000;
}

/* ===== AVATAR STYLES ===== */
.avatar-container {
    position: relative;
}

.avatar-img {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #e2e8f0;
    transition: border-color 0.3s ease;
}

.avatar-img:hover {
    border-color: #10b981;
}

.avatar-placeholder {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #e2e8f0;
}

.avatar-initial {
    color: #6b7280;
    font-weight: 700;
    font-size: 0.9rem;
    text-transform: uppercase;
}

/* ===== MODAL ===== */
.modal-content {
    border: none;
    border-radius: 12px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
    border-bottom: 1px solid #e5e7eb;
    padding: 1.5rem;
}

.modal-title {
    font-weight: 700;
    color: #1f2937;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    border-top: 1px solid #e5e7eb;
    padding: 1.5rem;
}
</style>
@endpush

@push('scripts')
<script>
function openValidationModal(vaccinationId, action) {
    const modal = new bootstrap.Modal(document.getElementById('validationModal'));
    const form = document.getElementById('validationForm');
    const confirmBtn = document.getElementById('confirmBtn');

    // Set form action
    form.action = `/admin/vaccinations/${vaccinationId}/validate`;

    // Add hidden input for action
    let actionInput = form.querySelector('input[name="action"]');
    if (!actionInput) {
        actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        form.appendChild(actionInput);
    }
    actionInput.value = action;

    // Update modal title and button text
    const modalTitle = document.getElementById('validationModalLabel');
    if (action === 'approve') {
        modalTitle.textContent = 'Setujui Vaksinasi';
        confirmBtn.textContent = 'Setujui';
        confirmBtn.className = 'btn btn-success';
    } else {
        modalTitle.textContent = 'Tolak Vaksinasi';
        confirmBtn.textContent = 'Tolak';
        confirmBtn.className = 'btn btn-danger';
    }

    // Clear form fields
    document.getElementById('admin_notes').value = '';
    document.getElementById('admin_recommendations').value = '';

    modal.show();
}
</script>
@endpush
