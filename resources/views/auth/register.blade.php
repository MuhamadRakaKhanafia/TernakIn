@extends('layouts.app')

@section('title', 'Register - TernakIN')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h2>Daftar ke TernakIN</h2>
            <p>Buat akun baru untuk mulai menggunakan layanan kami</p>
        </div>

        <form id="registerForm" class="auth-form">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" required>
                    <div class="error-message" id="nameError"></div>
                </div>

            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
                <div class="error-message" id="emailError"></div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <div class="error-message" id="passwordError"></div>
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required>
                    <div class="error-message" id="passwordConfirmationError"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="phone">Nomor Telepon</label>
                <input type="tel" id="phone" name="phone" required>
                <div class="error-message" id="phoneError"></div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="province_id">Provinsi</label>
                    <select id="province_id" name="province_id" required>
                        <option value="">Pilih Provinsi</option>
                    </select>
                    <div class="error-message" id="provinceIdError"></div>
                </div>
                <div class="form-group">
                    <label for="city_id">Kota/Kabupaten</label>
                    <select id="city_id" name="city_id" required disabled>
                        <option value="">Pilih Kota/Kabupaten</option>
                    </select>
                    <div class="error-message" id="cityIdError"></div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="district">Kecamatan</label>
                    <input type="text" id="district" name="district">
                    <div class="error-message" id="districtError"></div>
                </div>
                <div class="form-group">
                    <label for="village">Kelurahan/Desa</label>
                    <input type="text" id="village" name="village">
                    <div class="error-message" id="villageError"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="detailed_address">Alamat Lengkap</label>
                <textarea id="detailed_address" name="detailed_address" rows="3"></textarea>
                <div class="error-message" id="detailedAddressError"></div>
            </div>

            <button type="submit" class="btn btn-primary auth-btn">
                <span id="btnText">Daftar</span>
                <div id="btnLoader" class="btn-loader" style="display: none;">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
            </button>
        </form>



        <div class="auth-links">
            <p>Sudah punya akun? <a href="{{ route('login') }}">Masuk sekarang</a></p>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.auth-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: calc(100vh - 120px);
    padding: 20px;
}

.auth-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    padding: 40px;
    width: 100%;
    max-width: 600px;
}

.auth-header {
    text-align: center;
    margin-bottom: 30px;
}

.auth-header h2 {
    color: var(--primary-color);
    margin-bottom: 10px;
    font-size: 24px;
}

.auth-header p {
    color: #666;
    margin: 0;
}

.auth-form {
    margin-bottom: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group:last-child {
    margin-bottom: 0;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: var(--text-dark);
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    font-size: 16px;
    transition: border-color 0.3s ease;
    box-sizing: border-box;
}

.form-group textarea {
    resize: vertical;
    min-height: 80px;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary-color);
}

.error-message {
    color: #e74c3c;
    font-size: 14px;
    margin-top: 5px;
    min-height: 18px;
}

.auth-btn {
    width: 100%;
    padding: 12px;
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.auth-btn:hover:not(:disabled) {
    background: var(--primary-dark);
    transform: translateY(-2px);
}

.auth-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

.btn-loader {
    display: flex;
    align-items: center;
    justify-content: center;
}

.auth-links {
    text-align: center;
    margin-top: 20px;
}

.auth-links a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
}

.social-login {
    margin: 20px 0;
}

.divider {
    display: flex;
    align-items: center;
    margin: 20px 0;
    text-align: center;
}

.divider::before,
.divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: #e1e5e9;
}

.divider span {
    padding: 0 15px;
    color: #666;
    font-size: 14px;
    background: white;
}

.social-buttons {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 15px;
    border: 2px solid transparent;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
    cursor: pointer;
    gap: 10px;
}

.btn-google {
    background: #fff;
    color: #333;
    border-color: #ddd;
}

.btn-google:hover {
    background: #f8f9fa;
    border-color: #ccc;
}

.btn-facebook {
    background: #1877f2;
    color: white;
}

.btn-facebook:hover {
    background: #166fe5;
}

.btn i {
    font-size: 18px;
}

.auth-links a:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }

    .auth-card {
        padding: 30px 20px;
    }

    .auth-header h2 {
        font-size: 20px;
    }

    .social-buttons {
        gap: 8px;
    }

    .btn {
        padding: 10px 12px;
        font-size: 14px;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadProvinces();

    document.getElementById('province_id').addEventListener('change', function() {
        loadCities(this.value);
    });
});

async function loadProvinces() {
    try {
        const response = await fetch('/api/provinces');
        const data = await response.json();

        const provinceSelect = document.getElementById('province_id');
        provinceSelect.innerHTML = '<option value="">Pilih Provinsi</option>';

        data.forEach(province => {
            const option = document.createElement('option');
            option.value = province.id;
            option.textContent = province.name;
            provinceSelect.appendChild(option);
        });
    } catch (error) {
        console.error('Error loading provinces:', error);
    }
}

async function loadCities(provinceId) {
    const citySelect = document.getElementById('city_id');
    citySelect.disabled = true;
    citySelect.innerHTML = '<option value="">Memuat...</option>';

    if (!provinceId) {
        citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
        return;
    }

    try {
        const response = await fetch(`/api/provinces/${provinceId}/cities`);
        const data = await response.json();

        citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';

        data.forEach(city => {
            const option = document.createElement('option');
            option.value = city.id;
            option.textContent = city.name;
            citySelect.appendChild(option);
        });

        citySelect.disabled = false;
    } catch (error) {
        console.error('Error loading cities:', error);
        citySelect.innerHTML = '<option value="">Gagal memuat data</option>';
    }
}

document.getElementById('registerForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const btnText = document.getElementById('btnText');
    const btnLoader = document.getElementById('btnLoader');
    const submitBtn = this.querySelector('.auth-btn');

    // Clear previous errors
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');

    // Show loading
    submitBtn.disabled = true;
    btnText.style.display = 'none';
    btnLoader.style.display = 'flex';

    try {
        const formData = new FormData(this);
        const response = await fetch('/register', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                name: formData.get('name'),
                email: formData.get('email'),
                password: formData.get('password'),
                password_confirmation: formData.get('password_confirmation'),
                phone: formData.get('phone'),
                province_id: formData.get('province_id'),
                city_id: formData.get('city_id'),
                district: formData.get('district'),
                village: formData.get('village'),
                detailed_address: formData.get('detailed_address')
            })
        });

        const data = await response.json();

        if (data.success) {
            // Store token if needed
            if (data.access_token) {
                localStorage.setItem('auth_token', data.access_token);
            }

            // Redirect to dashboard
            window.location.href = '{{ route("dashboard") }}';
        } else {
            // Show errors
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const errorEl = document.getElementById(field.replace('_', '') + 'Error');
                    if (errorEl) {
                        errorEl.textContent = data.errors[field][0];
                    }
                });
            } else if (data.error) {
                document.getElementById('nameError').textContent = data.error;
            }
        }
    } catch (error) {
        console.error('Register error:', error);
        document.getElementById('nameError').textContent = 'Terjadi kesalahan. Silakan coba lagi.';
    } finally {
        // Hide loading
        submitBtn.disabled = false;
        btnText.style.display = 'inline';
        btnLoader.style.display = 'none';
    }
});
</script>
@endpush
