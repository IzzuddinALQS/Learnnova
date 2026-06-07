<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    /**
     * Halaman manajemen enrollment per kelas (akademik/super_admin)
     */
    public function index(Course $course)
    {
        if (!Auth::user()->hasPermission('courses.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $course->load('instructor');

        // Pelajar yang sudah terdaftar
        $enrollments = Enrollment::with('student')
            ->where('course_id', $course->id)
            ->latest()
            ->get();

        // Semua pelajar yang belum terdaftar di kelas ini
        $enrolledStudentIds = $enrollments->pluck('student_id');
        $pelajarRoleId = Role::where('name', 'pelajar')->value('id');
        $availableStudents = User::whereHas('roles', fn($q) => $q->where('roles.id', $pelajarRoleId))
            ->whereNotIn('id', $enrolledStudentIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('enrollments.index', compact('course', 'enrollments', 'availableStudents'));
    }

    /**
     * Daftarkan pelajar ke kelas
     */
    public function store(Request $request, Course $course)
    {
        if (!Auth::user()->hasPermission('courses.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'student_ids'   => 'required|array|min:1',
            'student_ids.*' => 'exists:users,id',
        ]);

        $added = 0;
        foreach ($request->student_ids as $studentId) {
            // Cegah duplikat
            $exists = Enrollment::where('course_id', $course->id)
                ->where('student_id', $studentId)
                ->exists();

            if (!$exists) {
                Enrollment::create([
                    'course_id'   => $course->id,
                    'student_id'  => $studentId,
                    'status'      => 'active',
                    'enrolled_at' => now(),
                ]);
                $added++;
            }
        }

        ActivityLog::log($added . ' pelajar didaftarkan ke kelas: ' . $course->title, 'courses', $course);

        return response()->json([
            'message' => "$added pelajar berhasil didaftarkan ke kelas.",
        ]);
    }

    /**
     * Update status enrollment (active / completed / dropped)
     */
    public function update(Request $request, Course $course, Enrollment $enrollment)
    {
        if (!Auth::user()->hasPermission('courses.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'status' => 'required|in:active,completed,dropped',
        ]);

        $enrollment->update([
            'status'       => $request->status,
            'completed_at' => $request->status === 'completed' ? now() : null,
        ]);

        return response()->json(['message' => 'Status enrollment diperbarui.']);
    }

    /**
     * Hapus enrollment (keluarkan pelajar dari kelas)
     */
    public function destroy(Course $course, Enrollment $enrollment)
    {
        if (!Auth::user()->hasPermission('courses.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $enrollment->delete();

        ActivityLog::log('Pelajar dikeluarkan dari kelas: ' . $course->title, 'courses', $course);

        return response()->json(['message' => 'Pelajar berhasil dikeluarkan dari kelas.']);
    }

    /**
     * Halaman daftar semua pelajar beserta kelas yang diikuti (akademik)
     */
    public function studentOverview()
    {
        if (!Auth::user()->hasPermission('users.view')) {
            abort(403, 'Unauthorized action.');
        }

        $pelajarRoleId = Role::where('name', 'pelajar')->value('id');
        $students = User::whereHas('roles', fn($q) => $q->where('roles.id', $pelajarRoleId))
            ->with(['enrollments.course'])
            ->orderBy('name')
            ->paginate(15);

        return view('enrollments.students', compact('students'));
    }

    /**
     * Halaman daftar semua pengajar beserta kelas yang diajar (akademik)
     */
    public function teacherOverview()
    {
        if (!Auth::user()->hasPermission('users.view')) {
            abort(403, 'Unauthorized action.');
        }

        $pengajarRoleId = Role::where('name', 'pengajar')->value('id');
        $teachers = User::whereHas('roles', fn($q) => $q->where('roles.id', $pengajarRoleId))
            ->with(['taughtCourses' => fn($q) => $q->withPivot('is_primary')])
            ->orderBy('name')
            ->paginate(15);

        return view('enrollments.teachers', compact('teachers'));
    }
}
