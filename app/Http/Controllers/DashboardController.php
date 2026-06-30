<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\User;
use App\Models\Syllabus;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Material;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Routing pimpinan
        if ($user->hasRole('pimpinan')) {
            return redirect()->route('dashboard.pimpinan');
        }

        // 2. Inisialisasi variabel
        $stats = [
            'total_users' => 0,
            'total_courses' => 0,
            'total_syllabus' => 0,
            'teacher_materials_count' => 0,
            'pending_grading_count' => 0
        ];
        
        $courses = collect();
        $taughtCourses = collect();
        $activeCourses = collect();
        $upcomingAssignments = collect();

        // 3. Logic untuk Super Admin & Akademik
        if ($user->hasRole('super_admin') || $user->hasRole('akademik')) {
        $stats['total_users'] = User::count();
        $stats['total_courses'] = Course::count();
        $stats['total_syllabus'] = Syllabus::count();
        }

        // 4. Logic untuk Pengajar
        if ($user->hasRole('pengajar')) {
<<<<<<< HEAD
            $taughtCourses = $user->taughtCourses()->get();
            $courseIds = $taughtCourses->pluck('id')->all();
            
            $stats['teacher_materials_count'] = Material::whereHas('module', function ($q) use ($courseIds) {
                $q->whereIn('course_id', $courseIds);
            })->count();
            $stats['pending_grading_count'] = AssignmentSubmission::where('status', 'pending')
                ->whereHas('assignment', function ($q) use ($courseIds) {
                    $q->whereIn('course_id', $courseIds);
                })->count();
        }
=======
        $taughtCourses = $user->taughtCourses()->where('status', 'published')->get();
        $courseIds = $taughtCourses->pluck('id')->all();
    
        $stats['teacher_materials_count'] = Material::whereIn('module_id', $courseIds)->count();
    
        $assignmentIds = \App\Models\Assignment::whereIn('course_id', $courseIds)->pluck('id');

         $stats['pending_grading_count'] = AssignmentSubmission::where('status', 'pending')
        ->whereIn('assignment_id', $assignmentIds) 
        ->count();
}
>>>>>>> e076b69bd71db20a62e645dac44e8fb8cb6d8817

        // 5. Logic untuk Pelajar
        if ($user->hasRole('pelajar')) {
            $activeCourses = $user->enrolledCourses()->where('enrollments.status', 'active')->get();
            $upcomingAssignments = Assignment::where('due_date', '>=', now())->take(5)->get();
        }

        // 6. Return ke view 'dashboard'
        return view('dashboard', compact(
            'stats', 
            'courses', 
            'taughtCourses', 
            'activeCourses', 
            'upcomingAssignments'
        ));
    }
}