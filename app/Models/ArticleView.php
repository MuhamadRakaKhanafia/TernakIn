<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleView extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'user_id',
        'viewed_at',
        'time_spent_seconds'
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
        'time_spent_seconds' => 'integer'
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}