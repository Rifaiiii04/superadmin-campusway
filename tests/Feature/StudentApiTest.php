<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Student;
use App\Models\School;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StudentApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->createTestData();
    }

    private function createTestData()
    {
        // Create school
        School::create([
            'npsn' => '12345678',
            'nama_sekolah' => 'SMA Negeri 1 Jakarta',
            'alamat' => 'Jl. Jakarta No. 1',
            'email' => 'sman1@jakarta.sch.id',
            'telepon' => '021-12345678',
            'password_hash' => bcrypt('password123')
        ]);

        // Create subjects
        Subject::create([
            'name' => 'Bahasa Indonesia',
            'code' => 'BIN',
            'is_required' => true,
            'is_active' => true
        ]);

        Subject::create([
            'name' => 'Matematika',
            'code' => 'MTK',
            'is_required' => true,
            'is_active' => true
        ]);

        // Create question
        $question = Question::create([
            'question_text' => 'Apa arti dari kata "melaksanakan"?',
            'subject' => 'Bahasa Indonesia',
            'type' => 'multiple_choice',
            'media_url' => null
        ]);

        // Create options
        QuestionOption::create([
            'question_id' => $question->id,
            'option_text' => 'Melakukan',
            'is_correct' => true
        ]);

        QuestionOption::create([
            'question_id' => $question->id,
            'option_text' => 'Membuat',
            'is_correct' => false
        ]);
    }

    public function test_student_registration()
    {
        $response = $this->postJson('/api/student/register', [
            'nama_lengkap' => 'Ahmad Fadillah',
            'nisn' => '1234567890',
            'npsn_sekolah' => '12345678',
            'nama_sekolah' => 'SMA Negeri 1 Jakarta',
            'kelas' => 'XII IPA',
            'no_handphone' => '081234567890',
            'email' => 'ahmad@email.com',
            'no_orang_tua' => '081234567891'
        ]);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'Registrasi siswa berhasil'
                ]);

        $this->assertDatabaseHas('students', [
            'nisn' => '1234567890',
            'nama_lengkap' => 'Ahmad Fadillah'
        ]);
    }

    public function test_get_questions()
    {
        // First register a student
        $studentResponse = $this->postJson('/api/student/register', [
            'nama_lengkap' => 'Ahmad Fadillah',
            'nisn' => '1234567890',
            'npsn_sekolah' => '12345678',
            'nama_sekolah' => 'SMA Negeri 1 Jakarta',
            'kelas' => 'XII IPA',
            'no_handphone' => '081234567890',
            'email' => 'ahmad@email.com',
            'no_orang_tua' => '081234567891'
        ]);

        $studentId = $studentResponse->json('data.student_id');

        // Then get questions
        $response = $this->postJson('/api/student/questions', [
            'student_id' => $studentId,
            'subjects' => ['Bahasa Indonesia', 'Matematika']
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Soal berhasil diambil'
                ]);
    }
}
