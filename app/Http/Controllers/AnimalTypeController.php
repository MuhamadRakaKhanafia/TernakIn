<?php

namespace App\Http\Controllers;

use App\Models\AnimalType;
use Illuminate\Http\Request;

class AnimalTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = AnimalType::query();

        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        $animalTypes = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $animalTypes
        ]);
    }

    public function show($id)
    {
        $animalType = AnimalType::with(['diseases'])->find($id);

        if (!$animalType) {
            return response()->json([
                'success' => false,
                'error' => 'Jenis hewan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $animalType
        ]);
    }

    // Web view methods
    public function webIndex(Request $request)
    {
        $query = AnimalType::query();

        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        $animalTypes = $query->paginate(12);

        return view('animal-types.index', compact('animalTypes'));
    }
}
