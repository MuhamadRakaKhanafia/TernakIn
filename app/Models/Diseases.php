<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disease extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'disease_code', 
        'causative_agent',
        'description',
        'transmission_method',
        'general_treatment',
        'is_zoonotic',
        'is_active'
    ];

    // Relationships
    public function symptoms()
    {
        return $this->hasMany(Symptom::class);
    }

    public function preventions()
    {
        return $this->hasMany(Prevention::class);
    }

    public function medicines()
    {
        return $this->hasMany(Medicine::class);
    }

    public function animalTypes()
    {
        return $this->belongsToMany(AnimalType::class, 'disease_animal_type');
    }
}