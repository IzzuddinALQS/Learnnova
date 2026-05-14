<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;

class MateriHubController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermission('materials.view')) {
            abort(403, 'Unauthorized action.');
        }

        $user = Auth::user();

        // ── Pengajar: kelas yang diajar ──────────────────────────────────
        if ($user->hasRole('pengajar')) {
            $courses = Course::whereHas('instructors', fn($q) => $q->where('users.id', $user->id))
                ->with('instructors')
                ->orderByDesc('published_at')
                ->get();

            // Kalau hanya 1 kelas, langsung redirect ke materi kelas itu
            if ($courses->count() === 1) {
                return redirect()->route('courses.materials.index', $courses->first()->id);
            }

            return view('materi.hub', [
                'courses' => $courses,
                'role'    => 'pengajar',
            ]);
        }

        // ── Pelajar: kelas yang diikuti ──────────────────────────────────
        if ($user->hasRole('pelajar')) {
            $courses = Course::whereHas('enrollments', fn($q) =>
                    $q->where('student_id', $user->id)->where('status', 'active'))
                ->with('instructors')
                ->where('status', 'published')
                ->orderByDesc('published_at')
                ->get();

            // Kalau hanya 1 kelas, langsung redirect
            if ($courses->count() === 1) {
                return redirect()->route('courses.materials.index', $courses->first()->id);
            }

            return view('materi.hub', [
                'courses' => $courses,
                'role'    => 'pelajar',
            ]);
        }

        // ── Super admin / Akademik: semua kelas ─────────────────────────
        $courses = Course::with('instructors')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get();

        return view('materi.hub', [
            'courses' => $courses,
            'role'    => 'admin',
        ]);
    }
}
