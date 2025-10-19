<?php

namespace App\Http\Controllers;

use App\Models\Diseases;
use App\Models\AnimalType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class DiseaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Diseases::with(['animalTypes', 'symptoms', 'preventionMethods', 'diseaseImages'])
            ->where('is_active', true);

        // Filter by animal type
        if ($request->has('animal_type_id')) {
            $query->whereHas('animalTypes', function ($q) use ($request) {
                $q->where('animal_type_id', $request->animal_type_id);
            });
        }

        // Filter by causative agent
        if ($request->has('causative_agent')) {
            $query->where('causative_agent', $request->causative_agent);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('other_names', 'like', '%' . $request->search . '%');
            });
        }

        $diseases = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $diseases
        ]);
    }

    public function show($id)
    {
        $disease = Diseases::with([
            'animalTypes',
            'symptoms',
            'preventionMethods',
            'diseaseImages',
            'diseaseVideos',
            'diseaseMedicines.medicine'
        ])->find($id);

        if (!$disease) {
            return response()->json([
                'success' => false,
                'error' => 'Penyakit tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $disease
        ]);
    }

    public function quickDiagnosis(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'animal_type_id' => 'required|exists:animal_types,id',
            'symptom_ids' => 'required|array|min:1',
            'symptom_ids.*' => 'exists:symptoms,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $possibleDiseases = Diseases::with(['symptoms', 'animalTypes'])
            ->whereHas('animalTypes', function ($query) use ($request) {
                $query->where('animal_type_id', $request->animal_type_id);
            })
            ->whereHas('symptoms', function ($query) use ($request) {
                $query->whereIn('symptom_id', $request->symptom_ids);
            })
            ->get()
            ->map(function ($disease) use ($request) {
                $matchedSymptoms = $disease->symptoms->whereIn('id', $request->symptom_ids)->count();
                $totalSymptoms = $disease->symptoms->count();
                $matchPercentage = $totalSymptoms > 0 ? ($matchedSymptoms / $totalSymptoms) * 100 : 0;

                return [
                    'id' => $disease->id,
                    'name' => $disease->name,
                    'disease_code' => $disease->disease_code,
                    'causative_agent' => $disease->causative_agent,
                    'mortality_rate' => $disease->mortality_rate,
                    'is_zoonotic' => $disease->is_zoonotic,
                    'matched_symptoms_count' => $matchedSymptoms,
                    'total_symptoms_count' => $totalSymptoms,
                    'match_percentage' => round($matchPercentage, 2),
                    'severity' => $disease->animalTypes->firstWhere('id', $request->animal_type_id)->pivot->severity ?? 'sedang'
                ];
            })
            ->sortByDesc('match_percentage')
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'possible_diseases' => $possibleDiseases,
                'input_symptoms_count' => count($request->symptom_ids),
                'total_possible_diseases' => $possibleDiseases->count()
            ]
        ]);
    }

    public function searchDiseases(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:2',
            'animal_type_id' => 'nullable|exists:animal_types,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $query = Diseases::with(['animalTypes'])
            ->where('is_active', true)
            ->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->query . '%')
                  ->orWhere('other_names', 'like', '%' . $request->query . '%')
                  ->orWhere('disease_code', 'like', '%' . $request->query . '%');
            });

        if ($request->animal_type_id) {
            $query->whereHas('animalTypes', function ($q) use ($request) {
                $q->where('animal_type_id', $request->animal_type_id);
            });
        }

        $diseases = $query->orderBy('name')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $diseases
        ]);
    }

    // Web view methods
    public function webIndex(Request $request)
    {
        $query = Diseases::with(['animalTypes', 'symptoms'])
            ->where('is_active', true);

        if ($request->has('animal_type_id')) {
            $query->whereHas('animalTypes', function ($q) use ($request) {
                $q->where('animal_type_id', $request->animal_type_id);
            });
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $diseases = $query->paginate(12);
        $animalTypes = AnimalType::where('is_active', true)->get();

        return view('diseases.index', compact('diseases', 'animalTypes'));
    }

    public function webShow($id)
    {
        $disease = Diseases::with(['animalTypes', 'symptoms', 'preventionMethods', 'diseaseMedicines.medicine'])->findOrFail($id);

        return view('diseases.show', compact('disease'));
    }
}
