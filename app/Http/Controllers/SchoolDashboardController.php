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
}
