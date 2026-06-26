<?php

namespace App\Http\Controllers;

use App\Models\QuizQuestion;
use App\Models\QuizOption;
use Illuminate\Http\Request;

class QuizOptionController extends Controller
{
    public function index($question)
    {
        $question = QuizQuestion::with('quiz')->findOrFail($question);

        $options = QuizOption::where('question_id', $question->id)
            ->orderBy('order')
            ->get();

        return view('quizzes.options.index', compact(
            'question',
            'options'
        ));
    }

    public function store(Request $request, $question)
    {
        $question = QuizQuestion::findOrFail($question);

        $request->validate([
            'option_text' => 'required|string|max:255'
        ]);

        // Jika opsi baru dijadikan jawaban benar,
        // reset semua jawaban benar sebelumnya
        if ($request->has('is_correct')) {

            QuizOption::where(
                'question_id',
                $question->id
            )->update([
                'is_correct' => false
            ]);
        }

        QuizOption::create([
            'question_id' => $question->id,
            'option_text' => $request->option_text,
            'is_correct' => $request->has('is_correct'),
            'order' => QuizOption::where(
                'question_id',
                $question->id
            )->count() + 1
        ]);

        return back()->with(
            'success',
            'Opsi berhasil ditambahkan'
        );
    }

    public function update(Request $request, $question, $option)
{
    $option = QuizOption::findOrFail($option);

    $request->validate([
        'option_text' => 'required|string|max:255'
    ]);

    if ($request->has('is_correct')) {

        QuizOption::where(
            'question_id',
            $option->question_id
        )
        ->where('id', '!=', $option->id)
        ->update([
            'is_correct' => false
        ]);
    }

    $option->update([
        'option_text' => $request->option_text,
        'is_correct' => $request->has('is_correct')
    ]);

    return back()->with(
        'success',
        'Opsi berhasil diperbarui'
    );
}

    public function destroy($question, $option)
    {
        QuizOption::findOrFail($option)->delete();

        return back()->with(
            'success',
            'Opsi berhasil dihapus'
        );
    }
}