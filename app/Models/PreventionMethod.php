<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prevention extends Model
{
    use HasFactory;

    protected $fillable = [
        'disease_id',
        'prevention_method',
        'description',
        'effectiveness_level'
    ];

    public function disease()
    {
        return $this->belongsTo(Disease::class);
    }
}