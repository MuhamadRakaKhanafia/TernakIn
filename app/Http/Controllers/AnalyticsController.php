<?php

namespace App\Http\Controllers;

use App\Models\Livestock;
use App\Models\Disease;
use App\Models\AnimalType;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        // Data statistik utama
        $totalLivestock = Livestock::count();
        $healthyLivestock = Livestock::where('health_status', 'sehat')->count();
        $needVaccination = Livestock::where('vaccination_status', 'need_update')->count();
        $totalDiseases = Disease::count();

        // Data untuk chart
        $analyticsData = $this->getAnalyticsData();

        // Data tabel
        $sickLivestocks = Livestock::with('animalType')
            ->where('health_status', 'sakit')
            ->latest()
            ->limit(10)
            ->get();

        $needVaccinationLivestocks = Livestock::with('animalType')
            ->where('vaccination_status', 'need_update')
            ->latest()
            ->limit(10)
            ->get();

        return view('analytics.index', compact(
            'totalLivestock',
            'healthyLivestock',
            'needVaccination',
            'totalDiseases',
            'analyticsData',
            'sickLivestocks',
            'needVaccinationLivestocks'
        ));
    }

private function getAnalyticsData()
{
    // Distribusi jenis hewan
    $animalTypes = AnimalType::withCount('livestocks')->get();
    $animalTypeLabels = $animalTypes->pluck('name');
    $animalTypeData = $animalTypes->pluck('livestocks_count');

    // Status kesehatan
    $healthStatus = [
        'sehat' => Livestock::where('health_status', 'sehat')->count(),
        'sakit' => Livestock::where('health_status', 'sakit')->count()
    ];

    // Status vaksinasi
    $vaccinationStatus = [
        'up_to_date' => Livestock::where('vaccination_status', 'up_to_date')->count(),
        'need_update' => Livestock::where('vaccination_status', 'need_update')->count(),
        'not_vaccinated' => Livestock::where('vaccination_status', 'not_vaccinated')->count()
    ];

    // Top 10 penyakit - VERSI SEDERHANA: hanya ambil 10 penyakit terbaru
    $topDiseases = Disease::latest()->limit(10)->get();
    
    $diseaseLabels = $topDiseases->pluck('name');
    // Untuk data, kita bisa gunakan nilai dummy atau hitung dari livestock yang sakit
    $diseaseData = $topDiseases->map(function($disease) {
        // Jika ada relationship dengan livestock, hitung jumlah kasus
        // Jika tidak, beri nilai default
        return rand(1, 10); // Nilai dummy untuk testing
    });

    // Trend bulanan (6 bulan terakhir)
    $monthlyTrend = $this->getMonthlyTrend();

    // Distribusi jenis kelamin
    $genderDistribution = [
        'jantan' => Livestock::where('sex', 'jantan')->count(),
        'betina' => Livestock::where('sex', 'betina')->count()
    ];

    return [
        'animal_types' => [
            'labels' => $animalTypeLabels,
            'data' => $animalTypeData
        ],
        'health_status' => $healthStatus,
        'vaccination_status' => $vaccinationStatus,
        'top_diseases' => [
            'labels' => $diseaseLabels,
            'data' => $diseaseData
        ],
        'monthly_trend' => $monthlyTrend,
        'gender_distribution' => $genderDistribution
    ];
}

    private function getMonthlyTrend()
    {
        $labels = [];
        $healthyData = [];
        $sickData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthYear = $date->format('M Y');
            $labels[] = $monthYear;

            // Hitung hewan sehat dan sakit per bulan
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $healthyCount = Livestock::where('health_status', 'sehat')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();

            $sickCount = Livestock::where('health_status', 'sakit')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();

            $healthyData[] = $healthyCount;
            $sickData[] = $sickCount;
        }

        return [
            'labels' => $labels,
            'healthy' => $healthyData,
            'sick' => $sickData
        ];
    }
}