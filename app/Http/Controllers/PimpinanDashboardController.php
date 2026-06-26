<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class PimpinanDashboardController extends Controller
{
    public function index()
    {

        if (!auth()->user()->hasRole('pimpinan')) {
            abort(403, 'Unauthorized action.');
        }

        // Rata-rata nilai per kelas (diurutkan tertinggi ke terendah)
        $rataRata = DB::table('quiz_attempts')
            ->join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id')
            ->join('courses', 'quizzes.course_id', '=', 'courses.id')
            ->select(
                'courses.id',
                'courses.title',
                DB::raw('ROUND(AVG(quiz_attempts.score), 2) as rata_rata')
            )
            ->groupBy('courses.id', 'courses.title')
            ->orderByDesc('rata_rata')
            ->get();

        // Nilai tertinggi secara keseluruhan (Top 10)
        $nilaiTertinggi = DB::table('quiz_attempts')
            ->join('users', 'quiz_attempts.student_id', '=', 'users.id')
            ->join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id')
            ->join('courses', 'quizzes.course_id', '=', 'courses.id')
            ->select(
                'users.name as student_name',
                'courses.title as course_title',
                'quiz_attempts.score'
            )
            ->orderByDesc('quiz_attempts.score')
            ->limit(10)
            ->get();

        // Nilai terendah secara keseluruhan (Bottom 10)
        $nilaiTerendah = DB::table('quiz_attempts')
            ->join('users', 'quiz_attempts.student_id', '=', 'users.id')
            ->join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id')
            ->join('courses', 'quizzes.course_id', '=', 'courses.id')
            ->select(
                'users.name as student_name',
                'courses.title as course_title',
                'quiz_attempts.score'
            )
            ->orderBy('quiz_attempts.score')
            ->limit(10)
            ->get();

        // Nilai rata-rata keseluruhan
        $rataKeseluruhan = DB::table('quiz_attempts')->avg('score') ?? 0;

        // Total kelas terbit
        $totalKelas = DB::table('courses')->where('status', 'published')->count();

        // Detail nilai per kelas (dari tertinggi ke terendah)
        $nilaiPerKelas = DB::table('quiz_attempts')
            ->join('users', 'quiz_attempts.student_id', '=', 'users.id')
            ->join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id')
            ->join('courses', 'quizzes.course_id', '=', 'courses.id')
            ->select(
                'courses.id as course_id',
                'courses.title as course_title',
                'users.name as student_name',
                'quiz_attempts.score'
            )
            ->orderBy('courses.id')
            ->orderByDesc('quiz_attempts.score')
            ->get()
            ->groupBy('course_id');

        return view('dashboard.pimpinan', compact(
            'rataRata',
            'nilaiTertinggi',
            'nilaiTerendah',
            'rataKeseluruhan',
            'totalKelas',
            'nilaiPerKelas'
        ));

    }
}