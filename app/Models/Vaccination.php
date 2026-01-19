<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Livestock;

/**
 * @property \App\Models\User $user
 * @property \App\Models\Livestock $livestock
 */
class Vaccination extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'animal_type_id',
        'vaccine_name',
        'vaccination_date',
        'next_vaccination_date',
        'notes',
        'status',
        'admin_notes',
        'admin_recommendations',
        'admin_validated_at',
        'admin_validator_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'vaccination_date' => 'date',
        'next_vaccination_date' => 'date',
        'admin_validated_at' => 'datetime',
    ];

    /**
     * Relationship with User model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Relationship with Livestock model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function livestock()
    {
        return $this->belongsTo(\App\Models\Livestock::class);
    }

    /**
     * Relationship with AnimalType model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function animalType()
    {
        return $this->belongsTo(\App\Models\AnimalType::class);
    }

    /**
     * Relationship with Admin Validator (User model).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function adminValidator()
    {
        return $this->belongsTo(\App\Models\User::class, 'admin_validator_id');
    }

    /**
     * Check if the vaccination is pending.
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the vaccination is approved.
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Get the status badge HTML.
     *
     * @return string
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Menunggu</span>',
            'approved' => '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Disetujui</span>',
            'rejected' => '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Ditolak</span>',
            'completed' => '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Selesai</span>',
            default => '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Tidak Diketahui</span>',
        };
    }
}
