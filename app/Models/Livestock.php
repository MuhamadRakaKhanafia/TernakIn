<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\AnimalType;

/**
 * @property \App\Models\User $user
 * @property \App\Models\AnimalType $animalType
 */
class Livestock extends Model
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
        'name',
        'identification_number',
        'sex',
        'birth_date',
        'acquisition_date',
        'age_weeks',
        'age_months',
        'weight_kg',
        'health_status',
        'vaccination_status',
        'feed_type',
        'daily_feed_kg',
        'housing_type',
        'housing_size',
        'notes',
        'strain',
        'breed',
        'purpose',
        'egg_production',
        'milk_production_liter',
        'pregnancy_status',
        'flock_size'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birth_date' => 'date',
        'acquisition_date' => 'date',
        'weight_kg' => 'decimal:2',
        'daily_feed_kg' => 'decimal:2',
        'milk_production_liter' => 'decimal:2',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'age_display',
        'weight_display',
        'daily_feed_display',
        'is_poultry'
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
     * Relationship with AnimalType model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function animalType()
    {
        return $this->belongsTo(\App\Models\AnimalType::class);
    }

    /**
     * Scope a query to only include livestock for current authenticated user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForCurrentUser($query)
    {
        // Gunakan Auth::check() untuk memastikan user sudah login
        if (Auth::check()) {
            return $query->where('user_id', Auth::id());
        }
        
        return $query->where('user_id', 0); // Return empty if not authenticated
    }

    /**
     * Scope a query to only include livestock with specific health status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHealthStatus($query, $status)
    {
        return $query->where('health_status', $status);
    }

    /**
     * Scope a query to only include livestock with specific vaccination status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVaccinationStatus($query, $status)
    {
        return $query->where('vaccination_status', $status);
    }

    /**
     * Get age display attribute based on animal type.
     *
     * @return string
     */
    public function getAgeDisplayAttribute()
    {
        if ($this->animalType && $this->animalType->category === 'poultry' && $this->age_weeks) {
            return $this->age_weeks . ' minggu';
        } elseif ($this->age_months) {
            return $this->age_months . ' bulan';
        }
        
        return '-';
    }

    /**
     * Get weight display attribute.
     *
     * @return string
     */
    public function getWeightDisplayAttribute()
    {
        return $this->weight_kg ? number_format($this->weight_kg, 1) . ' kg' : '-';
    }

    /**
     * Get daily feed display attribute.
     *
     * @return string
     */
    public function getDailyFeedDisplayAttribute()
    {
        return $this->daily_feed_kg ? number_format($this->daily_feed_kg, 2) . ' kg' : '-';
    }

    /**
     * Get milk production display attribute.
     *
     * @return string
     */
    public function getMilkProductionDisplayAttribute()
    {
        return $this->milk_production_liter ? number_format($this->milk_production_liter, 1) . ' liter/hari' : '-';
    }

    /**
     * Get egg production display attribute.
     *
     * @return string
     */
    public function getEggProductionDisplayAttribute()
    {
        return $this->egg_production ? $this->egg_production . ' butir/bulan' : '-';
    }

    /**
     * Check if livestock is poultry.
     *
     * @return bool
     */
    public function getIsPoultryAttribute()
    {
        return $this->animalType && $this->animalType->category === 'poultry';
    }

    /**
     * Calculate age from birth date.
     *
     * @return int|null
     */
    public function calculateAgeFromBirthDate()
    {
        if (!$this->birth_date) {
            return null;
        }

        $birthDate = \Carbon\Carbon::parse($this->birth_date);
        $now = \Carbon\Carbon::now();
        
        if ($this->animalType && $this->animalType->category === 'poultry') {
            return $birthDate->diffInWeeks($now);
        } else {
            return $birthDate->diffInMonths($now);
        }
    }

    /**
     * Update age based on birth date.
     *
     * @return void
     */
    public function updateAgeFromBirthDate()
    {
        if (!$this->birth_date) {
            return;
        }

        $age = $this->calculateAgeFromBirthDate();
        
        if ($this->animalType && $this->animalType->category === 'poultry') {
            $this->age_weeks = $age;
            $this->age_months = null;
        } else {
            $this->age_months = $age;
            $this->age_weeks = null;
        }
    }

    /**
     * Check if livestock belongs to current authenticated user.
     *
     * @return bool
     */
    public function belongsToCurrentUser()
    {
        return Auth::check() && $this->user_id == Auth::id();
    }

    /**
     * Get health status badge class.
     *
     * @return string
     */
    public function getHealthStatusBadgeClass()
    {
        return $this->health_status === 'sehat' ? 'bg-success' : 'bg-danger';
    }

    /**
     * Get vaccination status badge class.
     *
     * @return string
     */
    public function getVaccinationStatusBadgeClass()
    {
        switch ($this->vaccination_status) {
            case 'up_to_date':
                return 'bg-success';
            case 'need_update':
                return 'bg-warning';
            case 'not_vaccinated':
                return 'bg-danger';
            default:
                return 'bg-secondary';
        }
    }

    /**
     * Get vaccination status text.
     *
     * @return string
     */
    public function getVaccinationStatusText()
    {
        switch ($this->vaccination_status) {
            case 'up_to_date':
                return 'Terkini';
            case 'need_update':
                return 'Perlu Update';
            case 'not_vaccinated':
                return 'Belum Vaksin';
            default:
                return '-';
        }
    }

    /**
     * Get sex text.
     *
     * @return string
     */
    public function getSexText()
    {
        return $this->sex === 'jantan' ? 'Jantan' : 'Betina';
    }

    /**
     * Get pregnancy status text.
     *
     * @return string
     */
    public function getPregnancyStatusText()
    {
        if (!$this->pregnancy_status) {
            return '-';
        }
        
        return $this->pregnancy_status === 'hamil' ? 'Hamil' : 'Tidak Hamil';
    }

    /**
     * Get purpose text.
     *
     * @return string
     */
    public function getPurposeText()
    {
        if (!$this->purpose) {
            return '-';
        }

        switch ($this->purpose) {
            case 'peternakan':
                return 'Peternakan';
            case 'daging':
                return 'Produksi Daging';
            case 'susu':
                return 'Produksi Susu';
            case 'kulit':
                return 'Produksi Kulit';
            default:
                return $this->purpose;
        }
    }

    /**
     * Check if vaccination is needed.
     *
     * @return bool
     */
    public function isVaccinationNeeded()
    {
        return $this->vaccination_status === 'need_update' || $this->vaccination_status === 'not_vaccinated';
    }

    /**
     * Check if livestock is sick.
     *
     * @return bool
     */
    public function isSick()
    {
        return $this->health_status === 'sakit';
    }

    /**
     * Check if livestock is healthy.
     *
     * @return bool
     */
    public function isHealthy()
    {
        return $this->health_status === 'sehat';
    }

    /**
     * Get the days since acquisition.
     *
     * @return int|null
     */
    public function getDaysSinceAcquisition()
    {
        if (!$this->acquisition_date) {
            return null;
        }

        $acquisitionDate = \Carbon\Carbon::parse($this->acquisition_date);
        return $acquisitionDate->diffInDays(now());
    }

    /**
     * Get the months since acquisition.
     *
     * @return int|null
     */
    public function getMonthsSinceAcquisition()
    {
        if (!$this->acquisition_date) {
            return null;
        }

        $acquisitionDate = \Carbon\Carbon::parse($this->acquisition_date);
        return $acquisitionDate->diffInMonths(now());
    }

    /**
     * Calculate total feed consumption per month.
     *
     * @return float|null
     */
    public function getMonthlyFeedConsumption()
    {
        if (!$this->daily_feed_kg) {
            return null;
        }

        return $this->daily_feed_kg * 30; // Approximate 30 days per month
    }

    /**
     * Calculate feed cost per month.
     *
     * @param float $pricePerKg Price per kg of feed
     * @return float|null
     */
    public function calculateMonthlyFeedCost($pricePerKg)
    {
        $monthlyConsumption = $this->getMonthlyFeedConsumption();
        
        if (!$monthlyConsumption) {
            return null;
        }

        return $monthlyConsumption * $pricePerKg;
    }

    /**
     * Boot the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically set user_id when creating new livestock
        static::creating(function ($livestock) {
            if (Auth::check() && !$livestock->user_id) {
                $livestock->user_id = Auth::id();
            }
        });

        // Update age from birth date when birth_date is updated
        static::updating(function ($livestock) {
            if ($livestock->isDirty('birth_date')) {
                $livestock->updateAgeFromBirthDate();
            }
        });
    }
}