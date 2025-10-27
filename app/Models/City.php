<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    // Tentukan nama tabel secara eksplisit
    protected $table = 'city';

    protected $fillable = [
        'province_id',
        'name',
        'type',
        'code'
    ];

    // Relasi dengan province
    public function province()
    {
        return $this->belongsTo(Province::class);
    }
}