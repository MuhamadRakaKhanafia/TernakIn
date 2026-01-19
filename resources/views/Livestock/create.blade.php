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

                <form id="livestockForm" method="POST" action="{{ route('livestocks.store') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Pilih Jenis Hewan -->
                    <div class="form-section">
                        <h4><i class="fas fa-paw me-2"></i>Pilih Jenis Hewan</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="animal_type_id" class="form-label required">Jenis Hewan</label>
                                    <select class="form-control @error('animal_type_id') is-invalid @enderror" id="animal_type_id" name="animal_type_id" required>
                                        <option value="">Pilih jenis hewan...</option>
                                        @foreach($animalTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('animal_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }} ({{ $type->category == 'poultry' ? 'Unggas' : ($type->category == 'large_animal' ? 'Ternak Besar' : 'Lainnya') }})
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('animal_type_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Sapi-001, Ayam-Broiler-A" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="identification_number" class="form-label">Nomor Identifikasi</label>
                                    <input type="text" class="form-control @error('identification_number') is-invalid @enderror" id="identification_number" name="identification_number" value="{{ old('identification_number') }}" placeholder="Opsional: Tag ID, RFID, dll.">
                                    @error('identification_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="birth_date" class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control @error('birth_date') is-invalid @enderror" id="birth_date" name="birth_date" value="{{ old('birth_date') }}">
                                    @error('birth_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Jika diisi, usia akan dihitung otomatis</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="acquisition_date" class="form-label required">Tanggal Akuisisi</label>
                                    <input type="date" class="form-control @error('acquisition_date') is-invalid @enderror" id="acquisition_date" name="acquisition_date" value="{{ old('acquisition_date', date('Y-m-d')) }}" required>
                                    @error('acquisition_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sex" class="form-label required">Jenis Kelamin</label>
                                    <select class="form-control @error('sex') is-invalid @enderror" id="sex" name="sex" required>
                                        <option value="">Pilih jenis kelamin...</option>
                                        <option value="jantan" {{ old('sex') == 'jantan' ? 'selected' : '' }}>Jantan</option>
                                        <option value="betina" {{ old('sex') == 'betina' ? 'selected' : '' }}>Betina</option>
                                    </select>
                                    @error('sex')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="health_status" class="form-label required">Status Kesehatan</label>
                                    <select class="form-control @error('health_status') is-invalid @enderror" id="health_status" name="health_status" required>
                                        <option value="">Pilih status kesehatan...</option>
                                        <option value="sehat" {{ old('health_status') == 'sehat' ? 'selected' : '' }}>Sehat</option>
                                        <option value="sakit" {{ old('health_status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                    </select>
                                    @error('health_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vaccination_status" class="form-label required">Status Vaksinasi</label>
                                    <select class="form-control @error('vaccination_status') is-invalid @enderror" id="vaccination_status" name="vaccination_status" required>
                                        <option value="">Pilih status vaksinasi...</option>
                                        <option value="up_to_date" {{ old('vaccination_status') == 'up_to_date' ? 'selected' : '' }}>Terkini</option>
                                        <option value="need_update" {{ old('vaccination_status') == 'need_update' ? 'selected' : '' }}>Perlu Update</option>
                                        <option value="not_vaccinated" {{ old('vaccination_status') == 'not_vaccinated' ? 'selected' : '' }}>Belum Vaksin</option>
                                    </select>
                                    @error('vaccination_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="weight_kg" class="form-label">Berat (kg)</label>
                                    <input type="number" step="0.01" class="form-control @error('weight_kg') is-invalid @enderror" id="weight_kg" name="weight_kg" value="{{ old('weight_kg') }}" min="0.1" placeholder="Berat dalam kg">
                                    @error('weight_kg')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                            <input type="text" class="form-control @error('strain') is-invalid @enderror" id="strain" name="strain" value="{{ old('strain') }}" placeholder="Contoh: Broiler, Layer, dll.">
                                            @error('strain')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="age_weeks" class="form-label">Usia (minggu)</label>
                                            <input type="number" class="form-control @error('age_weeks') is-invalid @enderror" id="age_weeks" name="age_weeks" value="{{ old('age_weeks') }}" min="1" max="104" placeholder="Usia dalam minggu">
                                            @error('age_weeks')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Akan diisi otomatis jika tanggal lahir diisi</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="egg_production" class="form-label">Produksi Telur (butir/bulan)</label>
                                            <input type="number" class="form-control @error('egg_production') is-invalid @enderror" id="egg_production" name="egg_production" value="{{ old('egg_production') }}" min="0" max="365" placeholder="Jumlah telur per bulan">
                                            @error('egg_production')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="flock_size" class="form-label">Ukuran Kandang (ekor)</label>
                                            <input type="number" class="form-control @error('flock_size') is-invalid @enderror" id="flock_size" name="flock_size" value="{{ old('flock_size') }}" min="1" max="10000" placeholder="Jumlah ayam dalam kandang">
                                            @error('flock_size')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
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
                                            <input type="text" class="form-control @error('breed') is-invalid @enderror" id="breed" name="breed" value="{{ old('breed') }}" placeholder="Contoh: Limosin, Peranakan Ongole, dll.">
                                            @error('breed')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="age_months" class="form-label">Usia (bulan)</label>
                                            <input type="number" class="form-control @error('age_months') is-invalid @enderror" id="age_months" name="age_months" value="{{ old('age_months') }}" min="1" max="240" placeholder="Usia dalam bulan">
                                            @error('age_months')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Akan diisi otomatis jika tanggal lahir diisi</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="purpose" class="form-label">Tujuan Pemeliharaan</label>
                                            <select class="form-control @error('purpose') is-invalid @enderror" id="purpose" name="purpose">
                                                <option value="">Pilih tujuan...</option>
                                                <option value="peternakan" {{ old('purpose') == 'peternakan' ? 'selected' : '' }}>Peternakan</option>
                                                <option value="daging" {{ old('purpose') == 'daging' ? 'selected' : '' }}>Produksi Daging</option>
                                                <option value="susu" {{ old('purpose') == 'susu' ? 'selected' : '' }}>Produksi Susu</option>
                                                <option value="kulit" {{ old('purpose') == 'kulit' ? 'selected' : '' }}>Produksi Kulit</option>
                                            </select>
                                            @error('purpose')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="milk_production_liter" class="form-label">Produksi Susu (liter/hari)</label>
                                            <input type="number" step="0.01" class="form-control @error('milk_production_liter') is-invalid @enderror" id="milk_production_liter" name="milk_production_liter" value="{{ old('milk_production_liter') }}" min="0" max="50" placeholder="Produksi susu per hari">
                                            @error('milk_production_liter')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="pregnancy_status" class="form-label">Status Kehamilan</label>
                                            <select class="form-control @error('pregnancy_status') is-invalid @enderror" id="pregnancy_status" name="pregnancy_status">
                                                <option value="">Pilih status...</option>
                                                <option value="tidak_hamil" {{ old('pregnancy_status') == 'tidak_hamil' ? 'selected' : '' }}>Tidak Hamil</option>
                                                <option value="hamil" {{ old('pregnancy_status') == 'hamil' ? 'selected' : '' }}>Hamil</option>
                                            </select>
                                            @error('pregnancy_status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
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
                                            <input type="text" class="form-control @error('breed') is-invalid @enderror" id="breed" name="breed" value="{{ old('breed') }}" placeholder="Opsional">
                                            @error('breed')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="age_months" class="form-label">Usia (bulan)</label>
                                            <input type="number" class="form-control @error('age_months') is-invalid @enderror" id="age_months" name="age_months" value="{{ old('age_months') }}" min="1" max="240" placeholder="Usia dalam bulan">
                                            @error('age_months')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Akan diisi otomatis jika tanggal lahir diisi</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="purpose" class="form-label">Tujuan Pemeliharaan</label>
                                            <input type="text" class="form-control @error('purpose') is-invalid @enderror" id="purpose" name="purpose" value="{{ old('purpose') }}" placeholder="Opsional">
                                            @error('purpose')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Pakan dan Kandang -->
                    <div class="form-section">
                        <h4><i class="fas fa-seedling me-2"></i>Informasi Pakan dan Kandang</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="feed_type" class="form-label">Jenis Pakan</label>
                                    <input type="text" class="form-control @error('feed_type') is-invalid @enderror" id="feed_type" name="feed_type" value="{{ old('feed_type') }}" placeholder="Contoh: Konsentrat, Jagung, Rumput, dll.">
                                    @error('feed_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="daily_feed_kg" class="form-label">Pakan Harian (kg)</label>
                                    <input type="number" step="0.01" class="form-control @error('daily_feed_kg') is-invalid @enderror" id="daily_feed_kg" name="daily_feed_kg" value="{{ old('daily_feed_kg') }}" min="0.01" placeholder="Jumlah pakan per hari">
                                    @error('daily_feed_kg')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="housing_type" class="form-label">Tipe Kandang</label>
                                    <input type="text" class="form-control @error('housing_type') is-invalid @enderror" id="housing_type" name="housing_type" value="{{ old('housing_type') }}" placeholder="Contoh: Kandang Terbuka, Battery Cage, dll.">
                                    @error('housing_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="housing_size" class="form-label">Ukuran Kandang</label>
                                    <input type="text" class="form-control @error('housing_size') is-invalid @enderror" id="housing_size" name="housing_size" value="{{ old('housing_size') }}" placeholder="Contoh: 10x5 meter, 100 ekor kapasitas">
                                    @error('housing_size')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4" placeholder="Catatan tambahan tentang hewan ternak...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-submit">
                            <i class="fas fa-save me-2"></i>Simpan Data
                        </button>
                        <a href="{{ route('livestocks.index') }}" class="btn btn-outline-secondary">
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
/* Styles dari sebelumnya tetap dipertahankan */
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
}

.form-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
    color: white;
    padding: 2.5rem 2rem;
    text-align: center;
}

.form-header h3 {
    margin: 0;
    font-size: 1.75rem;
    font-weight: 800;
    letter-spacing: -0.025em;
}

.form-header p {
    margin: 0.75rem 0 0 0;
    opacity: 0.9;
    font-size: 1rem;
    font-weight: 400;
}

.form-section {
    padding: 2.5rem;
    border-bottom: 1px solid var(--border-light);
    background: white;
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
}

.form-label {
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 0.75rem;
    display: block;
}

.form-label.required::after {
    content: " *";
    color: var(--error-color);
    font-weight: 700;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-control {
    padding: 1rem 1.25rem;
    border: 2px solid var(--border-medium);
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s;
    width: 100%;
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
    outline: none;
}

.is-invalid {
    border-color: var(--error-color) !important;
}

.invalid-feedback {
    display: block;
    color: var(--error-color);
    font-size: 0.875rem;
    margin-top: 0.5rem;
}

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
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
}

.btn-submit {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px -3px rgba(16, 185, 129, 0.3);
}

.btn-outline-secondary {
    background: white;
    color: var(--text-medium);
    border: 2px solid var(--border-medium);
}

.btn-outline-secondary:hover {
    background: var(--text-medium);
    color: white;
    border-color: var(--text-medium);
}

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
    }
    
    .btn {
        width: 100%;
    }
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

    // Animal type categories mapping
    const animalTypeData = {
        @foreach($animalTypes as $type)
        {{ $type->id }}: '{{ $type->category }}',
        @endforeach
    };

    // Hide all specific fields
    function hideAllSpecificFields() {
        [chickenFields, ruminantFields, otherFields].forEach(field => {
            if (field) field.style.display = 'none';
        });
        if (specificFields) specificFields.style.display = 'none';
    }

    // Show specific fields based on animal type
    function showSpecificFields(animalTypeId) {
        hideAllSpecificFields();

        if (animalTypeId && animalTypeData[animalTypeId]) {
            specificFields.style.display = 'block';
            const category = animalTypeData[animalTypeId];
            
            switch(category) {
                case 'poultry':
                    chickenFields.style.display = 'block';
                    break;
                case 'large_animal':
                    ruminantFields.style.display = 'block';
                    break;
                default:
                    otherFields.style.display = 'block';
                    break;
            }
        }
    }

    // Initialize based on selected animal type
    if (animalTypeSelect.value) {
        showSpecificFields(animalTypeSelect.value);
    }

    // Handle animal type change
    animalTypeSelect.addEventListener('change', function() {
        showSpecificFields(this.value);
    });

    // Form validation
    function validateForm() {
        let isValid = true;
        
        // Clear previous error states
        form.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        
        // Validate required fields
        const requiredFields = form.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            }
        });
        
        // Validate dates
        const birthDate = document.getElementById('birth_date');
        const acquisitionDate = document.getElementById('acquisition_date');
        
        if (birthDate.value && acquisitionDate.value) {
            const birth = new Date(birthDate.value);
            const acquisition = new Date(acquisitionDate.value);
            
            if (birth > acquisition) {
                birthDate.classList.add('is-invalid');
                acquisitionDate.classList.add('is-invalid');
                showMessage('Tanggal lahir tidak boleh setelah tanggal akuisisi', 'error');
                isValid = false;
            }
        }
        
        return isValid;
    }

    // Form submission
    form.addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            
            // Scroll to first error
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
                firstInvalid.focus();
            }
            return;
        }
        
        // Show loading state
        const submitBtn = form.querySelector('.btn-submit');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
        submitBtn.disabled = true;
    });

    // Real-time validation
    form.querySelectorAll('input, select, textarea').forEach(input => {
        input.addEventListener('blur', function() {
            if (this.hasAttribute('required') && !this.value.trim()) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    });

    // Message function (same as index)
    function showMessage(message, type = 'info') {
        // Implementation same as index page
        console.log(`${type}: ${message}`);
    }

    // Auto-calculate age from birth date
    const birthDateInput = document.getElementById('birth_date');
    const acquisitionDateInput = document.getElementById('acquisition_date');
    
    function calculateAge() {
        if (!birthDateInput.value || !acquisitionDateInput.value) return;
        
        const birthDate = new Date(birthDateInput.value);
        const acquisitionDate = new Date(acquisitionDateInput.value);
        
        if (birthDate > acquisitionDate) {
            return;
        }
        
        const animalTypeId = animalTypeSelect.value;
        if (!animalTypeId) return;
        
        const category = animalTypeData[animalTypeId];
        const diffMs = acquisitionDate - birthDate;
        const diffDays = diffMs / (1000 * 60 * 60 * 24);
        
        if (category === 'poultry') {
            const weeks = Math.floor(diffDays / 7);
            document.getElementById('age_weeks').value = weeks > 0 ? weeks : 1;
        } else {
            const months = Math.floor(diffDays / 30.44);
            document.getElementById('age_months').value = months > 0 ? months : 1;
        }
    }
    
    if (birthDateInput && acquisitionDateInput) {
        birthDateInput.addEventListener('change', calculateAge);
        acquisitionDateInput.addEventListener('change', calculateAge);
    }
});
</script>
@endpush