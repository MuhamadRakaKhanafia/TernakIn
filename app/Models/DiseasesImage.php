<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiseaseImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'disease_id',
        'image_url',
        'caption',
        'image_type',
        'is_primary'
    ];

    protected $casts = [
        'is_primary' => 'boolean'
    ];

    public function disease()
    {
        return $this->belongsTo(Diseases::class);
    }
}