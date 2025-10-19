<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\DiseaseMedicine;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    public function index(Request $request)
    {
        $query = Medicine::where('is_active', true);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $medicines = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $medicines
        ]);
    }

    public function show($id)
    {
        $medicine = Medicine::with(['diseases'])->find($id);

        if (!$medicine) {
            return response()->json([
                'success' => false,
                'error' => 'Obat tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $medicine
        ]);
    }

    public function getMedicinesByDisease($diseaseId)
    {
        $medicines = DiseaseMedicine::with(['medicine'])
            ->where('disease_id', $diseaseId)
            ->get()
            ->map(function ($diseaseMedicine) {
                return [
                    'medicine' => $diseaseMedicine->medicine,
                    'recommended_dosage' => $diseaseMedicine->recommended_dosage,
                    'administration_notes' => $diseaseMedicine->administration_notes,
                    'effectiveness' => $diseaseMedicine->effectiveness,
                    'is_preventive' => $diseaseMedicine->is_preventive
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $medicines
        ]);
    }

    // Web view methods
    public function webIndex(Request $request)
    {
        $query = Medicine::where('is_active', true);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $medicines = $query->paginate(12);

        return view('medicines.index', compact('medicines'));
    }

    public function webShow($id)
    {
        $medicine = Medicine::with(['diseases'])->findOrFail($id);

        return view('medicines.show', compact('medicine'));
    }
}
