<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreventionMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'disease_id',
        'method_type',
        'title',
        'description',
        'steps',
        'effectiveness',
        'cost_estimate'
    ];

    public function disease()
    {
        return $this->belongsTo(Disease::class);
    }
}