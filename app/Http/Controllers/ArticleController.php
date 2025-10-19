<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleView;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::where('is_published', true);

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        $articles = $query->orderBy('published_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $articles
        ]);
    }

    public function show($slug)
    {
        $article = Article::where('slug', $slug)
            ->where('is_published', true)
            ->first();

        if (!$article) {
            return response()->json([
                'success' => false,
                'error' => 'Artikel tidak ditemukan'
            ], 404);
        }

        // Record view
        ArticleView::create([
            'article_id' => $article->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'viewed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'data' => $article
        ]);
    }

    public function popularArticles(Request $request)
    {
        $limit = $request->get('limit', 5);

        $articles = Article::where('is_published', true)
            ->withCount('views')
            ->orderBy('views_count', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $articles
        ]);
    }

    public function recentArticles(Request $request)
    {
        $limit = $request->get('limit', 5);

        $articles = Article::where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $articles
        ]);
    }

    // Web view methods
    public function webIndex(Request $request)
    {
        $query = Article::where('is_published', true);

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        $articles = $query->orderBy('published_at', 'desc')->paginate(12);

        return view('articles.index', compact('articles'));
    }

    public function webShow($slug)
    {
        $article = Article::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        // Record view
        ArticleView::create([
            'article_id' => $article->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'viewed_at' => now()
        ]);

        return view('articles.show', compact('article'));
    }
}
