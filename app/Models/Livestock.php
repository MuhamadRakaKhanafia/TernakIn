<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Livestock extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'identification_number',
        'animal_type_id',
        'sex',
        'birth_date',
        'acquisition_date',
        'age_weeks',
        'age_months',
        'weight_kg',
        'health_status',
        'vaccination_status',
        'feed_type',
        'daily_feed_kg',
        'housing_type',
        'notes',
        'strain',
        'breed',
        'purpose',
        'egg_production',
        'milk_production_liter',
        'pregnancy_status',
        'flock_size'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'acquisition_date' => 'date',
        'weight_kg' => 'decimal:2',
        'daily_feed_kg' => 'decimal:2',
        'milk_production_liter' => 'decimal:2',
    ];

    /**
     * Relationship with AnimalType
     */
    public function animalType()
    {
        return $this->belongsTo(AnimalType::class);
    }

    /**
     * Get age display based on animal type
     */
    public function getAgeDisplayAttribute()
    {
        if ($this->animalType && $this->animalType->category === 'poultry' && $this->age_weeks) {
            return $this->age_weeks . ' minggu';
        } elseif ($this->age_months) {
            return $this->age_months . ' bulan';
        }
        return '-';
    }

    /**
     * Get weight display
     */
    public function getWeightDisplayAttribute()
    {
        return $this->weight_kg ? number_format($this->weight_kg, 1) . ' kg' : '-';
    }

    /**
     * Get daily feed display
     */
    public function getDailyFeedDisplayAttribute()
    {
        return $this->daily_feed_kg ? number_format($this->daily_feed_kg, 2) . ' kg' : '-';
    }
}