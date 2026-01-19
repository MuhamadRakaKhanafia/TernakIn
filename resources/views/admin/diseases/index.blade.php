@extends('layouts.app')

@section('title', 'Manage Diseases')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="content-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Manage Diseases</h1>
                <p class="page-subtitle">Manage disease information and related data</p>
            </div>
            <div class="btn-group">
                <a href="{{ route('admin.diseases.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Disease
                </a>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
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
            <span class="stat-number">{{ $totalDiseases }}</span>
            <span class="stat-label">Total Diseases</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">{{ $zoonoticDiseases }}</span>
            <span class="stat-label">Zoonotic Diseases</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">{{ $activeDiseases }}</span>
            <span class="stat-label">Active Diseases</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">{{ $inactiveDiseases }}</span>
            <span class="stat-label">Inactive Diseases</span>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="filter-section-card">
        <div class="filter-section">
            <form method="GET" action="{{ route('admin.diseases.index') }}" class="d-flex gap-3 flex-wrap">
                <div class="search-box">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search diseases..." class="form-control">
                </div>
                <select name="causative_agent" class="form-select">
                    <option value="">All Causative Agents</option>
                    <option value="virus" {{ request('causative_agent') == 'virus' ? 'selected' : '' }}>Virus</option>
                    <option value="bakteri" {{ request('causative_agent') == 'bakteri' ? 'selected' : '' }}>Bakteri</option>
                    <option value="parasit" {{ request('causative_agent') == 'parasit' ? 'selected' : '' }}>Parasit</option>
                    <option value="fungi" {{ request('causative_agent') == 'fungi' ? 'selected' : '' }}>Fungi</option>
                    <option value="defisiensi_nutrisi" {{ request('causative_agent') == 'defisiensi_nutrisi' ? 'selected' : '' }}>Defisiensi Nutrisi</option>
                    <option value="lainnya" {{ request('causative_agent') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fas fa-search me-2"></i>Filter
                </button>
                <a href="{{ route('admin.diseases.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Clear
                </a>
            </form>
        </div>
    </div>

    <!-- Diseases Table -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Disease Code</th>
                        <th>Name</th>
                        <th>Causative Agent</th>
                        <th>Animal Types</th>
                        <th>Symptoms</th>
                        <th>Status</th>
                        <th>Zoonotic</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($diseases as $index => $disease)
                    <tr>
                        <td>{{ $diseases->firstItem() + $index }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ $disease->disease_code }}</span>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $disease->name }}</div>
                            @if($disease->other_names)
                            <small class="text-muted">{{ Str::limit($disease->other_names, 30) }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-info">
                                {{ ucfirst(str_replace('_', ' ', $disease->causative_agent)) }}
                            </span>
                        </td>
                        <td>
                            @if($disease->animalTypes->count() > 0)
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($disease->animalTypes->take(2) as $animalType)
                                        <span class="badge bg-success">{{ $animalType->name }}</span>
                                    @endforeach
                                    @if($disease->animalTypes->count() > 2)
                                        <span class="badge bg-secondary">+{{ $disease->animalTypes->count() - 2 }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($disease->symptoms->count() > 0)
                                <span class="badge bg-warning">{{ $disease->symptoms->count() }} symptoms</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($disease->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            @if($disease->is_zoonotic)
                                <span class="badge bg-danger">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Zoonotic
                                </span>
                            @else
                                <span class="badge bg-light text-dark">No</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('diseases.show', $disease) }}" class="action-btn view" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.diseases.edit', $disease) }}" class="action-btn edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.diseases.destroy', $disease) }}" method="POST" class="delete-form"
                                      onsubmit="return confirm('Are you sure you want to delete this disease?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn delete" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="no-data">
                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No diseases found</h5>
                                <p class="text-muted">Try adjusting your search criteria or create a new disease.</p>
                                <a href="{{ route('admin.diseases.create') }}" class="btn btn-primary mt-2">
                                    <i class="fas fa-plus me-2"></i>Add New Disease
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
    @if($diseases->hasPages())
    <div class="pagination-wrapper">
        <div class="d-flex justify-content-between align-items-center">
            <div class="pagination-info">
                Showing {{ $diseases->firstItem() }} to {{ $diseases->lastItem() }} of {{ $diseases->total() }} diseases
            </div>
            {{ $diseases->appends(request()->query())->links() }}
        </div>
    </div>
    @endif
</div>

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

.action-btn.edit {
    background: #fefce8;
    color: #f59e0b;
    border-color: #fde68a;
}

.action-btn.edit:hover {
    background: #fef3c7;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(245, 158, 11, 0.2);
}

.action-btn.delete {
    background: #fef2f2;
    color: #ef4444;
    border-color: #fecaca;
}

.action-btn.delete:hover {
    background: #fee2e2;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(239, 68, 68, 0.2);
}

/* Delete form styling */
.delete-form {
    margin: 0;
    display: inline;
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
</style>
@endsection