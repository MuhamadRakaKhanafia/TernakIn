@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="content-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Admin Dashboard</h1>
                <p class="page-subtitle">Manage your application and monitor user activities</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-container">
        <div class="stat-item">
            <span class="stat-number">{{ $stats['total_users'] }}</span>
            <span class="stat-label">Total Users</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">{{ $stats['total_forms'] }}</span>
            <span class="stat-label">Total Forms</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">{{ $stats['total_diseases'] }}</span>
            <span class="stat-label">Total Diseases</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">{{ $stats['total_chat_queries'] }}</span>
            <span class="stat-label">Chat Queries</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">{{ $stats['active_users'] }}</span>
            <span class="stat-label">Active Users</span>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="content-header">
        <div class="row">
            <div class="col-12">
                <h3 class="mb-4" style="font-size: 1.5rem; font-weight: 700; color: #1f2937;">Quick Actions</h3>
                <div class="row g-4">
                    <div class="col-md-6 col-lg-4">
                        <a href="{{ route('admin.logged-in-users') }}" class="action-card">
                            <div class="d-flex align-items-center">
                                <div class="action-icon">
                                    <i class="fas fa-user-check"></i>
                                </div>
                                <div class="ms-3">
                                    <h5 class="mb-1">Logged-in Users</h5>
                                    <p class="mb-0 text-muted">View currently active users</p>
                                </div>
                                <div class="ms-auto">
                                    <i class="fas fa-arrow-right text-primary"></i>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <a href="{{ route('admin.form-submitters') }}" class="action-card">
                            <div class="d-flex align-items-center">
                                <div class="action-icon">
                                    <i class="fas fa-file-signature"></i>
                                </div>
                                <div class="ms-3">
                                    <h5 class="mb-1">Form Submitters</h5>
                                    <p class="mb-0 text-muted">View all user form submissions</p>
                                </div>
                                <div class="ms-auto">
                                    <i class="fas fa-arrow-right text-primary"></i>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <a href="{{ route('admin.chat-queries') }}" class="action-card">
                            <div class="d-flex align-items-center">
                                <div class="action-icon">
                                    <i class="fas fa-search"></i>
                                </div>
                                <div class="ms-3">
                                    <h5 class="mb-1">User Searches</h5>
                                    <p class="mb-0 text-muted">Monitor AI chat queries</p>
                                </div>
                                <div class="ms-auto">
                                    <i class="fas fa-arrow-right text-primary"></i>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <a href="{{ route('analytics.index') }}" class="action-card">
                            <div class="d-flex align-items-center">
                                <div class="action-icon">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                                <div class="ms-3">
                                    <h5 class="mb-1">Analytics</h5>
                                    <p class="mb-0 text-muted">View system analytics</p>
                                </div>
                                <div class="ms-auto">
                                    <i class="fas fa-arrow-right text-primary"></i>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <a href="{{ route('admin.diseases.index') }}" class="action-card">
                            <div class="d-flex align-items-center">
                                <div class="action-icon">
                                    <i class="fas fa-viruses"></i>
                                </div>
                                <div class="ms-3">
                                    <h5 class="mb-1">Disease Management</h5>
                                    <p class="mb-0 text-muted">CRUD operations for diseases</p>
                                </div>
                                <div class="ms-auto">
                                    <i class="fas fa-arrow-right text-primary"></i>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <a href="{{ route('admin.vaccinations.index') }}" class="action-card">
                            <div class="d-flex align-items-center">
                                <div class="action-icon">
                                    <i class="fas fa-syringe"></i>
                                </div>
                                <div class="ms-3">
                                    <h5 class="mb-1">Vaccination Management</h5>
                                    <p class="mb-0 text-muted">Manage vaccination schedules and approvals</p>
                                </div>
                                <div class="ms-auto">
                                    <i class="fas fa-arrow-right text-primary"></i>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <a href="{{ route('admin.broadcasts.index') }}" class="action-card">
                            <div class="d-flex align-items-center">
                                <div class="action-icon">
                                    <i class="fas fa-bullhorn"></i>
                                </div>
                                <div class="ms-3">
                                    <h5 class="mb-1">Broadcast Messages</h5>
                                    <p class="mb-0 text-muted">Create and manage broadcast messages</p>
                                </div>
                                <div class="ms-auto">
                                    <i class="fas fa-arrow-right text-primary"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
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

.action-card {
    background: white;
    padding: 1.5rem;
    border-radius: 16px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    border: 1px solid #e2e8f0;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    display: block;
}

.action-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    text-decoration: none;
    color: inherit;
}

.action-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.action-card h5 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.action-card p {
    font-size: 0.875rem;
    color: #6b7280;
}

/* Responsive Design */
@media (max-width: 768px) {
    .content-header {
        padding: 1.5rem;
    }

    .stats-container {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .action-card {
        margin-bottom: 1rem;
    }
}

@media (max-width: 576px) {
    .stats-container {
        grid-template-columns: 1fr;
    }

    .page-title {
        font-size: 1.5rem;
    }
}
</style>
@endsection
