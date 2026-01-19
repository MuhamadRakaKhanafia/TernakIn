<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnimalType extends Model
{
    use HasFactory;

    protected $table = 'animal_types';

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

    /**
     * Relationship with Livestock
     */
    public function livestocks(): HasMany
    {
        return $this->hasMany(Livestock::class);
    }

    /**
     * Get category name
     */
    public function getCategoryNameAttribute()
    {
        switch ($this->category) {
            case 'poultry':
                return 'Unggas';
            case 'large_animal':
                return 'Ternak Besar';
            case 'other':
                return 'Lainnya';
            default:
                return $this->category;
        }
    }
}