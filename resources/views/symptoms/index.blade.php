@extends('layouts.app')

@section('title', 'Gejala Penyakit - TernakIN')
@section('page-title', 'Gejala Penyakit')

@section('content')
<div class="content-header">
    <div class="header-actions">
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Cari gejala..." value="{{ request('search') }}">
            <button type="button" id="searchBtn">
                <i class="fas fa-search"></i>
            </button>
        </div>
        <div class="filters">
            <select id="animalTypeFilter">
                <option value="">Semua Jenis Hewan</option>
                @foreach($animalTypes as $type)
                <option value="{{ $type->id }}" {{ request('animal_type_id') == $type->id ? 'selected' : '' }}>
                    {{ $type->name }}
                </option>
                @endforeach
            </select>
            <label class="checkbox-label">
                <input type="checkbox" id="commonFilter" {{ request('common') ? 'checked' : '' }}>
                Gejala Umum Saja
            </label>
        </div>
    </div>
</div>

<div class="symptoms-grid">
    @forelse($symptoms as $symptom)
    <div class="symptom-card">
        <div class="symptom-header">
            <h3>{{ $symptom->name }}</h3>
            @if($symptom->is_common)
            <span class="common-badge">Umum</span>
            @endif
        </div>
        @if($symptom->description)
        <div class="symptom-description">
            <p>{{ Str::limit($symptom->description, 150) }}</p>
        </div>
        @endif
        <div class="symptom-diseases">
            <strong>Penyakit Terkait:</strong>
            <div class="diseases-list">
                @foreach($symptom->diseases->take(3) as $disease)
                <span class="disease-tag">{{ $disease->name }}</span>
                @endforeach
                @if($symptom->diseases->count() > 3)
                <span class="more-diseases">+{{ $symptom->diseases->count() - 3 }} lainnya</span>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="no-data">
        <i class="fas fa-search"></i>
        <h3>Tidak ada gejala ditemukan</h3>
        <p>Coba ubah kriteria pencarian atau filter</p>
    </div>
    @endforelse
</div>

<div class="pagination">
    {{ $symptoms->appends(request()->query())->links() }}
</div>
@endsection

@push('scripts')
<script>
document.getElementById('searchBtn').addEventListener('click', function() {
    const search = document.getElementById('searchInput').value;
    const animalType = document.getElementById('animalTypeFilter').value;
    const common = document.getElementById('commonFilter').checked;
    const url = new URL(window.location);

    if (search) url.searchParams.set('search', search);
    else url.searchParams.delete('search');

    if (animalType) url.searchParams.set('animal_type_id', animalType);
    else url.searchParams.delete('animal_type_id');

    if (common) url.searchParams.set('common', '1');
    else url.searchParams.delete('common');

    window.location.href = url.toString();
});

document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        document.getElementById('searchBtn').click();
    }
});

document.getElementById('animalTypeFilter').addEventListener('change', function() {
    document.getElementById('searchBtn').click();
});

document.getElementById('commonFilter').addEventListener('change', function() {
    document.getElementById('searchBtn').click();
});
</script>
@endpush
