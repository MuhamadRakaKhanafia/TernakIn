<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnimalType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'scientific_name',
        'description',
        'icon_url',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function diseases()
    {
        return $this->belongsToMany(Diseases::class, 'disease_animal_types')
                    ->withPivot('severity', 'specific_notes')
                    ->withTimestamps();
    }

    public function chatSessions()
    {
        return $this->hasMany(AiChatSession::class);
    }
}