<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use App\Models\QuizOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizAttemptController extends Controller
{
    public function index($quiz)
    {
        $quiz = Quiz::findOrFail($quiz);

        if (Auth::user()->hasPermission('quizzes.grade')) {
            $attempts = QuizAttempt::with('student')
                ->where('quiz_id', $quiz->id)
                ->latest()
                ->get();
        } else {
            $attempts = QuizAttempt::where('quiz_id', $quiz->id)
                ->where('student_id', Auth::id())
                ->latest()
                ->get();
        }

        return view('quizzes.attempts.index', compact('quiz', 'attempts'));
    }

    public function take($quiz)
    {
        if (!Auth::user()->hasPermission('quizzes.take')) {
            abort(403, 'Anda tidak memiliki akses untuk mengikuti quiz.');
        }

        $quiz = Quiz::with('questions.options')->findOrFail($quiz);

        if ($quiz->deadline && now()->gt($quiz->deadline)) {
            return redirect()->route('quizzes.show', $quiz->id)
                ->with('error', 'Deadline quiz telah lewat.');
        }

        $attemptCount = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', Auth::id())
            ->count();

        if ($quiz->max_attempts && $attemptCount >= $quiz->max_attempts) {
            return redirect()->route('quizzes.show', $quiz->id)
                ->with('error', 'Anda sudah mencapai jumlah percobaan maksimal.');
        }

        return view('quizzes.attempts.take', compact('quiz', 'attemptCount'));
    }

    public function store(Request $request, $quiz)
    {
        if (!Auth::user()->hasPermission('quizzes.take')) {
            abort(403, 'Anda tidak memiliki akses untuk mengikuti quiz.');
        }

        $quiz = Quiz::with('questions.options')->findOrFail($quiz);

        if ($quiz->deadline && now()->gt($quiz->deadline)) {
            return redirect()->route('quizzes.show', $quiz->id)
                ->with('error', 'Deadline quiz telah lewat.');
        }

        $attemptCount = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', Auth::id())
            ->count();

        if ($quiz->max_attempts && $attemptCount >= $quiz->max_attempts) {
            return redirect()->route('quizzes.show', $quiz->id)
                ->with('error', 'Anda sudah mencapai jumlah percobaan maksimal.');
        }

        $attempt = QuizAttempt::create([
            'quiz_id'    => $quiz->id,
            'student_id' => Auth::id(),
            'started_at' => now(),
            'finished_at' => now(),
        ]);

        $totalPoints = 0;
        $autoScore = 0;
        $manualReviewRequired = false;

        foreach ($quiz->questions as $question) {
            $totalPoints += $question->points;
            $answer = [
                'attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'option_id' => null,
                'text_answer' => null,
                'is_correct' => null,
                'points_earned' => null,
            ];

            if (in_array($question->type, ['multiple_choice', 'true_false'])) {
                $optionId = $request->input('answers.' . $question->id);
                $option = QuizOption::where('question_id', $question->id)
                    ->find($optionId);

                if ($option) {
                    $answer['option_id'] = $option->id;
                    $answer['is_correct'] = $option->is_correct;
                    $answer['points_earned'] = $option->is_correct ? $question->points : 0;
                    $autoScore += $answer['points_earned'];
                } else {
                    $answer['points_earned'] = 0;
                    $answer['is_correct'] = false;
                }
            } else {
                $manualReviewRequired = true;
                $answer['text_answer'] = trim($request->input('answers_text.' . $question->id));
            }

            QuizAnswer::create($answer);
        }

        $attempt->update([
            'score' => $autoScore,
            'is_passed' => $manualReviewRequired
                ? null
                : ($totalPoints > 0 && ($autoScore / $totalPoints * 100) >= $quiz->passing_score),
        ]);

        $message = $manualReviewRequired
            ? 'Quiz selesai. Nilai akan ditentukan setelah penilaian guru.'
            : 'Quiz selesai. Nilai otomatis telah dihitung.';

        return redirect()->route('quizzes.attempts.show', [$quiz->id, $attempt->id])
            ->with('success', $message);
    }

    public function show($quiz, $attempt)
    {
        $quiz = Quiz::findOrFail($quiz);
        $attempt = QuizAttempt::with(['student', 'answers.question.options', 'answers.option'])
            ->where('quiz_id', $quiz->id)
            ->findOrFail($attempt);

        if (Auth::id() !== $attempt->student_id && !Auth::user()->hasPermission('quizzes.grade')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat attempt ini.');
        }

        return view('quizzes.attempts.show', compact('quiz', 'attempt'));
    }

    public function gradeForm($quiz, $attempt)
    {
        if (!Auth::user()->hasPermission('quizzes.grade')) {
            abort(403, 'Anda tidak memiliki akses untuk menilai quiz.');
        }

        $quiz = Quiz::findOrFail($quiz);
        $attempt = QuizAttempt::with(['student', 'answers.question.options', 'answers.option'])
            ->where('quiz_id', $quiz->id)
            ->findOrFail($attempt);

        return view('quizzes.attempts.grade', compact('quiz', 'attempt'));
    }

    public function grade(Request $request, $quiz, $attempt)
    {
        if (!Auth::user()->hasPermission('quizzes.grade')) {
            abort(403, 'Anda tidak memiliki akses untuk menilai quiz.');
        }

        $quiz = Quiz::findOrFail($quiz);
        $attempt = QuizAttempt::where('quiz_id', $quiz->id)
            ->findOrFail($attempt);

        $validated = $request->validate([
            'score' => 'required|numeric|min:0',
            'is_passed' => 'required|in:0,1',
        ]);

        $attempt->update([
            'score' => $validated['score'],
            'is_passed' => $validated['is_passed'] === '1',
        ]);

        return redirect()->route('quizzes.attempts.show', [$quiz->id, $attempt->id])
            ->with('success', 'Nilai quiz berhasil diperbarui.');
    }
}
