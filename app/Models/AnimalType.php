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
        'description',
        'category'
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