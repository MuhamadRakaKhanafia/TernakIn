<?php

namespace App\Http\Controllers;

use App\Models\Symptom;
use App\Models\AnimalType;
use Illuminate\Http\Request;

class SymptomController extends Controller
{
    public function index(Request $request)
    {
        $query = Symptom::query();

        if ($request->has('animal_type_id')) {
            $query->whereHas('diseases.animalTypes', function ($q) use ($request) {
                $q->where('animal_type_id', $request->animal_type_id);
            });
        }

        if ($request->has('common')) {
            $query->where('is_common', true);
        }

        $symptoms = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $symptoms
        ]);
    }

    public function getCommonSymptoms($animalTypeId)
    {
        $symptoms = Symptom::whereHas('diseases.animalTypes', function ($query) use ($animalTypeId) {
                $query->where('animal_type_id', $animalTypeId);
            })
            ->where('is_common', true)
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $symptoms
        ]);
    }

    // Web view methods
    public function webIndex(Request $request)
    {
        $query = Symptom::query();

        if ($request->has('animal_type_id')) {
            $query->whereHas('diseases.animalTypes', function ($q) use ($request) {
                $q->where('animal_type_id', $request->animal_type_id);
            });
        }

        if ($request->has('common')) {
            $query->where('is_common', true);
        }

        $symptoms = $query->paginate(12);
        $animalTypes = AnimalType::where('is_active', true)->get();

        return view('symptoms.index', compact('symptoms', 'animalTypes'));
    }
}
