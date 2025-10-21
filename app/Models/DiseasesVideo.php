<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiseaseVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'disease_id',
        'video_url',
        'title',
        'description',
        'duration',
        'thumbnail_url'
    ];

    public function disease()
    {
        return $this->belongsTo(Diseases::class);
    }
}