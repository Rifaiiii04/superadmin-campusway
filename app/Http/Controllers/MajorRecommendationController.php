<?php

namespace App\Http\Controllers;

use App\Models\MajorRecommendation;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class MajorRecommendationController extends Controller
{
    public function index()
    {
        try {
            $majors = MajorRecommendation::with(['subjects'])->paginate(10);
            $subjects = Subject::where('is_active', true)->pluck('name');
            
            return Inertia::render('SuperAdmin/MajorRecommendations', [
                'title' => 'Rekomendasi Jurusan',
                'majorRecommendations' => $majors->items(),
                'availableSubjects' => $subjects,
                'pagination' => [
                    'current_page' => $majors->currentPage(),
                    'last_page' => $majors->lastPage(),
                    'per_page' => $majors->perPage(),
                    'total' => $majors->total(),
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching major recommendations: ' . $e->getMessage());
            return Inertia::render('SuperAdmin/MajorRecommendations', [
                'title' => 'Rekomendasi Jurusan',
                'majorRecommendations' => [],
                'availableSubjects' => [],
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 10,
                    'total' => 0,
                ],
                'error' => 'Gagal memuat data rekomendasi jurusan'
            ]);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'major_name' => 'required|string|max:255',
            'rumpun_ilmu' => 'required|string|max:255',
            'description' => 'nullable|string',
            'preferred_subjects' => 'nullable|array',
            'kurikulum_merdeka_subjects' => 'nullable|array',
            'kurikulum_2013_ipa_subjects' => 'nullable|array',
            'kurikulum_2013_ips_subjects' => 'nullable|array',
            'kurikulum_2013_bahasa_subjects' => 'nullable|array',
            'career_prospects' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            MajorRecommendation::create([
                'major_name' => $request->major_name,
                'rumpun_ilmu' => $request->rumpun_ilmu,
                'description' => $request->description,
                'preferred_subjects' => json_encode($request->preferred_subjects ?? []),
                'kurikulum_merdeka_subjects' => json_encode($request->kurikulum_merdeka_subjects ?? []),
                'kurikulum_2013_ipa_subjects' => json_encode($request->kurikulum_2013_ipa_subjects ?? []),
                'kurikulum_2013_ips_subjects' => json_encode($request->kurikulum_2013_ips_subjects ?? []),
                'kurikulum_2013_bahasa_subjects' => json_encode($request->kurikulum_2013_bahasa_subjects ?? []),
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
            'rumpun_ilmu' => 'required|string|max:255',
            'description' => 'nullable|string',
            'preferred_subjects' => 'nullable|array',
            'kurikulum_merdeka_subjects' => 'nullable|array',
            'kurikulum_2013_ipa_subjects' => 'nullable|array',
            'kurikulum_2013_ips_subjects' => 'nullable|array',
            'kurikulum_2013_bahasa_subjects' => 'nullable|array',
            'career_prospects' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $majorRecommendation->update([
                'major_name' => $request->major_name,
                'rumpun_ilmu' => $request->rumpun_ilmu,
                'description' => $request->description,
                'preferred_subjects' => json_encode($request->preferred_subjects ?? []),
                'kurikulum_merdeka_subjects' => json_encode($request->kurikulum_merdeka_subjects ?? []),
                'kurikulum_2013_ipa_subjects' => json_encode($request->kurikulum_2013_ipa_subjects ?? []),
                'kurikulum_2013_ips_subjects' => json_encode($request->kurikulum_2013_ips_subjects ?? []),
                'kurikulum_2013_bahasa_subjects' => json_encode($request->kurikulum_2013_bahasa_subjects ?? []),
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
                'Rumpun Ilmu',
                'Deskripsi',
                'Mata Pelajaran Pilihan',
                'Kurikulum Merdeka',
                'Kurikulum 2013 IPA',
                'Kurikulum 2013 IPS',
                'Kurikulum 2013 Bahasa',
                'Prospek Karir',
                'Status'
            ];

            foreach ($majors as $major) {
                $csvData[] = [
                    $major->id,
                    $major->major_name,
                    $major->rumpun_ilmu,
                    $major->description,
                    implode(';', json_decode($major->preferred_subjects ?? '[]')),
                    implode(';', json_decode($major->kurikulum_merdeka_subjects ?? '[]')),
                    implode(';', json_decode($major->kurikulum_2013_ipa_subjects ?? '[]')),
                    implode(';', json_decode($major->kurikulum_2013_ips_subjects ?? '[]')),
                    implode(';', json_decode($major->kurikulum_2013_bahasa_subjects ?? '[]')),
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
