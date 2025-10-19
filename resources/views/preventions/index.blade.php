@extends('layouts.app')

@section('title', 'Pencegahan Penyakit - TernakIN')
@section('page-title', 'Pencegahan Penyakit')

@section('content')
<div class="content-header">
    <div class="header-actions">
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Cari metode pencegahan..." value="{{ request('search') }}">
            <button type="button" id="searchBtn">
                <i class="fas fa-search"></i>
            </button>
        </div>
        <div class="filters">
            <select id="animalTypeFilter">
                <option value="">Semua Jenis Hewan</option>
                @foreach(\App\Models\AnimalType::where('is_active', true)->get() as $type)
                <option value="{{ $type->id }}" {{ request('animal_type_id') == $type->id ? 'selected' : '' }}>
                    {{ $type->name }}
                </option>
                @endforeach
            </select>
            <select id="categoryFilter">
                <option value="">Semua Kategori</option>
                <option value="vaksinasi" {{ request('category') == 'vaksinasi' ? 'selected' : '' }}>Vaksinasi</option>
                <option value="sanitasi" {{ request('category') == 'sanitasi' ? 'selected' : '' }}>Sanitasi</option>
                <option value="manajemen" {{ request('category') == 'manajemen' ? 'selected' : '' }}>Manajemen</option>
                <option value="nutrisi" {{ request('category') == 'nutrisi' ? 'selected' : '' }}>Nutrisi</option>
            </select>
        </div>
    </div>
</div>

<div class="preventions-grid">
    @forelse($preventions as $prevention)
    <div class="prevention-card">
        <div class="prevention-header">
            <h3>{{ $prevention->title }}</h3>
            @if($prevention->category)
            <span class="category-badge">{{ ucfirst($prevention->category) }}</span>
            @endif
        </div>
        <div class="prevention-disease">
            <strong>Penyakit:</strong>
            <a href="{{ route('diseases.show', $prevention->disease->id) }}" class="disease-link">
                {{ $prevention->disease->name }}
            </a>
        </div>
        <div class="prevention-description">
            <p>{{ Str::limit($prevention->description, 200) }}</p>
        </div>
        @if($prevention->priority)
        <div class="prevention-priority">
            <span class="priority-badge priority-{{ $prevention->priority }}">
                Prioritas {{ ucfirst($prevention->priority) }}
            </span>
        </div>
        @endif
        <div class="prevention-actions">
            <button class="btn-secondary" onclick="showPreventionDetail({{ $prevention->id }})">
                <i class="fas fa-eye"></i> Lihat Detail
            </button>
        </div>
    </div>
    @empty
    <div class="no-data">
        <i class="fas fa-shield-alt"></i>
        <h3>Tidak ada metode pencegahan ditemukan</h3>
        <p>Coba ubah kriteria pencarian atau filter</p>
    </div>
    @endforelse
</div>

<div class="pagination">
    {{ $preventions->appends(request()->query())->links() }}
</div>

<!-- Prevention Detail Modal -->
<div id="preventionModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle"></h2>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body" id="modalBody">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('searchBtn').addEventListener('click', function() {
    const search = document.getElementById('searchInput').value;
    const animalType = document.getElementById('animalTypeFilter').value;
    const category = document.getElementById('categoryFilter').value;
    const url = new URL(window.location);

    if (search) url.searchParams.set('search', search);
    else url.searchParams.delete('search');

    if (animalType) url.searchParams.set('animal_type_id', animalType);
    else url.searchParams.delete('animal_type_id');

    if (category) url.searchParams.set('category', category);
    else url.searchParams.delete('category');

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

document.getElementById('categoryFilter').addEventListener('change', function() {
    document.getElementById('searchBtn').click();
});

function showPreventionDetail(preventionId) {
    // This would typically make an AJAX call to get prevention details
    // For now, we'll just show a placeholder
    document.getElementById('modalTitle').textContent = 'Detail Pencegahan';
    document.getElementById('modalBody').innerHTML = '<p>Detail lengkap metode pencegahan akan dimuat di sini.</p>';
    document.getElementById('preventionModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('preventionModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('preventionModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
</script>
@endpush
