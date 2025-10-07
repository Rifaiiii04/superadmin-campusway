<?php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ResultController extends Controller
{
    public function index()
    {
        try {
            $results = Result::with(['student'])->paginate(10);
            
            return Inertia::render('SuperAdmin/Results', [
                'title' => 'Hasil Tes',
                'results' => $results,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching results: ' . $e->getMessage());
            return Inertia::render('SuperAdmin/Results', [
                'title' => 'Hasil Tes',
                'results' => [
                    'data' => [],
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 10,
                    'total' => 0,
                ],
                'error' => 'Gagal memuat data hasil tes'
            ]);
        }
    }

    public function show(Result $result)
    {
        $result->load(['student']);
        
        return Inertia::render('SuperAdmin/ResultDetail', [
            'title' => 'Detail Hasil Tes',
            'result' => $result,
        ]);
    }

    public function destroy(Result $result)
    {
        try {
            $result->delete();
            return redirect()->back()->with('success', 'Hasil tes berhasil dihapus');
        } catch (\Exception $e) {
            \Log::error('Error deleting result: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal menghapus hasil tes']);
        }
    }

    public function export()
    {
        try {
            $results = Result::with(['student'])->get();
            
            $csvData = [];
            $csvData[] = [
                'ID',
                'NISN',
                'Nama Siswa',
                'Sekolah',
                'Mata Pelajaran',
                'Skor',
                'Tanggal Tes'
            ];

            foreach ($results as $result) {
                $csvData[] = [
                    $result->id,
                    $result->student->nisn ?? '',
                    $result->student->name ?? '',
                    $result->student->school->name ?? '',
                    $result->subject,
                    $result->score,
                    $result->created_at->format('Y-m-d H:i:s')
                ];
            }

            $filename = 'test_results_' . date('Y-m-d_H-i-s') . '.csv';
            
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
            \Log::error('Error exporting results: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal mengexport data hasil tes']);
        }
    }
}
