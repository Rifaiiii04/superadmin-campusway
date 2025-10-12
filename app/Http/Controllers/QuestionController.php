<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class QuestionController extends Controller
{
    public function index()
    {
        try {
            // Debug: Log database connection and data count
            $totalQuestions = Question::count();
            Log::info('QuestionController::index - Total questions in database: ' . $totalQuestions);
            
            $questions = Question::with(['options'])->paginate(10);
            
            // Debug: Log pagination data
            Log::info('QuestionController::index - Pagination data:', [
                'total' => $questions->total(),
                'per_page' => $questions->perPage(),
                'current_page' => $questions->currentPage(),
                'data_count' => $questions->count()
            ]);
            
            return Inertia::render('SuperAdmin/Questions', [
                'title' => 'Bank Soal',
                'questions' => $questions,
                'debug' => [
                    'total_questions' => $totalQuestions,
                    'pagination_total' => $questions->total(),
                    'current_page' => $questions->currentPage(),
                    'per_page' => $questions->perPage()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('QuestionController::index - Error: ' . $e->getMessage());
            Log::error('QuestionController::index - Stack trace: ' . $e->getTraceAsString());
            
            return Inertia::render('SuperAdmin/Questions', [
                'title' => 'Bank Soal',
                'questions' => [
                    'data' => [],
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 10,
                    'total' => 0,
                ],
                'error' => 'Gagal memuat data bank soal: ' . $e->getMessage(),
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
            'subject' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'content' => 'required|string',
            'media_url' => 'nullable|string|max:255',
            'options' => 'required|array|min:2',
            'options.*.option_text' => 'required|string',
            'options.*.is_correct' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $question = Question::create([
                'subject' => $request->subject,
                'type' => $request->type,
                'content' => $request->content,
                'media_url' => $request->media_url,
            ]);

            foreach ($request->options as $option) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $option['option_text'],
                    'is_correct' => $option['is_correct'] ?? false,
                ]);
            }

            return redirect()->back()->with('success', 'Soal berhasil ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error creating question: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal menambahkan soal'])->withInput();
        }
    }

    /**
     * Store a newly created question (JSON response)
     */
    private function storeJson(Request $request)
    {
        try {
            Log::info('Question Store JSON Request Data:', $request->all());
            
            $validator = Validator::make($request->all(), [
                'subject' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'content' => 'required|string',
                'media_url' => 'nullable|string|max:255',
                'options' => 'required|array|min:2',
                'options.*.option_text' => 'required|string',
                'options.*.is_correct' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $question = Question::create([
                'subject' => $request->subject,
                'type' => $request->type,
                'content' => $request->content,
                'media_url' => $request->media_url,
            ]);

            foreach ($request->options as $option) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $option['option_text'],
                    'is_correct' => $option['is_correct'] ?? false,
                ]);
            }

            $question->load('options');

            return response()->json([
                'success' => true,
                'message' => 'Soal berhasil ditambahkan',
                'data' => $question
            ], 201);

        } catch (\Exception $e) {
            Log::error('Question store JSON error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan soal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Question $question)
    {
        $question->load('options');
        
        return Inertia::render('SuperAdmin/QuestionDetail', [
            'title' => 'Detail Soal',
            'question' => $question,
        ]);
    }

    public function update(Request $request, Question $question)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'content' => 'required|string',
            'media_url' => 'nullable|string|max:255',
            'options' => 'required|array|min:2',
            'options.*.option_text' => 'required|string',
            'options.*.is_correct' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $question->update([
                'subject' => $request->subject,
                'type' => $request->type,
                'content' => $request->content,
                'media_url' => $request->media_url,
            ]);

            // Delete existing options
            $question->options()->delete();

            // Create new options
            foreach ($request->options as $option) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $option['option_text'],
                    'is_correct' => $option['is_correct'] ?? false,
                ]);
            }

            return redirect()->back()->with('success', 'Soal berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error updating question: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal memperbarui soal'])->withInput();
        }
    }

    public function destroy(Question $question)
    {
        try {
            $question->options()->delete();
            $question->delete();
            return redirect()->back()->with('success', 'Soal berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting question: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal menghapus soal']);
        }
    }
}
