<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use App\Models\QuizAttempt;


    class QuizController extends Controller
    {
    private function authorizeQuizManagement()
    {
        if (
            !auth()->user()->hasRole('pengajar') &&
            !auth()->user()->hasRole('akademik')
        ) {
            abort(403, 'Anda tidak memiliki akses.');
        }
    }

    private function getTeacherCourses()
{
    $user = Auth::user();

    if ($user->hasRole('pengajar')) {

        return Course::with('instructors')

            ->whereHas('instructors', function ($query) use ($user) {

                $query->where('users.id', $user->id);

            })

            ->orderBy('title')

            ->get();
    }

    return Course::orderBy('title')->get();
}

    public function index()
    {
        if (Auth::user()->hasRole('pengajar')) {
            $courseIds = $this->getTeacherCourses()->pluck('id');

            $quizzes = Quiz::with('course')
                ->withCount('questions')
                ->whereIn('course_id', $courseIds)
                ->latest()
                ->get();

            $totalQuiz = $quizzes->count();
        } elseif (Auth::user()->hasRole('pelajar')) {
            $courseIds = Auth::user()->enrolledCourses()->pluck('courses.id');

            $quizzes = Quiz::with('course')
                ->withCount('questions')
                ->whereIn('course_id', $courseIds)
                ->latest()
                ->get();

            $totalQuiz = $quizzes->count();
        } else {
            // Super admin & akademik lihat semua
            $quizzes   = Quiz::with('course')->withCount('questions')->latest()->get();
            $totalQuiz = $quizzes->count();
        }

        return view('quizzes.index', compact('quizzes', 'totalQuiz'));
    }

        
        public function create()
{
    $this->authorizeQuizManagement();

    $courses = $this->getTeacherCourses();

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

        $user = Auth::user();

    if ($user->hasRole('pengajar')) {

        $allowedCourses = $this->getTeacherCourses()

    ->pluck('id')

    ->toArray();

        if (!in_array($validated['course_id'], $allowedCourses)) {

            abort(403, 'Anda tidak dapat membuat quiz pada kelas ini.');
        }
    }

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

    $attemptUsed = 0;

    $attempts = collect();

    if (auth()->user()->hasRole('pelajar')) {

        $attempts = QuizAttempt::where(
                'quiz_id',
                $quiz->id
            )

            ->where(
                'student_id',
                auth()->id()
            )

            ->latest()

            ->get();

        $attemptUsed = $attempts->count();
    }

    return view(
        'quizzes.show',

        compact(
            'quiz',
            'attemptUsed',
            'attempts'
        )
    );
}

        
        public function edit($id)
    {
        $this->authorizeQuizManagement();

        $quiz = Quiz::findOrFail($id);

        $user = Auth::user();

        $courses = $this->getTeacherCourses();

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

        $user = Auth::user();

    if ($user->hasRole('pengajar')) {

        $allowedCourses = $this->getTeacherCourses()

    ->pluck('id')

    ->toArray();

        if (!in_array($validated['course_id'], $allowedCourses)) {

            abort(403, 'Anda tidak dapat mengubah quiz pada kelas ini.');
        }
    }

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