<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Symptom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relasi many-to-many dengan diseases
    public function diseases()
    {
        return $this->belongsToMany(Disease::class, 'disease_symptoms')
                    ->withPivot('probability', 'is_primary', 'notes')
                    ->withTimestamps();
    }

    // Relasi many-to-many dengan animal types
    public function animalTypes()
    {
        return $this->belongsToMany(AnimalType::class, 'animal_symptoms')
                    ->withPivot('commonality', 'notes')
                    ->withTimestamps();
    }

    // Scope untuk symptom aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
