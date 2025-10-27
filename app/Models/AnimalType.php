<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnimalType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relasi many-to-many dengan diseases
    public function diseases()
    {
        return $this->belongsToMany(Disease::class, 'disease_animal_types')
                    ->withPivot('severity', 'specific_notes')
                    ->withTimestamps();
    }
}