@extends('layouts.app')

@section('title', 'Tambah Vaksinasi - TernakIN')
@section('page-title', 'Tambah Vaksinasi')

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

    <!-- User Info Summary -->
    <div class="user-info-card mb-4">
        <div class="user-info-content">
            <div class="user-info-item">
                <i class="fas fa-user-circle"></i>
                <div>
                    <span class="user-name">{{ auth()->user()->name }}</span>
                    <span class="user-email">{{ auth()->user()->email }}</span>
                </div>
            </div>
            <div class="user-info-item">
                <i class="fas fa-cow"></i>
                <div>
                    <span class="total-count">{{ auth()->user()->livestocks()->count() }}</span>
                    <span class="total-label">Total Hewan Ternak</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Container -->
    <div class="form-container">
        <div class="form-header">
            <h3><i class="fas fa-plus-circle me-2"></i>Tambah Data Vaksinasi</h3>
            <p>Masukkan informasi vaksinasi hewan ternak Anda</p>
        </div>

        <form action="{{ route('vaccinations.store') }}" method="POST" class="vaccination-form">
            @csrf

            <!-- Row 1: Animal Type and Vaccine Name -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="animal_type_id" class="form-label">
                        <i class="fas fa-cow me-1"></i>Jenis Hewan Ternak <span class="text-danger">*</span>
                    </label>
                    <select name="animal_type_id" id="animal_type_id" class="form-select @error('animal_type_id') is-invalid @enderror" required>
                        <option value="">Pilih Jenis Hewan</option>
                        @foreach($animalTypes as $animalType)
                        <option value="{{ $animalType->id }}" {{ old('animal_type_id') == $animalType->id ? 'selected' : '' }}>
                            {{ $animalType->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('animal_type_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="vaccine_name" class="form-label">
                        <i class="fas fa-syringe me-1"></i>Nama Vaksin <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="vaccine_name" id="vaccine_name" class="form-control @error('vaccine_name') is-invalid @enderror"
                           value="{{ old('vaccine_name') }}" placeholder="Contoh: Vaksin PMK, Vaksin Anthrax" required>
                    @error('vaccine_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Row 2: Vaccination Date and Next Vaccination Date -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="vaccination_date" class="form-label">
                        <i class="fas fa-calendar-alt me-1"></i>Tanggal Vaksinasi <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="vaccination_date" id="vaccination_date" class="form-control @error('vaccination_date') is-invalid @enderror"
                           value="{{ old('vaccination_date') }}" required>
                    @error('vaccination_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="next_vaccination_date" class="form-label">
                        <i class="fas fa-calendar-plus me-1"></i>Tanggal Vaksinasi Selanjutnya
                    </label>
                    <input type="date" name="next_vaccination_date" id="next_vaccination_date" class="form-control @error('next_vaccination_date') is-invalid @enderror"
                           value="{{ old('next_vaccination_date') }}">
                    @error('next_vaccination_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Row 3: Notes (Full Width) -->
            <div class="row">
                <div class="col-12 mb-3">
                    <label for="notes" class="form-label">
                        <i class="fas fa-sticky-note me-1"></i>Catatan Tambahan
                    </label>
                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="4"
                              placeholder="Tambahkan catatan tentang vaksinasi ini (opsional)">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions-container">
                <div class="form-actions">
                    <a href="{{ route('vaccinations.index') }}" class="btn btn-secondary action-btn">
                        <i class="fas fa-arrow-left me-2"></i>
                        <span>Kembali</span>
                    </a>
                    <button type="submit" class="btn btn-primary action-btn">
                        <i class="fas fa-save me-2"></i>
                        <span>Simpan Vaksinasi</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
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
    max-width: 1200px;
    margin: 0 auto;
}

/* Flash Messages */
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

/* User Info Card */
.user-info-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e5e7eb;
    overflow: hidden;
}

.user-info-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
}

.user-info-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.user-info-item i {
    font-size: 1.5rem;
    color: var(--primary-color);
}

.user-name {
    display: block;
    font-weight: 600;
    color: var(--text-color);
    font-size: 1rem;
}

.user-email {
    display: block;
    font-size: 0.875rem;
    color: var(--text-light);
}

.total-count {
    display: block;
    font-weight: 700;
    color: var(--primary-color);
    font-size: 1.25rem;
}

.total-label {
    display: block;
    font-size: 0.875rem;
    color: var(--text-light);
}

/* Form Container */
.form-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    border: 1px solid #e5e7eb;
}

.form-header {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
    padding: 2rem;
    text-align: center;
}

.form-header h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.5rem;
    font-weight: 700;
}

.form-header p {
    margin: 0;
    opacity: 0.9;
    font-size: 1rem;
}

/* Form Styles */
.vaccination-form {
    padding: 2rem;
}

.form-label {
    font-weight: 600;
    color: var(--text-color);
    margin-bottom: 0.75rem;
    display: block;
    font-size: 0.95rem;
}

.form-control, .form-select {
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    padding: 0.875rem 1rem;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background: white;
    height: auto;
    line-height: 1.5;
}

.form-control:focus, .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.1);
    outline: none;
    transform: translateY(-1px);
}

.form-control::placeholder {
    color: #9ca3af;
    font-size: 0.9rem;
}

textarea.form-control {
    resize: vertical;
    min-height: 120px;
    line-height: 1.6;
}

/* Form Field Groups */
.mb-3 {
    margin-bottom: 1.5rem !important;
}

/* Row Spacing */
.row {
    margin-bottom: 0.5rem;
}

.row:last-child {
    margin-bottom: 0;
}

/* Field Consistency */
.form-select {
    height: auto;
    padding: 0.875rem 1rem;
}

input[type="date"] {
    padding: 0.875rem 1rem;
}

/* Invalid States */
.is-invalid {
    border-color: var(--danger-high) !important;
}

.invalid-feedback {
    display: block;
    color: var(--danger-high);
    font-size: 0.8rem;
    margin-top: 0.25rem;
}

/* Form Actions Container */
.form-actions-container {
    background: #f8fafc;
    border-radius: 0 0 12px 12px;
    padding: 2rem;
    margin: 0 -2rem -2rem -2rem;
    border-top: 2px solid #e5e7eb;
}

.form-actions {
    display: flex;
    gap: 1.5rem;
    justify-content: center;
    align-items: center;
    max-width: 400px;
    margin: 0 auto;
}

.action-btn {
    border-radius: 10px;
    padding: 1rem 2rem;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    min-width: 160px;
    position: relative;
    overflow: hidden;
}

.action-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s ease;
}

.action-btn:hover::before {
    left: 100%;
}

.btn-primary.action-btn {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
    box-shadow: 0 4px 15px rgba(5, 150, 105, 0.3);
}

.btn-primary.action-btn:hover {
    background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(5, 150, 105, 0.4);
    color: white;
}

.btn-secondary.action-btn {
    background: linear-gradient(135deg, #6b7280, #4b5563);
    color: white;
    box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3);
}

.btn-secondary.action-btn:hover {
    background: linear-gradient(135deg, #4b5563, #374151);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(107, 114, 128, 0.4);
    color: white;
}

.action-btn i {
    font-size: 1.1rem;
    transition: transform 0.3s ease;
}

.action-btn:hover i {
    transform: scale(1.1);
}

.action-btn span {
    font-weight: 600;
    letter-spacing: 0.025em;
}

/* Responsive Design */
@media (max-width: 768px) {
    .container-fluid {
        padding: 15px;
    }

    .user-info-content {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }

    .vaccination-form {
        padding: 1.5rem;
    }

    .form-header {
        padding: 1.5rem;
    }

    .form-header h3 {
        font-size: 1.25rem;
    }

    .form-actions {
        flex-direction: column;
    }

    .btn {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .container-fluid {
        padding: 10px;
    }

    .vaccination-form {
        padding: 1rem;
    }

    .form-header {
        padding: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set default vaccination date to today
    const vaccinationDateInput = document.getElementById('vaccination_date');
    if (vaccinationDateInput && !vaccinationDateInput.value) {
        const today = new Date().toISOString().split('T')[0];
        vaccinationDateInput.value = today;
    }

    // Auto-calculate next vaccination date (typically 6 months later)
    vaccinationDateInput.addEventListener('change', function() {
        const nextVaccinationInput = document.getElementById('next_vaccination_date');
        if (this.value && !nextVaccinationInput.value) {
            const vaccinationDate = new Date(this.value);
            const nextDate = new Date(vaccinationDate);
            nextDate.setMonth(nextDate.getMonth() + 6); // Add 6 months
            nextVaccinationInput.value = nextDate.toISOString().split('T')[0];
        }
    });

    // Form validation
    const form = document.querySelector('.vaccination-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Mohon lengkapi semua field yang wajib diisi.');
            }
        });
    }
});
</script>
@endpush
