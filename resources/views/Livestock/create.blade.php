@extends('layouts.app')

@section('title', 'Tambah Hewan Ternak - TernakIN')
@section('page-title', 'Tambah Hewan Ternak')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Form Container -->
            <div class="form-container">
                <div class="form-header">
                    <h3><i class="fas fa-plus-circle me-2"></i>Tambah Data Hewan Ternak</h3>
                    <p class="text-muted mb-0">Masukkan informasi lengkap tentang hewan ternak baru</p>
                </div>

                <form id="livestockForm" method="POST" action="{{ route('livestock.store') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Pilih Jenis Hewan -->
                    <div class="form-section">
                        <h4><i class="fas fa-paw me-2"></i>Pilih Jenis Hewan</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="animal_type_id" class="form-label required">Jenis Hewan</label>
                                    <select class="form-control" id="animal_type_id" name="animal_type_id" required>
                                        <option value="">Pilih jenis hewan...</option>
                                        @foreach($animalTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('animal_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        Jenis hewan harus dipilih.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Umum -->
                    <div class="form-section">
                        <h4><i class="fas fa-info-circle me-2"></i>Informasi Umum</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label required">Nama/Identifikasi Hewan</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Sapi-001, Ayam-Broiler-A" required>
                                    <div class="invalid-feedback">
                                        Nama/identifikasi hewan harus diisi.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="identification_number" class="form-label">Nomor Identifikasi</label>
                                    <input type="text" class="form-control" id="identification_number" name="identification_number" value="{{ old('identification_number') }}" placeholder="Opsional: Tag ID, RFID, dll.">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="birth_date" class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control" id="birth_date" name="birth_date" value="{{ old('birth_date') }}">
                                    <div class="invalid-feedback">
                                        Format tanggal tidak valid.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="acquisition_date" class="form-label required">Tanggal Akuisisi</label>
                                    <input type="date" class="form-control" id="acquisition_date" name="acquisition_date" value="{{ old('acquisition_date', date('Y-m-d')) }}" required>
                                    <div class="invalid-feedback">
                                        Tanggal akuisisi harus diisi.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sex" class="form-label required">Jenis Kelamin</label>
                                    <select class="form-control" id="sex" name="sex" required>
                                        <option value="">Pilih jenis kelamin...</option>
                                        <option value="jantan" {{ old('sex') == 'jantan' ? 'selected' : '' }}>Jantan</option>
                                        <option value="betina" {{ old('sex') == 'betina' ? 'selected' : '' }}>Betina</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Jenis kelamin harus dipilih.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="health_status" class="form-label required">Status Kesehatan</label>
                                    <select class="form-control" id="health_status" name="health_status" required>
                                        <option value="">Pilih status kesehatan...</option>
                                        <option value="sehat" {{ old('health_status') == 'sehat' ? 'selected' : '' }}>Sehat</option>
                                        <option value="sakit" {{ old('health_status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Status kesehatan harus dipilih.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vaccination_status" class="form-label required">Status Vaksinasi</label>
                                    <select class="form-control" id="vaccination_status" name="vaccination_status" required>
                                        <option value="">Pilih status vaksinasi...</option>
                                        <option value="up_to_date" {{ old('vaccination_status') == 'up_to_date' ? 'selected' : '' }}>Terkini</option>
                                        <option value="need_update" {{ old('vaccination_status') == 'need_update' ? 'selected' : '' }}>Perlu Update</option>
                                        <option value="not_vaccinated" {{ old('vaccination_status') == 'not_vaccinated' ? 'selected' : '' }}>Belum Vaksin</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Status vaksinasi harus dipilih.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Spesifik Berdasarkan Jenis Hewan -->
                    <div id="specific-fields" style="display: none;">
                        <!-- Fields untuk Unggas -->
                        <div id="chicken-fields" class="animal-specific" style="display: none;">
                            <div class="form-section">
                                <h4><i class="fas fa-egg me-2"></i>Informasi Khusus Unggas</h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="strain" class="form-label">Strain</label>
                                            <input type="text" class="form-control" id="strain" name="strain" value="{{ old('strain') }}" placeholder="Contoh: Broiler, Layer, dll.">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="age_weeks" class="form-label">Usia (minggu)</label>
                                            <input type="number" class="form-control" id="age_weeks" name="age_weeks" value="{{ old('age_weeks') }}" min="1" max="104" placeholder="Usia dalam minggu">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="weight_kg" class="form-label">Berat (kg)</label>
                                            <input type="number" step="0.01" class="form-control" id="weight_kg" name="weight_kg" value="{{ old('weight_kg') }}" min="0.1" max="5" placeholder="Berat dalam kg">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="egg_production" class="form-label">Produksi Telur (butir/bulan)</label>
                                            <input type="number" class="form-control" id="egg_production" name="egg_production" value="{{ old('egg_production') }}" min="0" max="365" placeholder="Jumlah telur per bulan">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="feed_type" class="form-label">Jenis Pakan</label>
                                            <input type="text" class="form-control" id="feed_type" name="feed_type" value="{{ old('feed_type') }}" placeholder="Contoh: Konsentrat, Jagung, dll.">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="daily_feed_kg" class="form-label">Pakan Harian (kg)</label>
                                            <input type="number" step="0.01" class="form-control" id="daily_feed_kg" name="daily_feed_kg" value="{{ old('daily_feed_kg') }}" min="0.01" max="0.5" placeholder="Jumlah pakan per hari">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="housing_type" class="form-label">Tipe Kandang</label>
                                            <select class="form-control" id="housing_type" name="housing_type">
                                                <option value="">Pilih tipe kandang...</option>
                                                <option value="kandang_terbuka" {{ old('housing_type') == 'kandang_terbuka' ? 'selected' : '' }}>Kandang Terbuka</option>
                                                <option value="kandang_tutup" {{ old('housing_type') == 'kandang_tutup' ? 'selected' : '' }}>Kandang Tertutup</option>
                                                <option value="battery_cage" {{ old('housing_type') == 'battery_cage' ? 'selected' : '' }}>Battery Cage</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="flock_size" class="form-label">Ukuran Kandang (ekor)</label>
                                            <input type="number" class="form-control" id="flock_size" name="flock_size" value="{{ old('flock_size') }}" min="1" max="10000" placeholder="Jumlah ayam dalam kandang">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fields untuk Ternak Besar -->
                        <div id="ruminant-fields" class="animal-specific" style="display: none;">
                            <div class="form-section">
                                <h4><i class="fas fa-cow me-2"></i>Informasi Khusus Ternak Besar</h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="breed" class="form-label">Breed/Ras</label>
                                            <input type="text" class="form-control" id="breed" name="breed" value="{{ old('breed') }}" placeholder="Contoh: Limosin, Peranakan Ongole, dll.">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="age_months" class="form-label">Usia (bulan)</label>
                                            <input type="number" class="form-control" id="age_months" name="age_months" value="{{ old('age_months') }}" min="1" max="240" placeholder="Usia dalam bulan">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="weight_kg" class="form-label">Berat (kg)</label>
                                            <input type="number" step="0.01" class="form-control" id="weight_kg" name="weight_kg" value="{{ old('weight_kg') }}" min="10" max="1000" placeholder="Berat dalam kg">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="purpose" class="form-label">Tujuan Pemeliharaan</label>
                                            <select class="form-control" id="purpose" name="purpose">
                                                <option value="">Pilih tujuan...</option>
                                                <option value="peternakan" {{ old('purpose') == 'peternakan' ? 'selected' : '' }}>Peternakan</option>
                                                <option value="daging" {{ old('purpose') == 'daging' ? 'selected' : '' }}>Produksi Daging</option>
                                                <option value="susu" {{ old('purpose') == 'susu' ? 'selected' : '' }}>Produksi Susu</option>
                                                <option value="kulit" {{ old('purpose') == 'kulit' ? 'selected' : '' }}>Produksi Kulit</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="feed_type" class="form-label">Jenis Pakan</label>
                                            <input type="text" class="form-control" id="feed_type" name="feed_type" value="{{ old('feed_type') }}" placeholder="Contoh: Rumput, Konsentrat, dll.">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="daily_feed_kg" class="form-label">Pakan Harian (kg)</label>
                                            <input type="number" step="0.01" class="form-control" id="daily_feed_kg" name="daily_feed_kg" value="{{ old('daily_feed_kg') }}" min="1" max="20" placeholder="Jumlah pakan per hari">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="milk_production_liter" class="form-label">Produksi Susu (liter/hari)</label>
                                            <input type="number" step="0.01" class="form-control" id="milk_production_liter" name="milk_production_liter" value="{{ old('milk_production_liter') }}" min="0" max="50" placeholder="Produksi susu per hari">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="pregnancy_status" class="form-label">Status Kehamilan</label>
                                            <select class="form-control" id="pregnancy_status" name="pregnancy_status">
                                                <option value="">Pilih status...</option>
                                                <option value="tidak_hamil" {{ old('pregnancy_status') == 'tidak_hamil' ? 'selected' : '' }}>Tidak Hamil</option>
                                                <option value="hamil" {{ old('pregnancy_status') == 'hamil' ? 'selected' : '' }}>Hamil</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="housing_type" class="form-label">Tipe Kandang</label>
                                            <select class="form-control" id="housing_type" name="housing_type">
                                                <option value="">Pilih tipe kandang...</option>
                                                <option value="kandang_terbuka" {{ old('housing_type') == 'kandang_terbuka' ? 'selected' : '' }}>Kandang Terbuka</option>
                                                <option value="kandang_tutup" {{ old('housing_type') == 'kandang_tutup' ? 'selected' : '' }}>Kandang Tertutup</option>
                                                <option value="padang_rumput" {{ old('housing_type') == 'padang_rumput' ? 'selected' : '' }}>Padang Rumput</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fields untuk Hewan Lainnya -->
                        <div id="other-fields" class="animal-specific" style="display: none;">
                            <div class="form-section">
                                <h4><i class="fas fa-question-circle me-2"></i>Informasi Khusus</h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="breed" class="form-label">Breed/Ras</label>
                                            <input type="text" class="form-control" id="breed" name="breed" value="{{ old('breed') }}" placeholder="Opsional">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="age_months" class="form-label">Usia (bulan)</label>
                                            <input type="number" class="form-control" id="age_months" name="age_months" value="{{ old('age_months') }}" min="1" max="240" placeholder="Usia dalam bulan">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="weight_kg" class="form-label">Berat (kg)</label>
                                            <input type="number" step="0.01" class="form-control" id="weight_kg" name="weight_kg" value="{{ old('weight_kg') }}" min="0.1" max="100" placeholder="Berat dalam kg">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="purpose" class="form-label">Tujuan Pemeliharaan</label>
                                            <input type="text" class="form-control" id="purpose" name="purpose" value="{{ old('purpose') }}" placeholder="Opsional">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="feed_type" class="form-label">Jenis Pakan</label>
                                            <input type="text" class="form-control" id="feed_type" name="feed_type" value="{{ old('feed_type') }}" placeholder="Jenis pakan">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="daily_feed_kg" class="form-label">Pakan Harian (kg)</label>
                                            <input type="number" step="0.01" class="form-control" id="daily_feed_kg" name="daily_feed_kg" value="{{ old('daily_feed_kg') }}" min="0.01" max="5" placeholder="Jumlah pakan per hari">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="housing_type" class="form-label">Tipe Kandang</label>
                                            <input type="text" class="form-control" id="housing_type" name="housing_type" value="{{ old('housing_type') }}" placeholder="Tipe kandang">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Catatan Tambahan -->
                    <div class="form-section">
                        <h4><i class="fas fa-sticky-note me-2"></i>Catatan Tambahan</h4>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="notes" class="form-label">Catatan</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="4" placeholder="Catatan tambahan tentang hewan ternak...">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-submit">
                            <i class="fas fa-save me-2"></i>Simpan Data
                        </button>
                        <a href="{{ route('livestock.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
:root {
    --primary-color: #10b981;
    --primary-light: #34d399;
    --primary-dark: #059669;
    --secondary-color: #6366f1;
    --accent-color: #8b5cf6;
    --text-dark: #1f2937;
    --text-medium: #4b5563;
    --text-light: #6b7280;
    --background-light: #f9fafb;
    --border-light: #e5e7eb;
    --border-medium: #d1d5db;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --error-color: #ef4444;
    --info-color: #3b82f6;
}

/* Form Container Modern */
.form-container {
    background: white;
    border-radius: 20px;
    box-shadow: 
        0 4px 6px -1px rgba(0, 0, 0, 0.05),
        0 10px 15px -3px rgba(0, 0, 0, 0.08),
        0 20px 25px -5px rgba(0, 0, 0, 0.04);
    overflow: hidden;
    margin-bottom: 2rem;
    border: 1px solid var(--border-light);
    backdrop-filter: blur(10px);
}

.form-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
    color: white;
    padding: 2.5rem 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.form-header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-10px) rotate(180deg); }
}

.form-header h3 {
    margin: 0;
    font-size: 1.75rem;
    font-weight: 800;
    letter-spacing: -0.025em;
    position: relative;
    z-index: 2;
}

.form-header p {
    margin: 0.75rem 0 0 0;
    opacity: 0.9;
    font-size: 1rem;
    font-weight: 400;
    position: relative;
    z-index: 2;
}

/* Form Sections */
.form-section {
    padding: 2.5rem;
    border-bottom: 1px solid var(--border-light);
    background: white;
    transition: all 0.3s ease;
}

.form-section:hover {
    background: #fafafa;
}

.form-section:last-child {
    border-bottom: none;
}

.form-section h4 {
    color: var(--text-dark);
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 1.75rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid var(--primary-color);
    display: flex;
    align-items: center;
    letter-spacing: -0.025em;
}

.form-section h4 i {
    color: var(--primary-color);
    margin-right: 0.75rem;
    font-size: 1.1em;
}

/* Form Labels */
.form-label {
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 0.75rem;
    display: block;
    font-size: 0.95rem;
    letter-spacing: -0.025em;
}

.form-label.required::after {
    content: " *";
    color: var(--error-color);
    font-weight: 700;
}

/* Form Controls */
.form-group {
    margin-bottom: 1.5rem;
    position: relative;
}

.form-control {
    padding: 1rem 1.25rem;
    border: 2px solid var(--border-medium);
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    width: 100%;
    background: white;
    color: var(--text-dark);
    font-weight: 500;
}

.form-control::placeholder {
    color: var(--text-light);
    font-weight: 400;
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 
        0 0 0 4px rgba(16, 185, 129, 0.1),
        0 2px 4px rgba(0, 0, 0, 0.05);
    outline: none;
    transform: translateY(-1px);
}

.form-control:hover:not(:focus) {
    border-color: var(--border-medium);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

/* Select Styling */
.form-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3e%3cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m19 9-7 7-7-7'/%3e%3c/svg%3e");
    background-position: right 1rem center;
    background-repeat: no-repeat;
    background-size: 1.25rem;
    padding-right: 3rem;
}

/* Textarea Styling */
textarea.form-control {
    resize: vertical;
    min-height: 120px;
    line-height: 1.6;
}

/* Validation States */
.form-control.is-invalid {
    border-color: var(--error-color);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23ef4444' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23ef4444' stroke='none'/%3e%3c/svg%3e");
    background-position: right calc(0.375em + 0.1875rem) center;
    background-repeat: no-repeat;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    padding-right: calc(1.5em + 0.75rem);
}

.form-control.is-invalid:focus {
    border-color: var(--error-color);
    box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
}

.invalid-feedback {
    display: none;
    color: var(--error-color);
    font-size: 0.875rem;
    margin-top: 0.5rem;
    font-weight: 500;
    padding-left: 0.5rem;
}

.form-control.is-invalid ~ .invalid-feedback {
    display: block;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-5px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Form Actions */
.form-actions {
    padding: 2.5rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-top: 1px solid var(--border-light);
    display: flex;
    gap: 1rem;
    justify-content: center;
    align-items: center;
}

.btn {
    padding: 1rem 2.5rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    border: none;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    letter-spacing: -0.025em;
}

.btn-submit {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
    box-shadow: 
        0 4px 6px -1px rgba(16, 185, 129, 0.2),
        0 2px 4px -1px rgba(16, 185, 129, 0.1);
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 
        0 8px 15px -3px rgba(16, 185, 129, 0.3),
        0 4px 6px -2px rgba(16, 185, 129, 0.15);
    background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary-color) 100%);
}

.btn-submit:active {
    transform: translateY(0);
}

.btn-outline-secondary {
    background: white;
    color: var(--text-medium);
    border: 2px solid var(--border-medium);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.btn-outline-secondary:hover {
    background: var(--text-medium);
    color: white;
    border-color: var(--text-medium);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Specific Fields Animation */
.animal-specific {
    animation: fadeInUp 0.5s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Grid System Enhancement */
.row {
    margin-left: -0.75rem;
    margin-right: -0.75rem;
}

.col-md-6, .col-12 {
    padding-left: 0.75rem;
    padding-right: 0.75rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .form-header {
        padding: 2rem 1.5rem;
    }

    .form-header h3 {
        font-size: 1.5rem;
    }

    .form-section {
        padding: 2rem 1.5rem;
    }

    .form-actions {
        padding: 2rem 1.5rem;
        flex-direction: column;
        gap: 1rem;
    }

    .btn {
        width: 100%;
        justify-content: center;
    }

    .form-control {
        padding: 0.875rem 1rem;
    }
}

@media (max-width: 576px) {
    .form-container {
        border-radius: 16px;
        margin: 0.5rem;
    }

    .form-header {
        padding: 1.75rem 1.25rem;
    }

    .form-header h3 {
        font-size: 1.375rem;
    }

    .form-section {
        padding: 1.75rem 1.25rem;
    }

    .form-section h4 {
        font-size: 1.125rem;
    }

    .form-actions {
        padding: 1.75rem 1.25rem;
    }

    .form-control {
        padding: 0.75rem 0.875rem;
        font-size: 0.95rem;
    }
}

/* Loading State */
.btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none !important;
}

.btn:disabled:hover {
    transform: none !important;
    box-shadow: none !important;
}

/* Focus States for Accessibility */
.form-control:focus-visible {
    outline: 2px solid var(--primary-color);
    outline-offset: 2px;
}

.btn:focus-visible {
    outline: 2px solid var(--primary-color);
    outline-offset: 2px;
}

/* Smooth Transitions */
.form-container,
.form-section,
.form-control,
.btn {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Custom Scrollbar for Textarea */
textarea.form-control::-webkit-scrollbar {
    width: 6px;
}

textarea.form-control::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

textarea.form-control::-webkit-scrollbar-thumb {
    background: var(--border-medium);
    border-radius: 3px;
}

textarea.form-control::-webkit-scrollbar-thumb:hover {
    background: var(--text-light);
}

/* Enhanced Select Dropdown */
select.form-control option {
    padding: 0.75rem;
    background: white;
    color: var(--text-dark);
}

select.form-control option:hover {
    background: var(--primary-color);
    color: white;
}

/* Number Input Styling */
input[type="number"].form-control {
    appearance: textfield;
}

input[type="number"].form-control::-webkit-outer-spin-button,
input[type="number"].form-control::-webkit-inner-spin-button {
    appearance: none;
    margin: 0;
}

/* Date Input Styling */
input[type="date"].form-control {
    position: relative;
}

input[type="date"].form-control::-webkit-calendar-picker-indicator {
    background: transparent;
    bottom: 0;
    color: transparent;
    cursor: pointer;
    height: auto;
    left: 0;
    position: absolute;
    right: 0;
    top: 0;
    width: auto;
}

/* Success State Preview */
.form-control:valid:not(:placeholder-shown) {
    border-color: var(--success-color);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath fill='%2310b981' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
    background-position: right calc(0.375em + 0.1875rem) center;
    background-repeat: no-repeat;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    padding-right: calc(1.5em + 0.75rem);
}

/* Container Fluid Enhancement */
.container-fluid {
    padding: 2rem;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    min-height: 100vh;
}

@media (max-width: 768px) {
    .container-fluid {
        padding: 1rem;
    }
}

@media (max-width: 576px) {
    .container-fluid {
        padding: 0.5rem;
    }
}

/* Form Group Hover Effects */
.form-group:hover .form-label {
    color: var(--primary-color);
}

/* Required Field Indicator Enhancement */
.form-label.required {
    position: relative;
}

.form-label.required::before {
    content: '';
    position: absolute;
    left: -8px;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 4px;
    background: var(--error-color);
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const animalTypeSelect = document.getElementById('animal_type_id');
    const specificFields = document.getElementById('specific-fields');
    const chickenFields = document.getElementById('chicken-fields');
    const ruminantFields = document.getElementById('ruminant-fields');
    const otherFields = document.getElementById('other-fields');
    const form = document.getElementById('livestockForm');

    // Data kategori hewan
    const animalCategories = {
        @foreach($animalTypes as $type)
        '{{ $type->id }}': '{{ $type->category }}',
        @endforeach
    };

    // Hide all specific fields dengan animasi
    function hideAllSpecificFields() {
        const fields = [chickenFields, ruminantFields, otherFields];
        fields.forEach(field => {
            if (field.style.display !== 'none') {
                field.style.opacity = '0';
                field.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    field.style.display = 'none';
                }, 300);
            }
        });
        specificFields.style.display = 'none';
    }

    // Show specific fields dengan animasi
    function showSpecificFields(animalTypeId) {
        hideAllSpecificFields();

        if (animalTypeId && animalCategories[animalTypeId]) {
            setTimeout(() => {
                specificFields.style.display = 'block';
                const category = animalCategories[animalTypeId];
                let targetField;

                switch(category) {
                    case 'poultry':
                        targetField = chickenFields;
                        break;
                    case 'large_animal':
                        targetField = ruminantFields;
                        break;
                    default:
                        targetField = otherFields;
                        break;
                }

                targetField.style.display = 'block';
                setTimeout(() => {
                    targetField.style.opacity = '1';
                    targetField.style.transform = 'translateY(0)';
                }, 50);
            }, 300);
        }
    }

    // Enhanced form validation
    function validateForm() {
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        // Validasi tanggal
        const birthDate = document.getElementById('birth_date');
        const acquisitionDate = document.getElementById('acquisition_date');
        
        if (birthDate.value && acquisitionDate.value) {
            if (new Date(birthDate.value) > new Date(acquisitionDate.value)) {
                birthDate.classList.add('is-invalid');
                acquisitionDate.classList.add('is-invalid');
                showMessage('Tanggal lahir tidak boleh lebih besar dari tanggal akuisisi', 'error');
                isValid = false;
            }
        }

        return isValid;
    }

    // Real-time validation
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.hasAttribute('required') && !this.value.trim()) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });

        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid') && this.value.trim()) {
                this.classList.remove('is-invalid');
            }
        });
    });

    // Event listener untuk perubahan jenis hewan
    animalTypeSelect.addEventListener('change', function() {
        const selectedTypeId = this.value;
        showSpecificFields(selectedTypeId);
        
        // Reset specific fields ketika jenis hewan berubah
        if (selectedTypeId) {
            const specificInputs = specificFields.querySelectorAll('input, select');
            specificInputs.forEach(input => {
                input.value = '';
                input.classList.remove('is-invalid');
            });
        }
    });

    // Form submission dengan validasi
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validateForm()) {
            // Show loading state
            const submitBtn = form.querySelector('.btn-submit');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
            submitBtn.disabled = true;

            // Submit form setelah delay kecil untuk animasi
            setTimeout(() => {
                form.submit();
            }, 500);
        } else {
            showMessage('Harap periksa kembali form yang diisi', 'error');
            
            // Scroll ke field pertama yang error
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
                firstInvalid.focus();
            }
        }
    });

    // Auto-format untuk nomor identifikasi
    const identificationInput = document.getElementById('identification_number');
    if (identificationInput) {
        identificationInput.addEventListener('input', function(e) {
            // Auto-uppercase untuk kode
            this.value = this.value.toUpperCase();
        });
    }

    // Initialize
    hideAllSpecificFields();
    
    // Show fields jika ada animal type yang sudah dipilih (saat form validation error)
    const initialAnimalTypeId = animalTypeSelect.value;
    if (initialAnimalTypeId) {
        showSpecificFields(initialAnimalTypeId);
    }

    // Message system (sama seperti di halaman index)
    function showMessage(message, type = 'info') {
        // Implementasi message box seperti di halaman index
        console.log(`${type.toUpperCase()}: ${message}`);
    }
});
</script>
@endpush