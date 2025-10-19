<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiUsageAnalytic extends Model
{
    use HasFactory;

    protected $fillable = [
        'usage_date',
        'user_id',
        'total_requests',
        'total_tokens',
        'total_cost',
        'model_used'
    ];

    protected $casts = [
        'usage_date' => 'date',
        'total_cost' => 'decimal:6',
        'total_requests' => 'integer',
        'total_tokens' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope for today's usage
    public function scopeToday($query)
    {
        return $query->where('usage_date', today());
    }

    // Scope for this month
    public function scopeThisMonth($query)
    {
        return $query->whereYear('usage_date', now()->year)
                    ->whereMonth('usage_date', now()->month);
    }

    // Method to get total cost in IDR (approximate conversion)
    public function getTotalCostIdrAttribute()
    {
        // Assuming 1 USD = 15000 IDR
        return $this->total_cost * 15000;
    }
}