@extends('layouts.app')

@section('title', 'Artikel Kesehatan Hewan - TernakIN')
@section('page-title', 'Artikel Kesehatan Hewan')

@section('content')
<div class="content-header">
    <div class="header-actions">
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Cari artikel..." value="{{ request('search') }}">
            <button type="button" id="searchBtn">
                <i class="fas fa-search"></i>
            </button>
        </div>
        <div class="filters">
            <select id="categoryFilter">
                <option value="">Semua Kategori</option>
                <option value="pencegahan" {{ request('category') == 'pencegahan' ? 'selected' : '' }}>Pencegahan</option>
                <option value="pengobatan" {{ request('category') == 'pengobatan' ? 'selected' : '' }}>Pengobatan</option>
                <option value="nutrisi" {{ request('category') == 'nutrisi' ? 'selected' : '' }}>Nutrisi</option>
                <option value="kesehatan_umum" {{ request('category') == 'kesehatan_umum' ? 'selected' : '' }}>Kesehatan Umum</option>
            </select>
        </div>
    </div>
</div>

<div class="articles-grid">
    @forelse($articles as $article)
    <div class="article-card">
        <div class="article-header">
            <h3><a href="{{ route('articles.show', $article->slug) }}">{{ $article->title }}</a></h3>
            @if($article->category)
            <span class="category-badge">{{ ucfirst(str_replace('_', ' ', $article->category)) }}</span>
            @endif
        </div>
        <div class="article-meta">
            <span class="date">
                <i class="fas fa-calendar"></i>
                {{ $article->published_at ? $article->published_at->format('d M Y') : $article->created_at->format('d M Y') }}
            </span>
            <span class="author">
                <i class="fas fa-user"></i>
                {{ $article->author ?? 'Admin' }}
            </span>
            @if($article->views_count ?? 0 > 0)
            <span class="views">
                <i class="fas fa-eye"></i>
                {{ $article->views_count }} dilihat
            </span>
            @endif
        </div>
        <div class="article-excerpt">
            <p>{{ Str::limit(strip_tags($article->content), 200) }}</p>
        </div>
        <div class="article-actions">
            <a href="{{ route('articles.show', $article->slug) }}" class="btn-secondary">
                <i class="fas fa-eye"></i> Baca Selengkapnya
            </a>
        </div>
    </div>
    @empty
    <div class="no-data">
        <i class="fas fa-newspaper"></i>
        <h3>Tidak ada artikel ditemukan</h3>
        <p>Coba ubah kriteria pencarian atau filter</p>
    </div>
    @endforelse
</div>

<div class="pagination">
    {{ $articles->appends(request()->query())->links() }}
</div>
@endsection

@push('scripts')
<script>
document.getElementById('searchBtn').addEventListener('click', function() {
    const search = document.getElementById('searchInput').value;
    const category = document.getElementById('categoryFilter').value;
    const url = new URL(window.location);

    if (search) url.searchParams.set('search', search);
    else url.searchParams.delete('search');

    if (category) url.searchParams.set('category', category);
    else url.searchParams.delete('category');

    window.location.href = url.toString();
});

document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        document.getElementById('searchBtn').click();
    }
});

document.getElementById('categoryFilter').addEventListener('change', function() {
    document.getElementById('searchBtn').click();
});
</script>
@endpush
