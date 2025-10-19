@extends('layouts.app')

@section('title', 'Obat dan Pengobatan - TernakIN')
@section('page-title', 'Obat dan Pengobatan')

@section('content')
<div class="content-header">
    <div class="header-actions">
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Cari obat..." value="{{ request('search') }}">
            <button type="button" id="searchBtn">
                <i class="fas fa-search"></i>
            </button>
        </div>
        <div class="filters">
            <select id="typeFilter">
                <option value="">Semua Jenis Obat</option>
                <option value="antibiotik" {{ request('type') == 'antibiotik' ? 'selected' : '' }}>Antibiotik</option>
                <option value="antivirus" {{ request('type') == 'antivirus' ? 'selected' : '' }}>Antivirus</option>
                <option value="antiparasit" {{ request('type') == 'antiparasit' ? 'selected' : '' }}>Antiparasit</option>
                <option value="vaksin" {{ request('type') == 'vaksin' ? 'selected' : '' }}>Vaksin</option>
                <option value="suplemen" {{ request('type') == 'suplemen' ? 'selected' : '' }}>Suplemen</option>
            </select>
        </div>
    </div>
</div>

<div class="medicines-grid">
    @forelse($medicines as $medicine)
    <div class="medicine-card">
        <div class="medicine-header">
            <h3>{{ $medicine->name }}</h3>
            <span class="medicine-type type-{{ $medicine->type }}">{{ ucfirst($medicine->type) }}</span>
        </div>
        @if($medicine->description)
        <div class="medicine-description">
            <p>{{ Str::limit($medicine->description, 150) }}</p>
        </div>
        @endif
        <div class="medicine-details">
            @if($medicine->active_ingredient)
            <div class="detail-item">
                <strong>Zat Aktif:</strong> {{ $medicine->active_ingredient }}
            </div>
            @endif
            @if($medicine->dosage_form)
            <div class="detail-item">
                <strong>Bentuk:</strong> {{ $medicine->dosage_form }}
            </div>
            @endif
            @if($medicine->manufacturer)
            <div class="detail-item">
                <strong>Produsen:</strong> {{ $medicine->manufacturer }}
            </div>
            @endif
        </div>
        <div class="medicine-diseases">
            <strong>Penyakit Terkait:</strong>
            <div class="diseases-list">
                @foreach($medicine->diseases->take(2) as $disease)
                <span class="disease-tag">{{ $disease->name }}</span>
                @endforeach
                @if($medicine->diseases->count() > 2)
                <span class="more-diseases">+{{ $medicine->diseases->count() - 2 }} lainnya</span>
                @endif
            </div>
        </div>
        <div class="medicine-actions">
            <a href="{{ route('medicines.show', $medicine->id) }}" class="btn-secondary">
                <i class="fas fa-eye"></i> Lihat Detail
            </a>
        </div>
    </div>
    @empty
    <div class="no-data">
        <i class="fas fa-pills"></i>
        <h3>Tidak ada obat ditemukan</h3>
        <p>Coba ubah kriteria pencarian atau filter</p>
    </div>
    @endforelse
</div>

<div class="pagination">
    {{ $medicines->appends(request()->query())->links() }}
</div>
@endsection

@push('scripts')
<script>
document.getElementById('searchBtn').addEventListener('click', function() {
    const search = document.getElementById('searchInput').value;
    const type = document.getElementById('typeFilter').value;
    const url = new URL(window.location);

    if (search) url.searchParams.set('search', search);
    else url.searchParams.delete('search');

    if (type) url.searchParams.set('type', type);
    else url.searchParams.delete('type');

    window.location.href = url.toString();
});

document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        document.getElementById('searchBtn').click();
    }
});

document.getElementById('typeFilter').addEventListener('change', function() {
    document.getElementById('searchBtn').click();
});
</script>
@endpush
