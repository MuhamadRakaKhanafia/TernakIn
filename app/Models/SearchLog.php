<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'search_query',
        'animal_type_id',
        'user_id',
        'search_results_count',
        'searched_at',
        'user_ip'
    ];

    protected $casts = [
        'searched_at' => 'datetime',
        'search_results_count' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function animalType()
    {
        return $this->belongsTo(AnimalType::class);
    }

    // Scope for popular searches
    public function scopePopular($query, $limit = 10)
    {
        return $query->select('search_query')
                    ->selectRaw('COUNT(*) as search_count')
                    ->groupBy('search_query')
                    ->orderBy('search_count', 'desc')
                    ->limit($limit);
    }
}