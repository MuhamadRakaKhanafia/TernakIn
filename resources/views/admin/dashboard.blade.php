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
    <div class="content-header quick-actions-section">
        <div class="quick-actions-grid">
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

<style>
/* Header Styles */
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

/* Statistics Cards */
.stats-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-item {
    background: white;
    padding: 1rem;
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
    font-size: 2rem;
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

/* Quick Actions Section Styles */
.quick-actions-section {
    background: white;
    padding: 2rem;
    border-radius: 20px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    margin-bottom: 2rem;
    border: 1px solid #e2e8f0;
}

.quick-actions-section h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 1.5rem;
    text-align: center;
}

/* Quick Actions Grid */
.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    justify-items: center;
    max-width: 900px;
    margin: 0 auto 2rem;

/* Action Cards */
.action-card {
    background: white;
    padding: 0.75rem;
    border-radius: 16px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    border: 1px solid #e2e8f0;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    display: block;
    position: relative;
    overflow: hidden;
    min-height: 120px;
    width: 100%;
}

.action-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    text-decoration: none;
    color: inherit;
    border-color: #667eea;
}

.action-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
    transition: left 0.5s;
}

.action-card:hover::before {
    left: 100%;
}

/* Action Icon */
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
    flex-shrink: 0;
    transition: transform 0.3s ease;
}

.action-card:hover .action-icon {
    transform: scale(1.1);
}

/* Action Card Content */
.action-card h5 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.25rem;
    margin-left: 1rem;
}

.action-card p {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 0;
    margin-left: 1rem;
}

/* Arrow Icon */
.action-card .fa-arrow-right {
    color: #667eea;
    font-size: 1rem;
    transition: transform 0.3s ease;
}

.action-card:hover .fa-arrow-right {
    transform: translateX(5px);
    color: #764ba2;
}

/* Responsive Design for Quick Actions */
@media (max-width: 768px) {
    .quick-actions-section {
        padding: 1.5rem;
    }

    .action-card {
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .action-icon {
        width: 45px;
        height: 45px;
        font-size: 1.1rem;
    }

    .action-card h5 {
        font-size: 1rem;
        margin-left: 0.75rem;
    }

    .action-card p {
        font-size: 0.8rem;
        margin-left: 0.75rem;
    }
}

@media (max-width: 576px) {
    .quick-actions-section {
        padding: 1rem;
    }

    .quick-actions-section h3 {
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }

    .action-card {
        padding: 0.75rem;
    }

    .action-icon {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
}
</style>
@endsection
