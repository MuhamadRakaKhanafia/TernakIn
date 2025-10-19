<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diseases extends Model
{
    use HasFactory;

    protected $fillable = [
        'disease_code',
        'name',
        'other_names',
        'causative_agent',
        'description',
        'mortality_rate',
        'is_zoonotic',
        'transmission_method',
        'diagnosis_method',
        'general_treatment',
        'emergency_actions',
        'is_active'
    ];

    protected $casts = [
        'is_zoonotic' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function animalTypes()
    {
        return $this->belongsToMany(AnimalType::class, 'disease_animal_types')
                    ->withPivot('severity', 'specific_notes')
                    ->withTimestamps();
    }

    public function symptoms()
    {
        return $this->belongsToMany(Symptom::class, 'disease_symptoms')
                    ->withPivot('probability', 'is_primary', 'notes')
                    ->withTimestamps();
    }

    public function preventionMethods()
    {
        return $this->hasMany(PreventionMethod::class);
    }

    public function diseaseMedicines()
    {
        return $this->hasMany(DiseaseMedicine::class);
    }

    public function diseaseImages()
    {
        return $this->hasMany(DiseaseImage::class);
    }

    public function diseaseVideos()
    {
        return $this->hasMany(DiseaseVideo::class);
    }
}