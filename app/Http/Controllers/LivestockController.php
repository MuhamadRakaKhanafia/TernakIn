<?php

namespace App\Http\Controllers;

use App\Models\Livestock;
use App\Models\AnimalType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LivestockController extends Controller
{
    /**
     * Display a listing of the livestock.
     */
    public function index(Request $request)
    {
        try {
            // Check if user is authenticated
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
            }

            // Start query with user's livestock
            $query = Livestock::with('animalType')->where('user_id', Auth::id());

            // Apply filters if provided
            if ($request->filled('animal_type_id')) {
                $query->where('animal_type_id', $request->animal_type_id);
            }

            if ($request->filled('health_status')) {
                $query->where('health_status', $request->health_status);
            }

            if ($request->filled('vaccination_status')) {
                $query->where('vaccination_status', $request->vaccination_status);
            }

            // Get paginated results
            $livestocks = $query->orderBy('created_at', 'desc')->paginate(10);

            // Calculate statistics
            $totalLivestock = Livestock::where('user_id', Auth::id())->count();
            $healthyLivestock = Livestock::where('user_id', Auth::id())->where('health_status', 'sehat')->count();
            $needVaccination = Livestock::where('user_id', Auth::id())->where('vaccination_status', 'need_update')->count();
            $sickLivestock = Livestock::where('user_id', Auth::id())->where('health_status', 'sakit')->count();

            // Get all animal types for filter dropdown
            $animalTypes = AnimalType::all();

            return view('livestock.index', compact(
                'livestocks',
                'animalTypes',
                'totalLivestock',
                'healthyLivestock',
                'needVaccination',
                'sickLivestock'
            ));

        } catch (\Exception $e) {
            Log::error('Error in livestock index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data hewan ternak.');
        }
    }

    /**
     * Show the form for creating a new livestock.
     */
    public function create()
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
            }

            $animalTypes = AnimalType::all();
            return view('livestock.create', compact('animalTypes'));

        } catch (\Exception $e) {
            Log::error('Error in livestock create form: ' . $e->getMessage());
            return redirect()->route('livestocks.index')->with('error', 'Terjadi kesalahan saat memuat form.');
        }
    }

    /**
     * Store a newly created livestock in storage.
     */
    public function store(Request $request)
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
            }

            DB::beginTransaction();

            // Validate the request data
            $validatedData = $this->validateLivestock($request);

            // Add user_id to validated data
            $validatedData['user_id'] = Auth::id();

            // Calculate age from birth date if provided
            if (!empty($validatedData['birth_date'])) {
                $this->calculateAgeFromBirthDate($validatedData);
            }

            // Log the data for debugging
            Log::info('Creating new livestock', [
                'user_id' => Auth::id(),
                'data' => $validatedData
            ]);

            // Create the livestock record
            $livestock = Livestock::create($validatedData);

            DB::commit();

            Log::info('Livestock created successfully', ['livestock_id' => $livestock->id]);

            return redirect()->route('livestocks.index')
                ->with('success', 'Data hewan ternak berhasil disimpan!');

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::warning('Validation failed in livestock store', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Terdapat kesalahan dalam pengisian form. Silakan periksa kembali.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing livestock: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'input' => $request->except(['_token'])
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified livestock.
     */
    public function show(Livestock $livestock)
    {
        try {
            // Check authorization
            if ($livestock->user_id !== Auth::id()) {
                Log::warning('Unauthorized access attempt to livestock', [
                    'requested_livestock_id' => $livestock->id,
                    'user_id' => Auth::id()
                ]);
                abort(403, 'Anda tidak memiliki izin untuk melihat data ini.');
            }

            $livestock->load('animalType');
            return view('livestock.show', compact('livestock'));

        } catch (\Exception $e) {
            Log::error('Error showing livestock: ' . $e->getMessage());
            return redirect()->route('livestocks.index')->with('error', 'Terjadi kesalahan saat memuat data.');
        }
    }

    /**
     * Show the form for editing the specified livestock.
     */
    public function edit(Livestock $livestock)
    {
        try {
            // Check authorization
            if ($livestock->user_id !== Auth::id()) {
                abort(403, 'Anda tidak memiliki izin untuk mengedit data ini.');
            }

            $animalTypes = AnimalType::all();
            $livestock->load('animalType');
            
            return view('livestock.edit', compact('livestock', 'animalTypes'));

        } catch (\Exception $e) {
            Log::error('Error in livestock edit form: ' . $e->getMessage());
            return redirect()->route('livestocks.index')->with('error', 'Terjadi kesalahan saat memuat form edit.');
        }
    }

    /**
     * Update the specified livestock in storage.
     */
    public function update(Request $request, Livestock $livestock)
    {
        try {
            // Check authorization
            if ($livestock->user_id !== Auth::id()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki izin untuk mengubah data ini.'
                    ], 403);
                }
                abort(403, 'Anda tidak memiliki izin untuk mengubah data ini.');
            }

            DB::beginTransaction();

            // Validate the request data
            $validatedData = $this->validateLivestock($request, $livestock->id);
            
            // Calculate age from birth date if provided
            if (!empty($validatedData['birth_date'])) {
                $this->calculateAgeFromBirthDate($validatedData);
            }
            
            // Update the livestock
            $livestock->update($validatedData);

            DB::commit();

            // Handle AJAX request (for inline editing)
            if ($request->expectsJson()) {
                $livestock->refresh()->load('animalType');
                
                return response()->json([
                    'success' => true,
                    'message' => 'Data hewan ternak berhasil diperbarui!',
                    'data' => $livestock
                ]);
            }

            return redirect()->route('livestocks.index')
                ->with('success', 'Data hewan ternak berhasil diperbarui!');

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::warning('Validation failed in livestock update', [
                'livestock_id' => $livestock->id,
                'errors' => $e->errors()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Terdapat kesalahan dalam pengisian form.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating livestock: ' . $e->getMessage(), [
                'livestock_id' => $livestock->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified livestock from storage.
     */
    public function destroy(Request $request, Livestock $livestock)
    {
        try {
            // Check authorization
            if ($livestock->user_id !== Auth::id()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki izin untuk menghapus data ini.'
                    ], 403);
                }
                abort(403, 'Anda tidak memiliki izin untuk menghapus data ini.');
            }

            $livestockId = $livestock->id;
            $livestock->delete();

            Log::info('Livestock deleted', ['livestock_id' => $livestockId, 'user_id' => Auth::id()]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data hewan ternak berhasil dihapus!'
                ]);
            }

            return redirect()->route('livestocks.index')
                ->with('success', 'Data hewan ternak berhasil dihapus!');

        } catch (\Exception $e) {
            Log::error('Error deleting livestock: ' . $e->getMessage(), [
                'livestock_id' => $livestock->id
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Get livestock statistics for AJAX requests.
     */
    public function stats()
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan login terlebih dahulu.'
                ], 401);
            }

            $userId = Auth::id();
            
            $stats = [
                'total' => Livestock::where('user_id', $userId)->count(),
                'healthy' => Livestock::where('user_id', $userId)->where('health_status', 'sehat')->count(),
                'needVaccination' => Livestock::where('user_id', $userId)->where('vaccination_status', 'need_update')->count(),
                'sick' => Livestock::where('user_id', $userId)->where('health_status', 'sakit')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting livestock stats: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil statistik.'
            ], 500);
        }
    }

    /**
     * Validate livestock data based on animal type.
     */
    private function validateLivestock(Request $request, $id = null)
    {
        // Base validation rules
        $rules = [
            'animal_type_id' => 'required|exists:animal_types,id',
            'name' => 'required|string|max:255',
            'identification_number' => 'nullable|string|max:255|unique:livestocks,identification_number,' . $id,
            'birth_date' => 'nullable|date|before_or_equal:today',
            'acquisition_date' => 'required|date|before_or_equal:today',
            'sex' => 'required|in:jantan,betina',
            'health_status' => 'required|in:sehat,sakit',
            'vaccination_status' => 'required|in:up_to_date,need_update,not_vaccinated',
            'notes' => 'nullable|string|max:1000',
            
            // Common fields for all types
            'weight_kg' => 'nullable|numeric|min:0.1',
            'feed_type' => 'nullable|string|max:255',
            'daily_feed_kg' => 'nullable|numeric|min:0.01',
            'housing_type' => 'nullable|string|max:255',
            'housing_size' => 'nullable|string|max:255',
        ];

        // Add conditional rules based on animal type
        if ($request->has('animal_type_id')) {
            $animalType = AnimalType::find($request->animal_type_id);
            
            if ($animalType) {
                switch ($animalType->category) {
                    case 'poultry':
                        $rules = array_merge($rules, [
                            'strain' => 'nullable|string|max:255',
                            'age_weeks' => 'nullable|integer|min:1|max:104',
                            'egg_production' => 'nullable|integer|min:0|max:365',
                            'flock_size' => 'nullable|integer|min:1|max:10000',
                        ]);
                        // Clear age_months for poultry
                        $request->merge(['age_months' => null]);
                        break;

                    case 'large_animal':
                        $rules = array_merge($rules, [
                            'breed' => 'nullable|string|max:255',
                            'age_months' => 'nullable|integer|min:1|max:240',
                            'purpose' => 'nullable|in:peternakan,daging,susu,kulit',
                            'milk_production_liter' => 'nullable|numeric|min:0|max:50',
                            'pregnancy_status' => 'nullable|in:tidak_hamil,hamil',
                        ]);
                        // Clear age_weeks for large animals
                        $request->merge(['age_weeks' => null]);
                        break;

                    default:
                        $rules = array_merge($rules, [
                            'breed' => 'nullable|string|max:255',
                            'age_months' => 'nullable|integer|min:1|max:240',
                            'purpose' => 'nullable|string|max:255',
                        ]);
                        // Clear age_weeks for other animals
                        $request->merge(['age_weeks' => null]);
                        break;
                }
            }
        }

        // Custom validation messages
        $messages = [
            'animal_type_id.required' => 'Jenis hewan harus dipilih.',
            'animal_type_id.exists' => 'Jenis hewan yang dipilih tidak valid.',
            'name.required' => 'Nama/identifikasi hewan harus diisi.',
            'name.max' => 'Nama/identifikasi hewan maksimal 255 karakter.',
            'birth_date.before_or_equal' => 'Tanggal lahir tidak boleh lebih dari hari ini.',
            'acquisition_date.required' => 'Tanggal akuisisi harus diisi.',
            'acquisition_date.before_or_equal' => 'Tanggal akuisisi tidak boleh lebih dari hari ini.',
            'sex.required' => 'Jenis kelamin harus dipilih.',
            'sex.in' => 'Jenis kelamin harus Jantan atau Betina.',
            'health_status.required' => 'Status kesehatan harus dipilih.',
            'health_status.in' => 'Status kesehatan harus Sehat atau Sakit.',
            'vaccination_status.required' => 'Status vaksinasi harus dipilih.',
            'vaccination_status.in' => 'Status vaksinasi tidak valid.',
            'weight_kg.numeric' => 'Berat harus berupa angka.',
            'weight_kg.min' => 'Berat minimal 0.1 kg.',
            'daily_feed_kg.numeric' => 'Pakan harian harus berupa angka.',
            'daily_feed_kg.min' => 'Pakan harian minimal 0.01 kg.',
        ];

        // Additional validation for birth date and acquisition date
        $validator = validator($request->all(), $rules, $messages);

        $validator->after(function ($validator) use ($request) {
            if ($request->birth_date && $request->acquisition_date) {
                $birthDate = strtotime($request->birth_date);
                $acquisitionDate = strtotime($request->acquisition_date);
                
                if ($birthDate > $acquisitionDate) {
                    $validator->errors()->add(
                        'birth_date',
                        'Tanggal lahir tidak boleh setelah tanggal akuisisi.'
                    );
                    $validator->errors()->add(
                        'acquisition_date',
                        'Tanggal akuisisi tidak boleh sebelum tanggal lahir.'
                    );
                }
            }
        });

        return $validator->validate();
    }

    /**
     * Calculate age from birth date and update validated data.
     */
    private function calculateAgeFromBirthDate(&$validatedData)
    {
        if (empty($validatedData['birth_date'])) {
            return;
        }

        $birthDate = \Carbon\Carbon::parse($validatedData['birth_date']);
        $now = \Carbon\Carbon::now();
        
        $animalType = AnimalType::find($validatedData['animal_type_id']);
        
        if (!$animalType) {
            return;
        }

        if ($animalType->category === 'poultry') {
            // Calculate weeks for poultry
            $weeks = $birthDate->diffInWeeks($now);
            $validatedData['age_weeks'] = $weeks > 0 ? $weeks : 1;
            $validatedData['age_months'] = null;
        } else {
            // Calculate months for other animals
            $months = $birthDate->diffInMonths($now);
            $validatedData['age_months'] = $months > 0 ? $months : 1;
            $validatedData['age_weeks'] = null;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    protected function rules()
    {
        return [
            'animal_type_id' => 'required|exists:animal_types,id',
            'name' => 'required|string|max:255',
            'identification_number' => 'nullable|string|max:255|unique:livestocks',
            'birth_date' => 'nullable|date|before_or_equal:today',
            'acquisition_date' => 'required|date|before_or_equal:today',
            'sex' => 'required|in:jantan,betina',
            'health_status' => 'required|in:sehat,sakit',
            'vaccination_status' => 'required|in:up_to_date,need_update,not_vaccinated',
            'notes' => 'nullable|string|max:1000',
            'weight_kg' => 'nullable|numeric|min:0.1',
            'feed_type' => 'nullable|string|max:255',
            'daily_feed_kg' => 'nullable|numeric|min:0.01',
            'housing_type' => 'nullable|string|max:255',
            'housing_size' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom validation messages.
     */
    protected function messages()
    {
        return [
            'animal_type_id.required' => 'Jenis hewan harus dipilih.',
            'animal_type_id.exists' => 'Jenis hewan yang dipilih tidak valid.',
            'name.required' => 'Nama/identifikasi hewan harus diisi.',
            'name.max' => 'Nama/identifikasi hewan maksimal 255 karakter.',
            'birth_date.before_or_equal' => 'Tanggal lahir tidak boleh lebih dari hari ini.',
            'acquisition_date.required' => 'Tanggal akuisisi harus diisi.',
            'acquisition_date.before_or_equal' => 'Tanggal akuisisi tidak boleh lebih dari hari ini.',
            'sex.required' => 'Jenis kelamin harus dipilih.',
            'sex.in' => 'Jenis kelamin harus Jantan atau Betina.',
            'health_status.required' => 'Status kesehatan harus dipilih.',
            'health_status.in' => 'Status kesehatan harus Sehat atau Sakit.',
            'vaccination_status.required' => 'Status vaksinasi harus dipilih.',
            'vaccination_status.in' => 'Status vaksinasi tidak valid.',
            'weight_kg.numeric' => 'Berat harus berupa angka.',
            'weight_kg.min' => 'Berat minimal 0.1 kg.',
            'daily_feed_kg.numeric' => 'Pakan harian harus berupa angka.',
            'daily_feed_kg.min' => 'Pakan harian minimal 0.01 kg.',
        ];
    }
}