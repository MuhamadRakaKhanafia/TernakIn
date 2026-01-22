<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Vaccination;
use App\Models\Livestock;
use App\Models\User;
use App\Models\Disease;
use App\Models\Broadcast;
use App\Models\AnimalType;
use App\Models\Symptom;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Get counts for dashboard
        $stats = [
            'total_users' => User::count(),
            'total_forms' => Livestock::count(), // Assuming forms are livestock entries
            'total_diseases' => Disease::count(),
            'total_chat_queries' => \App\Models\AiChatMessage::where('role', 'user')->count(),
            'active_users' => User::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        // Get recent vaccinations
        /** @var \Illuminate\Database\Eloquent\Collection $recentVaccinations */
        $recentVaccinations = Vaccination::with('user')
            ->with('livestock.animalType')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get recent broadcasts
        $recentBroadcasts = Broadcast::orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentVaccinations', 'recentBroadcasts'));
    }

    // Vaccination Management Methods
    public function vaccinationsIndex(Request $request)
    {
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = Vaccination::with('user')->with('livestock.animalType');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('vaccination_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('vaccination_date', '<=', $request->date_to);
        }

        $vaccinations = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.vaccinations.index', compact('vaccinations'));
    }

    public function vaccinationsShow(Vaccination $vaccination)
    {
        return view('admin.vaccinations.show', compact('vaccination'));
    }

    public function vaccinationsCreate()
    {
        $users = User::where('user_type', 'peternak')->get();
        /** @intelephense-ignore-next-line */
        $livestocks = Livestock::with('user', 'animalType')->get();

        return view('admin.vaccinations.create', compact('users', 'livestocks'));
    }

    public function vaccinationsStore(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'livestock_id' => 'required|exists:livestocks,id',
            'vaccine_name' => 'required|string|max:255',
            'vaccination_date' => 'required|date',
            'next_vaccination_date' => 'nullable|date|after:vaccination_date',
            'status' => 'required|in:pending,approved,rejected',
            'notes' => 'nullable|string',
            'admin_notes' => 'nullable|string|max:1000',
            'admin_recommendations' => 'nullable|string|max:1000',
        ]);

        // Verify that the livestock belongs to the selected user
        $livestock = Livestock::findOrFail($request->livestock_id);
        if ($livestock->user_id != $request->user_id) {
            return back()->withInput()->withErrors(['livestock_id' => 'Hewan ternak yang dipilih tidak sesuai dengan user yang dipilih.']);
        }

        $vaccination = Vaccination::create([
            'user_id' => $request->user_id,
            'livestock_id' => $request->livestock_id,
            'vaccine_name' => $request->vaccine_name,
            'vaccination_date' => $request->vaccination_date,
            'next_vaccination_date' => $request->next_vaccination_date,
            'status' => $request->status,
            'notes' => $request->notes,
            'admin_notes' => $request->admin_notes,
            'admin_recommendations' => $request->admin_recommendations,
            'admin_validated_at' => $request->status !== 'pending' ? now() : null,
            'admin_validator_id' => $request->status !== 'pending' ? Auth::user()->id : null,
        ]);

        return redirect()->route('admin.vaccinations.index')->with('success', 'Vaksinasi berhasil ditambahkan.');
    }

    public function vaccinationsValidate(Request $request, Vaccination $vaccination)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'admin_notes' => 'nullable|string|max:1000',
            'admin_recommendations' => 'nullable|string|max:1000',
        ]);

        $action = $request->action;

        if ($action === 'approve') {
            $vaccination->update([
                'status' => 'approved',
                'admin_notes' => $request->admin_notes,
                'admin_recommendations' => $request->admin_recommendations,
                'admin_validated_at' => now(),
                'admin_validator_id' => Auth::user()->id,
            ]);

            // TODO: Send notification to user about approval
            $message = 'Jadwal vaksinasi telah disetujui.';
        } else {
            $vaccination->update([
                'status' => 'rejected',
                'admin_notes' => $request->admin_notes,
                'admin_recommendations' => $request->admin_recommendations,
                'admin_validated_at' => now(),
                'admin_validator_id' => Auth::user()->id,
            ]);

            // TODO: Send notification to user about rejection
            $message = 'Jadwal vaksinasi telah ditolak.';
        }

        return redirect()->route('admin.vaccinations.show', $vaccination)->with('success', $message);
    }

    // Form Submitters Management Methods
    public function formSubmittersIndex(Request $request)
    {
        $query = User::with('location.province', 'location.city')
            ->withCount('livestocks');

        // Apply filters
        if ($request->filled('type')) {
            $query->whereHas('livestocks.animalType', function ($q) use ($request) {
                $q->where('name', $request->type);
            });
        }

        if ($request->filled('health_status')) {
            $query->whereHas('livestocks', function ($q) use ($request) {
                $q->where('health_status', $request->health_status);
            });
        }

        if ($request->filled('vaccination_status')) {
            $query->whereHas('livestocks', function ($q) use ($request) {
                $q->where('vaccination_status', $request->vaccination_status);
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        $animalTypes = AnimalType::all();
        $totalLivestock = Livestock::count();
        $healthyLivestock = Livestock::where('health_status', 'sehat')->count();

        return view('admin.form-submitters', compact('users', 'animalTypes', 'totalLivestock', 'healthyLivestock'));
    }

    public function formSubmittersShow(User $user, Request $request)
    {
        $query = Livestock::with('animalType')
            ->where('user_id', $user->id);

        // Apply filters
        if ($request->filled('type')) {
            $query->whereHas('animalType', function ($q) use ($request) {
                $q->where('name', $request->type);
            });
        }

        if ($request->filled('health_status')) {
            $query->where('health_status', $request->health_status);
        }

        if ($request->filled('vaccination_status')) {
            $query->where('vaccination_status', $request->vaccination_status);
        }

        $livestocks = $query->orderBy('created_at', 'desc')->paginate(15);
        $animalTypes = AnimalType::all();

        return view('admin.form-submitters', compact('user', 'livestocks', 'animalTypes'));
    }

    // Additional Vaccination Methods
    public function vaccinationsApprove(Request $request, Vaccination $vaccination)
    {
        $vaccination->update([
            'status' => 'approved',
            'admin_notes' => $request->admin_notes,
            'admin_recommendations' => $request->admin_recommendations,
            'admin_validated_at' => now(),
            'admin_validator_id' => Auth::user()->id,
        ]);

        return redirect()->route('admin.vaccinations.show', $vaccination)->with('success', 'Jadwal vaksinasi telah disetujui.');
    }

    public function vaccinationsReject(Request $request, Vaccination $vaccination)
    {
        $vaccination->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
            'admin_recommendations' => $request->admin_recommendations,
            'admin_validated_at' => now(),
            'admin_validator_id' => Auth::user()->id,
        ]);

        return redirect()->route('admin.vaccinations.show', $vaccination)->with('success', 'Jadwal vaksinasi telah ditolak.');
    }

    public function vaccinationsComplete(Request $request, Vaccination $vaccination)
    {
        $vaccination->update([
            'status' => 'completed',
            'admin_notes' => $request->admin_notes,
            'admin_recommendations' => $request->admin_recommendations,
            'admin_validated_at' => now(),
            'admin_validator_id' => Auth::user()->id,
        ]);

        return redirect()->route('admin.vaccinations.show', $vaccination)->with('success', 'Jadwal vaksinasi telah diselesaikan.');
    }

    // Chat Queries Management Methods
    public function chatQueriesIndex(Request $request)
    {
        // Get user messages with their sessions and users
        $userMessagesQuery = \App\Models\AiChatMessage::with(['session.user'])
            ->where('role', 'user');

        // Apply filters
        if ($request->filled('user_id')) {
            $userMessagesQuery->whereHas('session', function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            });
        }

        if ($request->filled('date_from')) {
            $userMessagesQuery->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $userMessagesQuery->whereDate('created_at', '<=', $request->date_to);
        }

        $userMessages = $userMessagesQuery->orderBy('created_at', 'desc')->paginate(15);

        // Transform the data to match the view expectations
        $queries = $userMessages->map(function ($userMessage) {
            // Find the next AI response in the same session after this user message
            $aiResponse = \App\Models\AiChatMessage::where('session_id', $userMessage->session_id)
                ->where('role', 'assistant')
                ->where('created_at', '>', $userMessage->created_at)
                ->orderBy('created_at', 'asc')
                ->first();

            return (object) [
                'id' => $userMessage->id,
                'session' => $userMessage->session,
                'query' => $userMessage->content,
                'response' => $aiResponse ? $aiResponse->content : 'N/A',
                'response_status' => $aiResponse ? 'success' : 'error',
                'created_at' => $userMessage->created_at,
            ];
        });

        // Create a custom paginator with the transformed data
        $paginatedQueries = new \Illuminate\Pagination\LengthAwarePaginator(
            $queries,
            $userMessages->total(),
            $userMessages->perPage(),
            $userMessages->currentPage(),
            ['path' => $userMessages->path(), 'pageName' => $userMessages->getPageName()]
        );

        return view('admin.chat-queries', compact('queries', 'paginatedQueries'));
    }

    // Broadcasts Management Methods
    public function broadcastsIndex(Request $request)
    {
        $query = Broadcast::query();

        // Apply search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('message', 'like', '%' . $search . '%')
                  ->orWhere('link_text', 'like', '%' . $search . '%');
            });
        }

        // Apply filters
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $broadcasts = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.broadcasts.index', compact('broadcasts'));
    }

    public function broadcastsCreate()
    {
        return view('admin.broadcasts.create');
    }

    public function broadcastsStore(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'link_url' => 'nullable|url',
            'link_text' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date|after:now',
            'is_active' => 'nullable|boolean',
        ]);

        Broadcast::create([
            'message' => $request->message,
            'link_url' => $request->link_url,
            'link_text' => $request->link_text,
            'expires_at' => $request->expires_at,
            'is_active' => $request->is_active ?? true,
            'created_by' => Auth::user()->id,
        ]);

        return redirect()->route('admin.broadcasts.index')->with('success', 'Broadcast berhasil dibuat.');
    }

    public function broadcastsEdit(Broadcast $broadcast)
    {
        return view('admin.broadcasts.edit', compact('broadcast'));
    }

    public function broadcastsUpdate(Request $request, Broadcast $broadcast)
    {
        $request->validate([
            'message' => 'required|string',
            'link_url' => 'nullable|url',
            'link_text' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date|after:now',
            'is_active' => 'nullable|boolean',
        ]);

        $broadcast->update([
            'message' => $request->message,
            'link_url' => $request->link_url,
            'link_text' => $request->link_text,
            'expires_at' => $request->expires_at,
            'is_active' => $request->is_active ?? false,
        ]);

        return redirect()->route('admin.broadcasts.index')->with('success', 'Broadcast berhasil diperbarui.');
    }

    public function broadcastsDestroy(Broadcast $broadcast)
    {
        $broadcast->delete();

        return redirect()->route('admin.broadcasts.index')->with('success', 'Broadcast berhasil dihapus.');
    }

    // Diseases Management Methods
    public function diseasesIndex(Request $request)
    {
        $query = Disease::query();

        // Apply filters
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('causative_agent')) {
            $query->where('causative_agent', $request->causative_agent);
        }

        if ($request->filled('status')) {
            if ($request->status == 'active') {
                $query->where('is_active', 1);
            } elseif ($request->status == 'inactive') {
                $query->where('is_active', 0);
            }
        }

        $diseases = $query->orderBy('name')->paginate(15);

        // Get total stats (not filtered)
        $totalDiseases = Disease::count();
        $zoonoticDiseases = Disease::where('is_zoonotic', 1)->count();
        $activeDiseases = Disease::where('is_active', 1)->count();
        $inactiveDiseases = Disease::where('is_active', 0)->count();

        return view('admin.diseases.index', compact('diseases', 'totalDiseases', 'zoonoticDiseases', 'activeDiseases', 'inactiveDiseases'));
    }

    public function diseasesCreate()
    {
        $symptoms = Symptom::all();
        $animalTypes = AnimalType::all();

        return view('admin.diseases.create', compact('symptoms', 'animalTypes'));
    }

    public function diseasesStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:diseases',
            'disease_code' => 'required|string|max:255|unique:diseases',
            'causative_agent' => 'required|in:virus,bakteri,parasit,fungi,defisiensi_nutrisi,lainnya',
            'description' => 'nullable|string',
            'other_names' => 'nullable|string',
            'treatment' => 'nullable|string',
            'prevention' => 'nullable|string',
            'animal_types' => 'required|array|min:1',
            'animal_types.*' => 'exists:animal_types,id',
            'symptoms' => 'nullable|array',
            'symptoms.*.symptom_id' => 'exists:symptoms,id',
            'symptoms.*.probability' => 'nullable|integer|min:1|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            // Create the disease
            $disease = Disease::create([
                'name' => $request->name,
                'disease_code' => $request->disease_code,
                'causative_agent' => $request->causative_agent,
                'description' => $request->description,
                'other_names' => $request->other_names,
                'general_treatment' => $request->treatment,
                'prevention_method' => $request->prevention,
                'is_active' => $request->is_active ?? true,
            ]);

            // Attach animal types
            if ($request->has('animal_types')) {
                $disease->animalTypes()->attach($request->animal_types);
            }

            // Attach symptoms
            if ($request->has('symptoms')) {
                foreach ($request->symptoms as $symptomData) {
                    if (!empty($symptomData['symptom_id'])) {
                        $disease->symptoms()->attach($symptomData['symptom_id'], [
                            'probability' => ($symptomData['probability'] ?? 50) / 100,
                            'is_primary' => isset($symptomData['is_primary']),
                        ]);
                    }
                }
            }

            return redirect()->route('admin.diseases.index')->with('success', 'Penyakit berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->route('admin.diseases.create')->with('error', 'Gagal menambahkan penyakit: ' . $e->getMessage())->withInput();
        }
    }

    public function diseasesEdit(Disease $disease)
    {
        $symptoms = Symptom::all();
        $animalTypes = AnimalType::all();

        return view('admin.diseases.edit', compact('disease', 'symptoms', 'animalTypes'));
    }

    public function diseasesUpdate(Request $request, Disease $disease)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:diseases,name,' . $disease->id,
            'disease_code' => 'required|string|max:255|unique:diseases,disease_code,' . $disease->id,
            'causative_agent' => 'required|in:virus,bakteri,parasit,fungi,defisiensi_nutrisi,lainnya',
            'description' => 'nullable|string',
            'other_names' => 'nullable|string',
            'treatment' => 'nullable|string',
            'prevention' => 'nullable|string',
            'animal_types' => 'required|array|min:1',
            'animal_types.*' => 'exists:animal_types,id',
            'symptoms' => 'nullable|array',
            'symptoms.*.symptom_id' => 'exists:symptoms,id',
            'symptoms.*.probability' => 'nullable|integer|min:1|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            // Update the disease
            $disease->update([
                'name' => $request->name,
                'disease_code' => $request->disease_code,
                'causative_agent' => $request->causative_agent,
                'description' => $request->description,
                'other_names' => $request->other_names,
                'general_treatment' => $request->treatment,
                'prevention_method' => $request->prevention,
                'is_active' => $request->is_active ?? true,
            ]);

            // Sync animal types
            if ($request->has('animal_types')) {
                $disease->animalTypes()->sync($request->animal_types);
            }

            // Sync symptoms
            if ($request->has('symptoms')) {
                $symptomsData = [];
                foreach ($request->symptoms as $symptomData) {
                    if (!empty($symptomData['symptom_id'])) {
                        $symptomsData[$symptomData['symptom_id']] = [
                            'probability' => ($symptomData['probability'] ?? 50) / 100,
                            'is_primary' => isset($symptomData['is_primary']),
                        ];
                    }
                }
                $disease->symptoms()->sync($symptomsData);
            } else {
                // If no symptoms provided, detach all
                $disease->symptoms()->detach();
            }

            return redirect()->route('admin.diseases.index')->with('success', 'Penyakit berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->route('admin.diseases.edit', $disease)->with('error', 'Gagal memperbarui penyakit: ' . $e->getMessage())->withInput();
        }
    }

    public function diseasesDestroy(Disease $disease)
    {
        $disease->delete();

        return redirect()->route('admin.diseases.index')->with('success', 'Penyakit berhasil dihapus.');
    }

    public function loggedInUsers(Request $request)
    {
        // Get all registered users with pagination
        $query = User::with('location.province', 'location.city');

        // Apply filters if needed
        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.logged-in-users', compact('users'));
    }
}

