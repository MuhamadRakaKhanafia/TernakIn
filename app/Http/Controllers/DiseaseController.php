<?php

namespace App\Http\Controllers;

use App\Models\Disease;
use App\Models\AnimalType;
use App\Models\Symptom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiseaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Disease::with(['animalTypes', 'symptoms'])
                        ->active()
                        ->orderBy('name');

        // Filter by animal_type
        if ($request->has('animal_type_id') && $request->animal_type_id) {
            $query->whereHas('animalTypes', function ($q) use ($request) {
                $q->where('animal_type_id', $request->animal_type_id);
            });
        }

        // Filter by causative agent
        if ($request->has('causative_agent') && $request->causative_agent) {
            $query->where('causative_agent', $request->causative_agent);
        }

        // Filter by search term
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('disease_code', 'like', '%' . $request->search . '%')
                  ->orWhere('other_names', 'like', '%' . $request->search . '%');
            });
        }

        $diseases = $query->paginate(12);
        $animalTypes = AnimalType::where('is_active', true)->get();
        $causativeAgents = [
            'virus' => 'Virus',
            'bakteri' => 'Bakteri',
            'parasit' => 'Parasit',
            'fungi' => 'Fungi',
            'defisiensi_nutrisi' => 'Defisiensi Nutrisi',
            'lainnya' => 'Lainnya'
        ];

        return view('diseases.index', compact('diseases', 'animalTypes', 'causativeAgents'));
    }

    public function show($id)
    {
        $disease = Disease::with([
            'animalTypes',
            'symptoms' => function ($query) {
                $query->orderBy('pivot_is_primary', 'desc')
                      ->orderBy('pivot_probability', 'desc');
            }
        ])->active()->findOrFail($id);

        return view('diseases.show', compact('disease'));
    }

    public function quickDiagnosis(Request $request)
    {
        $request->validate([
            'animal_type_id' => 'required|exists:animal_types,id',
            'symptoms' => 'required|array|min:1',
            'symptoms.*' => 'exists:symptoms,id'
        ]);

        $animalTypeId = $request->animal_type_id;
        $selectedSymptoms = $request->symptoms;

        // Cari diseases yang terkait dengan animal type dan symptoms yang dipilih
        $possibleDiseases = Disease::whereHas('animalTypes', function ($query) use ($animalTypeId) {
                $query->where('animal_type_id', $animalTypeId);
            })
            ->whereHas('symptoms', function ($query) use ($selectedSymptoms) {
                $query->whereIn('symptom_id', $selectedSymptoms);
            })
            ->with(['symptoms' => function ($query) use ($selectedSymptoms) {
                $query->whereIn('symptoms.id', $selectedSymptoms)
                      ->orderBy('pivot_probability', 'desc');
            }])
            ->active()
            ->get()
            ->map(function ($disease) use ($selectedSymptoms) {
                // Hitung match score berdasarkan probability symptoms
                $matchScore = $disease->symptoms->sum('pivot.probability') / count($selectedSymptoms);
                $disease->match_score = round($matchScore * 100, 2);
                return $disease;
            })
            ->sortByDesc('match_score');

        return response()->json([
            'success' => true,
            'diseases' => $possibleDiseases->values(),
            'selected_symptoms_count' => count($selectedSymptoms)
        ]);
    }

    public function searchDiseases(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2'
        ]);

        $diseases = Disease::where('name', 'like', '%' . $request->query . '%')
            ->orWhere('disease_code', 'like', '%' . $request->query . '%')
            ->orWhere('other_names', 'like', '%' . $request->query . '%')
            ->active()
            ->with('animalTypes')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'diseases' => $diseases
        ]);
    }
}