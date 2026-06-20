<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;


class QuizController extends Controller
{
    private function authorizeQuizManagement()
{
    if (
        !auth()->user()->hasRole('super_admin') &&
        !auth()->user()->hasRole('pengajar') &&
        !auth()->user()->hasRole('akademik')
    ) {
        abort(403, 'Anda tidak memiliki akses.');
    }
}
    
    public function index()
{
    $quizzes = Quiz::with('course')
    ->withCount('questions')
    ->latest()
    ->get();

    $totalQuiz = Quiz::count();

    return view(
    'quizzes.index', compact('quizzes','totalQuiz')
    );
}

    
    public function create()
{
    $this->authorizeQuizManagement();

    $courses = Course::all();

    return view('quizzes.form', compact('courses'));
}

    
    public function store(Request $request)
{
    $this->authorizeQuizManagement();

    $validated = $request->validate([
        'course_id' => 'required|exists:courses,id',
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'deadline' => 'nullable|date',
        'duration_minutes' => 'nullable|integer',
        'max_attempts' => 'nullable|integer',
        'passing_score' => 'nullable|numeric'
    ]);

    $quiz = Quiz::create($validated);

    event(new \App\Events\QuizCreated($quiz));

    return response()->json([
        'message' => 'Quiz berhasil dibuat',
        'redirect' => route('quizzes.index')
    ]);
}


    public function show($id)
{
    $quiz = Quiz::with('course')->findOrFail($id);

    return view('quizzes.show', compact('quiz'));
}

    
    public function edit($id)
{
    $this->authorizeQuizManagement();

    $quiz = Quiz::findOrFail($id);

    $courses = Course::all();

    return view('quizzes.form', compact('quiz', 'courses'));
}

    public function update(Request $request, $id)
{
    $this->authorizeQuizManagement();

    $quiz = Quiz::findOrFail($id);

    $validated = $request->validate([
        'course_id' => 'required|exists:courses,id',
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'deadline' => 'nullable|date',
        'duration_minutes' => 'nullable|integer',
        'max_attempts' => 'nullable|integer',
        'passing_score' => 'nullable|numeric'
    ]);

    $quiz->update($validated);

    return response()->json([
        'message' => 'Quiz berhasil diperbarui',
        'redirect' => route('quizzes.index')
    ]);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
{
    $this->authorizeQuizManagement();

    $quiz = Quiz::findOrFail($id);

    $quiz->delete();

    return redirect()
        ->route('quizzes.index')
        ->with('success', 'Quiz berhasil dihapus');
}

}