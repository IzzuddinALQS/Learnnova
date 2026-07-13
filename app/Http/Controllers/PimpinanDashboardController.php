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

        // Total kelas terbit
        $courses = \App\Models\Course::with(['assignments', 'quizzes.questions'])->where('status', 'published')->get();
        $totalKelas = $courses->count();

        $gradebookController = new \App\Http\Controllers\GradebookController();
        $allStudentScores = [];

        foreach ($courses as $course) {
            $students = \App\Models\User::whereHas('enrollments', fn($q) => $q->where('course_id', $course->id)->where('status', 'active'))->get();
            $courseGradebook = $gradebookController->buildCourseGradebook($course, $students);
            foreach ($courseGradebook as $row) {
                if ($row['final_score'] !== null) {
                    $allStudentScores[] = [
                        'course_id'    => $course->id,
                        'course_title' => $course->title,
                        'student_name' => $row['student']->name,
                        'final_score'  => $row['final_score']
                    ];
                }
            }
        }

        $validScores = collect($allStudentScores);

        // Rata-rata nilai per kelas (diurutkan tertinggi ke terendah)
        $rataRata = $validScores->groupBy('course_id')->map(function ($group) {
            return (object)[
                'id'        => $group->first()['course_id'],
                'title'     => $group->first()['course_title'],
                'rata_rata' => round($group->avg('final_score'), 2)
            ];
        })->sortByDesc('rata_rata')->values();

        // Nilai tertinggi secara keseluruhan (Top 10)
        $nilaiTertinggi = $validScores->sortByDesc('final_score')->take(10)->map(function ($item) {
            return (object)[
                'student_name' => $item['student_name'],
                'course_title' => $item['course_title'],
                'score'        => $item['final_score']
            ];
        })->values();

        // Nilai terendah secara keseluruhan (Bottom 10)
        $nilaiTerendah = $validScores->sortBy('final_score')->take(10)->map(function ($item) {
            return (object)[
                'student_name' => $item['student_name'],
                'course_title' => $item['course_title'],
                'score'        => $item['final_score']
            ];
        })->values();

        // Nilai rata-rata keseluruhan
        $rataKeseluruhan = $validScores->count() > 0 ? round($validScores->avg('final_score'), 2) : 0;

        // Detail nilai per kelas (dari tertinggi ke terendah)
        $nilaiPerKelas = $validScores->groupBy('course_id')->map(function ($group) {
            return $group->sortByDesc('final_score')->map(function ($item) {
                return (object)[
                    'course_id'    => $item['course_id'],
                    'course_title' => $item['course_title'],
                    'student_name' => $item['student_name'],
                    'score'        => $item['final_score']
                ];
            })->values();
        });

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