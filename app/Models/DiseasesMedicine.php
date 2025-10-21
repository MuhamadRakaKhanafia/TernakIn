<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiseaseMedicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'disease_id',
        'medicine_id',
        'recommended_dosage',
        'administration_notes',
        'effectiveness',
        'is_preventive'
    ];

    protected $casts = [
        'is_preventive' => 'boolean'
    ];

    public function disease()
    {
        return $this->belongsTo(Diseases::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}