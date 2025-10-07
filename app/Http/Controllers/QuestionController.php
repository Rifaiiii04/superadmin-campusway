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
            $questions = Question::with(['options'])->paginate(10);
            
            return Inertia::render('SuperAdmin/Questions', [
                'title' => 'Bank Soal',
                'questions' => $questions,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching questions: ' . $e->getMessage());
            return Inertia::render('SuperAdmin/Questions', [
                'title' => 'Bank Soal',
                'questions' => [
                    'data' => [],
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 10,
                    'total' => 0,
                ],
                'error' => 'Gagal memuat data bank soal'
            ]);
        }
    }

    public function store(Request $request)
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
            \Log::error('Error creating question: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal menambahkan soal'])->withInput();
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
            \Log::error('Error updating question: ' . $e->getMessage());
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
            \Log::error('Error deleting question: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal menghapus soal']);
        }
    }
}
