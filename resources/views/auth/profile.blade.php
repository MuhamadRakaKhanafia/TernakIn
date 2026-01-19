@extends('layouts.app')

@section('title', 'Profile - TernakIN')

@section('content')
<div class="profile-container">
    <div class="profile-card">
        <div class="profile-header">
            <h2>Profil Pengguna</h2>
            <p>Kelola informasi profil Anda</p>
        </div>

        <!-- Global Error Message -->
        <div id="globalError" class="global-error-message" style="display: none;"></div>

        <form id="profileForm" class="profile-form">
            @csrf

            <!-- Profile Picture Section -->
            <div class="form-section">
                <h4 class="section-title">Foto Profil</h4>
                <div class="profile-picture-section">
                    <div class="current-picture">
                        <img id="profilePreview"
                             src="{{ auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : asset('images/default-avatar.svg') }}"
                             alt="Profile Picture"
                             class="profile-avatar">
                    </div>
                    <div class="picture-controls">
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/*" style="display: none;">
                        <button type="button" id="changePictureBtn" class="btn btn-secondary">
                            <i class="fas fa-camera"></i> Ubah Foto
                        </button>
                        <button type="button" id="removePictureBtn" class="btn btn-outline-danger" style="display: none;">
                            <i class="fas fa-trash"></i> Hapus Foto
                        </button>
                    </div>
                </div>
            </div>

            <!-- Personal Information Section -->
            <div class="form-section">
                <h4 class="section-title">Informasi Pribadi</h4>

                <div class="form-group full-width">
                    <label for="name">Nama Lengkap <span class="required">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required placeholder="Masukkan nama lengkap Anda">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email <span class="required">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required placeholder="contoh@email.com" readonly>
                        <small class="form-text text-muted">Email tidak dapat diubah</small>
                    </div>
                    <div class="form-group">
                        <label for="phone">Nomor Telepon <span class="required">*</span></label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}" required placeholder="081234567890">
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
                            @foreach($provinces ?? [] as $province)
                                <option value="{{ $province->id }}" {{ (auth()->user()->location && auth()->user()->location->province_id == $province->id) ? 'selected' : '' }}>
                                    {{ $province->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="city_id">Kota/Kabupaten <span class="required">*</span></label>
                        <select id="city_id" name="city_id" required {{ !auth()->user()->location ? 'disabled' : '' }}>
                            <option value="">Pilih Kota/Kabupaten</option>
                            @if(auth()->user()->location && auth()->user()->location->province)
                                @foreach(auth()->user()->location->province->cities ?? [] as $city)
                                    <option value="{{ $city->id }}" {{ (auth()->user()->location && auth()->user()->location->city_id == $city->id) ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="district">Kecamatan</label>
                        <input type="text" id="district" name="district" value="{{ old('district', auth()->user()->location ? auth()->user()->location->district : '') }}" placeholder="Masukkan nama kecamatan">
                    </div>
                    <div class="form-group">
                        <label for="village">Kelurahan/Desa</label>
                        <input type="text" id="village" name="village" value="{{ old('village', auth()->user()->location ? auth()->user()->location->village : '') }}" placeholder="Masukkan nama kelurahan/desa">
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="detailed_address">Alamat Lengkap</label>
                    <textarea id="detailed_address" name="detailed_address" rows="3" placeholder="Masukkan alamat lengkap (nama jalan, nomor rumah, RT/RW, dll)">{{ old('detailed_address', auth()->user()->location ? auth()->user()->location->detailed_address : '') }}</textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary profile-btn">
                    <span id="btnText">Simpan Perubahan</span>
                    <div id="btnLoader" class="btn-loader" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i> Menyimpan...
                    </div>
                </button>
                <a href="{{ route('welcome') }}" class="btn btn-outline-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
.profile-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: calc(100vh - 120px);
    padding: 20px;
    background: linear-gradient(135deg, #f5f7fa 0%);
}

.profile-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    padding: 40px;
    width: 100%;
    max-width: 800px;
    margin: 20px;
}

.profile-header {
    text-align: center;
    margin-bottom: 40px;
}

.profile-header h2 {
    color: var(--primary-color, #3498db);
    margin-bottom: 10px;
    font-size: 28px;
    font-weight: 700;
}

.profile-header p {
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

.profile-form {
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

.form-group input[readonly] {
    background-color: #f8f9fa;
    color: #6c757d;
    cursor: not-allowed;
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

.text-muted {
    color: #6c757d;
}

.text-danger { color: #e74c3c; }
.text-warning { color: #f39c12; }
.text-success { color: #27ae60; }

/* Profile Picture Section */
.profile-picture-section {
    display: flex;
    align-items: center;
    gap: 30px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 12px;
    border: 2px dashed #dee2e6;
}

.current-picture {
    flex-shrink: 0;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.picture-controls {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.btn {
    padding: 10px 16px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-1px);
}

.btn-outline-danger {
    background: transparent;
    color: #dc3545;
    border: 1px solid #dc3545;
}

.btn-outline-danger:hover {
    background: #dc3545;
    color: white;
    transform: translateY(-1px);
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-color, #3498db) 0%, var(--primary-dark, #2980b9) 100%);
    color: white;
}

.btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(52, 152, 219, 0.3);
}

.btn-outline-secondary {
    background: transparent;
    color: #6c757d;
    border: 1px solid #6c757d;
    margin-left: 10px;
}

.btn-outline-secondary:hover {
    background: #6c757d;
    color: white;
}

.form-actions {
    display: flex;
    gap: 15px;
    align-items: center;
    justify-content: center;
    margin-top: 30px;
}

.profile-btn {
    width: auto;
    padding: 16px 32px;
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

.profile-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(52, 152, 219, 0.3);
}

.profile-btn:disabled {
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

/* Responsive Design */
@media (max-width: 768px) {
    .profile-card {
        padding: 30px 25px;
        margin: 10px;
        border-radius: 12px;
    }

    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }

    .profile-header h2 {
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

    .profile-btn {
        padding: 14px 28px;
        font-size: 15px;
    }

    .profile-picture-section {
        flex-direction: column;
        text-align: center;
        gap: 20px;
    }

    .picture-controls {
        flex-direction: row;
        justify-content: center;
    }

    .form-actions {
        flex-direction: column;
    }

    .btn-outline-secondary {
        margin-left: 0;
        margin-top: 10px;
    }
}

@media (max-width: 480px) {
    .profile-card {
        padding: 25px 20px;
    }

    .profile-container {
        padding: 15px;
        min-height: calc(100vh - 80px);
    }

    .form-section {
        margin-bottom: 25px;
        padding-bottom: 20px;
    }

    .picture-controls {
        flex-direction: column;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Load provinces when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadProvinces();

    // Check if user has profile picture
    const profilePicture = "{{ auth()->user()->profile_picture }}";
    if (profilePicture) {
        document.getElementById('removePictureBtn').style.display = 'inline-flex';
    }
});

// Load provinces
function loadProvinces() {
    fetch('/api/provinces')
        .then(response => response.json())
        .then(data => {
            const provinceSelect = document.getElementById('province_id');
            provinceSelect.innerHTML = '<option value="">Pilih Provinsi</option>';
            data.forEach(province => {
                const option = document.createElement('option');
                option.value = province.id;
                option.textContent = province.name;
                if ("{{ auth()->user()->location ? auth()->user()->location->province_id : '' }}" == province.id) {
                    option.selected = true;
                }
                provinceSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error loading provinces:', error);
        });
}

// Dynamic dropdown untuk cities
document.getElementById('province_id').addEventListener('change', function() {
    const provinceId = this.value;
    const citySelect = document.getElementById('city_id');

    console.log('Province changed:', provinceId);

    if (provinceId) {
        citySelect.disabled = false;
        citySelect.innerHTML = '<option value="">Memuat data kota...</option>';

        fetch(`/api/provinces/${provinceId}/cities`)
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
                        const option = document.createElement('option');
                        option.value = city.id;
                        option.textContent = city.name;
                        if ("{{ auth()->user()->location ? auth()->user()->location->city_id : '' }}" == city.id) {
                            option.selected = true;
                        }
                        citySelect.appendChild(option);
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

// Profile picture handling
document.getElementById('changePictureBtn').addEventListener('click', function() {
    document.getElementById('profile_picture').click();
});

document.getElementById('profile_picture').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Validate file type
        if (!file.type.startsWith('image/')) {
            showGlobalError('File harus berupa gambar');
            return;
        }

        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            showGlobalError('Ukuran file maksimal 5MB');
            return;
        }

        // Preview image
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profilePreview').src = e.target.result;
            document.getElementById('removePictureBtn').style.display = 'inline-flex';
        };
        reader.readAsDataURL(file);
    }
});

document.getElementById('removePictureBtn').addEventListener('click', function() {
    document.getElementById('profile_picture').value = '';
    document.getElementById('profilePreview').src = "{{ asset('images/default-avatar.svg') }}";
    this.style.display = 'none';
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
document.getElementById('profileForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const btnText = document.getElementById('btnText');
    const btnLoader = document.getElementById('btnLoader');
    const submitBtn = this.querySelector('.profile-btn');

    // Clear previous errors
    clearErrors();

    // Show loading
    submitBtn.disabled = true;
    btnText.style.display = 'none';
    btnLoader.style.display = 'flex';

    try {
        const formData = new FormData(this);

        const response = await fetch('/profile', {
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
            showGlobalSuccess('Profil berhasil diperbarui!');

            // Update navbar profile picture if changed
            if (data.user.profile_picture) {
                // Update profile picture in navbar
                const navbarAvatar = document.querySelector('.navbar-avatar');
                if (navbarAvatar) {
                    navbarAvatar.src = `/storage/${data.user.profile_picture}`;
                }
            }

            // Redirect after 2 seconds
            setTimeout(() => {
                window.location.reload();
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
        console.error('Profile update error:', error);
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
