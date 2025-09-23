<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentChoice;
use App\Models\MajorRecommendation;
use Illuminate\Support\Facades\Log;

class SchoolDashboardController extends Controller
{
    /**
     * Get dashboard overview data untuk sekolah
     */
    public function dashboard(Request $request)
    {
        try {
            $school = School::find($request->school_id);

            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sekolah tidak ditemukan'
                ], 404);
            }

            // Hitung total siswa
            $totalStudents = Student::where('school_id', $school->id)->count();

            // Hitung siswa yang sudah memilih jurusan
            $studentsWithChoice = StudentChoice::whereHas('student', function($query) use ($school) {
                $query->where('school_id', $school->id);
            })->count();

            // Hitung siswa yang belum memilih jurusan
            $studentsWithoutChoice = $totalStudents - $studentsWithChoice;

            // Get top 5 jurusan yang paling diminati
            $topMajors = StudentChoice::whereHas('student', function($query) use ($school) {
                $query->where('school_id', $school->id);
            })
            ->with('major')
            ->selectRaw('major_id, COUNT(*) as student_count')
            ->groupBy('major_id')
            ->orderBy('student_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function($choice) {
                return [
                    'major_id' => $choice->major_id,
                    'major_name' => $choice->major->major_name ?? 'Jurusan Tidak Ditemukan',
                    'category' => $choice->major->category ?? 'Saintek',
                    'student_count' => $choice->student_count
                ];
            });

            // Get data siswa per kelas (jika ada field kelas)
            $studentsByClass = Student::where('school_id', $school->id)
                ->selectRaw('kelas, COUNT(*) as student_count')
                ->groupBy('kelas')
                ->orderBy('kelas')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'school' => [
                        'id' => $school->id,
                        'npsn' => $school->npsn,
                        'name' => $school->name
                    ],
                    'statistics' => [
                        'total_students' => $totalStudents,
                        'students_with_choice' => $studentsWithChoice,
                        'students_without_choice' => $studentsWithoutChoice,
                        'completion_percentage' => $totalStudents > 0 ? round(($studentsWithChoice / $totalStudents) * 100, 2) : 0
                    ],
                    'top_majors' => $topMajors,
                    'students_by_class' => $studentsByClass
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get school dashboard error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Get daftar semua siswa sekolah
     */
    public function students(Request $request)
    {
        try {
            $school = School::find($request->school_id);

            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sekolah tidak ditemukan'
                ], 404);
            }

            $students = Student::where('school_id', $school->id)
                ->with(['studentChoice.major'])
                ->orderBy('name')
                ->get()
                ->map(function($student) {
                    return [
                        'id' => $student->id,
                        'nisn' => $student->nisn,
                        'name' => $student->name,
                        'class' => $student->kelas,
                        'email' => $student->email,
                        'phone' => $student->phone,
                        'parent_phone' => $student->parent_phone,
                        'has_choice' => $student->studentChoice ? true : false,
                        'chosen_major' => $student->studentChoice ? $this->getMajorWithSubjects($student->studentChoice->major) : null,
                        'choice_date' => $student->studentChoice ? $student->studentChoice->created_at : null
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'school' => [
                        'id' => $school->id,
                        'npsn' => $school->npsn,
                        'name' => $school->name
                    ],
                    'students' => $students,
                    'total_students' => $students->count()
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get school students error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Get detail siswa tertentu
     */
    public function studentDetail(Request $request, $studentId)
    {
        try {
            $school = School::find($request->school_id);

            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sekolah tidak ditemukan'
                ], 404);
            }

            $student = Student::where('id', $studentId)
                ->where('school_id', $school->id)
                ->with(['studentChoice.major'])
                ->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa tidak ditemukan'
                ], 404);
            }

            $studentData = [
                'id' => $student->id,
                'nisn' => $student->nisn,
                'name' => $student->name,
                'class' => $student->kelas,
                'email' => $student->email,
                'phone' => $student->phone,
                'parent_phone' => $student->parent_phone,
                'created_at' => $student->created_at,
                'updated_at' => $student->updated_at,
                'has_choice' => $student->studentChoice ? true : false
            ];

            if ($student->studentChoice) {
                $majorData = $this->getMajorWithSubjects($student->studentChoice->major);
                $majorData['choice_date'] = $student->studentChoice->created_at;
                $studentData['chosen_major'] = $majorData;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'school' => [
                        'id' => $school->id,
                        'npsn' => $school->npsn,
                        'name' => $school->name
                    ],
                    'student' => $studentData
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get student detail error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Get major with subjects from database mapping
     */
    private function getMajorWithSubjects($major)
    {
        $educationLevel = $this->determineEducationLevel($major->rumpun_ilmu);
        
        // Get subjects from database mapping
        $mappings = \App\Models\MajorSubjectMapping::where('major_id', $major->id)
            ->with('subject')
            ->get();
        
        $mandatorySubjects = $mappings->where('mapping_type', 'wajib')
            ->pluck('subject.name')
            ->toArray();
            
        $optionalSubjects = $mappings->where('mapping_type', 'pilihan')
            ->pluck('subject.name')
            ->toArray();
        
        return [
            'id' => $major->id,
            'name' => $major->major_name,
            'description' => $major->description,
            'career_prospects' => $major->career_prospects,
            'category' => $major->rumpun_ilmu ?? 'Saintek',
            'education_level' => $educationLevel,
            'required_subjects' => $mandatorySubjects,
            'preferred_subjects' => $optionalSubjects,
            'kurikulum_merdeka_subjects' => $major->kurikulum_merdeka_subjects ?? [],
            'kurikulum_2013_ipa_subjects' => $major->kurikulum_2013_ipa_subjects ?? [],
            'kurikulum_2013_ips_subjects' => $major->kurikulum_2013_ips_subjects ?? [],
            'kurikulum_2013_bahasa_subjects' => $major->kurikulum_2013_bahasa_subjects ?? []
        ];
    }

    /**
     * Determine education level based on rumpun ilmu
     */
    private function determineEducationLevel($rumpunIlmu)
    {
        $smaRumpun = ['ILMU ALAM', 'ILMU SOSIAL', 'ILMU BUDAYA', 'HUMANIORA', 'ILMU FORMAL'];
        
        if (in_array($rumpunIlmu, $smaRumpun)) {
            return 'SMA/MA';
        } else {
            return 'SMK/MAK';
        }
    }

    /**
     * Get statistik jurusan yang diminati siswa
     */
    public function majorStatistics(Request $request)
    {
        try {
            $school = School::find($request->school_id);

            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sekolah tidak ditemukan'
                ], 404);
            }

            // Get semua jurusan yang dipilih siswa dari sekolah ini
            $majorStats = StudentChoice::whereHas('student', function($query) use ($school) {
                $query->where('school_id', $school->id);
            })
            ->with('major')
            ->selectRaw('major_id, COUNT(*) as student_count')
            ->groupBy('major_id')
            ->orderBy('student_count', 'desc')
            ->get()
            ->map(function($choice) {
                return [
                    'major_id' => $choice->major_id,
                    'major_name' => $choice->major->major_name ?? 'Jurusan Tidak Ditemukan',
                    'description' => $choice->major->description ?? '',
                    'category' => $choice->major->category ?? 'Saintek',
                    'student_count' => $choice->student_count,
                    'percentage' => 0 // Akan dihitung setelah semua data terkumpul
                ];
            });

            // Hitung total siswa yang sudah memilih
            $totalStudentsWithChoice = $majorStats->sum('student_count');

            // Hitung persentase untuk setiap jurusan
            $majorStats = $majorStats->map(function($major) use ($totalStudentsWithChoice) {
                $major['percentage'] = $totalStudentsWithChoice > 0 ? 
                    round(($major['student_count'] / $totalStudentsWithChoice) * 100, 2) : 0;
                return $major;
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'school' => [
                        'id' => $school->id,
                        'npsn' => $school->npsn,
                        'name' => $school->name
                    ],
                    'total_students_with_choice' => $totalStudentsWithChoice,
                    'major_statistics' => $majorStats
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get major statistics error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Tambah siswa baru
     */
    public function addStudent(Request $request)
    {
        try {
            $request->validate([
                'nisn' => 'required|string|size:10|unique:students,nisn',
                'name' => 'required|string|max:255',
                'kelas' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'parent_phone' => 'nullable|string|max:20',
                'password' => 'required|string|min:6',
                'school_id' => 'required|exists:schools,id'
            ]);

            $school = School::find($request->school_id);
            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sekolah tidak ditemukan'
                ], 404);
            }

            // Cek apakah NISN sudah ada
            $existingStudent = Student::where('nisn', $request->nisn)->first();
            if ($existingStudent) {
                return response()->json([
                    'success' => false,
                    'message' => 'NISN sudah terdaftar'
                ], 400);
            }

            // Buat siswa baru
            $student = Student::create([
                'nisn' => $request->nisn,
                'name' => $request->name,
                'school_id' => $request->school_id,
                'kelas' => $request->kelas,
                'email' => $request->email,
                'phone' => $request->phone,
                'parent_phone' => $request->parent_phone,
                'password' => bcrypt($request->password),
                'status' => 'active'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Siswa berhasil ditambahkan',
                'data' => [
                    'student' => [
                        'id' => $student->id,
                        'nisn' => $student->nisn,
                        'name' => $student->name,
                        'kelas' => $student->kelas,
                        'email' => $student->email,
                        'phone' => $student->phone,
                        'parent_phone' => $student->parent_phone,
                        'status' => $student->status,
                        'created_at' => $student->created_at
                    ]
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Add student error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Get daftar siswa yang belum memilih jurusan
     */
    public function studentsWithoutChoice(Request $request)
    {
        try {
            $school = School::find($request->school_id);

            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sekolah tidak ditemukan'
                ], 404);
            }

            $studentsWithoutChoice = Student::where('school_id', $school->id)
                ->whereDoesntHave('studentChoice')
                ->orderBy('name')
                ->get()
                ->map(function($student) {
                    return [
                        'id' => $student->id,
                        'nisn' => $student->nisn,
                        'name' => $student->name,
                        'class' => $student->kelas,
                        'email' => $student->email,
                        'phone' => $student->phone,
                        'parent_phone' => $student->parent_phone
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'school' => [
                        'id' => $school->id,
                        'npsn' => $school->npsn,
                        'name' => $school->name
                    ],
                    'students_without_choice' => $studentsWithoutChoice,
                    'total_count' => $studentsWithoutChoice->count()
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get students without choice error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Export data siswa dengan detail jurusan dan mata pelajaran
     */
    public function exportStudents(Request $request)
    {
        try {
            $school = School::find($request->school_id);

            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sekolah tidak ditemukan'
                ], 404);
            }

            // Get semua siswa dengan detail jurusan dan mata pelajaran
            $students = Student::where('school_id', $school->id)
                ->with(['studentChoice.major'])
                ->orderBy('name')
                ->get()
                ->map(function($student) {
                    $exportData = [
                        'nama_siswa' => $student->name,
                        'nisn' => $student->nisn,
                        'kelas' => $student->kelas,
                        'email' => $student->email,
                        'no_handphone' => $student->phone,
                        'no_orang_tua' => $student->parent_phone,
                        'status_pilihan_jurusan' => $student->studentChoice ? 'Sudah Memilih' : 'Belum Memilih',
                        'tanggal_memilih' => $student->studentChoice ? $student->studentChoice->created_at->format('d/m/Y H:i') : '-'
                    ];

                    if ($student->studentChoice && $student->studentChoice->major) {
                        $major = $student->studentChoice->major;
                        $exportData = array_merge($exportData, [
                            'nama_jurusan' => $major->major_name,
                            'kategori_jurusan' => $major->category ?? 'Saintek',
                            'prospek_karir' => $major->career_prospects ?? '-',
                            'mata_pelajaran_wajib' => $major->required_subjects ? implode(', ', $major->required_subjects) : '-',
                            'mata_pelajaran_diutamakan' => $major->preferred_subjects ? implode(', ', $major->preferred_subjects) : '-',
                            'mata_pelajaran_kurikulum_merdeka' => $major->kurikulum_merdeka_subjects ? implode(', ', $major->kurikulum_merdeka_subjects) : '-',
                            'mata_pelajaran_kurikulum_2013_ipa' => $major->kurikulum_2013_ipa_subjects ? implode(', ', $major->kurikulum_2013_ipa_subjects) : '-',
                            'mata_pelajaran_kurikulum_2013_ips' => $major->kurikulum_2013_ips_subjects ? implode(', ', $major->kurikulum_2013_ips_subjects) : '-',
                            'mata_pelajaran_kurikulum_2013_bahasa' => $major->kurikulum_2013_bahasa_subjects ? implode(', ', $major->kurikulum_2013_bahasa_subjects) : '-'
                        ]);
                    } else {
                        $exportData = array_merge($exportData, [
                            'nama_jurusan' => '-',
                            'kategori_jurusan' => '-',
                            'prospek_karir' => '-',
                            'mata_pelajaran_wajib' => '-',
                            'mata_pelajaran_diutamakan' => '-',
                            'mata_pelajaran_kurikulum_merdeka' => '-',
                            'mata_pelajaran_kurikulum_2013_ipa' => '-',
                            'mata_pelajaran_kurikulum_2013_ips' => '-',
                            'mata_pelajaran_kurikulum_2013_bahasa' => '-'
                        ]);
                    }

                    return $exportData;
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'school' => [
                        'id' => $school->id,
                        'npsn' => $school->npsn,
                        'name' => $school->name
                    ],
                    'export_data' => $students,
                    'total_students' => $students->count(),
                    'students_with_choice' => $students->where('status_pilihan_jurusan', 'Sudah Memilih')->count(),
                    'students_without_choice' => $students->where('status_pilihan_jurusan', 'Belum Memilih')->count(),
                    'export_date' => now()->format('d/m/Y H:i:s')
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Export students error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Update data siswa
     */
    public function updateStudent(Request $request, $studentId)
    {
        try {
            $school = School::find($request->school_id);

            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sekolah tidak ditemukan'
                ], 404);
            }

            // Cari siswa yang akan diupdate
            $student = Student::where('id', $studentId)
                ->where('school_id', $school->id)
                ->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa tidak ditemukan'
                ], 404);
            }

            // Validasi input
            $request->validate([
                'name' => 'required|string|max:255',
                'nisn' => 'required|string|max:10|unique:students,nisn,' . $studentId,
                'kelas' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'parent_phone' => 'nullable|string|max:20',
                'password' => 'nullable|string|min:6'
            ]);

            // Update data siswa
            $updateData = [
                'name' => $request->name,
                'nisn' => $request->nisn,
                'kelas' => $request->kelas,
                'email' => $request->email,
                'phone' => $request->phone,
                'parent_phone' => $request->parent_phone,
            ];

            // Jika ada password baru, hash password
            if ($request->password) {
                $updateData['password'] = bcrypt($request->password);
            }

            $student->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Data siswa berhasil diperbarui',
                'data' => [
                    'id' => $student->id,
                    'nisn' => $student->nisn,
                    'name' => $student->name,
                    'class' => $student->kelas,
                    'email' => $student->email,
                    'phone' => $student->phone,
                    'parent_phone' => $student->parent_phone,
                    'updated_at' => $student->updated_at
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating student: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Delete student
     */
    public function deleteStudent(Request $request, $studentId)
    {
        try {
            $school = School::find($request->school_id);

            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sekolah tidak ditemukan'
                ], 404);
            }

            // Cari siswa yang akan dihapus
            $student = Student::where('id', $studentId)
                ->where('school_id', $school->id)
                ->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa tidak ditemukan'
                ], 404);
            }

            // Hapus pilihan jurusan siswa terlebih dahulu (jika ada)
            StudentChoice::where('student_id', $studentId)->delete();

            // Hapus siswa
            $student->delete();

            return response()->json([
                'success' => true,
                'message' => 'Siswa berhasil dihapus'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error deleting student: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Import data siswa dari CSV/Excel
     */
    public function importStudents(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:csv,xlsx,xls|max:10240', // Max 10MB
                'school_id' => 'required|exists:schools,id'
            ]);

            $school = School::find($request->school_id);
            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sekolah tidak ditemukan'
                ], 404);
            }

            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            
            // Baca file berdasarkan ekstensi
            $data = [];
            if ($extension === 'csv') {
                $data = $this->readCSV($file);
            } elseif (in_array($extension, ['xlsx', 'xls'])) {
                $data = $this->readExcel($file);
            }

            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File kosong atau format tidak valid'
                ], 400);
            }

            // Validasi header kolom - support both old and new format
            $requiredColumns = ['nisn', 'name', 'kelas', 'email', 'phone', 'parent_phone', 'password'];
            $requiredColumnsNew = ['NISN', 'Nama Lengkap', 'Kelas', 'Email', 'No Handphone', 'No Handphone Orang Tua', 'Password'];
            
            $headers = array_keys($data[0]);
            $headersLower = array_map('strtolower', array_map('trim', $headers));
            
            // Check if using new format (with spaces and proper names)
            $isNewFormat = false;
            $missingColumns = [];
            
            if (count(array_intersect($requiredColumnsNew, $headers)) >= 3) {
                // Using new format
                $isNewFormat = true;
                $missingColumns = array_diff($requiredColumnsNew, $headers);
            } else {
                // Using old format
                $missingColumns = array_diff($requiredColumns, $headersLower);
            }
            
            if (!empty($missingColumns)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kolom yang diperlukan tidak ditemukan: ' . implode(', ', $missingColumns)
                ], 400);
            }

            $importedCount = 0;
            $skippedCount = 0;
            $errors = [];

            foreach ($data as $index => $row) {
                try {
                    // Normalize data based on format
                    if ($isNewFormat) {
                        // New format with proper column names
                        $normalizedRow = [
                            'nisn' => $row['NISN'] ?? '',
                            'name' => $row['Nama Lengkap'] ?? '',
                            'kelas' => $row['Kelas'] ?? '',
                            'email' => $row['Email'] ?? '',
                            'phone' => $row['No Handphone'] ?? '',
                            'parent_phone' => $row['No Handphone Orang Tua'] ?? '',
                            'password' => $row['Password'] ?? ''
                        ];
                    } else {
                        // Old format - normalize to lowercase
                        $normalizedRow = [
                            'nisn' => $row['nisn'] ?? $row['NISN'] ?? '',
                            'name' => $row['name'] ?? $row['Nama Lengkap'] ?? '',
                            'kelas' => $row['kelas'] ?? $row['Kelas'] ?? '',
                            'email' => $row['email'] ?? $row['Email'] ?? '',
                            'phone' => $row['phone'] ?? $row['No Handphone'] ?? '',
                            'parent_phone' => $row['parent_phone'] ?? $row['No Handphone Orang Tua'] ?? '',
                            'password' => $row['password'] ?? $row['Password'] ?? ''
                        ];
                    }
                    
                    // Validasi data per baris
                    $rowNumber = $index + 2; // +2 karena header di baris 1 dan array dimulai dari 0
                    
                    if (empty($normalizedRow['nisn']) || empty($normalizedRow['name']) || empty($normalizedRow['kelas'])) {
                        $errors[] = "Baris {$rowNumber}: NISN, nama, dan kelas harus diisi";
                        $skippedCount++;
                        continue;
                    }

                    // Validasi NISN format (10 digit)
                    if (!preg_match('/^\d{10}$/', $normalizedRow['nisn'])) {
                        $errors[] = "Baris {$rowNumber}: NISN harus 10 digit angka";
                        $skippedCount++;
                        continue;
                    }

                    // Validasi email format (jika diisi)
                    if (!empty($normalizedRow['email']) && !filter_var($normalizedRow['email'], FILTER_VALIDATE_EMAIL)) {
                        $errors[] = "Baris {$rowNumber}: Format email tidak valid";
                        $skippedCount++;
                        continue;
                    }

                    // Normalisasi dan validasi phone format (jika diisi)
                    if (!empty($normalizedRow['phone'])) {
                        $phone = $this->normalizePhoneNumber($normalizedRow['phone']);
                        if (!$phone || !preg_match('/^08\d{8,11}$/', $phone)) {
                            $errors[] = "Baris {$rowNumber}: Format nomor handphone tidak valid (contoh: 081234567890). Gunakan format: =\"081234567890\" di Excel";
                            $skippedCount++;
                            continue;
                        }
                        $normalizedRow['phone'] = $phone;
                    }

                    // Normalisasi dan validasi parent phone format (jika diisi)
                    if (!empty($normalizedRow['parent_phone'])) {
                        $parentPhone = $this->normalizePhoneNumber($normalizedRow['parent_phone']);
                        if (!$parentPhone || !preg_match('/^08\d{8,11}$/', $parentPhone)) {
                            $errors[] = "Baris {$rowNumber}: Format nomor handphone orang tua tidak valid (contoh: 081234567890). Gunakan format: =\"081234567890\" di Excel";
                            $skippedCount++;
                            continue;
                        }
                        $normalizedRow['parent_phone'] = $parentPhone;
                    }

                    // Cek apakah NISN sudah ada
                    $existingStudent = Student::where('nisn', $normalizedRow['nisn'])->first();
                    if ($existingStudent) {
                        $errors[] = "Baris {$rowNumber}: NISN {$normalizedRow['nisn']} sudah terdaftar";
                        $skippedCount++;
                        continue;
                    }

                    // Buat siswa baru
                    Student::create([
                        'nisn' => $normalizedRow['nisn'],
                        'name' => $normalizedRow['name'],
                        'school_id' => $request->school_id,
                        'kelas' => $normalizedRow['kelas'],
                        'email' => $normalizedRow['email'] ?: null,
                        'phone' => $normalizedRow['phone'] ?: null,
                        'parent_phone' => $normalizedRow['parent_phone'] ?: null,
                        'password' => bcrypt($normalizedRow['password'] ?: 'password123'), // Default password jika kosong
                        'status' => 'active'
                    ]);

                    $importedCount++;

                } catch (\Exception $e) {
                    $errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                    $skippedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Import selesai. {$importedCount} siswa berhasil diimport, {$skippedCount} siswa dilewati",
                'data' => [
                    'imported_count' => $importedCount,
                    'skipped_count' => $skippedCount,
                    'total_rows' => count($data),
                    'errors' => $errors
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Import students error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Baca file CSV
     */
    private function readCSV($file)
    {
        $data = [];
        $handle = fopen($file->getPathname(), 'r');
        
        if ($handle !== false) {
            // Skip BOM if present
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }
            
            // Coba baca dengan semicolon dulu, jika gagal coba dengan comma
            $headers = fgetcsv($handle, 1000, ';');
            if (count($headers) < 3) {
                // Jika tidak cukup kolom dengan semicolon, coba dengan comma
                rewind($handle);
                $bom = fread($handle, 3);
                if ($bom !== "\xEF\xBB\xBF") {
                    rewind($handle);
                }
                $headers = fgetcsv($handle, 1000, ',');
            }
            
            // Clean headers (remove quotes and trim)
            $headers = array_map(function($header) {
                return trim($header, '"');
            }, $headers);
            
            // Tentukan delimiter berdasarkan jumlah kolom yang ditemukan
            $delimiter = count($headers) >= 3 ? ';' : ',';
            
            // Reset file pointer
            rewind($handle);
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }
            
            // Baca ulang dengan delimiter yang benar
            $headers = fgetcsv($handle, 1000, $delimiter);
            $headers = array_map(function($header) {
                return trim($header, '"');
            }, $headers);
            
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (count($row) === count($headers)) {
                    // Clean row data (remove quotes and trim)
                    $cleanRow = array_map(function($field) {
                        return trim($field, '"');
                    }, $row);
                    
                    $data[] = array_combine($headers, $cleanRow);
                }
            }
            
            fclose($handle);
        }
        
        return $data;
    }

    /**
     * Baca file Excel
     */
    private function readExcel($file)
    {
        try {
            $data = [];
            
            // Load PhpSpreadsheet
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getPathname());
            $spreadsheet = $reader->load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            
            // Get all rows
            $rows = $worksheet->toArray();
            
            if (empty($rows)) {
                return $data;
            }
            
            // First row as headers - keep original case for new format
            $headers = array_map('trim', $rows[0]);
            
            // Process data rows
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }
                
                // Combine headers with row data
                $rowData = [];
                for ($j = 0; $j < count($headers); $j++) {
                    $rowData[$headers[$j]] = isset($row[$j]) ? trim($row[$j]) : '';
                }
                
                $data[] = $rowData;
            }
            
            return $data;
            
        } catch (\Exception $e) {
            Log::error('Excel read error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Download template CSV untuk import data siswa
     */
    public function downloadImportTemplate()
    {
        try {
            // Header CSV dengan nama yang lebih jelas
            $headers = [
                'NISN',
                'Nama Lengkap', 
                'Kelas',
                'Email',
                'No Handphone (Format: ="081234567890")',
                'No Handphone Orang Tua (Format: ="081234567890")',
                'Password'
            ];

            // Data contoh yang lebih lengkap dengan format yang Excel-friendly
            $sampleData = [
                [
                    'NISN' => '1234567890',
                    'Nama Lengkap' => 'Ahmad Rizki Pratama',
                    'Kelas' => 'X IPA 1',
                    'Email' => 'ahmad.rizki@example.com',
                    'No Handphone' => '="081234567890"',
                    'No Handphone Orang Tua' => '="081234567891"',
                    'Password' => 'password123'
                ],
                [
                    'NISN' => '1234567891',
                    'Nama Lengkap' => 'Siti Nurhaliza',
                    'Kelas' => 'X IPA 1',
                    'Email' => 'siti.nurhaliza@example.com',
                    'No Handphone' => '="081234567892"',
                    'No Handphone Orang Tua' => '="081234567893"',
                    'Password' => 'password123'
                ],
                [
                    'NISN' => '1234567892',
                    'Nama Lengkap' => 'Budi Santoso',
                    'Kelas' => 'X IPA 2',
                    'Email' => 'budi.santoso@example.com',
                    'No Handphone' => '="081234567894"',
                    'No Handphone Orang Tua' => '="081234567895"',
                    'Password' => 'password123'
                ],
                [
                    'NISN' => '1234567893',
                    'Nama Lengkap' => 'Dewi Kartika',
                    'Kelas' => 'X IPS 1',
                    'Email' => 'dewi.kartika@example.com',
                    'No Handphone' => '="081234567896"',
                    'No Handphone Orang Tua' => '="081234567897"',
                    'Password' => 'password123'
                ]
            ];

            // Buat CSV content dengan format yang benar untuk Excel
            $csvContent = '';
            
            // BOM untuk UTF-8 (agar Excel mengenali encoding dengan benar)
            $csvContent .= "\xEF\xBB\xBF";
            
            // Header dengan semicolon sebagai delimiter (lebih kompatibel dengan Excel Indonesia)
            $csvContent .= implode(';', $headers) . "\n";
            
            // Data contoh dengan semicolon sebagai delimiter
            foreach ($sampleData as $row) {
                $csvContent .= implode(';', array_map(function($field) {
                    // Escape semicolon dan quotes jika ada
                    $field = str_replace('"', '""', $field);
                    // Wrap dengan quotes jika mengandung semicolon, comma, atau quotes
                    if (strpos($field, ';') !== false || strpos($field, ',') !== false || strpos($field, '"') !== false) {
                        return '"' . $field . '"';
                    }
                    return $field;
                }, $row)) . "\n";
            }

            // Set headers untuk download
            $filename = 'Template_Import_Siswa_' . date('Y-m-d') . '.csv';
            
            return response($csvContent)
                ->header('Content-Type', 'text/csv; charset=UTF-8')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Content-Length', strlen($csvContent))
                ->header('Cache-Control', 'no-cache, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Access-Control-Expose-Headers', 'Content-Disposition, Content-Type, Content-Length');

        } catch (\Exception $e) {
            Log::error('Download template error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunduh template'
            ], 500);
        }
    }

    /**
     * Normalize phone number from various formats
     */
    private function normalizePhoneNumber($phone)
    {
        if (empty($phone)) {
            return null;
        }

        // Remove all non-digit characters except +
        $phone = preg_replace('/[^\d+]/', '', $phone);
        
        // Handle Excel formula format: ="081234567890"
        if (strpos($phone, '=') !== false) {
            $phone = str_replace(['=', '"', "'"], '', $phone);
        }
        
        // Remove leading +62 and replace with 0
        if (substr($phone, 0, 3) === '+62') {
            $phone = '0' . substr($phone, 3);
        }
        
        // Remove leading 62 and add 0
        if (substr($phone, 0, 2) === '62' && strlen($phone) >= 10) {
            $phone = '0' . substr($phone, 2);
        }
        
        // Ensure it starts with 08
        if (substr($phone, 0, 1) === '8' && strlen($phone) >= 10) {
            $phone = '0' . $phone;
        }
        
        // Remove any remaining non-digit characters
        $phone = preg_replace('/[^\d]/', '', $phone);
        
        // Validate length (10-13 digits for Indonesian mobile numbers)
        if (strlen($phone) < 10 || strlen($phone) > 13) {
            return null;
        }
        
        return $phone;
    }

    /**
     * Get classes list for the school
     */
    public function getClasses(Request $request)
    {
        try {
            $school = School::find($request->school_id);

            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sekolah tidak ditemukan'
                ], 404);
            }

            // Get unique classes from students data
            $classes = Student::where('school_id', $school->id)
                ->select('kelas')
                ->distinct()
                ->whereNotNull('kelas')
                ->where('kelas', '!=', '')
                ->orderBy('kelas')
                ->pluck('kelas')
                ->map(function($kelas) {
                    return [
                        'name' => $kelas,
                        'value' => $kelas
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'classes' => $classes,
                    'total_classes' => $classes->count()
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get classes error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar kelas'
            ], 500);
        }
    }

    /**
     * Get import rules and requirements
     */
    public function getImportRules()
    {
        try {
            $rules = [
                'required_columns' => [
                    'nisn' => 'NISN siswa (wajib, 10 digit, unik)',
                    'name' => 'Nama lengkap siswa (wajib)',
                    'kelas' => 'Kelas siswa (wajib)',
                    'email' => 'Email siswa (opsional)',
                    'phone' => 'Nomor handphone siswa (opsional)',
                    'parent_phone' => 'Nomor handphone orang tua (opsional)',
                    'password' => 'Password default (opsional, default: password123)'
                ],
                'file_requirements' => [
                    'max_size' => '10MB',
                    'allowed_formats' => ['CSV', 'Excel (.xlsx, .xls)'],
                    'encoding' => 'UTF-8'
                ],
                'validation_rules' => [
                    'nisn' => 'Harus 10 digit, unik di sistem',
                    'name' => 'Tidak boleh kosong',
                    'kelas' => 'Tidak boleh kosong',
                    'email' => 'Format email yang valid (jika diisi)',
                    'phone' => 'Format nomor handphone yang valid (jika diisi)',
                    'parent_phone' => 'Format nomor handphone yang valid (jika diisi)'
                ],
                'tips' => [
                    'Gunakan template yang disediakan untuk memastikan format yang benar',
                    'Pastikan NISN unik dan tidak duplikat',
                    'Untuk nomor handphone di Excel: gunakan format =\"081234567890\" agar angka 0 di depan tidak hilang',
                    'Alternatif: format kolom sebagai "Text" sebelum memasukkan nomor handphone',
                    'Gunakan koma (,) sebagai pemisah kolom',
                    'Gunakan tanda kutip ganda (") untuk data yang mengandung koma',
                    'Hapus baris kosong di akhir file',
                    'Pastikan encoding file adalah UTF-8'
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $rules
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get import rules error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil aturan import'
            ], 500);
        }
    }
}
