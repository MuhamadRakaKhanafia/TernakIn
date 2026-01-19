<?php

namespace App\Http\Controllers;

use App\Models\Vaccination;
use App\Models\Livestock;
use App\Models\AnimalType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VaccinationController extends Controller
{
    /**
     * Display a listing of the vaccinations.
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $vaccinations = Vaccination::with('animalType')
            ->where('user_id', Auth::id())
            ->orderBy('vaccination_date', 'desc')
            ->paginate(10);

        $animalTypes = AnimalType::where('is_active', true)->get();

        return view('vaccinations.index', compact('vaccinations', 'animalTypes'));
    }

    /**
     * Show the form for creating a new vaccination.
     */
    public function create()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $animalTypes = AnimalType::where('is_active', true)->get();
        return view('vaccinations.create', compact('animalTypes'));
    }

    /**
     * Store a newly created vaccination in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $request->validate([
            'animal_type_id' => 'required|exists:animal_types,id',
            'vaccine_name' => 'required|string|max:255',
            'vaccination_date' => 'required|date',
            'next_vaccination_date' => 'nullable|date|after:vaccination_date',
            'notes' => 'nullable|string',
        ]);

        $vaccination = Vaccination::create([
            'user_id' => Auth::id(),
            'animal_type_id' => $request->animal_type_id,
            'vaccine_name' => $request->vaccine_name,
            'vaccination_date' => $request->vaccination_date,
            'next_vaccination_date' => $request->next_vaccination_date,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        return redirect()->route('vaccinations.index')->with('success', 'Vaksinasi berhasil ditambahkan.');
    }

    /**
     * Display the specified vaccination.
     */
    public function show(Vaccination $vaccination)
    {
        if ($vaccination->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        return view('vaccinations.show', compact('vaccination'));
    }

    /**
     * Show the form for editing the specified vaccination.
     */
    public function edit(Vaccination $vaccination)
    {
        if ($vaccination->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        $animalTypes = AnimalType::where('is_active', true)->get();
        return view('vaccinations.edit', compact('vaccination', 'animalTypes'));
    }

    /**
     * Update the specified vaccination in storage.
     */
    public function update(Request $request, Vaccination $vaccination)
    {
        if ($vaccination->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'animal_type_id' => 'required|exists:animal_types,id',
            'vaccine_name' => 'required|string|max:255',
            'vaccination_date' => 'required|date',
            'next_vaccination_date' => 'nullable|date|after:vaccination_date',
            'notes' => 'nullable|string',
        ]);

        $vaccination->update($request->only([
            'animal_type_id',
            'vaccine_name',
            'vaccination_date',
            'next_vaccination_date',
            'notes',
        ]));

        return redirect()->route('vaccinations.index')->with('success', 'Vaksinasi berhasil diperbarui.');
    }

    /**
     * Remove the specified vaccination from storage.
     */
    public function destroy(Vaccination $vaccination)
    {
        if ($vaccination->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        $vaccination->delete();

        return redirect()->route('vaccinations.index')->with('success', 'Vaksinasi berhasil dihapus.');
    }
}
