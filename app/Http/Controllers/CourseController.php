<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Course;
use App\Models\Role;
use App\Models\Syllabus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermission('courses.view')) {
            abort(403, 'Unauthorized action.');
        }

        $query = Course::query()->with('instructors');

        // Pengajar: lihat kelas yang diajar (via pivot)
        if (Auth::user()->hasRole('pengajar')) {
            $query->whereHas('instructors', fn($q) => $q->where('users.id', Auth::id()));
        }

        // Pelajar: hanya kelas yang sudah dipublish dan diikuti
        if (Auth::user()->hasRole('pelajar')) {
            $query->whereHas('enrollments', fn($q) => $q->where('student_id', Auth::id())->where('status', 'active'));
        }

        $courses = $query
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get();

        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        if (!Auth::user()->hasPermission('courses.create')) {
            abort(403, 'Unauthorized action.');
        }

        $syllabuses  = Syllabus::orderBy('name')->get();
        $instructors = $this->getInstructors();

        return view('courses.form', compact('syllabuses', 'instructors'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('courses.create')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string',
            'syllabus_id'     => 'nullable|exists:syllabus,id',
            'instructor_ids'  => 'required|array|min:1',
            'instructor_ids.*'=> 'exists:users,id',
            'status'          => 'required|in:draft,published,archived',
            'duration_weeks'  => 'nullable|integer|min:1',
            'max_students'    => 'nullable|integer|min:1',
            'thumbnail'       => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        // Pengajar pertama di array = pengajar utama, simpan juga ke instructor_id (backward-compat)
        $validated['instructor_id'] = $validated['instructor_ids'][0];

        $course = Course::create($validated);

        // Sync ke pivot — pengajar pertama is_primary = true
        $this->syncInstructors($course, $validated['instructor_ids']);

        ActivityLog::log('Kelas baru dibuat: ' . $course->title, 'courses', $course);

        return response()->json([
            'message'  => 'Kelas berhasil dibuat.',
            'redirect' => route('courses.index'),
        ]);
    }

    public function show(string $id)
    {
        if (!Auth::user()->hasPermission('courses.view')) {
            abort(403, 'Unauthorized action.');
        }

        $course = Course::with('instructors')->findOrFail($id);

        // Pelajar tidak boleh membuka kelas yang tidak diikutinya
        if (Auth::user()->hasRole('pelajar')) {
            if ($course->status !== 'published') {
                abort(403, 'Kelas belum dipublish.');
            }
            if (!$course->isEnrolled(Auth::id())) {
                abort(403, 'Anda belum terdaftar di kelas ini.');
            }
        }

        // Pengajar hanya boleh membuka kelas yang diajarnya
        if (Auth::user()->hasRole('pengajar')) {
            $isMember = $course->instructors->contains('id', Auth::id());
            if (!$isMember) {
                abort(403, 'Unauthorized action.');
            }
        }

        return view('courses.show', compact('course'));
    }

    public function edit(string $id)
    {
        if (!Auth::user()->hasPermission('courses.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $course      = Course::with('instructors')->findOrFail($id);
        $syllabuses  = Syllabus::orderBy('name')->get();
        $instructors = $this->getInstructors();

        return view('courses.form', compact('course', 'syllabuses', 'instructors'));
    }

    public function update(Request $request, string $id)
    {
        if (!Auth::user()->hasPermission('courses.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string',
            'syllabus_id'     => 'nullable|exists:syllabus,id',
            'instructor_ids'  => 'required|array|min:1',
            'instructor_ids.*'=> 'exists:users,id',
            'status'          => 'required|in:draft,published,archived',
            'duration_weeks'  => 'nullable|integer|min:1',
            'max_students'    => 'nullable|integer|min:1',
            'thumbnail'       => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        } else {
            unset($validated['thumbnail']);
        }

        if ($validated['status'] === 'published' && !$course->published_at) {
            $validated['published_at'] = now();
        }

        // Update instructor_id (backward-compat) ke pengajar pertama
        $validated['instructor_id'] = $validated['instructor_ids'][0];

        $course->update($validated);

        // Sync pivot
        $this->syncInstructors($course, $validated['instructor_ids']);

        ActivityLog::log('Kelas diperbarui: ' . $course->title, 'courses', $course);

        return response()->json([
            'message'  => 'Kelas berhasil diperbarui.',
            'redirect' => route('courses.index'),
        ]);
    }

    public function destroy(string $id)
    {
        if (!Auth::user()->hasPermission('courses.delete')) {
            abort(403, 'Unauthorized action.');
        }

        $course = Course::findOrFail($id);
        $course->delete();

        ActivityLog::log('Kelas dihapus: ' . $course->title, 'courses');

        return response()->json(['message' => 'Kelas berhasil dihapus.']);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function getInstructors()
    {
        $pengajarRoleId = Role::where('name', 'pengajar')->value('id');
        return User::whereHas('roles', fn($q) => $q->where('roles.id', $pengajarRoleId))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Sync tabel course_instructors.
     * Pengajar pertama dalam array = is_primary true.
     */
    private function syncInstructors(Course $course, array $instructorIds): void
    {
        $syncData = [];
        foreach ($instructorIds as $index => $userId) {
            $syncData[$userId] = ['is_primary' => $index === 0];
        }
        $course->instructors()->sync($syncData);
    }
}
