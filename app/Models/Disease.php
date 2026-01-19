<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disease extends Model
{
    use HasFactory;

    /**
     * @property int $id
     */
    protected $table = 'diseases';
    protected $primaryKey = 'id';

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
        'prevention_method',
        'risk_factors',
        'emergency_actions',
        'is_active',
        'image'
    ];

    protected $casts = [
        'is_zoonotic' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relasi many-to-many dengan animal types
    public function animalTypes()
    {
        return $this->belongsToMany(AnimalType::class, 'disease_animal_types')
                    ->withPivot('severity', 'specific_notes')
                    ->withTimestamps();
    }

    // Relasi many-to-many dengan symptoms
    public function symptoms()
    {
        return $this->belongsToMany(Symptom::class, 'disease_symptoms')
                    ->withPivot('probability', 'is_primary', 'notes')
                    ->withTimestamps();
    }

    // Scope untuk disease aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the ID of the disease
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the ID of the disease (for linter compatibility)
     *
     * @return int
     */
    public function id()
    {
        return $this->getKey();
    }
}
