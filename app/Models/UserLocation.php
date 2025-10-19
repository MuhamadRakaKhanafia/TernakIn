<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'province_id',
        'city_id',
        'district',
        'village',
        'detailed_address'
    ];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'location_id');
    }

    // Accessor untuk alamat lengkap
    public function getFullAddressAttribute()
    {
        $addressParts = [
            $this->detailed_address,
            $this->village,
            $this->district,
            $this->city->name ?? '',
            $this->province->name ?? ''
        ];

        return implode(', ', array_filter($addressParts));
    }
}