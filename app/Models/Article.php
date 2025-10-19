<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'author_id',
        'featured_image',
        'article_type',
        'reading_time',
        'is_published',
        'published_at'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'reading_time' => 'integer'
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function animalTypes()
    {
        return $this->belongsToMany(AnimalType::class, 'article_animal_types')
                    ->withTimestamps();
    }

    public function articleViews()
    {
        return $this->hasMany(ArticleView::class);
    }
}