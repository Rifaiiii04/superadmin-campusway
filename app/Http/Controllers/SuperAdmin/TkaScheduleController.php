<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\TkaSchedule;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class TkaScheduleController extends Controller
{
    /**
     * Display a listing of TKA schedules for Super Admin
     */
    public function index()
    {
        try {
            // Debug: Log database connection and data count
            $totalSchedules = TkaSchedule::count();
            Log::info('TkaScheduleController::index - Total schedules in database: ' . $totalSchedules);
            
            // Use pagination instead of limit for better performance
            $schedules = TkaSchedule::select('id', 'title', 'description', 'start_date', 'end_date', 'status', 'type', 'instructions', 'target_schools', 'created_by', 'created_at', 'updated_at')
                ->orderBy('start_date', 'desc')
                ->paginate(10);
            
            $schools = School::select('id', 'name')
                ->limit(50) // Limit schools to prevent timeout
                ->get();

            // Debug data
            Log::info('TkaScheduleController::index - Schools count: ' . $schools->count());
            Log::info('TkaScheduleController::index - Schedules pagination data:', [
                'total' => $schedules->total(),
                'per_page' => $schedules->perPage(),
                'current_page' => $schedules->currentPage(),
                'data_count' => $schedules->count()
            ]);

            return Inertia::render('SuperAdmin/TkaSchedules', [
                'title' => 'Jadwal TKA',
                'schedules' => $schedules,
                'schools' => $schools,
                'auth' => [
                    'user' => auth()->guard('admin')->user()
                ],
                'debug' => [
                    'total_schedules' => $totalSchedules,
                    'pagination_total' => $schedules->total(),
                    'current_page' => $schedules->currentPage(),
                    'per_page' => $schedules->perPage(),
                    'schools_count' => $schools->count()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('TkaScheduleController::index - Error: ' . $e->getMessage());
            Log::error('TkaScheduleController::index - Stack trace: ' . $e->getTraceAsString());
            
            // Return minimal data to prevent blank page
            return Inertia::render('SuperAdmin/TkaSchedules', [
                'title' => 'Jadwal TKA',
                'schedules' => [
                    'data' => [],
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 10,
                    'total' => 0,
                ],
                'schools' => [],
                'auth' => [
                    'user' => auth()->guard('admin')->user()
                ],
                'error' => 'Gagal memuat data: ' . $e->getMessage(),
                'debug' => [
                    'error_message' => $e->getMessage(),
                    'error_file' => $e->getFile(),
                    'error_line' => $e->getLine()
                ]
            ]);
        }
    }

    /**
     * Display a listing of TKA schedules for API (JSON response)
     */
    public function apiIndex()
    {
        try {
            // Optimize queries with timeout protection
            $schedules = TkaSchedule::select('id', 'title', 'description', 'start_date', 'end_date', 'status', 'type', 'instructions', 'target_schools', 'created_by', 'created_at', 'updated_at')
                ->orderBy('start_date', 'desc')
                ->limit(100) // Limit results to prevent timeout
                ->get();
            
            $schools = School::select('id', 'name')
                ->limit(50) // Limit schools to prevent timeout
                ->get();

            return response()->json([
                'success' => true,
                'data' => $schedules,
                'schools' => $schools
            ]);

        } catch (\Exception $e) {
            Log::error('Super Admin TKA Schedule API index error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage(),
                'data' => [],
                'schools' => []
            ], 500);
        }
    }

    /**
     * Store a newly created TKA schedule
     */
    public function store(Request $request)
    {
        try {
            Log::info('TKA Schedule Store Request Data:', $request->all());
            
            // Always return JSON response for AJAX requests
            if ($request->ajax() || $request->expectsJson() || $request->is('api/*') || $request->header('Accept') === 'application/json' || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return $this->storeJson($request);
            }
            
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'type' => 'required|in:regular,makeup,special',
                'instructions' => 'nullable|string',
                'target_schools' => 'nullable|array',
                'target_schools.*' => 'integer|exists:schools,id',
                // PUSMENDIK Essential Fields
                'gelombang' => 'nullable|string|in:1,2',
                'hari_pelaksanaan' => 'nullable|string|in:Hari Pertama,Hari Kedua',
                'exam_venue' => 'nullable|string|max:255',
                'exam_room' => 'nullable|string|max:100',
                'contact_person' => 'nullable|string|max:255',
                'contact_phone' => 'nullable|string|max:20',
                'requirements' => 'nullable|string',
                'materials_needed' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $schedule = TkaSchedule::create([
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'type' => $request->type,
                'instructions' => $request->instructions,
                'target_schools' => $request->target_schools,
                'created_by' => 'Super Admin',
                // PUSMENDIK Essential Fields
                'gelombang' => $request->gelombang,
                'hari_pelaksanaan' => $request->hari_pelaksanaan,
                'exam_venue' => $request->exam_venue,
                'exam_room' => $request->exam_room,
                'contact_person' => $request->contact_person,
                'contact_phone' => $request->contact_phone,
                'requirements' => $request->requirements,
                'materials_needed' => $request->materials_needed
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Jadwal TKA berhasil dibuat',
                'data' => $schedule
            ], 201);

        } catch (\Exception $e) {
            Log::error('Super Admin TKA Schedule store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat jadwal TKA'
            ], 500);
        }
    }

    /**
     * Store a newly created TKA schedule (JSON response)
     */
    private function storeJson(Request $request)
    {
        try {
            Log::info('TKA Schedule Store JSON Request Data:', $request->all());
            
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'type' => 'required|in:regular,makeup,special',
                'status' => 'required|in:scheduled,ongoing,completed,cancelled',
                'instructions' => 'nullable|string',
                'target_schools' => 'nullable|array',
                'target_schools.*' => 'integer|exists:schools,id',
                'is_active' => 'nullable|boolean',
                'created_by' => 'nullable|string|max:255',
                // PUSMENDIK Essential Fields
                'gelombang' => 'nullable|string|in:1,2',
                'hari_pelaksanaan' => 'nullable|string|in:Hari Pertama,Hari Kedua',
                'exam_venue' => 'nullable|string|max:255',
                'exam_room' => 'nullable|string|max:100',
                'contact_person' => 'nullable|string|max:255',
                'contact_phone' => 'nullable|string|max:20',
                'requirements' => 'nullable|string',
                'materials_needed' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $schedule = TkaSchedule::create([
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'type' => $request->type,
                'status' => $request->status,
                'instructions' => $request->instructions,
                'target_schools' => $request->target_schools,
                'is_active' => $request->is_active ?? true,
                'created_by' => $request->created_by ?? 'Super Admin',
                // PUSMENDIK Essential Fields
                'gelombang' => $request->gelombang,
                'hari_pelaksanaan' => $request->hari_pelaksanaan,
                'exam_venue' => $request->exam_venue,
                'exam_room' => $request->exam_room,
                'contact_person' => $request->contact_person,
                'contact_phone' => $request->contact_phone,
                'requirements' => $request->requirements,
                'materials_needed' => $request->materials_needed
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Jadwal TKA berhasil dibuat',
                'data' => $schedule
            ], 201);

        } catch (\Exception $e) {
            Log::error('Super Admin TKA Schedule store JSON error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat jadwal TKA: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified TKA schedule (JSON response)
     */
    private function updateJson(Request $request, $id)
    {
        try {
            Log::info('TKA Schedule Update JSON Request Data:', $request->all());
            
            $schedule = TkaSchedule::find($id);

            if (!$schedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal TKA tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'type' => 'required|in:regular,makeup,special',
                'instructions' => 'nullable|string',
                'target_schools' => 'nullable|array',
                'target_schools.*' => 'integer|exists:schools,id',
                // PUSMENDIK Essential Fields
                'gelombang' => 'nullable|string|in:1,2',
                'hari_pelaksanaan' => 'nullable|string|in:Hari Pertama,Hari Kedua',
                'exam_venue' => 'nullable|string|max:255',
                'exam_room' => 'nullable|string|max:100',
                'contact_person' => 'nullable|string|max:255',
                'contact_phone' => 'nullable|string|max:20',
                'requirements' => 'nullable|string',
                'materials_needed' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $schedule->update([
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'type' => $request->type,
                'instructions' => $request->instructions,
                'target_schools' => $request->target_schools,
                // PUSMENDIK Essential Fields
                'gelombang' => $request->gelombang,
                'hari_pelaksanaan' => $request->hari_pelaksanaan,
                'exam_venue' => $request->exam_venue,
                'exam_room' => $request->exam_room,
                'contact_person' => $request->contact_person,
                'contact_phone' => $request->contact_phone,
                'requirements' => $request->requirements,
                'materials_needed' => $request->materials_needed
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Jadwal TKA berhasil diperbarui',
                'data' => $schedule
            ]);

        } catch (\Exception $e) {
            Log::error('Super Admin TKA Schedule update JSON error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui jadwal TKA: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified TKA schedule
     */
    public function show($id)
    {
        try {
            $schedule = TkaSchedule::find($id);

            if (!$schedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal TKA tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $schedule
            ]);

        } catch (\Exception $e) {
            Log::error('Super Admin TKA Schedule show error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data jadwal TKA'
            ], 500);
        }
    }

    /**
     * Update the specified TKA schedule
     */
    public function update(Request $request, $id)
    {
        try {
            // Always return JSON response for AJAX requests
            if ($request->ajax() || $request->expectsJson() || $request->is('api/*') || $request->header('Accept') === 'application/json' || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return $this->updateJson($request, $id);
            }
            
            $schedule = TkaSchedule::find($id);

            if (!$schedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal TKA tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'type' => 'required|in:regular,makeup,special',
                'instructions' => 'nullable|string',
                'target_schools' => 'nullable|array',
                'target_schools.*' => 'integer|exists:schools,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $schedule->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Jadwal TKA berhasil diperbarui',
                'data' => $schedule
            ]);

        } catch (\Exception $e) {
            Log::error('Super Admin TKA Schedule update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui jadwal TKA'
            ], 500);
        }
    }

    /**
     * Cancel TKA schedule
     */
    public function cancel($id)
    {
        try {
            // Always return JSON response for AJAX requests
            if (request()->ajax() || request()->expectsJson() || request()->is('api/*') || request()->header('Accept') === 'application/json' || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return $this->cancelJson($id);
            }
            
            $schedule = TkaSchedule::find($id);

            if (!$schedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal TKA tidak ditemukan'
                ], 404);
            }

            $schedule->update(['status' => 'cancelled']);

            return response()->json([
                'success' => true,
                'message' => 'Jadwal TKA berhasil dibatalkan',
                'data' => $schedule
            ]);

        } catch (\Exception $e) {
            Log::error('Super Admin TKA Schedule cancel error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan jadwal TKA'
            ], 500);
        }
    }

    /**
     * Cancel TKA schedule (JSON response)
     */
    private function cancelJson($id)
    {
        try {
            Log::info('TKA Schedule Cancel JSON Request for ID:', $id);
            
            $schedule = TkaSchedule::find($id);

            if (!$schedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal TKA tidak ditemukan'
                ], 404);
            }

            $schedule->update(['status' => 'cancelled']);

            return response()->json([
                'success' => true,
                'message' => 'Jadwal TKA berhasil dibatalkan',
                'data' => $schedule
            ]);

        } catch (\Exception $e) {
            Log::error('Super Admin TKA Schedule cancel JSON error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan jadwal TKA: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified TKA schedule
     */
    public function destroy($id)
    {
        try {
            // Always return JSON response for AJAX requests
            if (request()->ajax() || request()->expectsJson() || request()->is('api/*') || request()->header('Accept') === 'application/json' || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return $this->destroyJson($id);
            }
            
            $schedule = TkaSchedule::find($id);

            if (!$schedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal TKA tidak ditemukan'
                ], 404);
            }

            // Permanently delete from database
            $schedule->delete();

            return response()->json([
                'success' => true,
                'message' => 'Jadwal TKA berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            Log::error('Super Admin TKA Schedule destroy error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus jadwal TKA'
            ], 500);
        }
    }

    /**
     * Remove the specified TKA schedule (JSON response)
     */
    private function destroyJson($id)
    {
        try {
            Log::info('TKA Schedule Destroy JSON Request for ID:', $id);
            
            $schedule = TkaSchedule::find($id);

            if (!$schedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal TKA tidak ditemukan'
                ], 404);
            }

            // Permanently delete from database
            $schedule->delete();

            return response()->json([
                'success' => true,
                'message' => 'Jadwal TKA berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            Log::error('Super Admin TKA Schedule destroy JSON error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus jadwal TKA: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics for dashboard
     */
    public function statistics()
    {
        try {
            $stats = [
                'total' => TkaSchedule::count(),
                'scheduled' => TkaSchedule::where('status', 'scheduled')->count(),
                'ongoing' => TkaSchedule::where('status', 'ongoing')->count(),
                'completed' => TkaSchedule::where('status', 'completed')->count(),
                'cancelled' => TkaSchedule::where('status', 'cancelled')->count(),
                'upcoming' => TkaSchedule::where('start_date', '>', now())->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Super Admin TKA Schedule statistics error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik jadwal TKA'
            ], 500);
        }
    }
}
