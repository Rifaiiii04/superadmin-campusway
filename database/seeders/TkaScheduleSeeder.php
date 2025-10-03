<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TkaScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing schedules
        DB::table('tka_schedules')->truncate();

        // PUSMENDIK 2025 Standard TKA Schedules
        $schedules = [
            [
                'title' => 'Tes Kemampuan Akademik (TKA) Reguler 2025 - Gelombang I',
                'description' => 'Tes Kemampuan Akademik reguler untuk siswa kelas 12 sesuai standar PUSMENDIK 2025. Ujian ini mencakup mata pelajaran inti dan pilihan sesuai dengan kurikulum nasional.',
                'start_date' => '2025-03-15 08:00:00',
                'end_date' => '2025-03-15 12:00:00',
                'status' => 'scheduled',
                'type' => 'regular',
                'instructions' => "1. Masuk 30 menit sebelum ujian dimulai
2. Pastikan koneksi internet stabil (minimal 2 Mbps)
3. Baca instruksi dengan teliti sebelum memulai
4. Kerjakan sesuai waktu yang disediakan
5. Submit jawaban sebelum waktu habis
6. Pastikan perangkat dalam kondisi baik
7. Siapkan identitas diri (KTP/Kartu Pelajar)",
                'target_schools' => null,
                'is_active' => true,
                'created_by' => 'Super Admin',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'title' => 'Tes Kemampuan Akademik (TKA) Susulan 2025 - Gelombang I',
                'description' => 'Tes Kemampuan Akademik susulan untuk siswa yang berhalangan hadir pada jadwal reguler. Hanya untuk siswa dengan surat keterangan resmi.',
                'start_date' => '2025-03-22 08:00:00',
                'end_date' => '2025-03-22 12:00:00',
                'status' => 'scheduled',
                'type' => 'makeup',
                'instructions' => "1. Hanya untuk siswa dengan surat keterangan berhalangan
2. Masuk 30 menit sebelum ujian dimulai
3. Pastikan koneksi internet stabil
4. Baca instruksi dengan teliti
5. Kerjakan sesuai waktu yang disediakan
6. Submit jawaban sebelum waktu habis
7. Bawa surat keterangan asli",
                'target_schools' => null,
                'is_active' => true,
                'created_by' => 'Super Admin',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'title' => 'Tes Kemampuan Akademik (TKA) Reguler 2025 - Gelombang II',
                'description' => 'Tes Kemampuan Akademik reguler gelombang kedua untuk siswa kelas 12. Ujian ini memberikan kesempatan kedua bagi siswa yang belum mengikuti gelombang pertama.',
                'start_date' => '2025-04-15 08:00:00',
                'end_date' => '2025-04-15 12:00:00',
                'status' => 'scheduled',
                'type' => 'regular',
                'instructions' => "1. Masuk 30 menit sebelum ujian dimulai
2. Pastikan koneksi internet stabil
3. Baca instruksi dengan teliti
4. Kerjakan sesuai waktu yang disediakan
5. Submit jawaban sebelum waktu habis
6. Pastikan perangkat dalam kondisi baik
7. Siapkan identitas diri (KTP/Kartu Pelajar)",
                'target_schools' => null,
                'is_active' => true,
                'created_by' => 'Super Admin',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'title' => 'Tes Kemampuan Akademik (TKA) Khusus Kelas 12 - 2025',
                'description' => 'Tes Kemampuan Akademik khusus untuk siswa kelas 12 yang akan lulus tahun ini. Prioritas untuk siswa yang belum menentukan pilihan jurusan.',
                'start_date' => '2025-09-14 04:54:55',
                'end_date' => '2025-09-14 06:54:55',
                'status' => 'scheduled',
                'type' => 'special',
                'instructions' => 'Tes khusus untuk kelas 12 dengan durasi 2 jam. Prioritas untuk siswa yang belum menentukan pilihan jurusan. Ujian ini dirancang khusus untuk membantu siswa dalam menentukan pilihan jurusan yang tepat.',
                'target_schools' => null,
                'is_active' => true,
                'created_by' => 'Super Admin',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'title' => 'Tes Kemampuan Akademik (TKA) Remedial 2025',
                'description' => 'Tes Kemampuan Akademik remedial untuk siswa yang belum mencapai nilai minimum pada ujian sebelumnya. Ujian ini memberikan kesempatan untuk memperbaiki nilai.',
                'start_date' => '2025-05-15 08:00:00',
                'end_date' => '2025-05-15 12:00:00',
                'status' => 'scheduled',
                'type' => 'special',
                'instructions' => "1. Hanya untuk siswa yang belum mencapai nilai minimum
2. Masuk 30 menit sebelum ujian dimulai
3. Pastikan koneksi internet stabil
4. Baca instruksi dengan teliti
5. Kerjakan sesuai waktu yang disediakan
6. Submit jawaban sebelum waktu habis
7. Fokus pada mata pelajaran yang kurang",
                'target_schools' => null,
                'is_active' => true,
                'created_by' => 'Super Admin',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'title' => 'Tes Kemampuan Akademik (TKA) Pra-Ujian Nasional 2025',
                'description' => 'Tes Kemampuan Akademik sebagai persiapan menghadapi Ujian Nasional. Ujian ini dirancang untuk mengukur kesiapan siswa dalam menghadapi UN.',
                'start_date' => '2025-02-15 08:00:00',
                'end_date' => '2025-02-15 12:00:00',
                'status' => 'scheduled',
                'type' => 'regular',
                'instructions' => "1. Ujian persiapan menghadapi UN
2. Masuk 30 menit sebelum ujian dimulai
3. Pastikan koneksi internet stabil
4. Baca instruksi dengan teliti
5. Kerjakan sesuai waktu yang disediakan
6. Submit jawaban sebelum waktu habis
7. Gunakan hasil untuk evaluasi diri",
                'target_schools' => null,
                'is_active' => true,
                'created_by' => 'Super Admin',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'title' => 'Tes Kemampuan Akademik (TKA) Susulan 2025 - Gelombang II',
                'description' => 'Tes Kemampuan Akademik susulan gelombang kedua untuk siswa yang berhalangan hadir pada jadwal reguler gelombang II.',
                'start_date' => '2025-04-22 08:00:00',
                'end_date' => '2025-04-22 12:00:00',
                'status' => 'scheduled',
                'type' => 'makeup',
                'instructions' => "1. Hanya untuk siswa dengan surat keterangan berhalangan
2. Masuk 30 menit sebelum ujian dimulai
3. Pastikan koneksi internet stabil
4. Baca instruksi dengan teliti
5. Kerjakan sesuai waktu yang disediakan
6. Submit jawaban sebelum waktu habis
7. Bawa surat keterangan asli",
                'target_schools' => null,
                'is_active' => true,
                'created_by' => 'Super Admin',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'title' => 'Tes Kemampuan Akademik (TKA) Akhir Tahun 2025',
                'description' => 'Tes Kemampuan Akademik akhir tahun untuk evaluasi kemampuan siswa secara menyeluruh. Ujian ini menjadi penilaian akhir sebelum kelulusan.',
                'start_date' => '2025-06-15 08:00:00',
                'end_date' => '2025-06-15 12:00:00',
                'status' => 'scheduled',
                'type' => 'regular',
                'instructions' => "1. Ujian evaluasi akhir tahun
2. Masuk 30 menit sebelum ujian dimulai
3. Pastikan koneksi internet stabil
4. Baca instruksi dengan teliti
5. Kerjakan sesuai waktu yang disediakan
6. Submit jawaban sebelum waktu habis
7. Hasil akan digunakan untuk evaluasi kelulusan",
                'target_schools' => null,
                'is_active' => true,
                'created_by' => 'Super Admin',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ];

        // Insert schedules
        foreach ($schedules as $schedule) {
            DB::table('tka_schedules')->insert($schedule);
        }

        $this->command->info('✅ TKA Schedules seeded successfully!');
        $this->command->info('📊 Total schedules created: ' . count($schedules));
    }
}
