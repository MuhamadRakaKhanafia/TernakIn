<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    // Tentukan nama tabel secara eksplisit
    protected $table = 'province';

    protected $fillable = [
        'name',
        'code'
    ];

    // Relasi dengan city
    public function cities()
    {
        return $this->hasMany(City::class);
    }
}