@extends('layouts.app')

@section('title', $disease->name . ' - TernakIN')
@section('page-title', 'Detail Penyakit')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main Content -->
        <div class="col-12 col-lg-8">
            <div class="disease-detail-card">
                <!-- Header -->
                <div class="disease-header">
                    <div class="disease-title-section">
                        <div class="disease-code-badge {{ $disease->is_zoonotic ? 'zoonotic' : '' }}">
                            {{ $disease->disease_code }}
                            @if($disease->is_zoonotic)
                            <span class="zoonotic-indicator">
                                <i class="fas fa-exclamation-triangle me-2"></i> Zoonosis
                            </span>
                            @endif
                        </div>
                        <h1 class="disease-name">{{ $disease->name }}</h1>
                        @if($disease->other_names)
                        <p class="disease-alias">Juga dikenal sebagai: <em>{{ $disease->other_names }}</em></p>
                        @endif
                    </div>
                </div>

                <!-- Disease Image -->
                <div class="disease-image-section">
                    @if($disease->image)
                    <img src="{{ asset('storage/' . $disease->image) }}" alt="{{ $disease->name }}" class="disease-main-image">
                    @else
                    <div class="disease-image-placeholder">
                        <i class="fas fa-bug"></i>
                        <p>Gambar tidak tersedia</p>
                    </div>
                    @endif
                </div>

                <!-- Description -->
                <div class="info-section">
                    <div class="section-header">
                        <i class="fas fa-info-circle text-primary section-icon"></i>
                        <h3>Deskripsi Penyakit</h3>
                    </div>
                    <div class="info-content">
                        <p>{{ $disease->description }}</p>
                    </div>
                </div>

                <!-- Basic Information -->
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="info-section">
                            <div class="section-header">
                                <i class="fas fa-bug text-danger section-icon"></i>
                                <h3>Informasi Dasar</h3>
                            </div>
                            <div class="info-content">
                                <div class="info-item">
                                    <strong>Status Zoonosis:</strong>
                                    <span class="badge {{ $disease->is_zoonotic ? 'bg-danger' : 'bg-success' }}">
                                        {{ $disease->is_zoonotic ? 'Ya - Berisiko ke Manusia' : 'Tidak - Hanya Hewan' }}
                                    </span>
                                </div>
                                <div class="info-item">
                                    <strong>Jenis Hewan Terkena:</strong>
                                    <div class="animal-types">
                                        @foreach($disease->animalTypes as $animalType)
                                        <span class="animal-badge">{{ $animalType->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="info-section">
                            <div class="section-header">
                                <i class="fas fa-share-alt text-warning section-icon"></i>
                                <h3>Cara Penularan</h3>
                            </div>
                            <div class="info-content">
                                <p>{{ $disease->transmission_method }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Symptoms -->
                @if($disease->symptoms->count() > 0)
                <div class="info-section">
                    <div class="section-header">
                        <i class="fas fa-stethoscope text-info section-icon"></i>
                        <h3>Gejala Klinis</h3>
                    </div>
                    <div class="info-content">
                        <div class="symptoms-grid">
                            @foreach($disease->symptoms as $symptom)
                            <div class="symptom-item {{ $symptom->pivot->is_primary ? 'primary-symptom' : '' }}">
                                <div class="symptom-header">
                                    <span class="symptom-name">{{ $symptom->name }}</span>
                                    @if($symptom->pivot->is_primary)
                                    <span class="primary-badge" title="Gejala Primer">
                                        <i class="fas fa-star me-2"></i> Primer
                                    </span>
                                    @endif
                                </div>
                                @if($symptom->pivot->probability)
                                <div class="symptom-probability">
                                    <div class="probability-bar">
                                        <div class="probability-fill" style="width: {{ $symptom->pivot->probability }}%"></div>
                                    </div>
                                    <span class="probability-text">{{ $symptom->pivot->probability }}% kemunculan</span>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Treatment & Prevention -->
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="info-section">
                            <div class="section-header">
                                <i class="fas fa-medkit text-success section-icon"></i>
                                <h3>Penanganan & Pengobatan</h3>
                            </div>
                            <div class="info-content">
                                <p>{{ $disease->general_treatment }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="info-section">
                            <div class="section-header">
                                <i class="fas fa-shield-alt text-primary section-icon"></i>
                                <h3>Pencegahan</h3>
                            </div>
                            <div class="info-content">
                                <p>{{ $disease->prevention_method ?? 'Tidak ada informasi pencegahan spesifik.' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Risk Factors -->
                @if($disease->risk_factors)
                <div class="info-section">
                    <div class="section-header">
                        <i class="fas fa-exclamation-triangle text-warning section-icon"></i>
                        <h3>Faktor Risiko</h3>
                    </div>
                    <div class="info-content">
                        <p>{{ $disease->risk_factors }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-12 col-lg-4">
            <!-- Quick Actions -->
            <div class="sidebar-card">
                <div class="section-header">
                    <i class="fas fa-lightbulb section-icon"></i>
                    <h4>Tips Cepat</h4>
                </div>
                <div class="quick-tips">
                    <div class="tip-item">
                        <i class="fas fa-notes-medical text-primary tip-icon"></i>
                        <span>Segera konsultasi dengan dokter hewan jika menemukan gejala</span>
                    </div>
                    <div class="tip-item">
                        <i class="fas fa-handshake text-success tip-icon"></i>
                        <span>Gunakan alat pelindung diri saat menangani hewan sakit</span>
                    </div>
                    <div class="tip-item">
                        <i class="fas fa-syringe text-info tip-icon"></i>
                        <span>Lakukan vaksinasi rutin sesuai jadwal</span>
                    </div>
                    @if($disease->is_zoonotic)
                    <div class="tip-item">
                        <i class="fas fa-user-shield text-danger tip-icon"></i>
                        <span>Hati-hati! Penyakit ini dapat menular ke manusia</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Related Diseases -->
            <div class="sidebar-card">
                <div class="section-header">
                    <i class="fas fa-link section-icon"></i>
                    <h4>Penyakit Terkait</h4>
                </div>
                <div class="related-diseases">
                    @php
                        $relatedDiseases = \App\Models\Disease::where('id', '!=', $disease->id)
                            ->whereHas('animalTypes', function($query) use ($disease) {
                                $animalTypeIds = $disease->animalTypes->pluck('id');
                                $query->whereIn('animal_type_id', $animalTypeIds);
                            })
                            ->limit(3)
                            ->get();
                    @endphp
                    
                    @forelse($relatedDiseases as $related)
                    <a href="{{ route('diseases.show', $related->id) }}" class="related-disease-item">
                        <div class="related-disease-name">{{ $related->name }}</div>
                        <div class="related-disease-code">{{ $related->disease_code }}</div>
                    </a>
                    @empty
                    <p class="text-muted">Tidak ada penyakit terkait</p>
                    @endforelse
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="sidebar-card emergency-contact">
                <div class="section-header">
                    <i class="fas fa-phone-alt text-danger section-icon"></i>
                    <h4>Kontak Darurat</h4>
                </div>
                <div class="emergency-info">
                    <p>Jika keadaan darurat, segera hubungi:</p>
                    <div class="contact-item">
                        <i class="fas fa-hospital contact-icon"></i>
                        <div>
                            <strong>Klinik Hewan Terdekat</strong>
                            <small>Buka 24 jam</small>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone contact-icon"></i>
                        <div>
                            <strong>119</strong>
                            <small>Hotline Kesehatan Hewan</small>
                        </div>
                    </div>
                </div>
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
    --secondary-color: #8b5cf6;
    --accent-color: #f59e0b;
    --danger-color: #ef4444;
    --warning-color: #f59e0b;
    --info-color: #3b82f6;
    --text-dark: #1f2937;
    --text-medium: #4b5563;
    --text-light: #6b7280;
    --background-light: #f8fafc;
    --border-light: #e2e8f0;
    --border-medium: #d1d5db;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    --gradient-primary: linear-gradient(135deg, #10b981 0%, #059669 100%);
    --gradient-secondary: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    --gradient-danger: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

/* Disease Detail Styles */
.disease-detail-card {
    background: white;
    border-radius: 24px;
    box-shadow: var(--shadow-xl);
    overflow: hidden;
    margin-bottom: 2rem;
    border: 1px solid var(--border-light);
    backdrop-filter: blur(10px);
    position: relative;
}

.disease-detail-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--gradient-primary);
}

.disease-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 3rem 2rem;
    position: relative;
    overflow: hidden;
}

.disease-header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
    animation: float 8s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-15px) rotate(180deg); }
}

.disease-title-section {
    text-align: center;
    position: relative;
    z-index: 2;
}

.disease-code-badge {
    background: rgba(255,255,255,0.25);
    padding: 12px 24px;
    border-radius: 25px;
    font-size: 0.95rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    box-shadow: var(--shadow-md);
}

.zoonotic-indicator {
    background: rgba(220, 38, 38, 0.9);
    padding: 8px 16px;
    border-radius: 15px;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    animation: pulse 2s infinite;
    border: 1px solid rgba(255,255,255,0.3);
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.disease-name {
    font-size: 2.5rem;
    font-weight: 800;
    margin: 0 0 0.75rem 0;
    letter-spacing: -0.025em;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.disease-alias {
    opacity: 0.95;
    margin: 0;
    font-size: 1.2rem;
    font-weight: 500;
}

/* Disease Image */
.disease-image-section {
    padding: 2.5rem;
    text-align: center;
    border-bottom: 1px solid var(--border-light);
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

.disease-main-image {
    max-width: 100%;
    max-height: 450px;
    border-radius: 20px;
    box-shadow: var(--shadow-xl);
    border: 3px solid white;
    transition: all 0.3s ease;
}

.disease-main-image:hover {
    transform: scale(1.02);
    box-shadow: var(--shadow-xl), 0 0 0 4px rgba(16, 185, 129, 0.1);
}

.disease-image-placeholder {
    height: 350px;
    background: var(--gradient-primary);
    border-radius: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 5rem;
    box-shadow: var(--shadow-lg);
    position: relative;
    overflow: hidden;
}

.disease-image-placeholder::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
    animation: float 6s ease-in-out infinite;
}

.disease-image-placeholder p {
    font-size: 1.1rem;
    margin: 1.5rem 0 0 0;
    font-weight: 600;
    z-index: 2;
    position: relative;
}

/* Section Header Modern */
.section-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.75rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid var(--border-light);
}

.section-icon {
    font-size: 1.6rem;
    flex-shrink: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    background: var(--gradient-primary);
    color: white;
    box-shadow: var(--shadow-md);
}

.section-header h3,
.section-header h4 {
    color: var(--text-dark);
    font-size: 1.4rem;
    font-weight: 700;
    margin: 0;
    letter-spacing: -0.025em;
}

/* Info Sections Modern */
.info-section {
    padding: 2.5rem;
    border-bottom: 1px solid var(--border-light);
    transition: all 0.3s ease;
    position: relative;
}

.info-section:hover {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

.info-section:last-child {
    border-bottom: none;
}

.info-content {
    color: var(--text-medium);
    line-height: 1.7;
    font-size: 1.05rem;
}

.info-item {
    margin-bottom: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    padding: 1.25rem;
    background: white;
    border-radius: 12px;
    border: 1px solid var(--border-light);
    transition: all 0.3s ease;
}

.info-item:hover {
    border-color: var(--primary-color);
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.info-item strong {
    color: var(--text-dark);
    font-size: 1rem;
    font-weight: 600;
}

.causative-agent {
    background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%);
    color: #1d4ed8;
    padding: 10px 18px;
    border-radius: 10px;
    font-weight: 600;
    display: inline-block;
    border: 2px solid #dbeafe;
    box-shadow: var(--shadow-sm);
}

.animal-types {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 0.5rem;
}

.animal-badge {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    color: #166534;
    padding: 10px 18px;
    border-radius: 10px;
    font-size: 0.9rem;
    font-weight: 600;
    border: 2px solid #bbf7d0;
    transition: all 0.3s ease;
    box-shadow: var(--shadow-sm);
}

.animal-badge:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-md);
    border-color: var(--primary-color);
}

/* Symptoms Modern */
.symptoms-grid {
    display: grid;
    gap: 1.5rem;
}

.symptom-item {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 2px solid var(--border-light);
    border-radius: 16px;
    padding: 1.5rem;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.symptom-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: var(--gradient-primary);
    transform: scaleY(0);
    transition: transform 0.3s ease;
}

.symptom-item:hover {
    border-color: var(--primary-color);
    box-shadow: var(--shadow-lg);
    transform: translateY(-4px);
}

.symptom-item:hover::before {
    transform: scaleY(1);
}

.symptom-item.primary-symptom {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border-color: #7dd3fc;
    border-left: 6px solid #0ea5e9;
}

.symptom-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    gap: 1rem;
}

.symptom-name {
    font-weight: 700;
    color: var(--text-dark);
    flex: 1;
    font-size: 1.1rem;
    letter-spacing: -0.025em;
}

.primary-badge {
    background: var(--gradient-warning);
    color: white;
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 0.85rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: var(--shadow-md);
}

.symptom-probability {
    margin-top: 1rem;
}

.probability-bar {
    background: #e5e7eb;
    border-radius: 12px;
    height: 12px;
    overflow: hidden;
    margin-bottom: 0.75rem;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
}

.probability-fill {
    background: var(--gradient-primary);
    height: 100%;
    border-radius: 12px;
    transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.probability-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { left: -100%; }
    100% { left: 100%; }
}

.probability-text {
    font-size: 0.9rem;
    color: var(--text-medium);
    font-weight: 600;
}

/* Sidebar Modern */
.sidebar-card {
    background: white;
    border-radius: 20px;
    box-shadow: var(--shadow-lg);
    padding: 2rem;
    margin-bottom: 2rem;
    border: 1px solid var(--border-light);
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.sidebar-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--gradient-primary);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.sidebar-card:hover::before {
    transform: scaleX(1);
}

.sidebar-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-xl);
}

.quick-tips {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.tip-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.25rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 12px;
    border: 1px solid transparent;
    transition: all 0.3s ease;
}

.tip-item:hover {
    border-color: var(--primary-color);
    background: white;
    transform: translateX(8px);
    box-shadow: var(--shadow-md);
}

.tip-icon {
    margin-top: 0.1rem;
    flex-shrink: 0;
    font-size: 1.3rem;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    background: var(--gradient-primary);
    color: white;
}

.tip-item span {
    font-size: 0.95rem;
    color: var(--text-medium);
    line-height: 1.6;
    flex: 1;
    font-weight: 500;
}

.related-diseases {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.related-disease-item {
    padding: 1.5rem;
    border: 2px solid var(--border-light);
    border-radius: 12px;
    text-decoration: none;
    color: inherit;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    background: white;
    position: relative;
    overflow: hidden;
}

.related-disease-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(16, 185, 129, 0.05), transparent);
    transition: left 0.5s ease;
}

.related-disease-item:hover {
    border-color: var(--primary-color);
    background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
    transform: translateX(8px) scale(1.02);
    box-shadow: var(--shadow-lg);
}

.related-disease-item:hover::before {
    left: 100%;
}

.related-disease-name {
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 0.5rem;
    font-size: 1rem;
    letter-spacing: -0.025em;
}

.related-disease-code {
    font-size: 0.85rem;
    color: var(--text-light);
    font-weight: 500;
}

.emergency-contact {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    border: 2px solid #fecaca;
    position: relative;
}

.emergency-contact::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--gradient-danger);
}

.emergency-info p {
    color: #dc2626;
    font-weight: 600;
    margin-bottom: 1.75rem;
    font-size: 1rem;
    text-align: center;
    padding: 0.75rem;
    background: rgba(220, 38, 38, 0.05);
    border-radius: 8px;
    border: 1px solid rgba(220, 38, 38, 0.1);
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem 0;
    border-bottom: 1px solid #fecaca;
    transition: all 0.3s ease;
}

.contact-item:hover {
    transform: translateX(4px);
}

.contact-item:last-child {
    border-bottom: none;
}

.contact-icon {
    color: #dc2626;
    font-size: 1.5rem;
    flex-shrink: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(220, 38, 38, 0.1);
    border-radius: 10px;
}

.contact-item div {
    flex: 1;
}

.contact-item strong {
    color: var(--text-dark);
    display: block;
    margin-bottom: 0.25rem;
    font-size: 1rem;
    font-weight: 700;
}

.contact-item small {
    color: var(--text-light);
    font-size: 0.85rem;
    font-weight: 500;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .disease-name {
        font-size: 2.2rem;
    }
    
    .info-section {
        padding: 2rem;
    }
}

@media (max-width: 768px) {
    .disease-header {
        padding: 2rem 1.5rem;
    }
    
    .disease-name {
        font-size: 1.8rem;
    }
    
    .info-section {
        padding: 1.75rem;
    }
    
    .disease-image-section {
        padding: 2rem 1.5rem;
    }
    
    .symptom-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .section-header {
        gap: 0.875rem;
    }
    
    .sidebar-card {
        padding: 1.75rem;
    }
    
    .disease-code-badge {
        flex-direction: column;
        gap: 0.75rem;
        text-align: center;
    }
}

@media (max-width: 576px) {
    .disease-header {
        padding: 1.75rem 1.25rem;
    }
    
    .disease-name {
        font-size: 1.5rem;
    }
    
    .info-section {
        padding: 1.5rem;
    }
    
    .sidebar-card {
        padding: 1.5rem;
    }
    
    .section-header {
        gap: 0.75rem;
    }
    
    .tip-item {
        gap: 0.875rem;
        padding: 1rem;
    }
    
    .contact-item {
        gap: 0.875rem;
    }
    
    .disease-image-placeholder {
        height: 250px;
        font-size: 3.5rem;
    }
    
    .animal-badge {
        padding: 8px 14px;
        font-size: 0.85rem;
    }
}

/* Smooth Animations */
.disease-detail-card,
.sidebar-card,
.info-item,
.symptom-item,
.tip-item,
.related-disease-item,
.contact-item {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Loading Animation */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.disease-detail-card,
.sidebar-card {
    animation: fadeInUp 0.6s ease-out;
}

/* Custom Scrollbar */
.info-content::-webkit-scrollbar {
    width: 6px;
}

.info-content::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.info-content::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.info-content::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
@endpush