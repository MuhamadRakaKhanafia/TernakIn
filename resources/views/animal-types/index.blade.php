@extends('layouts.app')

@section('title', 'Jenis Hewan - TernakIN')
@section('page-title', 'Jenis Hewan')

@section('content')
<div class="content-header">
    <div class="header-actions">
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Cari jenis hewan..." value="{{ request('search') }}">
            <button type="button" id="searchBtn">
                <i class="fas fa-search"></i>
            </button>
        </div>
        <div class="filters">
            <label class="checkbox-label">
                <input type="checkbox" id="activeFilter" {{ request('active') !== 'false' ? 'checked' : '' }}>
                Hanya Jenis Aktif
            </label>
        </div>
    </div>
</div>

<div class="animal-types-grid">
    @forelse($animalTypes as $animalType)
    <div class="animal-type-card {{ !$animalType->is_active ? 'inactive' : '' }}">
        <div class="animal-type-header">
            <h3>{{ $animalType->name }}</h3>
            @if(!$animalType->is_active)
            <span class="inactive-badge">Tidak Aktif</span>
            @endif
        </div>
        @if($animalType->description)
        <div class="animal-type-description">
            <p>{{ Str::limit($animalType->description, 150) }}</p>
        </div>
        @endif
        <div class="animal-type-stats">
            <div class="stat-item">
                <span class="stat-number">{{ $animalType->diseases->count() }}</span>
                <span class="stat-label">Penyakit</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $animalType->symptoms->count() }}</span>
                <span class="stat-label">Gejala</span>
            </div>
        </div>
        <div class="animal-type-diseases">
            <strong>Penyakit Utama:</strong>
            <div class="diseases-list">
                @foreach($animalType->diseases->take(3) as $disease)
                <span class="disease-tag">{{ $disease->name }}</span>
                @endforeach
                @if($animalType->diseases->count() > 3)
                <span class="more-diseases">+{{ $animalType->diseases->count() - 3 }} lainnya</span>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="no-data">
        <i class="fas fa-paw"></i>
        <h3>Tidak ada jenis hewan ditemukan</h3>
        <p>Coba ubah kriteria pencarian atau filter</p>
    </div>
    @endforelse
</div>

<div class="pagination">
    {{ $animalTypes->appends(request()->query())->links() }}
</div>
@endsection

@push('scripts')
<script>
document.getElementById('searchBtn').addEventListener('click', function() {
    const search = document.getElementById('searchInput').value;
    const active = document.getElementById('activeFilter').checked;
    const url = new URL(window.location);

    if (search) url.searchParams.set('search', search);
    else url.searchParams.delete('search');

    url.searchParams.set('active', active ? 'true' : 'false');

    window.location.href = url.toString();
});

document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        document.getElementById('searchBtn').click();
    }
});

document.getElementById('activeFilter').addEventListener('change', function() {
    document.getElementById('searchBtn').click();
});
</script>
@endpush
