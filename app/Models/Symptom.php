<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Symptom extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'category', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relasi many-to-many dengan diseases
    public function diseases()
    {
        return $this->belongsToMany(Disease::class, 'disease_symptoms')
                    ->withPivot('probability', 'is_primary', 'notes')
                    ->withTimestamps();
    }
}