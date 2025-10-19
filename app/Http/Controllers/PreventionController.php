<?php

namespace App\Http\Controllers;

use App\Models\PreventionMethod;
use Illuminate\Http\Request;

class PreventionController extends Controller
{
    public function getByDisease($diseaseId)
    {
        $preventions = PreventionMethod::where('disease_id', $diseaseId)
            ->orderBy('priority')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $preventions
        ]);
    }

    public function getPreventionTips(Request $request)
    {
        $query = PreventionMethod::with(['disease']);

        if ($request->has('animal_type_id')) {
            $query->whereHas('disease.animalTypes', function ($q) use ($request) {
                $q->where('animal_type_id', $request->animal_type_id);
            });
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        $tips = $query->orderBy('priority')->get();

        return response()->json([
            'success' => true,
            'data' => $tips
        ]);
    }

    // Web view methods
    public function webIndex(Request $request)
    {
        $query = PreventionMethod::with(['disease']);

        if ($request->has('animal_type_id')) {
            $query->whereHas('disease.animalTypes', function ($q) use ($request) {
                $q->where('animal_type_id', $request->animal_type_id);
            });
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        $preventions = $query->paginate(12);

        return view('preventions.index', compact('preventions'));
    }
}
