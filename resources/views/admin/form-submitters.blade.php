@extends('layouts.app')

@section('title', 'Form Submitters')

@section('content')
@if(isset($user))
    <!-- Show specific user's livestock data -->
    <div class="container-fluid">
        <!-- Header -->
        <div class="content-header">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h1 class="page-title">Data Hewan Ternak</h1>
                    <div class="user-details mb-3">
                        <div class="fw-bold fs-5">{{ $user->name }}</div>
                        <div class="text-muted">{{ $user->email }}</div>
                        <div class="mt-2">
                            <i class="fas fa-map-marker-alt text-muted me-2"></i>
                            {{ $user->location->province->name ?? 'Unknown Province' }},
                            {{ $user->location->city->name ?? 'Unknown City' }}
                        </div>
                    </div>
                </div>
                <a href="{{ route('admin.form-submitters') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
                </a>
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

        <!-- Filter Section -->
        <div class="filter-section-card">
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
                                    <p>Pengguna ini belum menginput data hewan ternak</p>
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
@else
    <!-- Show all users (form submitters) -->
    <div class="container-fluid">
        <!-- Header -->
        <div class="content-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">Form Submitters</h1>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-container">
            <div class="stat-item">
                <span class="stat-number">{{ $users->total() }}</span>
                <span class="stat-label">Total Users</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $users->where('is_active', true)->count() }}</span>
                <span class="stat-label">Active Users</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $totalLivestock }}</span>
                <span class="stat-label">Total Livestock</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $healthyLivestock }}</span>
                <span class="stat-label">Healthy Livestock</span>
            </div>
        </div>

        <!-- Users Table -->
        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>User Profile</th>
                            <th>Contact Info</th>
                            <th>Location</th>
                            <th>Livestock Count</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-container me-2">
                                        @if($user->profile_picture)
                                            <img class="avatar-img" src="{{ asset('storage/' . $user->profile_picture) }}" alt="{{ $user->name }}">
                                        @else
                                            <div class="avatar-placeholder">
                                                <span class="avatar-initial">{{ substr($user->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $user->name }}</div>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="mb-1">
                                    <i class="fas fa-envelope text-muted me-2"></i>
                                    {{ $user->email }}
                                </div>
                                <div>
                                    <i class="fas fa-phone text-muted me-2"></i>
                                    {{ $user->phone ?? 'Not provided' }}
                                </div>
                            </td>
                            <td>
                                <div class="mb-1">
                                    <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                    {{ $user->location->province->name ?? 'Unknown Province' }}
                                </div>
                                <small class="text-muted">{{ $user->location->city->name ?? 'Unknown City' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-primary fs-6">{{ $user->livestocks->count() }} animals</span>
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                                @if($user->email_verified_at)
                                    <div class="mt-1">
                                        <i class="fas fa-shield-alt text-success" title="Email Verified"></i>
                                        <small class="text-success">Verified</small>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.form-submitters.user', $user->id) }}" class="action-btn view" title="View Livestock">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="no-data">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Users Found</h5>
                                    <p class="text-muted">There are no registered users in the system yet.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
        <div class="pagination-wrapper">
            <div class="d-flex justify-content-between align-items-center">
                <div class="pagination-info">
                    Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                </div>
                {{ $users->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editUserForm">
                    <div class="modal-body">
                        <input type="hidden" id="editUserId" name="user_id">
                        <div class="mb-3">
                            <label for="editUserName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="editUserName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editUserEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editUserEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="editUserPhone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="editUserPhone" name="phone">
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="editUserActive" name="is_active">
                                <label class="form-check-label" for="editUserActive">
                                    Active User
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection

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

.filter-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
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

.action-btn.edit-user-btn {
    background: #fef3c7;
    color: #d97706;
    border-color: #fcd34d;
    pointer-events: auto;
    position: relative;
    z-index: 100;
    cursor: pointer;
}

.action-btn.edit-user-btn:hover {
    background: #fde68a;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(217, 119, 6, 0.2);
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

/* Ensure form buttons don't have default button styling - but preserve specific button styles */
button.action-btn {
    /* Remove background and border reset that was interfering with specific button styles */
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

/* Edit User Modal Styles */
.modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.modal-header {
    border-bottom: 1px solid #e5e7eb;
    padding: 1.5rem;
}

.modal-title {
    font-weight: 600;
    color: #1f2937;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    border-top: 1px solid #e5e7eb;
    padding: 1.5rem;
}

.form-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.form-control {
    border: 1px solid #d1d5db;
    border-radius: 8px;
    padding: 0.625rem 0.75rem;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.form-control:focus {
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    outline: none;
}

.form-check {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-check-input {
    width: 1rem;
    height: 1rem;
    border-radius: 4px;
    border: 1px solid #d1d5db;
    background-color: white;
    cursor: pointer;
}

.form-check-input:checked {
    background-color: #10b981;
    border-color: #10b981;
}

.form-check-label {
    font-size: 0.875rem;
    color: #374151;
    cursor: pointer;
    margin-bottom: 0;
}

/* ===== AVATAR STYLES ===== */
.avatar-container {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
}

.avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.avatar-initial {
    color: white;
    font-weight: 600;
    font-size: 1rem;
    text-transform: uppercase;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle edit user button click
    document.querySelectorAll('.edit-user-btn').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const userName = this.getAttribute('data-user-name');
            const userEmail = this.getAttribute('data-user-email');
            const userPhone = this.getAttribute('data-user-phone');
            const userActive = this.getAttribute('data-user-active') === '1';

            // Populate modal with user data
            document.getElementById('editUserId').value = userId;
            document.getElementById('editUserName').value = userName;
            document.getElementById('editUserEmail').value = userEmail;
            document.getElementById('editUserPhone').value = userPhone || '';
            document.getElementById('editUserActive').checked = userActive;

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
            modal.show();
        });
    });

    // Handle form submission
    document.getElementById('editUserForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const userId = formData.get('user_id');

        // Send AJAX request
        fetch(`/admin/users/${userId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                name: formData.get('name'),
                email: formData.get('email'),
                phone: formData.get('phone'),
                is_active: formData.get('is_active') ? true : false,
                _method: 'PUT'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('editUserModal'));
                modal.hide();

                // Show success message
                showAlert('User updated successfully!', 'success');

                // Reload page to show updated data
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showAlert(data.message || 'Failed to update user.', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('An error occurred while updating the user.', 'danger');
        });
    });

    // Handle inline edit for livestock
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const editableCells = row.querySelectorAll('.editable-cell');

            // Show edit inputs and hide display values
            editableCells.forEach(cell => {
                const displayValue = cell.querySelector('.display-value');
                const editInput = cell.querySelector('.edit-input');

                if (displayValue && editInput) {
                    displayValue.style.display = 'none';
                    editInput.style.display = 'block';
                }
            });

            // Hide edit button, show save and cancel buttons
            this.style.display = 'none';
            row.querySelector('.save-btn').style.display = 'inline-flex';
            row.querySelector('.cancel-btn').style.display = 'inline-flex';
        });
    });

    // Handle save button for livestock
    document.querySelectorAll('.save-btn').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const livestockId = row.getAttribute('data-id');
            const editableCells = row.querySelectorAll('.editable-cell');
            const formData = new FormData();

            // Collect updated data
            editableCells.forEach(cell => {
                const field = cell.getAttribute('data-field');
                const editInput = cell.querySelector('.edit-input');

                if (editInput) {
                    let value = editInput.value;

                    // Handle checkbox for health_status
                    if (field === 'health_status') {
                        value = editInput.value;
                    }

                    // Only append non-empty values
                    if (value !== '' && value !== null && value !== undefined) {
                        formData.append(field, value);
                    }
                }
            });

            // Send AJAX request to update livestock
            fetch(`/admin/livestocks/${livestockId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update display values
                    editableCells.forEach(cell => {
                        const field = cell.getAttribute('data-field');
                        const displayValue = cell.querySelector('.display-value');
                        const editInput = cell.querySelector('.edit-input');

                        if (displayValue && editInput) {
                            let newValue = editInput.value;

                            // Format display values
                            if (field === 'weight_kg' && newValue) {
                                newValue = parseFloat(newValue).toFixed(1);
                            } else if (field === 'daily_feed_kg' && newValue) {
                                newValue = parseFloat(newValue).toFixed(2);
                            } else if (field === 'age_weeks' && newValue) {
                                newValue = newValue + ' minggu';
                            } else if (field === 'age_months' && newValue) {
                                newValue = newValue + ' bulan';
                            } else if (field === 'sex') {
                                newValue = newValue === 'jantan' ? 'Jantan' : 'Betina';
                            } else if (field === 'health_status') {
                                newValue = newValue === 'sehat' ?
                                    '<span class="badge bg-success">Sehat</span>' :
                                    '<span class="badge bg-danger">Sakit</span>';
                            } else if (field === 'vaccination_status') {
                                const badges = {
                                    'up_to_date': '<span class="badge vaccination-badge up_to_date">Terkini</span>',
                                    'need_update': '<span class="badge vaccination-badge need_update">Perlu Update</span>',
                                    'not_vaccinated': '<span class="badge vaccination-badge not_vaccinated">Belum Vaksin</span>'
                                };
                                newValue = badges[newValue] || '<span class="badge bg-secondary">-</span>';
                            } else if (!newValue) {
                                newValue = '-';
                            }

                            displayValue.innerHTML = newValue;
                            displayValue.style.display = 'block';
                            editInput.style.display = 'none';
                        }
                    });

                    // Hide save and cancel buttons, show edit button
                    this.style.display = 'none';
                    row.querySelector('.cancel-btn').style.display = 'none';
                    row.querySelector('.edit-btn').style.display = 'inline-flex';

                    showAlert('Livestock updated successfully!', 'success');
                } else {
                    showAlert(data.message || 'Failed to update livestock.', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('An error occurred while updating the livestock.', 'danger');
            });
        });
    });

    // Handle cancel button for livestock
    document.querySelectorAll('.cancel-btn').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const editableCells = row.querySelectorAll('.editable-cell');

            // Reset values and hide edit inputs
            editableCells.forEach(cell => {
                const displayValue = cell.querySelector('.display-value');
                const editInput = cell.querySelector('.edit-input');

                if (displayValue && editInput) {
                    // Reset input value to original
                    editInput.value = cell.getAttribute('data-value');
                    displayValue.style.display = 'block';
                    editInput.style.display = 'none';
                }
            });

            // Hide save and cancel buttons, show edit button
            this.style.display = 'none';
            row.querySelector('.save-btn').style.display = 'none';
            row.querySelector('.edit-btn').style.display = 'inline-flex';
        });
    });

    // Handle delete button for livestock
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const livestockId = this.getAttribute('data-id');

            if (confirm('Are you sure you want to delete this livestock?')) {
                // Send AJAX request to delete livestock
                fetch(`/admin/livestocks/${livestockId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        _method: 'DELETE'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the row from the table
                        this.closest('tr').remove();
                        showAlert('Livestock deleted successfully!', 'success');
                    } else {
                        showAlert(data.message || 'Failed to delete livestock.', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('An error occurred while deleting the livestock.', 'danger');
                });
            }
        });
    });

    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.setAttribute('role', 'alert');
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        // Insert at the top of the container
        const container = document.querySelector('.container-fluid');
        container.insertBefore(alertDiv, container.firstChild);

        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
});
</script>
