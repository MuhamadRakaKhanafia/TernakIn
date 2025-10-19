@extends('layouts.app')

@section('title', 'Penyakit Hewan - TernakIN')
@section('page-title', 'Penyakit Hewan')

@section('content')
<div class="content-header">
    <div class="header-actions">
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Cari penyakit..." value="{{ request('search') }}">
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
        </div>
    </div>
</div>

<div class="diseases-grid">
    @forelse($diseases as $disease)
    <div class="disease-card">
        <div class="disease-header">
            <h3>{{ $disease->name }}</h3>
            <div class="disease-code">{{ $disease->disease_code }}</div>
        </div>
        <div class="disease-info">
            <div class="info-item">
                <i class="fas fa-bug"></i>
                <span>{{ $disease->causative_agent }}</span>
            </div>
            <div class="info-item">
                <i class="fas fa-dove"></i>
                <span>{{ $disease->animalTypes->pluck('name')->join(', ') }}</span>
            </div>
        </div>
        <div class="disease-symptoms">
            <strong>Gejala Utama:</strong>
            <p>{{ Str::limit($disease->symptoms->pluck('name')->join(', '), 100) }}</p>
        </div>
        <div class="disease-actions">
            <a href="{{ route('diseases.show', $disease->id) }}" class="btn-secondary">
                <i class="fas fa-eye"></i> Lihat Detail
            </a>
        </div>
    </div>
    @empty
    <div class="no-data">
        <i class="fas fa-search"></i>
        <h3>Tidak ada penyakit ditemukan</h3>
        <p>Coba ubah kriteria pencarian atau filter</p>
    </div>
    @endforelse
</div>

<div class="pagination">
    {{ $diseases->appends(request()->query())->links() }}
</div>
@endsection

@push('scripts')
<script>
document.getElementById('searchBtn').addEventListener('click', function() {
    const search = document.getElementById('searchInput').value;
    const animalType = document.getElementById('animalTypeFilter').value;
    const url = new URL(window.location);

    if (search) url.searchParams.set('search', search);
    else url.searchParams.delete('search');

    if (animalType) url.searchParams.set('animal_type_id', animalType);
    else url.searchParams.delete('animal_type_id');

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
</script>
@endpush
