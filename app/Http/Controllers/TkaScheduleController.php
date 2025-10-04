<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TkaSchedule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class TkaScheduleController extends Controller
{
    /**
     * Get all TKA schedules
     */
    public function index(Request $request)
    {
        try {
            $query = TkaSchedule::query();

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by school if provided
            if ($request->has('school_id')) {
                $query->forSchool($request->school_id);
            }

            $schedules = $query->active()->orderBy('start_date', 'asc')->get();

            // Transform data to include PUSMENDIK fields and formatted data
            $transformedSchedules = $schedules->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'title' => $schedule->title,
                    'description' => $schedule->description,
                    'start_date' => $schedule->start_date->toISOString(),
                    'end_date' => $schedule->end_date->toISOString(),
                    'status' => $schedule->status,
                    'type' => $schedule->type,
                    'instructions' => $schedule->instructions,
                    'target_schools' => $schedule->target_schools,
                    'is_active' => $schedule->is_active,
                    'created_by' => $schedule->created_by,
                    'created_at' => $schedule->created_at->toISOString(),
                    'updated_at' => $schedule->updated_at->toISOString(),
                    
                    // PUSMENDIK Essential Fields
                    'gelombang' => $schedule->gelombang,
                    'hari_pelaksanaan' => $schedule->hari_pelaksanaan,
                    'exam_venue' => $schedule->exam_venue,
                    'exam_room' => $schedule->exam_room,
                    'contact_person' => $schedule->contact_person,
                    'contact_phone' => $schedule->contact_phone,
                    'requirements' => $schedule->requirements,
                    'materials_needed' => $schedule->materials_needed,
                    
                    // Formatted data for frontend
                    'formatted_start_date' => $schedule->formatted_start_date,
                    'formatted_end_date' => $schedule->formatted_end_date,
                    'status_badge' => $schedule->status_badge,
                    'type_badge' => $schedule->type_badge,
                    'duration' => $schedule->duration,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedSchedules,
                'total' => $transformedSchedules->count()
            ]);

        } catch (\Exception $e) {
            Log::error('TKA Schedule index error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data jadwal TKA'
            ], 500);
        }
    }

    /**
     * Get upcoming TKA schedules
     */
    public function upcoming(Request $request)
    {
        try {
            $query = TkaSchedule::upcoming()->active();

            if ($request->has('school_id')) {
                $query->forSchool($request->school_id);
            }

            $schedules = $query->orderBy('start_date', 'asc')->get();

            // Transform data to include PUSMENDIK fields and formatted data
            $transformedSchedules = $schedules->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'title' => $schedule->title,
                    'description' => $schedule->description,
                    'start_date' => $schedule->start_date->toISOString(),
                    'end_date' => $schedule->end_date->toISOString(),
                    'status' => $schedule->status,
                    'type' => $schedule->type,
                    'instructions' => $schedule->instructions,
                    'target_schools' => $schedule->target_schools,
                    'is_active' => $schedule->is_active,
                    'created_by' => $schedule->created_by,
                    'created_at' => $schedule->created_at->toISOString(),
                    'updated_at' => $schedule->updated_at->toISOString(),
                    
                    // PUSMENDIK Essential Fields
                    'gelombang' => $schedule->gelombang,
                    'hari_pelaksanaan' => $schedule->hari_pelaksanaan,
                    'exam_venue' => $schedule->exam_venue,
                    'exam_room' => $schedule->exam_room,
                    'contact_person' => $schedule->contact_person,
                    'contact_phone' => $schedule->contact_phone,
                    'requirements' => $schedule->requirements,
                    'materials_needed' => $schedule->materials_needed,
                    
                    // Formatted data for frontend
                    'formatted_start_date' => $schedule->formatted_start_date,
                    'formatted_end_date' => $schedule->formatted_end_date,
                    'status_badge' => $schedule->status_badge,
                    'type_badge' => $schedule->type_badge,
                    'duration' => $schedule->duration,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedSchedules,
                'total' => $transformedSchedules->count()
            ]);

        } catch (\Exception $e) {
            Log::error('TKA Schedule upcoming error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil jadwal TKA yang akan datang'
            ], 500);
        }
    }

    /**
     * Create new TKA schedule
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_date' => 'required|date|after:now',
                'end_date' => 'required|date|after:start_date',
                'type' => 'required|in:regular,makeup,special',
                'instructions' => 'nullable|string',
                'target_schools' => 'nullable|array',
                // PUSMENDIK Essential Fields
                'gelombang' => 'nullable|in:1,2',
                'hari_pelaksanaan' => 'nullable|in:Hari Pertama,Hari Kedua',
                'exam_venue' => 'nullable|string|max:255',
                'exam_room' => 'nullable|string|max:255',
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
            Log::error('TKA Schedule store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat jadwal TKA'
            ], 500);
        }
    }

    /**
     * Update TKA schedule
     */
    public function update(Request $request, $id)
    {
        try {
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
                // PUSMENDIK Essential Fields
                'gelombang' => 'nullable|in:1,2',
                'hari_pelaksanaan' => 'nullable|in:Hari Pertama,Hari Kedua',
                'exam_venue' => 'nullable|string|max:255',
                'exam_room' => 'nullable|string|max:255',
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

            $schedule->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Jadwal TKA berhasil diperbarui',
                'data' => $schedule
            ]);

        } catch (\Exception $e) {
            Log::error('TKA Schedule update error: ' . $e->getMessage());
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
            Log::error('TKA Schedule cancel error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan jadwal TKA'
            ], 500);
        }
    }

    /**
     * Delete TKA schedule
     */
    public function destroy($id)
    {
        try {
            $schedule = TkaSchedule::find($id);

            if (!$schedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal TKA tidak ditemukan'
                ], 404);
            }

            $schedule->update(['is_active' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Jadwal TKA berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            Log::error('TKA Schedule destroy error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus jadwal TKA'
            ], 500);
        }
    }
}
