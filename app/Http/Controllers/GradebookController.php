<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GradebookExport;

class GradebookController extends Controller
{
    // ── Pelajar: lihat nilai sendiri ──────────────────────────────────
    public function index(Request $request)
    {
        if (!Auth::user()->hasRole('pelajar')) {
            abort(403, 'Unauthorized action.');
        }

        $student        = Auth::user();
        $allCourses     = $student->enrolledCourses()->with(['assignments', 'quizzes'])->get();
        $selectedCourse = $request->get('course_id');

        $courses   = $selectedCourse ? $allCourses->where('id', $selectedCourse) : $allCourses;
        $gradebook = $this->buildStudentGradebook($student, $courses);

        return view('gradebook.index', compact('gradebook', 'allCourses', 'selectedCourse'));
    }

    // ── Pengajar: lihat nilai semua pelajar per course ────────────────
    public function course(Course $course)
    {
        if (!Auth::user()->hasPermission('reports.view')) {
            abort(403, 'Unauthorized action.');
        }

        $students  = User::whereHas('enrollments', fn($q) => $q->where('course_id', $course->id)->where('status', 'active'))->get();
        $gradebook = $this->buildCourseGradebook($course, $students);

        return view('gradebook.course', compact('gradebook', 'course', 'students'));
    }

    // ── Export PDF Pelajar ────────────────────────────────────────────
    public function exportPdfStudent(Request $request)
    {
        $student        = Auth::user();
        $allCourses     = $student->enrolledCourses()->with(['assignments', 'quizzes'])->get();
        $selectedCourse = $request->get('course_id');

        $courses   = $selectedCourse ? $allCourses->where('id', $selectedCourse) : $allCourses;
        $gradebook = $this->buildStudentGradebook($student, $courses);

        $pdf = Pdf::loadView('gradebook.exports.pdf_student', compact('gradebook', 'student'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('nilai-' . str()->slug($student->name) . '.pdf');
    }

    // ── Export Excel Pelajar ──────────────────────────────────────────
    public function exportExcelStudent(Request $request)
    {
        $student        = Auth::user();
        $selectedCourse = $request->get('course_id') ? (int) $request->get('course_id') : null;

        return Excel::download(
            new GradebookExport($student, null, null, $selectedCourse),
            'nilai-' . str()->slug($student->name) . '.xlsx'
        );
    }

    // ── Export PDF Pengajar ───────────────────────────────────────────
    public function exportPdfCourse(Course $course)
    {
        if (!Auth::user()->hasPermission('reports.export')) {
            abort(403, 'Unauthorized action.');
        }

        $students  = User::whereHas('enrollments', fn($q) => $q->where('course_id', $course->id)->where('status', 'active'))->get();
        $gradebook = $this->buildCourseGradebook($course, $students);

        $pdf = Pdf::loadView('gradebook.exports.pdf_course', compact('gradebook', 'course', 'students'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('rekap-nilai-' . str()->slug($course->title) . '.pdf');
    }

    // ── Export Excel Pengajar ─────────────────────────────────────────
    public function exportExcelCourse(Course $course)
    {
        if (!Auth::user()->hasPermission('reports.export')) {
            abort(403, 'Unauthorized action.');
        }

        $students = User::whereHas('enrollments', fn($q) => $q->where('course_id', $course->id)->where('status', 'active'))->get();
        return Excel::download(new GradebookExport(null, $course, $students), 'rekap-nilai-' . str()->slug($course->title) . '.xlsx');
    }

    // ── Helper: Bangun data gradebook pelajar ─────────────────────────
    private function buildStudentGradebook(User $student, $courses): array
    {
        $gradebook = [];

        foreach ($courses as $course) {
            $assignmentWeight = $course->assignment_weight / 100;
            $quizWeight       = $course->quiz_weight / 100;

            $assignments          = [];
            $totalAssignmentScore = 0;
            $totalAssignmentMax   = 0;

            foreach ($course->assignments as $assignment) {
                $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
                    ->where('student_id', $student->id)
                    ->first();

                $score    = $submission?->score;
                $maxScore = $assignment->max_score;

                $assignments[] = [
                    'title'  => $assignment->title,
                    'score'  => $score,
                    'max'    => $maxScore,
                    'status' => $submission?->status ?? 'belum',
                ];

                $totalAssignmentMax += $maxScore;
                if ($score !== null) {
                    $totalAssignmentScore += $score;
                }
            }

            $avgAssignment = $totalAssignmentMax > 0
                ? round(($totalAssignmentScore / $totalAssignmentMax) * 100, 2)
                : null;

            $quizzes        = [];
            $totalQuizScore = 0;
            $totalQuizMax   = 0;

            foreach ($course->quizzes as $quiz) {
                $attempt = QuizAttempt::where('quiz_id', $quiz->id)
                    ->where('student_id', $student->id)
                    ->orderBy('score', 'desc')
                    ->first();

                $maxScore = $quiz->questions->sum('points');
                $score    = $attempt?->score;

                $quizzes[] = [
                    'title'     => $quiz->title,
                    'score'     => $score,
                    'max'       => $maxScore,
                    'is_passed' => $attempt?->is_passed,
                ];

                $totalQuizMax += $maxScore;
                if ($score !== null) {
                    $totalQuizScore += $score;
                }
            }

            $avgQuiz = $totalQuizMax > 0
                ? round(($totalQuizScore / $totalQuizMax) * 100, 2)
                : (count($course->quizzes) > 0 ? 0 : null);

            $avgAssignment = $totalAssignmentMax > 0
                ? round(($totalAssignmentScore / $totalAssignmentMax) * 100, 2)
                : (count($course->assignments) > 0 ? 0 : null);

            $finalScore = null;
            if ($avgAssignment !== null || $avgQuiz !== null) {
                $valAssignment = $avgAssignment ?? 0;
                $valQuiz = $avgQuiz ?? 0;
                $finalScore = round(($valAssignment * $assignmentWeight) + ($valQuiz * $quizWeight), 2);
            }

            $gradebook[] = [
                'course'            => $course,
                'assignments'       => $assignments,
                'quizzes'           => $quizzes,
                'avg_assignment'    => $avgAssignment,
                'avg_quiz'          => $avgQuiz,
                'final_score'       => $finalScore,
                'assignment_weight' => $course->assignment_weight,
                'quiz_weight'       => $course->quiz_weight,
            ];
        }

        return $gradebook;
    }

    // ── Helper: Bangun data gradebook per course ──────────────────────
    public function buildCourseGradebook(Course $course, $students): array
    {
        $course->load(['assignments', 'quizzes.questions']);
        $assignmentWeight = $course->assignment_weight / 100;
        $quizWeight       = $course->quiz_weight / 100;
        $gradebook        = [];

        foreach ($students as $student) {
            $assignmentScores     = [];
            $totalAssignmentScore = 0;
            $totalAssignmentMax   = 0;

            foreach ($course->assignments as $assignment) {
                $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
                    ->where('student_id', $student->id)
                    ->first();

                $score              = $submission?->score;
                $assignmentScores[] = $score;

                $totalAssignmentMax += $assignment->max_score;
                if ($score !== null) {
                    $totalAssignmentScore += $score;
                }
            }

            $avgAssignment = $totalAssignmentMax > 0
                ? round(($totalAssignmentScore / $totalAssignmentMax) * 100, 2)
                : null;

            $quizScores     = [];
            $totalQuizScore = 0;
            $totalQuizMax   = 0;

            foreach ($course->quizzes as $quiz) {
                $attempt = QuizAttempt::where('quiz_id', $quiz->id)
                    ->where('student_id', $student->id)
                    ->orderBy('score', 'desc')
                    ->first();

                $maxScore     = $quiz->questions->sum('points');
                $score        = $attempt?->score;
                $quizScores[] = $score;

                $totalQuizMax += $maxScore;
                if ($score !== null) {
                    $totalQuizScore += $score;
                }
            }

            $avgQuiz = $totalQuizMax > 0
                ? round(($totalQuizScore / $totalQuizMax) * 100, 2)
                : (count($course->quizzes) > 0 ? 0 : null);

            $avgAssignment = $totalAssignmentMax > 0
                ? round(($totalAssignmentScore / $totalAssignmentMax) * 100, 2)
                : (count($course->assignments) > 0 ? 0 : null);

            $finalScore = null;
            if ($avgAssignment !== null || $avgQuiz !== null) {
                $valAssignment = $avgAssignment ?? 0;
                $valQuiz = $avgQuiz ?? 0;
                $finalScore = round(($valAssignment * $assignmentWeight) + ($valQuiz * $quizWeight), 2);
            }

            $gradebook[] = [
                'student'           => $student,
                'assignment_scores' => $assignmentScores,
                'quiz_scores'       => $quizScores,
                'avg_assignment'    => $avgAssignment,
                'avg_quiz'          => $avgQuiz,
                'final_score'       => $finalScore,
            ];
        }

        return $gradebook;
    }

    // ── Admin: Laporan Akademik Keseluruhan ───────────────────────────
    public function academicReport()
    {
        if (!Auth::user()->hasPermission('reports.view')) {
            abort(403, 'Unauthorized action.');
        }

        $courses  = Course::with(['assignments', 'quizzes.questions'])->get();
        $students = User::whereHas('roles', fn($q) => $q->where('name', 'pelajar'))->get();

        $summary = [
            'total_students'    => $students->count(),
            'total_courses'     => $courses->count(),
            'total_assignments' => Assignment::count(),
            'total_quizzes'     => Quiz::count(),
        ];

        $report = [];
        foreach ($courses as $course) {
            $courseStudents = User::whereHas(
                'enrollments',
                fn($q) =>
                $q->where('course_id', $course->id)->where('status', 'active')
            )->get();

            $gradebook   = $this->buildCourseGradebook($course, $courseStudents);
            $finalScores = collect($gradebook)->pluck('final_score')->filter()->values();

            $report[] = [
                'course'         => $course,
                'total_students' => $courseStudents->count(),
                'avg_assignment' => collect($gradebook)->pluck('avg_assignment')->filter()->avg()
                    ? round(collect($gradebook)->pluck('avg_assignment')->filter()->avg(), 2)
                    : null,
                'avg_quiz'       => collect($gradebook)->pluck('avg_quiz')->filter()->avg()
                    ? round(collect($gradebook)->pluck('avg_quiz')->filter()->avg(), 2)
                    : null,
                'avg_final'      => $finalScores->count() > 0
                    ? round($finalScores->avg(), 2)
                    : null,
                'gradebook'      => $gradebook,
            ];
        }

        return view('gradebook.academic_report', compact('summary', 'report', 'courses', 'students'));
    }

    // ── Export Excel Laporan Admin ────────────────────────────────────
    public function exportExcelReport()
    {
        if (!Auth::user()->hasPermission('reports.export')) {
            abort(403, 'Unauthorized action.');
        }

        $courses = Course::with(['assignments', 'quizzes.questions'])->get();

        return Excel::download(
            new \App\Exports\AcademicReportExport($courses),
            'laporan-akademik-' . now()->format('d-m-Y') . '.xlsx'
        );
    }

    // ── Export PDF Laporan Admin ──────────────────────────────────────
    public function exportPdfReport()
    {
        if (!Auth::user()->hasPermission('reports.export')) {
            abort(403, 'Unauthorized action.');
        }

        $courses  = Course::with(['assignments', 'quizzes.questions'])->get();
        $students = User::whereHas('roles', fn($q) => $q->where('name', 'pelajar'))->get();

        $summary = [
            'total_students'    => $students->count(),
            'total_courses'     => $courses->count(),
            'total_assignments' => Assignment::count(),
            'total_quizzes'     => Quiz::count(),
        ];

        $report = [];
        foreach ($courses as $course) {
            $courseStudents = User::whereHas(
                'enrollments',
                fn($q) =>
                $q->where('course_id', $course->id)->where('status', 'active')
            )->get();

            $gradebook   = $this->buildCourseGradebook($course, $courseStudents);
            $finalScores = collect($gradebook)->pluck('final_score')->filter()->values();

            $report[] = [
                'course'         => $course,
                'total_students' => $courseStudents->count(),
                'avg_assignment' => collect($gradebook)->pluck('avg_assignment')->filter()->avg()
                    ? round(collect($gradebook)->pluck('avg_assignment')->filter()->avg(), 2)
                    : null,
                'avg_quiz'       => collect($gradebook)->pluck('avg_quiz')->filter()->avg()
                    ? round(collect($gradebook)->pluck('avg_quiz')->filter()->avg(), 2)
                    : null,
                'avg_final'      => $finalScores->count() > 0
                    ? round($finalScores->avg(), 2)
                    : null,
                'gradebook'      => $gradebook,
            ];
        }

        $pdf = Pdf::loadView('gradebook.exports.pdf_report', compact('summary', 'report', 'courses'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-akademik-' . now()->format('d-m-Y') . '.pdf');
    }

    // ── Pengajar: pilih kelas ─────────────────────────────────────────
    public function courses()
    {
        if (!Auth::user()->hasPermission('reports.view')) {
            abort(403, 'Unauthorized action.');
        }

        if (Auth::user()->hasRole('super_admin')) {
            $courses = Course::with(['assignments', 'quizzes'])->get();
        } else {
            $courses = Course::with(['assignments', 'quizzes'])
                ->where('instructor_id', Auth::id())
                ->orWhereHas('instructors', fn($q) => $q->where('user_id', Auth::id()))
                ->get();
        }

        return view('gradebook.courses', compact('courses'));
    }
}
