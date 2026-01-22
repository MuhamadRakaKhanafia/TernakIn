<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Broadcast extends Model
{
    protected $fillable = [
        'message',
        'link_url',
        'link_text',
        'expires_at',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    /**
     * Scope for active broadcasts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope for ordering broadcasts by creation date
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Get the user who created the broadcast
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
