<?php

namespace App\Http\Controllers;

use App\Models\MajorRecommendation;
use App\Models\Subject;
use App\Models\RumpunIlmu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class MajorRecommendationController extends Controller
{
    public function index()
    {
        try {
            // Debug: Check if there are any major recommendations in database
            $totalMajors = MajorRecommendation::count();
            Log::info('MajorRecommendationController::index - Total major recommendations in database: ' . $totalMajors);
            
            $majors = MajorRecommendation::orderBy('rumpun_ilmu')
                ->orderBy('major_name')
                ->paginate(10);
            
            // Debug: Log pagination data
            Log::info('MajorRecommendationController::index - Pagination data:', [
                'total' => $majors->total(),
                'per_page' => $majors->perPage(),
                'current_page' => $majors->currentPage(),
                'data_count' => $majors->count()
            ]);
            
            $subjects = Subject::select('id', 'name', 'code', 'subject_type')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
            
            // Debug: Log subjects data
            Log::info('MajorRecommendationController::index - Subjects count: ' . $subjects->count());
            
            $rumpunIlmu = RumpunIlmu::select('id', 'name')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
            
            // Debug: Log rumpun ilmu data
            Log::info('MajorRecommendationController::index - Rumpun ilmu count: ' . $rumpunIlmu->count());
            
            return Inertia::render('SuperAdmin/MajorRecommendations', [
                'title' => 'Rekomendasi Jurusan',
                'majorRecommendations' => $majors,
                'availableSubjects' => $subjects,
                'rumpunIlmu' => $rumpunIlmu,
                'debug' => [
                    'total_majors' => $totalMajors,
                    'pagination_total' => $majors->total(),
                    'current_page' => $majors->currentPage(),
                    'per_page' => $majors->perPage(),
                    'subjects_count' => $subjects->count(),
                    'rumpun_ilmu_count' => $rumpunIlmu->count()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('MajorRecommendationController::index - Error: ' . $e->getMessage());
            Log::error('MajorRecommendationController::index - Stack trace: ' . $e->getTraceAsString());
            
            return Inertia::render('SuperAdmin/MajorRecommendations', [
                'title' => 'Rekomendasi Jurusan',
                'majorRecommendations' => [
                    'data' => [],
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 10,
                    'total' => 0,
                ],
                'availableSubjects' => [],
                'rumpunIlmu' => [],
                'error' => 'Gagal memuat data rekomendasi jurusan: ' . $e->getMessage(),
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
        // Always return JSON response for AJAX requests
        if ($request->ajax() || $request->expectsJson() || $request->is('api/*') || $request->header('Accept') === 'application/json' || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return $this->storeJson($request);
        }

        $validator = Validator::make($request->all(), [
            'major_name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'rumpun_ilmu' => 'required|string|max:255',
            'description' => 'nullable|string',
            'required_subjects' => 'nullable|array',
            'preferred_subjects' => 'nullable|array',
            'kurikulum_merdeka_subjects' => 'nullable|array',
            'kurikulum_2013_ipa_subjects' => 'nullable|array',
            'kurikulum_2013_ips_subjects' => 'nullable|array',
            'kurikulum_2013_bahasa_subjects' => 'nullable|array',
            'optional_subjects' => 'nullable|array',
            'career_prospects' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            MajorRecommendation::create([
                'major_name' => $request->major_name,
                'category' => 'S1', // Default category
                'rumpun_ilmu' => $request->rumpun_ilmu,
                'description' => $request->description,
                'required_subjects' => $request->required_subjects ?? [],
                'preferred_subjects' => $request->preferred_subjects ?? [],
                'kurikulum_merdeka_subjects' => $request->kurikulum_merdeka_subjects ?? [],
                'kurikulum_2013_ipa_subjects' => $request->kurikulum_2013_ipa_subjects ?? [],
                'kurikulum_2013_ips_subjects' => $request->kurikulum_2013_ips_subjects ?? [],
                'kurikulum_2013_bahasa_subjects' => $request->kurikulum_2013_bahasa_subjects ?? [],
                'optional_subjects' => $request->optional_subjects ?? [],
                'career_prospects' => $request->career_prospects,
                'is_active' => $request->is_active ?? true,
            ]);

            return redirect()->back()->with('success', 'Rekomendasi jurusan berhasil ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error creating major recommendation: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal menambahkan rekomendasi jurusan'])->withInput();
        }
    }

    /**
     * Store a newly created major recommendation (JSON response)
     */
    private function storeJson(Request $request)
    {
        try {
            Log::info('Major Recommendation Store JSON Request Data:', $request->all());
            
            $validator = Validator::make($request->all(), [
                'major_name' => 'required|string|max:255',
                'rumpun_ilmu' => 'required|string|max:255',
                'description' => 'nullable|string',
                'required_subjects' => 'nullable|array',
                'preferred_subjects' => 'nullable|array',
                'kurikulum_merdeka_subjects' => 'nullable|array',
                'kurikulum_2013_ipa_subjects' => 'nullable|array',
                'kurikulum_2013_ips_subjects' => 'nullable|array',
                'kurikulum_2013_bahasa_subjects' => 'nullable|array',
                'optional_subjects' => 'nullable|array',
                'career_prospects' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $major = MajorRecommendation::create([
                'major_name' => $request->major_name,
                'category' => $request->category,
                'rumpun_ilmu' => $request->rumpun_ilmu,
                'description' => $request->description,
                'required_subjects' => $request->required_subjects ?? [],
                'preferred_subjects' => $request->preferred_subjects ?? [],
                'kurikulum_merdeka_subjects' => $request->kurikulum_merdeka_subjects ?? [],
                'kurikulum_2013_ipa_subjects' => $request->kurikulum_2013_ipa_subjects ?? [],
                'kurikulum_2013_ips_subjects' => $request->kurikulum_2013_ips_subjects ?? [],
                'kurikulum_2013_bahasa_subjects' => $request->kurikulum_2013_bahasa_subjects ?? [],
                'optional_subjects' => $request->optional_subjects ?? [],
                'career_prospects' => $request->career_prospects,
                'is_active' => $request->is_active ?? true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Rekomendasi jurusan berhasil ditambahkan',
                'data' => $major
            ], 201);

        } catch (\Exception $e) {
            Log::error('Major Recommendation store JSON error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan rekomendasi jurusan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(MajorRecommendation $majorRecommendation)
    {
        return Inertia::render('SuperAdmin/MajorRecommendationDetail', [
            'title' => 'Detail Rekomendasi Jurusan',
            'majorRecommendation' => $majorRecommendation,
        ]);
    }

    public function update(Request $request, MajorRecommendation $majorRecommendation)
    {
        // Always return JSON response for AJAX requests
        if ($request->ajax() || $request->expectsJson() || $request->is('api/*') || $request->header('Accept') === 'application/json' || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return $this->updateJson($request, $majorRecommendation);
        }

        $validator = Validator::make($request->all(), [
            'major_name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'rumpun_ilmu' => 'required|string|max:255',
            'description' => 'nullable|string',
            'required_subjects' => 'nullable|array',
            'preferred_subjects' => 'nullable|array',
            'kurikulum_merdeka_subjects' => 'nullable|array',
            'kurikulum_2013_ipa_subjects' => 'nullable|array',
            'kurikulum_2013_ips_subjects' => 'nullable|array',
            'kurikulum_2013_bahasa_subjects' => 'nullable|array',
            'optional_subjects' => 'nullable|array',
            'career_prospects' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $majorRecommendation->update([
                'major_name' => $request->major_name,
                'category' => 'S1', // Default category
                'rumpun_ilmu' => $request->rumpun_ilmu,
                'description' => $request->description,
                'required_subjects' => $request->required_subjects ?? [],
                'preferred_subjects' => $request->preferred_subjects ?? [],
                'kurikulum_merdeka_subjects' => $request->kurikulum_merdeka_subjects ?? [],
                'kurikulum_2013_ipa_subjects' => $request->kurikulum_2013_ipa_subjects ?? [],
                'kurikulum_2013_ips_subjects' => $request->kurikulum_2013_ips_subjects ?? [],
                'kurikulum_2013_bahasa_subjects' => $request->kurikulum_2013_bahasa_subjects ?? [],
                'optional_subjects' => $request->optional_subjects ?? [],
                'career_prospects' => $request->career_prospects,
                'is_active' => $request->is_active ?? true,
            ]);

            return redirect()->back()->with('success', 'Rekomendasi jurusan berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error updating major recommendation: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal memperbarui rekomendasi jurusan'])->withInput();
        }
    }

    /**
     * Update the specified major recommendation (JSON response)
     */
    private function updateJson(Request $request, MajorRecommendation $majorRecommendation)
    {
        try {
            Log::info('Major Recommendation Update JSON Request Data:', $request->all());
            
            $validator = Validator::make($request->all(), [
                'major_name' => 'required|string|max:255',
                'rumpun_ilmu' => 'required|string|max:255',
                'description' => 'nullable|string',
                'required_subjects' => 'nullable|array',
                'preferred_subjects' => 'nullable|array',
                'kurikulum_merdeka_subjects' => 'nullable|array',
                'kurikulum_2013_ipa_subjects' => 'nullable|array',
                'kurikulum_2013_ips_subjects' => 'nullable|array',
                'kurikulum_2013_bahasa_subjects' => 'nullable|array',
                'optional_subjects' => 'nullable|array',
                'career_prospects' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $majorRecommendation->update([
                'major_name' => $request->major_name,
                'category' => 'S1', // Default category
                'rumpun_ilmu' => $request->rumpun_ilmu,
                'description' => $request->description,
                'required_subjects' => $request->required_subjects ?? [],
                'preferred_subjects' => $request->preferred_subjects ?? [],
                'kurikulum_merdeka_subjects' => $request->kurikulum_merdeka_subjects ?? [],
                'kurikulum_2013_ipa_subjects' => $request->kurikulum_2013_ipa_subjects ?? [],
                'kurikulum_2013_ips_subjects' => $request->kurikulum_2013_ips_subjects ?? [],
                'kurikulum_2013_bahasa_subjects' => $request->kurikulum_2013_bahasa_subjects ?? [],
                'optional_subjects' => $request->optional_subjects ?? [],
                'career_prospects' => $request->career_prospects,
                'is_active' => $request->is_active ?? true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Rekomendasi jurusan berhasil diperbarui',
                'data' => $majorRecommendation
            ]);

        } catch (\Exception $e) {
            Log::error('Major Recommendation update JSON error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui rekomendasi jurusan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, MajorRecommendation $majorRecommendation)
    {
        // Always return JSON response for AJAX requests
        if ($request->ajax() || $request->expectsJson() || $request->is('api/*') || $request->header('Accept') === 'application/json' || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return $this->destroyJson($majorRecommendation);
        }

        try {
            $majorRecommendation->delete();
            return redirect()->back()->with('success', 'Rekomendasi jurusan berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting major recommendation: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal menghapus rekomendasi jurusan']);
        }
    }

    /**
     * Remove the specified major recommendation (JSON response)
     */
    private function destroyJson(MajorRecommendation $majorRecommendation)
    {
        try {
            Log::info('Major Recommendation Destroy JSON Request for ID:', $majorRecommendation->id);
            
            $majorRecommendation->delete();

            return response()->json([
                'success' => true,
                'message' => 'Rekomendasi jurusan berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            Log::error('Major Recommendation destroy JSON error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus rekomendasi jurusan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggle(MajorRecommendation $majorRecommendation)
    {
        try {
            $majorRecommendation->update([
                'is_active' => !$majorRecommendation->is_active
            ]);

            return redirect()->back()->with('success', 'Status rekomendasi jurusan berhasil diubah');
        } catch (\Exception $e) {
            Log::error('Error toggling major recommendation: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal mengubah status rekomendasi jurusan']);
        }
    }

    public function export()
    {
        try {
            $majors = MajorRecommendation::all();
            
            $csvData = [];
            $csvData[] = [
                'ID',
                'Nama Jurusan',
                'Kategori',
                'Rumpun Ilmu',
                'Deskripsi',
                'Mata Pelajaran Wajib',
                'Mata Pelajaran Pilihan',
                'Kurikulum Merdeka',
                'Kurikulum 2013 IPA',
                'Kurikulum 2013 IPS',
                'Kurikulum 2013 Bahasa',
                'Mata Pelajaran Opsional',
                'Prospek Karir',
                'Status'
            ];

            foreach ($majors as $major) {
                $csvData[] = [
                    $major->id,
                    $major->major_name,
                    $major->category,
                    $major->rumpun_ilmu,
                    $major->description,
                    implode(';', $major->required_subjects ?? []),
                    implode(';', $major->preferred_subjects ?? []),
                    implode(';', $major->kurikulum_merdeka_subjects ?? []),
                    implode(';', $major->kurikulum_2013_ipa_subjects ?? []),
                    implode(';', $major->kurikulum_2013_ips_subjects ?? []),
                    implode(';', $major->kurikulum_2013_bahasa_subjects ?? []),
                    implode(';', $major->optional_subjects ?? []),
                    $major->career_prospects,
                    $major->is_active ? 'Aktif' : 'Non-Aktif'
                ];
            }

            $filename = 'major_recommendations_' . date('Y-m-d_H-i-s') . '.csv';
            
            $callback = function() use ($csvData) {
                $file = fopen('php://output', 'w');
                foreach ($csvData as $row) {
                    fputcsv($file, $row);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (\Exception $e) {
            Log::error('Error exporting major recommendations: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal mengexport data rekomendasi jurusan']);
        }
    }
}
