<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class SchoolController extends Controller
{
    public function index()
    {
        try {
            // Debug: Log database connection and data count
            $totalSchools = School::count();
            Log::info('SchoolController::index - Total schools in database: ' . $totalSchools);
            
            $schools = School::paginate(10);
            
            // Debug: Log pagination data
            Log::info('SchoolController::index - Pagination data:', [
                'total' => $schools->total(),
                'per_page' => $schools->perPage(),
                'current_page' => $schools->currentPage(),
                'data_count' => $schools->count()
            ]);
            
            return Inertia::render('SuperAdmin/Schools', [
                'title' => 'Manajemen Sekolah',
                'schools' => $schools,
                'debug' => [
                    'total_schools' => $totalSchools,
                    'pagination_total' => $schools->total(),
                    'current_page' => $schools->currentPage(),
                    'per_page' => $schools->perPage()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching schools: ' . $e->getMessage());
            Log::error('SchoolController::index - Stack trace: ' . $e->getTraceAsString());
            
            return Inertia::render('SuperAdmin/Schools', [
                'title' => 'Manajemen Sekolah',
                'schools' => [
                    'data' => [],
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 10,
                    'total' => 0,
                ],
                'error' => 'Gagal memuat data sekolah: ' . $e->getMessage(),
                'debug' => [
                    'error_message' => $e->getMessage(),
                    'error_file' => $e->getFile(),
                    'error_line' => $e->getLine()
                ]
            ]);
        }
    }

    public function store(Request $request)
    {
        // Only return JSON for explicit API requests
        if ($request->is('api/*') || $request->header('Accept') === 'application/json') {
            return $this->storeJson($request);
        }

        $validator = Validator::make($request->all(), [
            'npsn' => 'required|string|unique:schools,npsn',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            School::create([
                'npsn' => $request->npsn,
                'name' => $request->name,
                'password' => bcrypt($request->password),
            ]);

            return redirect()->back()->with('success', 'Sekolah berhasil ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error creating school: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal menambahkan sekolah'])->withInput();
        }
    }

    /**
     * Store a newly created school (JSON response)
     */
    private function storeJson(Request $request)
    {
        try {
            Log::info('School Store JSON Request Data:', $request->all());
            
            $validator = Validator::make($request->all(), [
                'npsn' => 'required|string|unique:schools,npsn',
                'name' => 'required|string|max:255',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $school = School::create([
                'npsn' => $request->npsn,
                'name' => $request->name,
                'password' => bcrypt($request->password),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sekolah berhasil ditambahkan',
                'data' => $school
            ], 201);

        } catch (\Exception $e) {
            Log::error('School store JSON error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan sekolah: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(School $school)
    {
        try {
            // Load students with their choices and major details
            $school->load([
                'students' => function($query) {
                    $query->orderBy('created_at', 'desc');
                },
                'students.studentChoice.majorRecommendation'
            ]);
            
            // Get students count - use fresh query to ensure accurate count
            $studentsCount = $school->students()->count();
            
            // Get students with choices count
            $studentsWithChoices = $school->students()->whereHas('studentChoice')->count();
            
            // Get students without choices count
            $studentsWithoutChoices = $studentsCount - $studentsWithChoices;
            
            // Debug data
            Log::info('School Detail - School ID: ' . $school->id);
            Log::info('School Detail - Students count: ' . $studentsCount);
            Log::info('School Detail - Students with choices: ' . $studentsWithChoices);
            Log::info('School Detail - Students without choices: ' . $studentsWithoutChoices);
            Log::info('School Detail - Students data: ', $school->students->toArray());
            
            // Additional debug for Inertia data
            $inertiaData = [
                'title' => 'Detail Sekolah',
                'school' => $school,
                'studentsCount' => $studentsCount,
                'studentsWithChoices' => $studentsWithChoices,
                'studentsWithoutChoices' => $studentsWithoutChoices,
            ];
            
            Log::info('School Detail - Inertia data being sent:', $inertiaData);
            
            return Inertia::render('SuperAdmin/SchoolDetail', $inertiaData);
        } catch (\Exception $e) {
            Log::error('Error fetching school detail: ' . $e->getMessage());
            Log::error('SchoolController::show - Stack trace: ' . $e->getTraceAsString());
            
            return Inertia::render('SuperAdmin/SchoolDetail', [
                'title' => 'Detail Sekolah',
                'school' => $school,
                'studentsCount' => 0,
                'studentsWithChoices' => 0,
                'studentsWithoutChoices' => 0,
                'error' => 'Gagal memuat detail sekolah: ' . $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, School $school)
    {
        $validator = Validator::make($request->all(), [
            'npsn' => 'required|string|unique:schools,npsn,' . $school->id,
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $updateData = [
                'npsn' => $request->npsn,
                'name' => $request->name,
            ];

            if ($request->password) {
                $updateData['password'] = bcrypt($request->password);
            }

            $school->update($updateData);

            return redirect()->back()->with('success', 'Sekolah berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error updating school: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal memperbarui sekolah'])->withInput();
        }
    }

    public function destroy(School $school)
    {
        try {
            $school->delete();
            return redirect()->back()->with('success', 'Sekolah berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting school: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal menghapus sekolah']);
        }
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $file = $request->file('file');
            $csvData = array_map('str_getcsv', file($file->getRealPath()));
            $header = array_shift($csvData);

            $imported = 0;
            $errors = [];

            foreach ($csvData as $row) {
                if (count($row) >= 3) {
                    try {
                        School::create([
                            'npsn' => $row[0],
                            'name' => $row[1],
                            'password' => bcrypt($row[2]),
                        ]);
                        $imported++;
                    } catch (\Exception $e) {
                        $errors[] = "Baris " . ($imported + count($errors) + 1) . ": " . $e->getMessage();
                    }
                }
            }

            if ($imported > 0) {
                return redirect()->back()->with('success', "Berhasil mengimport {$imported} sekolah");
            } else {
                return back()->withErrors(['error' => 'Tidak ada data yang berhasil diimport']);
            }
        } catch (\Exception $e) {
            Log::error('Error importing schools: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal mengimport sekolah: ' . $e->getMessage()]);
        }
    }
}
