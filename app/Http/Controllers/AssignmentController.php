<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Notifications\AssignmentGradedNotification;

class AssignmentController extends Controller
{
    private function getInstructorCourses()
    {
        if (Auth::user()->hasRole('super_admin')) {
            return Course::pluck('id');
        }

        return Course::where('instructor_id', Auth::id())
            ->orWhereHas('instructors', fn($q) => $q->where('user_id', Auth::id()))
            ->pluck('id');
    }

    private function authorizeAssignment(Assignment $assignment)
    {
        if (Auth::user()->hasRole('super_admin')) {
            return;
        }

        if ($assignment->created_by !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke tugas ini.');
        }
    }

    public function index()
    {
        if (!Auth::user()->hasPermission('assignments.view')) {
            abort(403, 'Unauthorized action.');
        }

        // Pelajar
        if (Auth::user()->hasRole('pelajar')) {
            $enrolledCourseIds = Auth::user()->enrolledCourses()->pluck('courses.id');

            $assignments = Assignment::with(['course', 'submissions' => function ($q) {
                $q->where('student_id', Auth::id());
            }])
                ->whereIn('course_id', $enrolledCourseIds)
                ->latest()
                ->get();

            return view('submissions.index', compact('assignments'));
        }

        // Super Admin
        if (Auth::user()->hasRole('super_admin')) {
            $assignments = Assignment::with('course')
                ->withCount('submissions')
                ->latest()
                ->get();
        } else {
            // Pengajar
            $courseIds   = $this->getInstructorCourses();
            $assignments = Assignment::with('course')
                ->withCount('submissions')
                ->whereIn('course_id', $courseIds)
                ->where('created_by', Auth::id())
                ->latest()
                ->get();
        }

        return view('assignments.index', compact('assignments'));
    }

    public function create()
    {
        if (!Auth::user()->hasPermission('assignments.create')) {
            abort(403, 'Unauthorized action.');
        }

        $courseIds = $this->getInstructorCourses();
        $courses   = Course::whereIn('id', $courseIds)->get();

        return view('assignments.form', compact('courses'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('assignments.create')) {
            abort(403, 'Unauthorized action.');
        }

        $courseIds = $this->getInstructorCourses();

        $validator = validator($request->all(), [
            'course_id'   => ['required', 'exists:courses,id', function ($attr, $val, $fail) use ($courseIds) {
                if (!$courseIds->contains($val)) $fail('Course tidak valid.');
            }],
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'due_date'    => 'required|date|after:now',
            'max_score'   => 'required|integer|min:1|max:100',
            'file'        => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,png,jpg,jpeg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = array_merge($validator->validated(), ['created_by' => Auth::id()]);

        if ($request->hasFile('file')) {
            $data['file'] = $request->file('file')->store('assignments/files', 'public');
        }

        Assignment::create($data);

        return response()->json([
            'message'  => 'Tugas berhasil dibuat.',
            'redirect' => route('assignments.index'),
        ]);
    }

    public function show(Assignment $assignment)
    {
        if (!Auth::user()->hasPermission('assignments.view')) {
            abort(403, 'Unauthorized action.');
        }

        // Pelajar
        if (Auth::user()->hasRole('pelajar')) {
            $enrolled = Auth::user()->enrolledCourses()->where('courses.id', $assignment->course_id)->exists();
            if (!$enrolled) {
                abort(403, 'Anda tidak terdaftar di kelas ini.');
            }

            $assignment->load('course');
            $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
                ->where('student_id', Auth::id())
                ->first();

            return view('submissions.show', compact('assignment', 'submission'));
        }

        // Pengajar & Super Admin
        $this->authorizeAssignment($assignment);
        $assignment->load(['course', 'creator']);
        $assignment->loadCount('submissions');

        return view('assignments.show', compact('assignment'));
    }

    public function edit(Assignment $assignment)
    {
        if (!Auth::user()->hasPermission('assignments.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $this->authorizeAssignment($assignment);

        $courseIds = $this->getInstructorCourses();
        $courses   = Course::whereIn('id', $courseIds)->get();

        return view('assignments.form', compact('assignment', 'courses'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        if (!Auth::user()->hasPermission('assignments.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $this->authorizeAssignment($assignment);

        $courseIds = $this->getInstructorCourses();

        $validator = validator($request->all(), [
            'course_id'   => ['required', 'exists:courses,id', function ($attr, $val, $fail) use ($courseIds) {
                if (!$courseIds->contains($val)) $fail('Course tidak valid.');
            }],
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'due_date'    => 'required|date|after:now',
            'max_score'   => 'required|integer|min:1|max:100',
            'file'        => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,png,jpg,jpeg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        if ($request->hasFile('file')) {
            if ($assignment->file && Storage::disk('public')->exists($assignment->file)) {
                Storage::disk('public')->delete($assignment->file);
            }
            $data['file'] = $request->file('file')->store('assignments/files', 'public');
        } else {
            unset($data['file']);
        }

        $assignment->update($data);

        return response()->json([
            'message'  => 'Tugas berhasil diperbarui.',
            'redirect' => route('assignments.show', $assignment->id),
        ]);
    }

    public function destroy(Assignment $assignment)
    {
        if (!Auth::user()->hasPermission('assignments.delete')) {
            abort(403, 'Unauthorized action.');
        }

        $this->authorizeAssignment($assignment);

        if ($assignment->file && Storage::disk('public')->exists($assignment->file)) {
            Storage::disk('public')->delete($assignment->file);
        }

        $assignment->delete();

        return response()->json(['message' => 'Tugas berhasil dihapus.']);
    }

    public function submit(Request $request, Assignment $assignment)
    {
        if (!Auth::user()->hasPermission('assignments.submit')) {
            abort(403, 'Unauthorized action.');
        }

        if ($assignment->due_date->isPast()) {
            return response()->json(['message' => 'Deadline telah lewat.'], 422);
        }

        $existing = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', Auth::id())
            ->first();

        // Graded tidak bisa diubah, returned boleh submit ulang
        if ($existing && $existing->status === 'graded') {
            return response()->json(['message' => 'Tugas sudah dinilai, tidak bisa diubah.'], 422);
        }

        $isDraft = $request->input('action') === 'draft';

        $validator = validator($request->all(), [
            'file_path'   => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,png,jpg,jpeg|max:2048',
            'text_answer' => 'nullable|string',
            'note'        => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Jika bukan draft, minimal harus ada jawaban
        if (!$isDraft) {
            $hasFile    = $request->hasFile('file_path');
            $hasText    = !empty(trim($request->text_answer ?? ''));
            $hasOldFile = $existing?->file_path !== null;

            if (!$hasFile && !$hasText && !$hasOldFile) {
                return response()->json([
                    'errors' => [
                        'text_answer' => ['Harap isi jawaban teks atau upload file sebelum mengumpulkan.']
                    ]
                ], 422);
            }
        }

        $data = [
            'assignment_id' => $assignment->id,
            'student_id'    => Auth::id(),
            'text_answer'   => $request->text_answer,
            'note'          => $request->note,
            'status'        => $isDraft ? 'draft' : 'submitted',
            'submitted_at'  => $isDraft ? null : now(),
        ];

        if ($request->hasFile('file_path')) {
            if ($existing?->file_path && Storage::disk('public')->exists($existing->file_path)) {
                Storage::disk('public')->delete($existing->file_path);
            }
            $data['file_path'] = $request->file('file_path')->store('submissions/files', 'public');
        }

        AssignmentSubmission::updateOrCreate(
            ['assignment_id' => $assignment->id, 'student_id' => Auth::id()],
            $data
        );

        $message = $isDraft ? 'Draft berhasil disimpan.' : 'Tugas berhasil dikumpulkan.';

        return response()->json([
            'message'  => $message,
            'redirect' => route('assignments.show', $assignment->id),
        ]);
    }

    // Daftar submission per tugas
    public function submissions(Assignment $assignment)
    {
        if (!Auth::user()->hasPermission('assignments.grade')) {
            abort(403, 'Unauthorized action.');
        }

        $this->authorizeAssignment($assignment);

        $assignment->load('course');
        $submissions = AssignmentSubmission::with('student')
            ->where('assignment_id', $assignment->id)
            ->latest()
            ->get();

        return view('assignments.submissions', compact('assignment', 'submissions'));
    }

    // Form grading
    public function gradeForm(Assignment $assignment, AssignmentSubmission $submission)
    {
        if (!Auth::user()->hasPermission('assignments.grade')) {
            abort(403, 'Unauthorized action.');
        }

        $this->authorizeAssignment($assignment);
        $assignment->load('course');
        $submission->load('student');

        return view('assignments.grade', compact('assignment', 'submission'));
    }

    // Simpan nilai & feedback
    public function grade(Request $request, Assignment $assignment, AssignmentSubmission $submission)
    {
        if (!Auth::user()->hasPermission('assignments.grade')) {
            abort(403, 'Unauthorized action.');
        }

        $this->authorizeAssignment($assignment);

        $validator = validator($request->all(), [
            'score'    => 'required|numeric|min:0|max:' . $assignment->max_score,
            'feedback' => 'nullable|string',
            'status'   => 'required|in:graded,returned',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $submission->update([
            'score'      => $request->score,
            'feedback'   => $request->feedback,
            'status'     => $request->status,
            'graded_at'  => now(),
        ]);

        // Kirim notifikasi ke pelajar
        $submission->student->notify(
            new \App\Notifications\AssignmentGradedNotification($assignment, $submission)
        );

        return response()->json([
            'message'  => 'Nilai berhasil disimpan.',
            'redirect' => route('assignments.submissions', $assignment->id),
        ]);
    }
}
