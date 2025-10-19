<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'phone',
        'user_type',
        'location_id',
        'last_login',
        'is_active',
        'provider',
        'provider_id',
        'avatar'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
        'is_active' => 'boolean'
    ];

    // Default values untuk atribut
    protected $attributes = [
        'user_type' => 'peternak',
        'is_active' => true,
    ];

    public function location()
    {
        return $this->belongsTo(UserLocation::class, 'location_id');
    }

    public function chatSessions()
    {
        return $this->hasMany(AiChatSession::class);
    }

    public function aiUsageAnalytics()
    {
        return $this->hasMany(AiUsageAnalytic::class);
    }

    // Scope untuk peternak
    public function scopePeternak($query)
    {
        return $query->where('user_type', 'peternak');
    }

    // Scope untuk admin
    public function scopeAdmin($query)
    {
        return $query->where('user_type', 'admin');
    }

    // Cek apakah user adalah peternak
    public function isPeternak()
    {
        return $this->user_type === 'peternak';
    }

    // Cek apakah user adalah admin
    public function isAdmin()
    {
        return $this->user_type === 'admin';
    }
}