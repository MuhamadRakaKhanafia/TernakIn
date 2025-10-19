@extends('layouts.app')

@section('title', $article->title . ' - TernakIN')
@section('page-title', $article->title)

@section('content')
<div class="article-detail">
    <div class="article-header">
        <div class="article-title">
            <h1>{{ $article->title }}</h1>
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
    </div>

    <div class="article-content">
        {!! $article->content !!}
    </div>

    <div class="article-footer">
        <div class="article-tags">
            @if($article->tags)
            @foreach(explode(',', $article->tags) as $tag)
            <span class="tag">{{ trim($tag) }}</span>
            @endforeach
            @endif
        </div>
    </div>

    <div class="article-actions">
        <a href="{{ route('articles.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Artikel
        </a>
        <button class="btn btn-primary" onclick="shareArticle()">
            <i class="fas fa-share"></i> Bagikan
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
function shareArticle() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $article->title }}',
            text: 'Baca artikel: {{ $article->title }} di TernakIN',
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
