@extends('layouts.app')

@section('title', $medicine->name . ' - TernakIN')
@section('page-title', $medicine->name)

@section('content')
<div class="medicine-detail">
    <div class="medicine-header">
        <div class="medicine-title">
            <h1>{{ $medicine->name }}</h1>
            <span class="medicine-type type-{{ $medicine->type }}">{{ ucfirst($medicine->type) }}</span>
        </div>
        <div class="medicine-meta">
            @if($medicine->active_ingredient)
            <div class="meta-item">
                <i class="fas fa-flask"></i>
                <span>{{ $medicine->active_ingredient }}</span>
            </div>
            @endif
            @if($medicine->dosage_form)
            <div class="meta-item">
                <i class="fas fa-capsules"></i>
                <span>{{ $medicine->dosage_form }}</span>
            </div>
            @endif
            @if($medicine->manufacturer)
            <div class="meta-item">
                <i class="fas fa-industry"></i>
                <span>{{ $medicine->manufacturer }}</span>
            </div>
            @endif
        </div>
    </div>

    <div class="medicine-content">
        @if($medicine->description)
        <div class="content-section">
            <h2>Deskripsi</h2>
            <p>{{ $medicine->description }}</p>
        </div>
        @endif

        @if($medicine->indications)
        <div class="content-section">
            <h2>Indikasi</h2>
            <p>{{ $medicine->indications }}</p>
        </div>
        @endif

        @if($medicine->contraindications)
        <div class="content-section">
            <h2>Kontraindikasi</h2>
            <p>{{ $medicine->contraindications }}</p>
        </div>
        @endif

        @if($medicine->side_effects)
        <div class="content-section">
            <h2>Efek Samping</h2>
            <p>{{ $medicine->side_effects }}</p>
        </div>
        @endif

        @if($medicine->storage_conditions)
        <div class="content-section">
            <h2>Kondisi Penyimpanan</h2>
            <p>{{ $medicine->storage_conditions }}</p>
        </div>
        @endif

        @if($medicine->diseases->count() > 0)
        <div class="content-section">
            <h2>Penyakit Terkait</h2>
            <div class="related-diseases">
                @foreach($medicine->diseases as $disease)
                <div class="disease-item">
                    <h4><a href="{{ route('diseases.show', $disease->id) }}">{{ $disease->name }}</a></h4>
                    <p>{{ Str::limit($disease->description, 100) }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <div class="medicine-actions">
        <a href="{{ route('medicines.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
        </a>
        <button class="btn btn-primary" onclick="shareMedicine()">
            <i class="fas fa-share"></i> Bagikan
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
function shareMedicine() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $medicine->name }}',
            text: 'Pelajari tentang {{ $medicine->name }} di TernakIN',
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
