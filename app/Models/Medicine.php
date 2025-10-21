<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'disease_id',
        'medicine_name',
        'dosage',
        'administration_method',
        'side_effects'
    ];

    public function disease()
    {
        return $this->belongsTo(Disease::class);
    }
}