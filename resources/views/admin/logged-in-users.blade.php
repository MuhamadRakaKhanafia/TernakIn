@extends('layouts.app')

@section('title', 'Logged-in Users')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="content-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Registered Users</h1>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-container">
        <div class="stat-item">
            <span class="stat-number">{{ $users->total() }}</span>
            <span class="stat-label">Total Registered</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">{{ $users->where('is_active', 1)->count() }}</span>
            <span class="stat-label">Active Users</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">{{ $users->where('user_type', 'admin')->count() }}</span>
            <span class="stat-label">Administrators</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">{{ $users->where('email_verified_at', '!=', null)->count() }}</span>
            <span class="stat-label">Verified Emails</span>
        </div>
    </div>

    <!-- Users Table -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>User Profile</th>
                        <th>Telephone</th>
                        <th>Email</th>
                        <th>Location</th>
                        <th>Account Details</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="user-profile">
                                <div class="avatar-container">
                                    @if($user->profile_picture)
                                        <img class="avatar-img" src="{{ asset('storage/' . $user->profile_picture) }}" alt="{{ $user->name }}">
                                    @else
                                        <div class="avatar-placeholder">
                                            <span class="avatar-initial">{{ substr($user->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="user-info">
                                    <div class="user-name">{{ $user->name }}</div>
                                    <div class="user-role">
                                        <span class="badge {{ $user->user_type === 'admin' ? 'bg-warning text-dark' : 'bg-light text-dark' }}">
                                            <i class="fas {{ $user->user_type === 'admin' ? 'fa-crown' : 'fa-user' }} me-1"></i>
                                            {{ ucfirst($user->user_type) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="Telephone">
                                <div class="contact-userphone">
                                    <i class="fas fa-phone text-muted me-2"></i>
                                    {{ $user->phone ?? 'N/A' }}
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="contact-info">
                                <div class="contact-email">
                                    <i class="fas fa-envelope text-muted me-2"></i>
                                    {{ $user->email }}
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="location-info">
                                <div class="location-province">
                                    <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                    {{ $user->location->province->name ?? 'Unknown Province' }}
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="account-details">
                                <div class="registration-date">
                                    <i class="fas fa-calendar-plus text-muted me-2"></i>
                                    <span class="date-text">{{ $user->created_at->format('M j, Y') }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="status-container">
                                <span class="status-badge {{ $user->is_active ? 'status-active' : 'status-inactive' }}">
                                    <i class="fas {{ $user->is_active ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="no-data">
                                <i class="fas fa-users fa-4x text-muted mb-4"></i>
                                <h4 class="text-muted mb-2">No Users Found</h4>
                                <p class="text-muted mb-0">There are no registered users in the system yet.</p>
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
                <i class="fas fa-list-ol me-2"></i>
                Showing <strong>{{ $users->firstItem() }}</strong> to <strong>{{ $users->lastItem() }}</strong> of <strong>{{ $users->total() }}</strong> users
            </div>
            {{ $users->appends(request()->query())->links() }}
        </div>
    </div>
    @endif
</div>

<style>
.content-header {
    background: white;
    padding: 2rem;
    border-radius: 20px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    margin-bottom: 2rem;
    border: 1px solid #e2e8f0;
}

.page-title {
    font-size: 2rem;
    font-weight: 800;
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    color: #6b7280;
    font-size: 1rem;
}

.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-item {
    background: white;
    padding: 1.5rem;
    border-radius: 16px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    text-align: center;
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
}

.stat-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    color: #10b981;
    line-height: 1;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.9rem;
    color: #4b5563;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table-container {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    border: 1px solid #e2e8f0;
    margin: 0 auto;
    max-width: 1200px;
}

.table {
    margin-bottom: 0;
}

.table thead th {
    padding: 1.25rem 1.5rem;
    font-weight: 700;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e2e8f0;
    background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
    color: white;
}

.table tbody td {
    padding: 0.5rem 1.5rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
}

.table tbody tr:hover {
    background-color: #f8fafc;
}

.user-profile {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.avatar-container {
    position: relative;
}

.avatar-img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #e2e8f0;
    transition: border-color 0.3s ease;
}

.avatar-img:hover {
    border-color: #667eea;
}

.avatar-placeholder {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid #e2e8f0;
}

.avatar-initial {
    color: #6b7280;
    font-weight: 700;
    font-size: 1.25rem;
    text-transform: uppercase;
}

.user-info {
    flex: 1;
}

.user-name {
    font-weight: 700;
    font-size: 1.1rem;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.user-role .badge {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

.contact-info,
.location-info,
.account-details {
    line-height: 1.6;
}

.contact-email,
.location-province,
.registration-date {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.25rem;
}

.contact-phone,
.location-city,
.registration-time {
    color: #6b7280;
    font-size: 0.875rem;
}

.date-text {
    color: #374151;
    font-weight: 600;
}

.status-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-flex;
    align-items: center;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.status-active {
    background: linear-gradient(135deg, #006823ff 0%, #007928ff 100%);
    color: white;
}

.status-inactive {
    background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%);
    color: white;
}

.verification-badge {
    font-size: 1.2rem;
}

.no-data {
    text-align: center;
    padding: 4rem 2rem;
}

.no-data i {
    opacity: 0.3;
    margin-bottom: 1.5rem;
}

.pagination-wrapper {
    background: white;
    padding: 2rem;
    border-radius: 20px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    margin-top: 2rem;
    border: 1px solid #e2e8f0;
}

.pagination-info {
    color: #6b7280;
    font-weight: 500;
    font-size: 0.9rem;
}

.pagination-info strong {
    color: #1f2937;
}

/* Responsive Design */
@media (max-width: 992px) {
    .table thead th {
        padding: 1rem;
        font-size: 0.8rem;
    }

    .table tbody td {
        padding: 1rem;
    }

    .user-profile {
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
    }

    .contact-info,
    .location-info,
    .account-details {
        text-align: center;
    }
}

@media (max-width: 768px) {
    .content-header {
        padding: 1.5rem;
    }

    .content-header .d-flex {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start !important;
    }

    .stats-summary {
        text-align: left;
    }

    .table-responsive {
        font-size: 0.875rem;
    }

    .table thead th,
    .table tbody td {
        padding: 0.75rem 0.5rem;
    }

    .user-name {
        font-size: 1rem;
    }

    .avatar-img,
    .avatar-placeholder {
        width: 40px;
        height: 40px;
    }

    .avatar-initial {
        font-size: 1rem;
    }
}

@media (max-width: 576px) {
    .page-title {
        font-size: 1.5rem;
    }

    .table-responsive {
        font-size: 0.8rem;
    }

    .status-container {
        flex-direction: row;
        justify-content: center;
    }

    .status-badge {
        font-size: 0.7rem;
        padding: 0.375rem 0.75rem;
    }
}
</style>
@endsection
