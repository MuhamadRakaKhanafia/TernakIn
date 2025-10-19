<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'description',
        'dosage_guideline',
        'administration_method',
        'side_effects',
        'price_range',
        'is_prescription_required',
        'is_active'
    ];

    protected $casts = [
        'is_prescription_required' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function diseases()
    {
        return $this->belongsToMany(Disease::class, 'disease_medicines')
                    ->withPivot('recommended_dosage', 'administration_notes', 'effectiveness', 'is_preventive')
                    ->withTimestamps();
    }
}