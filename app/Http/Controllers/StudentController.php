<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function index()
    {
        try {
            $students = Student::with(['school'])->paginate(10);
            $schools = School::select('id', 'name')->get();
            
            return Inertia::render('SuperAdmin/Students', [
                'title' => 'Manajemen Siswa',
                'students' => $students,
                'schools' => $schools,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching students: ' . $e->getMessage());
            return Inertia::render('SuperAdmin/Students', [
                'title' => 'Manajemen Siswa',
                'students' => [
                    'data' => [],
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 10,
                    'total' => 0,
                ],
                'schools' => [],
                'error' => 'Gagal memuat data siswa'
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
            'nisn' => 'required|string|max:10|unique:students,nisn',
            'name' => 'required|string|max:255',
            'school_id' => 'required|exists:schools,id',
            'kelas' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
            'status' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            Student::create([
                'nisn' => $request->nisn,
                'name' => $request->name,
                'school_id' => $request->school_id,
                'kelas' => $request->kelas,
                'email' => $request->email,
                'phone' => $request->phone,
                'parent_phone' => $request->parent_phone,
                'password' => $request->password ? bcrypt($request->password) : null,
                'status' => $request->status,
            ]);

            return redirect()->back()->with('success', 'Siswa berhasil ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error creating student: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal menambahkan siswa'])->withInput();
        }
    }

    /**
     * Store a newly created student (JSON response)
     */
    private function storeJson(Request $request)
    {
        try {
            Log::info('Student Store JSON Request Data:', $request->all());
            
            $validator = Validator::make($request->all(), [
                'nisn' => 'required|string|max:10|unique:students,nisn',
                'name' => 'required|string|max:255',
                'school_id' => 'required|exists:schools,id',
                'kelas' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:255',
                'parent_phone' => 'nullable|string|max:20',
                'password' => 'nullable|string|min:6',
                'status' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $student = Student::create([
                'nisn' => $request->nisn,
                'name' => $request->name,
                'school_id' => $request->school_id,
                'kelas' => $request->kelas,
                'email' => $request->email,
                'phone' => $request->phone,
                'parent_phone' => $request->parent_phone,
                'password' => $request->password ? bcrypt($request->password) : null,
                'status' => $request->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Siswa berhasil ditambahkan',
                'data' => $student
            ], 201);

        } catch (\Exception $e) {
            Log::error('Student store JSON error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan siswa: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Student $student)
    {
        $student->load(['school', 'studentSubjects', 'results', 'recommendations']);
        
        return Inertia::render('SuperAdmin/StudentDetail', [
            'title' => 'Detail Siswa',
            'student' => $student,
        ]);
    }

    public function update(Request $request, Student $student)
    {
        $validator = Validator::make($request->all(), [
            'nisn' => 'required|string|max:10|unique:students,nisn,' . $student->id,
            'name' => 'required|string|max:255',
            'school_id' => 'required|exists:schools,id',
            'kelas' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
            'status' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $updateData = [
                'nisn' => $request->nisn,
                'name' => $request->name,
                'school_id' => $request->school_id,
                'kelas' => $request->kelas,
                'email' => $request->email,
                'phone' => $request->phone,
                'parent_phone' => $request->parent_phone,
                'status' => $request->status,
            ];

            if ($request->password) {
                $updateData['password'] = bcrypt($request->password);
            }

            $student->update($updateData);

            return redirect()->back()->with('success', 'Siswa berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error updating student: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal memperbarui siswa'])->withInput();
        }
    }

    public function destroy(Student $student)
    {
        try {
            $student->delete();
            return redirect()->back()->with('success', 'Siswa berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting student: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal menghapus siswa']);
        }
    }

    public function export()
    {
        try {
            $students = Student::with(['school'])->get();
            
            $csvData = [];
            $csvData[] = [
                'ID',
                'NISN',
                'Nama',
                'Sekolah',
                'Kelas',
                'Email',
                'Phone',
                'Parent Phone',
                'Status',
                'Tanggal Dibuat'
            ];

            foreach ($students as $student) {
                $csvData[] = [
                    $student->id,
                    $student->nisn,
                    $student->name,
                    $student->school->name ?? '',
                    $student->kelas,
                    $student->email ?? '',
                    $student->phone ?? '',
                    $student->parent_phone ?? '',
                    $student->status,
                    $student->created_at->format('Y-m-d H:i:s')
                ];
            }

            $filename = 'students_' . date('Y-m-d_H-i-s') . '.csv';
            
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
            Log::error('Error exporting students: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal mengexport data siswa']);
        }
    }
}
