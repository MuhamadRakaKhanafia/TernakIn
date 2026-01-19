<?php

namespace App\Http\Controllers;

use App\Models\Livestock;
use App\Models\Disease;
use App\Models\User;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        try {
            // Hitung statistik dengan fallback values
            $totalLivestock = Livestock::count() ?? 0;
            $totalDiseases = Disease::count() ?? 0;
            $totalUsers = User::count() ?? 0;
            
            // Hitung persentase kesehatan
            $healthyLivestock = Livestock::where('health_status', 'sehat')->count();
            $healthyPercentage = $totalLivestock > 0 ? round(($healthyLivestock / $totalLivestock) * 100) : 0;

            return view('welcome', [
                'totalLivestock' => $totalLivestock,
                'totalDiseases' => $totalDiseases,
                'totalUsers' => $totalUsers,
                'healthyPercentage' => $healthyPercentage
            ]);

        } catch (\Exception $e) {
            // Fallback jika ada error database
            return view('welcome', [
                'totalLivestock' => 0,
                'totalDiseases' => 0,
                'totalUsers' => 0,
                'healthyPercentage' => 0
            ]);
        }
    }
}