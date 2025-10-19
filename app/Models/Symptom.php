<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Symptom extends Model
{
    use HasFactory;

    protected $fillable = [
        'symptom_code',
        'name',
        'description',
        'severity_level',
        'is_common'
    ];

    protected $casts = [
        'is_common' => 'boolean'
    ];

    public function diseases()
    {
        return $this->belongsToMany(Diseases::class, 'disease_symptoms')
                    ->withPivot('probability', 'is_primary', 'notes')
                    ->withTimestamps();
    }
}
