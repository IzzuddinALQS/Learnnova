<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
<<<<<<< Updated upstream
=======
use Illuminate\Support\Facades\Storage;
use App\Notifications\AssignmentGradedNotification;
use App\Models\ActivityLog;
>>>>>>> Stashed changes

class AssignmentController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermission('assignments.view')) {
            abort(403, 'Unauthorized action.');
        }

        return view('assignments.index');
    }

<<<<<<< Updated upstream
    public function show($id)
=======
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

        $assignment = Assignment::create($data);

        ActivityLog::log("Membuat tugas baru: {$assignment->title}", $assignment, $assignment->toArray(), 'assignment');

        return response()->json([
            'message'  => 'Tugas berhasil dibuat.',
            'redirect' => route('assignments.index'),
        ]);
    }

    public function show(Assignment $assignment)
>>>>>>> Stashed changes
    {
        if (!Auth::user()->hasPermission('assignments.view')) {
            abort(403, 'Unauthorized action.');
        }

        return view('assignments.detail-tugas');
    }

<<<<<<< Updated upstream
=======
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

        ActivityLog::log("Memperbarui tugas: {$assignment->title}", $assignment, $assignment->getChanges(), 'assignment');

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

        $title = $assignment->title;
        $assignment->delete();

        ActivityLog::log("Menghapus tugas: {$title}", $assignment, ['title' => $title], 'assignment');

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

        if ($existing && $existing->status === 'graded') {
            return response()->json(['message' => 'Tugas sudah dinilai, tidak bisa diubah.'], 422);
        }

        $validator = validator($request->all(), [
            'file_path'   => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,png,jpg,jpeg|max:2048',
            'text_answer' => 'nullable|string',
            'note'        => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Wajib ada jawaban
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

        $data = [
            'assignment_id' => $assignment->id,
            'student_id'    => Auth::id(),
            'text_answer'   => $request->text_answer,
            'note'          => $request->note,
            'status'        => 'submitted',
            'submitted_at'  => now(),
        ];

        if ($request->hasFile('file_path')) {
            if ($existing?->file_path && Storage::disk('public')->exists($existing->file_path)) {
                Storage::disk('public')->delete($existing->file_path);
            }
            $data['file_path'] = $request->file('file_path')->store('submissions/files', 'public');
        }

        $submission = AssignmentSubmission::updateOrCreate(
            ['assignment_id' => $assignment->id, 'student_id' => Auth::id()],
            $data
        );

        ActivityLog::log("Mengumpulkan tugas: {$assignment->title}", $submission, [
            'assignment_id' => $assignment->id,
            'assignment_title' => $assignment->title,
            'student_id' => Auth::id(),
            'student_name' => Auth::user()->name,
        ], 'assignment');

        return response()->json([
            'message'  => 'Tugas berhasil dikumpulkan.',
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

        ActivityLog::log("Menilai tugas: {$assignment->title} untuk siswa {$submission->student->name}", $submission, [
            'assignment_id' => $assignment->id,
            'assignment_title' => $assignment->title,
            'student_id' => $submission->student_id,
            'student_name' => $submission->student->name,
            'score' => $request->score,
            'status' => $request->status,
        ], 'assignment');

        // Kirim notifikasi ke pelajar
        $submission->student->notify(
            new \App\Notifications\AssignmentGradedNotification($assignment, $submission)
        );

        return response()->json([
            'message'  => 'Nilai berhasil disimpan.',
            'redirect' => route('assignments.submissions', $assignment->id),
        ]);
    }
>>>>>>> Stashed changes
}
