<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $announcements = [];
        $courses = collect();
        $pengajarStats = [
            'published_courses' => 0,
            'active_students' => 0,
            'active_enrollments' => 0,
        ];
        $user = Auth::user();

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
        }

        return view('dashboard', compact('announcements', 'courses', 'pengajarStats'));
    }
}
