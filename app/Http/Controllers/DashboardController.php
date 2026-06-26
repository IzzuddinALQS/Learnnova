<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Enrollment;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('pimpinan')) {

     return redirect()->route('dashboard.pimpinan');
}

        $announcements = [];

        $courses = collect();

        $pengajarStats = [
            'published_courses'  => 0,
            'active_students'    => 0,
            'active_enrollments' => 0,
            'completion_rate'     => 0,
            'material_views'      => 0,
        ];

        $completionRate = 0;

        $totalViews = 0;

        $pimpinanStats = [

            'average_per_class' => collect(),

            'highest_score' => null,

            'lowest_score' => null,

            'overall_average' => 0,

        ];

        /*
        |--------------------------------------------------------------------------
        | PELAJAR
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('pelajar')) {

            $announcements = Announcement::where('is_published', true)

                ->whereNotNull('published_at')

                ->where('published_at', '<=', now())

                ->orderByDesc('published_at')

                ->take(3)

                ->get();

            $courses = $user->enrolledCourses()

                ->where('courses.status', 'published')

                ->where('enrollments.status', 'active')

                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | PENGAJAR
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('pengajar')) {

            $courses = $user->taughtCourses()

                ->where('status', 'published')

                ->get();

            $courseIds = $courses->pluck('id')->all();

            $pengajarStats['published_courses'] = $courses->count();

            if (!empty($courseIds)) {

                $pengajarStats['active_enrollments'] = Enrollment::whereIn('course_id', $courseIds)

                    ->where('status', 'active')

                    ->count();

                $pengajarStats['active_students'] = Enrollment::whereIn('course_id', $courseIds)

                    ->where('status', 'active')

                    ->distinct('student_id')

                    ->count('student_id');
            }

            $totalMateri = DB::table('materials')

                ->count();

            $totalSelesai = DB::table('material_progress')

                ->where('is_completed', 1)

                ->count();

            if ($totalMateri > 0) {

                $completionRate = round(

                    ($totalSelesai / $totalMateri) * 100,

                    2

                );
            }

            $totalViews = DB::table('material_progress')

                ->count();
            $pengajarStats['completion_rate'] = $completionRate;
            $pengajarStats['material_views'] = $totalViews;
        }

        /*
        |--------------------------------------------------------------------------
        | PIMPINAN
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('pimpinan')) {

            $pimpinanStats['average_per_class'] = DB::table('courses')

                ->join('quizzes', 'courses.id', '=', 'quizzes.course_id')

                ->join('quiz_attempts', 'quizzes.id', '=', 'quiz_attempts.quiz_id')

                ->select(

                    'courses.title',

                    DB::raw('AVG(quiz_attempts.score) as average_score')

                )

                ->groupBy('courses.id', 'courses.title')

                ->get();

            $pimpinanStats['highest_score'] = DB::table('quiz_attempts')

                ->orderByDesc('score')

                ->first();

            $pimpinanStats['lowest_score'] = DB::table('quiz_attempts')

                ->orderBy('score')

                ->first();

            $pimpinanStats['overall_average'] = DB::table('quiz_attempts')

                ->avg('score');
        }

        return view(

            'dashboard',

            compact(

                'announcements',

                'courses',

                'pengajarStats',

                'completionRate',

                'totalViews',

                'pimpinanStats',

            )

        );
    }
};