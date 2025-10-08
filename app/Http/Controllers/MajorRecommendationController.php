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
            // Test database connection first
            $majorsCount = MajorRecommendation::count();
            \Log::info('MajorRecommendations count from database: ' . $majorsCount);
            
            if ($majorsCount == 0) {
                \Log::warning('No major recommendations found in database');
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
                    'error' => 'Tidak ada data rekomendasi jurusan di database'
                ]);
            }
            
            // Get all major recommendations
            $allMajors = MajorRecommendation::orderBy('category')
                ->orderBy('major_name')
                ->get();
            
            // Create simple pagination
            $perPage = 10;
            $currentPage = request()->get('page', 1);
            $offset = ($currentPage - 1) * $perPage;
            $items = $allMajors->slice($offset, $perPage)->values();
            
            $majors = new \Illuminate\Pagination\LengthAwarePaginator(
                $items,
                $allMajors->count(),
                $perPage,
                $currentPage,
                [
                    'path' => request()->url(),
                    'pageName' => 'page',
                ]
            );
            
            // Get subjects and rumpun ilmu
            $subjects = Subject::select('id', 'name', 'code', 'subject_type')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
            
            $rumpunIlmu = RumpunIlmu::select('id', 'name')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
            
            // Log for debugging
            \Log::info('MajorRecommendations Controller Debug:', [
                'all_majors_count' => $allMajors->count(),
                'majors_total' => $majors->total(),
                'majors_count' => $majors->count(),
                'current_page' => $majors->currentPage(),
                'first_item' => $majors->first() ? $majors->first()->major_name : 'No items',
                'subjects_count' => $subjects->count(),
                'rumpun_ilmu_count' => $rumpunIlmu->count()
            ]);
            
            return Inertia::render('SuperAdmin/MajorRecommendations', [
                'title' => 'Rekomendasi Jurusan',
                'majorRecommendations' => $majors,
                'availableSubjects' => $subjects,
                'rumpunIlmu' => $rumpunIlmu,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching major recommendations: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
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
                'error' => 'Gagal memuat data rekomendasi jurusan: ' . $e->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
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

            return redirect()->back()->with('success', 'Rekomendasi jurusan berhasil ditambahkan');
        } catch (\Exception $e) {
            \Log::error('Error creating major recommendation: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal menambahkan rekomendasi jurusan'])->withInput();
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

            return redirect()->back()->with('success', 'Rekomendasi jurusan berhasil diperbarui');
        } catch (\Exception $e) {
            \Log::error('Error updating major recommendation: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal memperbarui rekomendasi jurusan'])->withInput();
        }
    }

    public function destroy(MajorRecommendation $majorRecommendation)
    {
        try {
            $majorRecommendation->delete();
            return redirect()->back()->with('success', 'Rekomendasi jurusan berhasil dihapus');
        } catch (\Exception $e) {
            \Log::error('Error deleting major recommendation: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal menghapus rekomendasi jurusan']);
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
            \Log::error('Error toggling major recommendation: ' . $e->getMessage());
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
            \Log::error('Error exporting major recommendations: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal mengexport data rekomendasi jurusan']);
        }
    }
}
