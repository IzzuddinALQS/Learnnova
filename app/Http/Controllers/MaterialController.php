<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Bookmark;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Material;
use App\Models\MaterialProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    /**
     * Show the form for creating a new material.
     */
    public function create($courseId)
    {
        if (!Auth::user()->hasPermission('materials.create')) {
            abort(403, 'Unauthorized action.');
        }

        $course  = Course::findOrFail($courseId);
        $modules = $course->modules()->get();

        return view('materials.create', compact('course', 'modules'));
    }

    /**
     * Store a newly created material.
     */
    public function store(Request $request, $courseId)
    {
        if (!Auth::user()->hasPermission('materials.create')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $validator = validator($request->all(), [
            'title'            => 'required|string|max:255',
            'module_id'        => 'required|exists:course_modules,id',
            'type'             => 'required|in:video,pdf,text,link,audio,image',
            'order'            => 'required|integer|min:0',
            'is_preview'       => 'boolean',
            'duration_minutes' => 'nullable|integer|min:0',
            'content'          => 'nullable|string',
            'file_path'        => 'required_if:type,pdf,image,audio|nullable|file|mimes:pdf,jpg,jpeg,png,webp,mp3,wav,mp4,webm|max:102400',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $filePath = null;
        if ($request->hasFile('file_path')) {
            $type     = $request->type;
            $filePath = $request->file('file_path')->store("materials/{$type}", 'public');
        }

        $material = Material::create([
            'module_id'        => $request->module_id,
            'title'            => $request->title,
            'type'             => $request->type,
            'file_path'        => $filePath,
            'content'          => $request->content,
            'duration_minutes' => $request->duration_minutes,
            'order'            => $request->order,
            'is_preview'       => $request->boolean('is_preview'),
        ]);

        ActivityLog::log('Materi baru ditambahkan: ' . $material->title, 'materials', $material);

        return response()->json([
            'message'  => 'Materi berhasil ditambahkan.',
            'redirect' => route('courses.materials.index', $courseId),
        ]);
    }

    /**
     * Display the specified material.
     */
    public function show($courseId, Material $material)
    {
        if (!Auth::user()->hasPermission('materials.view')) {
            abort(403, 'Unauthorized action.');
        }

        $course = Course::findOrFail($courseId);

        $isEnrolled = DB::table('enrollments')
            ->where('course_id', $courseId)
            ->where('student_id', Auth::id())
            ->where('status', 'active')
            ->exists();

        $isPengajar = Auth::user()->hasPermission('materials.create');

        // Block non-enrolled students from non-preview materials
        if (!$isPengajar && !$isEnrolled && !$material->is_preview) {
            abort(403, 'Anda belum terdaftar di kelas ini.');
        }

        $progress     = $material->progressFor(Auth::id());
        $isBookmarked = $material->isBookmarkedBy(Auth::id());

        // Load semua bab + materi untuk sidebar navigasi
        $modules = $course->modules()->with(['materials' => function ($q) {
            $q->orderBy('order');
        }])->get();

        // Flatten semua materi berurutan (bab order → materi order)
        $allMaterials = $modules->flatMap(fn($m) => $m->materials)->values();

        // Cari posisi materi saat ini
        $currentIndex = $allMaterials->search(fn($m) => $m->id === $material->id);
        $prevMaterial = $currentIndex > 0 ? $allMaterials[$currentIndex - 1] : null;
        $nextMaterial = $currentIndex < $allMaterials->count() - 1 ? $allMaterials[$currentIndex + 1] : null;

        // Progress semua materi (untuk checklist di sidebar)
        $completedIds = [];
        if ($isEnrolled && !$isPengajar) {
            $completedIds = MaterialProgress::where('student_id', Auth::id())
                ->whereIn('material_id', $allMaterials->pluck('id'))
                ->where('is_completed', true)
                ->pluck('material_id')
                ->toArray();
        }

        // AJAX request — return JSON payload untuk swap konten
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'material' => [
                    'id'               => $material->id,
                    'title'            => $material->title,
                    'type'             => $material->type,
                    'content'          => $material->content,
                    'file_path'        => $material->file_path
                        ? asset('storage/' . $material->file_path)
                        : null,
                    'duration_minutes' => $material->duration_minutes,
                    'is_preview'       => $material->is_preview,
                    'url'              => route('courses.materials.show', [$courseId, $material->id]),
                    'edit_url'         => $isPengajar
                        ? route('courses.materials.edit', [$courseId, $material->id])
                        : null,
                    'delete_url'       => $isPengajar
                        ? route('courses.materials.destroy', [$courseId, $material->id])
                        : null,
                ],
                'progress' => [
                    'is_completed'  => $progress?->is_completed ?? false,
                    'completed_at'  => $progress?->completed_at?->format('d M Y H:i'),
                    'last_position' => $progress?->last_position ?? 0,
                ],
                'is_bookmarked' => $isBookmarked,
                'prev_url' => $prevMaterial
                    ? route('courses.materials.show', [$courseId, $prevMaterial->id])
                    : null,
                'next_url' => $nextMaterial
                    ? route('courses.materials.show', [$courseId, $nextMaterial->id])
                    : null,
                'completed_ids' => $completedIds,
            ]);
        }

        return view('materials.show', compact(
            'course',
            'material',
            'isEnrolled',
            'isPengajar',
            'progress',
            'isBookmarked',
            'modules',
            'allMaterials',
            'prevMaterial',
            'nextMaterial',
            'completedIds'
        ));
    }

    /**
     * Show the form for editing the specified material.
     */
    public function edit($courseId, Material $material)
    {
        if (!Auth::user()->hasPermission('materials.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $course  = Course::findOrFail($courseId);
        $modules = $course->modules()->get();

        return view('materials.edit', compact('course', 'material', 'modules'));
    }

    /**
     * Update the specified material.
     */
    public function update(Request $request, $courseId, Material $material)
    {
        if (!Auth::user()->hasPermission('materials.edit')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        // file_path required only if type needs file AND no existing file
        $fileRequired = in_array($request->type, ['pdf', 'image', 'audio']) && !$material->file_path;

        $validator = validator($request->all(), [
            'title'            => 'required|string|max:255',
            'module_id'        => 'required|exists:course_modules,id',
            'type'             => 'required|in:video,pdf,text,link,audio,image',
            'order'            => 'required|integer|min:0',
            'is_preview'       => 'boolean',
            'duration_minutes' => 'nullable|integer|min:0',
            'content'          => 'nullable|string',
            'file_path'        => ($fileRequired ? 'required' : 'nullable') . '|file|mimes:pdf,jpg,jpeg,png,webp,mp3,wav,mp4,webm|max:102400',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $filePath = $material->file_path;

        if ($request->hasFile('file_path')) {
            // Delete old file
            if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
                try {
                    Storage::disk('public')->delete($material->file_path);
                } catch (\Exception $e) {
                    Log::warning('Failed to delete old material file: ' . $e->getMessage());
                }
            }

            $type     = $request->type;
            $filePath = $request->file('file_path')->store("materials/{$type}", 'public');
        }

        $material->update([
            'module_id'        => $request->module_id,
            'title'            => $request->title,
            'type'             => $request->type,
            'file_path'        => $filePath,
            'content'          => $request->content,
            'duration_minutes' => $request->duration_minutes,
            'order'            => $request->order,
            'is_preview'       => $request->boolean('is_preview'),
        ]);

        ActivityLog::log('Materi diperbarui: ' . $material->title, 'materials', $material);

        return response()->json([
            'message'  => 'Materi berhasil diperbarui.',
            'redirect' => route('courses.materials.index', $courseId),
        ]);
    }

    /**
     * Soft delete the specified material and remove its file.
     */
    public function destroy($courseId, Material $material)
    {
        if (!Auth::user()->hasPermission('materials.delete')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        // Delete file from storage
        if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
            Storage::disk('public')->delete($material->file_path);
        }

        $material->delete();

        ActivityLog::log('Materi dihapus: ' . $material->title, 'materials');

        return response()->json(['message' => 'Materi berhasil dihapus.']);
    }

    /**
     * Update or create progress record for the current student.
     */
    public function progress(Request $request, $courseId, Material $material)
    {
        // Hanya pelajar yang enrolled yang bisa update progress
        if (!Auth::user()->hasPermission('materials.view')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $isEnrolled = DB::table('enrollments')
            ->where('course_id', $courseId)
            ->where('student_id', Auth::id())
            ->where('status', 'active')
            ->exists();

        $isPengajar = Auth::user()->hasPermission('materials.create');

        if (!$isEnrolled && !$isPengajar) {
            return response()->json(['message' => 'Anda belum terdaftar di kelas ini.'], 403);
        }

        $validator = validator($request->all(), [
            'is_completed'  => 'boolean',
            'last_position' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $isCompleted = $request->boolean('is_completed', false);
        $data        = [
            'is_completed'  => $isCompleted,
            'last_position' => $request->input('last_position', 0),
        ];

        if ($isCompleted) {
            $data['completed_at'] = now();
        }

        $progress = MaterialProgress::updateOrCreate(
            [
                'student_id'  => Auth::id(),
                'material_id' => $material->id,
            ],
            $data
        );

        return response()->json([
            'message'       => $isCompleted ? 'Materi ditandai selesai.' : 'Progress disimpan.',
            'is_completed'  => $progress->is_completed,
            'last_position' => $progress->last_position,
        ]);
    }

    /**
     * Toggle bookmark for the current student.
     */
    public function bookmark(Request $request, $courseId, Material $material)
    {
        // Hanya pelajar yang enrolled yang bisa bookmark
        if (!Auth::user()->hasPermission('materials.view')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $isEnrolled = DB::table('enrollments')
            ->where('course_id', $courseId)
            ->where('student_id', Auth::id())
            ->where('status', 'active')
            ->exists();

        $isPengajar = Auth::user()->hasPermission('materials.create');

        if (!$isEnrolled && !$isPengajar) {
            return response()->json(['message' => 'Anda belum terdaftar di kelas ini.'], 403);
        }

        $existing = Bookmark::where('student_id', Auth::id())
            ->where('material_id', $material->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json([
                'message'      => 'Bookmark dihapus.',
                'is_bookmarked' => false,
            ]);
        }

        Bookmark::create([
            'student_id'  => Auth::id(),
            'material_id' => $material->id,
            'note'        => $request->input('note'),
        ]);

        return response()->json([
            'message'      => 'Materi berhasil di-bookmark.',
            'is_bookmarked' => true,
        ]);
    }
}
