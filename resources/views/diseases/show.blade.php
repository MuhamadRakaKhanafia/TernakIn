@extends('layouts.app')

@section('title', $disease->name . ' - TernakIN')
@section('page-title', $disease->name)

@section('content')
<div class="disease-detail">
    <div class="disease-header">
        <div class="disease-title">
            <h1>{{ $disease->name }}</h1>
            <div class="disease-code">{{ $disease->disease_code }}</div>
        </div>
        <div class="disease-meta">
            <div class="meta-item">
                <i class="fas fa-bug"></i>
                <span>{{ $disease->causative_agent }}</span>
            </div>
            <div class="meta-item">
                <i class="fas fa-dove"></i>
                <span>{{ $disease->animalTypes->pluck('name')->join(', ') }}</span>
            </div>
            @if($disease->mortality_rate)
            <div class="meta-item">
                <i class="fas fa-chart-line"></i>
                <span>Mortalitas: {{ $disease->mortality_rate }}%</span>
            </div>
            @endif
            @if($disease->is_zoonotic)
            <div class="meta-item zoonotic">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Zoonosis</span>
            </div>
            @endif
        </div>
    </div>

    <div class="disease-content">
        <div class="content-section">
            <h2>Deskripsi</h2>
            <p>{{ $disease->description }}</p>
        </div>

        @if($disease->symptoms->count() > 0)
        <div class="content-section">
            <h2>Gejala</h2>
            <ul class="symptoms-list">
                @foreach($disease->symptoms as $symptom)
                <li>{{ $symptom->name }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if($disease->preventionMethods->count() > 0)
        <div class="content-section">
            <h2>Pencegahan</h2>
            <div class="prevention-methods">
                @foreach($disease->preventionMethods as $method)
                <div class="prevention-item">
                    <h4>{{ $method->title }}</h4>
                    <p>{{ $method->description }}</p>
                    @if($method->category)
                    <span class="category">{{ $method->category }}</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($disease->diseaseMedicines->count() > 0)
        <div class="content-section">
            <h2>Pengobatan</h2>
            <div class="medicines-list">
                @foreach($disease->diseaseMedicines as $diseaseMedicine)
                <div class="medicine-item">
                    <h4>{{ $diseaseMedicine->medicine->name }}</h4>
                    <div class="medicine-details">
                        @if($diseaseMedicine->recommended_dosage)
                        <p><strong>Dosis:</strong> {{ $diseaseMedicine->recommended_dosage }}</p>
                        @endif
                        @if($diseaseMedicine->administration_notes)
                        <p><strong>Cara Pemberian:</strong> {{ $diseaseMedicine->administration_notes }}</p>
                        @endif
                        @if($diseaseMedicine->effectiveness)
                        <p><strong>Efektivitas:</strong> {{ $diseaseMedicine->effectiveness }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($disease->other_names)
        <div class="content-section">
            <h2>Nama Lain</h2>
            <p>{{ $disease->other_names }}</p>
        </div>
        @endif
    </div>

    <div class="disease-actions">
        <a href="{{ route('diseases.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
        </a>
        <button class="btn btn-primary" onclick="shareDisease()">
            <i class="fas fa-share"></i> Bagikan
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
function shareDisease() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $disease->name }}',
            text: 'Pelajari tentang {{ $disease->name }} di TernakIN',
            url: window.location.href
        });
    } else {
        // Fallback untuk browser yang tidak mendukung Web Share API
        const url = window.location.href;
        navigator.clipboard.writeText(url).then(() => {
            alert('Link berhasil disalin ke clipboard!');
        });
    }
}
</script>
@endpush
