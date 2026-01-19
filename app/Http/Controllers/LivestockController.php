<?php

namespace App\Http\Controllers;

use App\Models\Livestock;
use App\Models\AnimalType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LivestockController extends Controller
{
    public function index(Request $request)
    {
        $query = Livestock::with('animalType');

        // Filter logic
        if ($request->has('type') && $request->type != '') {
            $query->whereHas('animalType', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->type . '%');
            });
        }

        if ($request->has('health_status') && $request->health_status != '') {
            $query->where('health_status', $request->health_status);
        }

        if ($request->has('vaccination_status') && $request->vaccination_status != '') {
            $query->where('vaccination_status', $request->vaccination_status);
        }

        $livestocks = $query->latest()->paginate(10);

        // Statistics
        $totalLivestock = Livestock::count();
        $healthyLivestock = Livestock::where('health_status', 'sehat')->count();
        $needVaccination = Livestock::where('vaccination_status', 'need_update')->count();
        $sickLivestock = Livestock::where('health_status', 'sakit')->count();

        $animalTypes = AnimalType::all();

        return view('livestock.index', compact(
            'livestocks',
            'animalTypes',
            'totalLivestock',
            'healthyLivestock',
            'needVaccination',
            'sickLivestock'
        ));
    }

    public function create()
    {
        $animalTypes = AnimalType::all();
        return view('livestock.create', compact('animalTypes'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $this->validateLivestock($request);

            // Debug data
            Log::info('Creating livestock with validated data:', $validated);

            $livestock = Livestock::create($validated);

            // Debug created data
            Log::info('Livestock created successfully:', $livestock->toArray());

            DB::commit();

            return redirect()->route('livestock.index')
                ->with('success', 'Data hewan ternak berhasil disimpan!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error:', $e->errors());
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Livestock $livestock)
    {
        $livestock->load('animalType');
        return view('livestock.show', compact('livestock'));
    }

    public function edit(Livestock $livestock)
    {
        $animalTypes = AnimalType::all();
        $livestock->load('animalType');
        return view('livestock.edit', compact('livestock', 'animalTypes'));
    }

    public function update(Request $request, Livestock $livestock)
    {
        try {
            DB::beginTransaction();

            $validated = $this->validateLivestock($request, $livestock->id);

            $livestock->update($validated);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data hewan ternak berhasil diperbarui!',
                    'data' => $livestock->load('animalType')
                ]);
            }

            return redirect()->route('livestock.index')->with('success', 'Data hewan ternak berhasil diperbarui!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Request $request, Livestock $livestock)
    {
        try {
            $livestock->delete();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data hewan ternak berhasil dihapus!'
                ]);
            }

            return redirect()->route('livestock.index')->with('success', 'Data hewan ternak berhasil dihapus!');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Validate livestock data based on animal type
     */
    private function validateLivestock(Request $request, $id = null)
    {
        $animalType = AnimalType::find($request->animal_type_id);
        $rules = [
            'animal_type_id' => 'required|exists:animal_types,id',
            'name' => 'required|string|max:255',
            'identification_number' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'acquisition_date' => 'required|date',
            'sex' => 'required|in:jantan,betina',
            'health_status' => 'required|in:sehat,sakit',
            'vaccination_status' => 'required|in:up_to_date,need_update,not_vaccinated',
            'notes' => 'nullable|string',
            
            // Common fields for all types
            'weight_kg' => 'nullable|numeric|min:0.1',
            'feed_type' => 'nullable|string|max:255',
            'daily_feed_kg' => 'nullable|numeric|min:0.01',
            'housing_type' => 'nullable|string|max:255',
        ];

        if ($animalType) {
            switch ($animalType->category) {
                case 'poultry':
                    $rules = array_merge($rules, [
                        'strain' => 'nullable|string|max:255',
                        'age_weeks' => 'nullable|integer|min:1|max:104',
                        'egg_production' => 'nullable|integer|min:0|max:365',
                        'flock_size' => 'nullable|integer|min:1|max:10000',
                    ]);
                    break;

                case 'large_animal':
                    $rules = array_merge($rules, [
                        'breed' => 'nullable|string|max:255',
                        'age_months' => 'nullable|integer|min:1|max:240',
                        'purpose' => 'nullable|in:peternakan,daging,susu,kulit',
                        'milk_production_liter' => 'nullable|numeric|min:0|max:50',
                        'pregnancy_status' => 'nullable|in:tidak_hamil,hamil',
                    ]);
                    break;

                default:
                    $rules = array_merge($rules, [
                        'breed' => 'nullable|string|max:255',
                        'age_months' => 'nullable|integer|min:1|max:240',
                        'purpose' => 'nullable|string|max:255',
                    ]);
                    break;
            }
        }

        return $request->validate($rules);
    }
}