<?php

namespace App\Http\Controllers;

use App\Models\TkaSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class TkaScheduleController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Check if this is an API request
            if ($request->wantsJson() || $request->is('api/*')) {
                $schoolId = $request->query('school_id');
                
                $query = TkaSchedule::where('is_active', true)
                    ->orderBy('start_date', 'desc');
                
                // Filter by school if provided
                if ($schoolId) {
                    $query->where(function($q) use ($schoolId) {
                        $q->whereNull('target_schools')
                          ->orWhereJsonContains('target_schools', $schoolId);
                    });
                }
                
                $schedules = $query->get();
                
                return response()->json([
                    'success' => true,
                    'data' => $schedules
                ], 200);
            }
            
            // For web requests (Inertia)
            $schedules = TkaSchedule::orderBy('start_date', 'desc')->paginate(10);
            
            return Inertia::render('SuperAdmin/TkaSchedules', [
                'title' => 'Jadwal TKA',
                'schedules' => $schedules,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching TKA schedules: ' . $e->getMessage());
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memuat data jadwal TKA',
                    'data' => []
                ], 500);
            }
            
            return Inertia::render('SuperAdmin/TkaSchedules', [
                'title' => 'Jadwal TKA',
                'schedules' => [
                    'data' => [],
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 10,
                    'total' => 0,
                ],
                'error' => 'Gagal memuat data jadwal TKA'
            ]);
        }
    }
    
    public function upcoming(Request $request)
    {
        try {
            $schoolId = $request->query('school_id');
            $now = now();
            
            $query = TkaSchedule::where('is_active', true)
                ->where('end_date', '>=', $now)
                ->orderBy('start_date', 'asc');
            
            // Filter by school if provided
            if ($schoolId) {
                $query->where(function($q) use ($schoolId) {
                    $q->whereNull('target_schools')
                      ->orWhereJsonContains('target_schools', $schoolId);
                });
            }
            
            $schedules = $query->get();
            
            return response()->json([
                'success' => true,
                'data' => $schedules
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error fetching upcoming TKA schedules: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat jadwal TKA mendatang',
                'data' => []
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'instructions' => 'nullable|string',
            'target_schools' => 'nullable|string',
            'is_active' => 'boolean',
            'created_by' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            TkaSchedule::create([
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status,
                'type' => $request->type,
                'instructions' => $request->instructions,
                'target_schools' => $request->target_schools,
                'is_active' => $request->is_active ?? true,
                'created_by' => $request->created_by ?? auth()->user()->name ?? 'System',
            ]);

            return redirect()->back()->with('success', 'Jadwal TKA berhasil ditambahkan');
        } catch (\Exception $e) {
            \Log::error('Error creating TKA schedule: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal menambahkan jadwal TKA'])->withInput();
        }
    }

    public function show(TkaSchedule $tkaSchedule)
    {
        return Inertia::render('SuperAdmin/TkaScheduleDetail', [
            'title' => 'Detail Jadwal TKA',
            'schedule' => $tkaSchedule,
        ]);
    }

    public function update(Request $request, TkaSchedule $tkaSchedule)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'instructions' => 'nullable|string',
            'target_schools' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $tkaSchedule->update([
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status,
                'type' => $request->type,
                'instructions' => $request->instructions,
                'target_schools' => $request->target_schools,
                'is_active' => $request->is_active ?? true,
            ]);

            return redirect()->back()->with('success', 'Jadwal TKA berhasil diperbarui');
        } catch (\Exception $e) {
            \Log::error('Error updating TKA schedule: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal memperbarui jadwal TKA'])->withInput();
        }
    }

    public function destroy(TkaSchedule $tkaSchedule)
    {
        try {
            $tkaSchedule->delete();
            return redirect()->back()->with('success', 'Jadwal TKA berhasil dihapus');
        } catch (\Exception $e) {
            \Log::error('Error deleting TKA schedule: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal menghapus jadwal TKA']);
        }
    }

    public function toggle(TkaSchedule $tkaSchedule)
    {
        try {
            $tkaSchedule->update([
                'is_active' => !$tkaSchedule->is_active
            ]);

            return redirect()->back()->with('success', 'Status jadwal TKA berhasil diubah');
        } catch (\Exception $e) {
            \Log::error('Error toggling TKA schedule: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal mengubah status jadwal TKA']);
        }
    }
}