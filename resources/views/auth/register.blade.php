@extends('layouts.app')

@section('title', 'Register - TernakIN')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h2>Daftar ke TernakIN</h2>
            <p>Buat akun baru untuk mulai menggunakan layanan kami</p>
        </div>

        <!-- Global Error Message -->
        <div id="globalError" class="global-error-message" style="display: none;"></div>

        <form id="registerForm" class="auth-form">
            @csrf

            <!-- Personal Information Section -->
            <div class="form-section">
                <h4 class="section-title">Informasi Pribadi</h4>

                <div class="form-group full-width">
                    <label for="name">Nama Lengkap <span class="required">*</span></label>
                    <input type="text" id="name" name="name" required placeholder="Masukkan nama lengkap Anda">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email <span class="required">*</span></label>
                        <input type="email" id="email" name="email" required placeholder="contoh@email.com">
                    </div>
                    <div class="form-group">
                        <label for="phone">Nomor Telepon <span class="required">*</span></label>
                        <input type="tel" id="phone" name="phone" required placeholder="081234567890">
                    </div>
                </div>
            </div>

            <!-- Security Section -->
            <div class="form-section">
                <h4 class="section-title">Keamanan Akun</h4>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password <span class="required">*</span></label>
                        <input type="password" id="password" name="password" required placeholder="Minimal 8 karakter">
                        <div class="form-text" id="passwordStrength"></div>
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password <span class="required">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi password Anda">
                    </div>
                </div>
            </div>

            <!-- Location Section -->
            <div class="form-section">
                <h4 class="section-title">Lokasi</h4>

                <div class="form-row">
                    <div class="form-group">
                        <label for="province_id">Provinsi <span class="required">*</span></label>
                        <select id="province_id" name="province_id" required>
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province->id }}">{{ $province->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="city_id">Kota/Kabupaten <span class="required">*</span></label>
                        <select id="city_id" name="city_id" required disabled>
                            <option value="">Pilih Kota/Kabupaten</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="district">Kecamatan</label>
                        <input type="text" id="district" name="district" placeholder="Masukkan nama kecamatan">
                    </div>
                    <div class="form-group">
                        <label for="village">Kelurahan/Desa</label>
                        <input type="text" id="village" name="village" placeholder="Masukkan nama kelurahan/desa">
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="detailed_address">Alamat Lengkap</label>
                    <textarea id="detailed_address" name="detailed_address" rows="3" placeholder="Masukkan alamat lengkap (nama jalan, nomor rumah, RT/RW, dll)"></textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-primary auth-btn">
                <span id="btnText">Daftar Sekarang</span>
                <div id="btnLoader" class="btn-loader" style="display: none;">
                    <i class="fas fa-spinner fa-spin"></i> Memproses...
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
    background: linear-gradient(135deg, #f5f7fa 0%);
}

.auth-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    padding: 40px;
    width: 100%;
    max-width: 700px;
    margin: 20px;
}

.auth-header {
    text-align: center;
    margin-bottom: 40px;
}

.auth-header h2 {
    color: var(--primary-color, #3498db);
    margin-bottom: 10px;
    font-size: 28px;
    font-weight: 700;
}

.auth-header p {
    color: #666;
    margin: 0;
    font-size: 16px;
}

.global-error-message {
    background: #fee;
    border: 1px solid #f5c6cb;
    color: #721c24;
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 14px;
    font-weight: 500;
}

.auth-form {
    margin-bottom: 20px;
}

.form-section {
    margin-bottom: 35px;
    padding-bottom: 25px;
    border-bottom: 1px solid #f0f0f0;
}

.form-section:last-of-type {
    border-bottom: none;
    margin-bottom: 25px;
}

.section-title {
    color: #2c3e50;
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--primary-color, #3498db);
    display: inline-block;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
    position: relative;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-group:last-child {
    margin-bottom: 0;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #2c3e50;
    font-size: 14px;
}

.required {
    color: #e74c3c;
    font-weight: bold;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid #e1e5e9;
    border-radius: 10px;
    font-size: 15px;
    transition: all 0.3s ease;
    box-sizing: border-box;
    background: #fff;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary-color, #3498db);
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    background: #fafbfc;
}

.form-group input.error,
.form-group select.error,
.form-group textarea.error {
    border-color: #e74c3c;
    box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
    font-family: inherit;
}

.form-group input::placeholder,
.form-group textarea::placeholder {
    color: #a0a0a0;
}

.form-group select {
    appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 16px center;
    background-size: 16px;
    padding-right: 45px;
}

.form-group select:disabled {
    background-color: #f8f9fa;
    color: #6c757d;
    cursor: not-allowed;
}

.form-text {
    font-size: 12px;
    margin-top: 5px;
    min-height: 16px;
}

.text-danger { color: #e74c3c; }
.text-warning { color: #f39c12; }
.text-success { color: #27ae60; }

.auth-btn {
    width: 100%;
    padding: 16px;
    background: linear-gradient(135deg, var(--primary-color, #3498db) 0%, var(--primary-dark, #2980b9) 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    position: relative;
    overflow: hidden;
}

.auth-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(52, 152, 219, 0.3);
}

.auth-btn:active:not(:disabled) {
    transform: translateY(0);
}

.auth-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.btn-loader {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-size: 14px;
}

.auth-links {
    text-align: center;
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid #f0f0f0;
}

.auth-links p {
    color: #666;
    margin: 0;
}

.auth-links a {
    color: var(--primary-color, #3498db);
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}

.auth-links a:hover {
    color: var(--primary-dark, #2980b9);
    text-decoration: underline;
}

/* Responsive Design */
@media (max-width: 768px) {
    .auth-card {
        padding: 30px 25px;
        margin: 10px;
        border-radius: 12px;
    }

    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }

    .auth-header h2 {
        font-size: 24px;
    }

    .section-title {
        font-size: 16px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        padding: 12px 14px;
        font-size: 14px;
    }

    .auth-btn {
        padding: 14px;
        font-size: 15px;
    }
}

@media (max-width: 480px) {
    .auth-card {
        padding: 25px 20px;
    }

    .auth-container {
        padding: 15px;
        min-height: calc(100vh - 80px);
    }

    .form-section {
        margin-bottom: 25px;
        padding-bottom: 20px;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Load provinces when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded, provinces should be populated from server');
});

// Dynamic dropdown untuk cities
document.getElementById('province_id').addEventListener('change', function() {
    const provinceId = this.value;
    const citySelect = document.getElementById('city_id');

    console.log('Province changed:', provinceId);

    if (provinceId) {
        citySelect.disabled = false;
        citySelect.innerHTML = '<option value="">Memuat data kota...</option>';

        fetch(`/api/cities/${provinceId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Cities data received:', data);
                citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
                if (data.length > 0) {
                    data.forEach(city => {
                        citySelect.innerHTML += `<option value="${city.id}">${city.name}</option>`;
                    });
                } else {
                    citySelect.innerHTML = '<option value="">Tidak ada data kota</option>';
                }
            })
            .catch(error => {
                console.error('Error loading cities:', error);
                citySelect.innerHTML = '<option value="">Error memuat data kota</option>';
                showGlobalError('Gagal memuat data kota. Silakan refresh halaman.');
            });
    } else {
        citySelect.disabled = true;
        citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
    }
});

// Password strength indicator
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strengthText = document.getElementById('passwordStrength');

    let strength = '';
    let strengthClass = '';

    if (password.length === 0) {
        strength = '';
    } else if (password.length < 6) {
        strength = '❌ Password terlalu pendek';
        strengthClass = 'text-danger';
    } else if (password.length < 8) {
        strength = '⚠️ Password lemah';
        strengthClass = 'text-warning';
    } else {
        // Check for complexity
        const hasUpperCase = /[A-Z]/.test(password);
        const hasLowerCase = /[a-z]/.test(password);
        const hasNumbers = /\d/.test(password);
        const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);

        const complexityScore = [hasUpperCase, hasLowerCase, hasNumbers, hasSpecialChar].filter(Boolean).length;

        if (complexityScore >= 3) {
            strength = '✅ Password kuat';
            strengthClass = 'text-success';
        } else {
            strength = '⚠️ Password cukup';
            strengthClass = 'text-warning';
        }
    }

    if (strengthText) {
        strengthText.textContent = strength;
        strengthText.className = `form-text ${strengthClass}`;
    }
});

// Fungsi untuk menampilkan global error
function showGlobalError(message) {
    const errorDiv = document.getElementById('globalError');
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';

    // Auto hide setelah 5 detik
    setTimeout(() => {
        errorDiv.style.display = 'none';
    }, 5000);
}

// Fungsi untuk clear errors
function clearErrors() {
    document.getElementById('globalError').style.display = 'none';
    document.querySelectorAll('.form-group input, .form-group select, .form-group textarea').forEach(el => {
        el.classList.remove('error');
    });
}

// Form submission dengan AJAX
document.getElementById('registerForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const btnText = document.getElementById('btnText');
    const btnLoader = document.getElementById('btnLoader');
    const submitBtn = this.querySelector('.auth-btn');

    // Clear previous errors
    clearErrors();

    // Basic validation
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password_confirmation').value;

    if (password.length < 8) {
        showGlobalError('Password harus minimal 8 karakter');
        document.getElementById('password').classList.add('error');
        return;
    }

    if (password !== passwordConfirm) {
        showGlobalError('Konfirmasi password tidak cocok');
        document.getElementById('password_confirmation').classList.add('error');
        return;
    }

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
            },
            credentials: 'same-origin',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            // Show success message
            showGlobalSuccess('Registrasi berhasil! Mengarahkan ke dashboard...');

            // Redirect to dashboard after 2 seconds
            setTimeout(() => {
                window.location.href = data.redirect_url || '{{ route("dashboard") }}';
            }, 2000);

        } else {
            // Show errors
            if (data.message) {
                showGlobalError(data.message);
            } else if (data.errors) {
                // Jika ada multiple errors, tampilkan yang pertama saja
                const firstError = Object.values(data.errors)[0][0];
                showGlobalError(firstError);

                // Highlight field yang error
                Object.keys(data.errors).forEach(field => {
                    const fieldElement = document.getElementById(field);
                    if (fieldElement) {
                        fieldElement.classList.add('error');
                    }
                });
            } else {
                showGlobalError('Terjadi kesalahan. Silakan coba lagi.');
            }
        }
    } catch (error) {
        console.error('Register error:', error);
        showGlobalError('Terjadi kesalahan jaringan. Silakan coba lagi.');
    } finally {
        // Hide loading
        submitBtn.disabled = false;
        btnText.style.display = 'inline';
        btnLoader.style.display = 'none';
    }
});

// Fungsi untuk menampilkan success message
function showGlobalSuccess(message) {
    const errorDiv = document.getElementById('globalError');
    errorDiv.textContent = message;
    errorDiv.style.background = '#d4edda';
    errorDiv.style.borderColor = '#c3e6cb';
    errorDiv.style.color = '#155724';
    errorDiv.style.display = 'block';

    // Auto hide setelah 3 detik
    setTimeout(() => {
        errorDiv.style.display = 'none';
        // Reset style
        errorDiv.style.background = '#fee';
        errorDiv.style.borderColor = '#f5c6cb';
        errorDiv.style.color = '#721c24';
    }, 3000);
}
</script>
@endpush
