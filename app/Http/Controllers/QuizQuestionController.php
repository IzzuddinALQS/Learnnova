<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;

class QuizQuestionController extends Controller
{
    public function index($quiz)
    {
        $quiz = Quiz::findOrFail($quiz);

        $questions = QuizQuestion::withCount('options')
            ->where('quiz_id', $quiz->id)
            ->orderBy('order')
            ->get();

        return view('quizzes.questions.index', compact(
            'quiz',
            'questions'
        ));
    }

    public function create($quiz)
    {
        $quiz = Quiz::findOrFail($quiz);

        return view('quizzes.questions.form', compact('quiz'));
    }

    public function store(Request $request, $quiz)
    {
        $quiz = Quiz::findOrFail($quiz);

        $validated = $request->validate([
            'question' => 'required',
            'type' => 'required',
            'points' => 'required|integer|min:1',
            'explanation' => 'nullable'
        ]);

        $validated['quiz_id'] = $quiz->id;

        QuizQuestion::create($validated);

        return redirect()
            ->route('quizzes.questions.index', $quiz->id)
            ->with('success', 'Soal berhasil ditambahkan');
    }

    public function edit($quiz, $question)
    {
        $quiz = Quiz::findOrFail($quiz);

        $question = QuizQuestion::findOrFail($question);

        return view('quizzes.questions.form', compact(
            'quiz',
            'question'
        ));
    }

    public function update(Request $request, $quiz, $question)
    {
        $question = QuizQuestion::findOrFail($question);

        $validated = $request->validate([
            'question' => 'required',
            'type' => 'required',
            'points' => 'required|integer|min:1',
            'explanation' => 'nullable'
        ]);

        $question->update($validated);

        return redirect()
            ->route('quizzes.questions.index', $quiz)
            ->with('success', 'Soal berhasil diperbarui');
    }

    public function destroy($quiz, $question)
    {
        QuizQuestion::findOrFail($question)->delete();

        return redirect()
            ->route('quizzes.questions.index', $quiz)
            ->with('success', 'Soal berhasil dihapus');
    }
}