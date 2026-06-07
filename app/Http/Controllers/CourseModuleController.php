<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\MaterialProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourseModuleController extends Controller
{
    /**
     * Display the list of modules and materials for a course.
     */
    public function index($courseId)
    {
        if (!Auth::user()->hasPermission('materials.view')) {
            abort(403, 'Unauthorized action.');
        }

        $course = Course::findOrFail($courseId);

        // Load modules with their materials
        $modules = $course->modules()->with('materials')->get();

        // Check if current user is enrolled
        $isEnrolled = DB::table('enrollments')
            ->where('course_id', $courseId)
            ->where('student_id', Auth::id())
            ->where('status', 'active')
            ->exists();

        $isPengajar = Auth::user()->hasPermission('materials.create');

        // Calculate progress per module for enrolled students
        $moduleProgress = [];
        if ($isEnrolled && !$isPengajar) {
            foreach ($modules as $module) {
                $materialIds    = $module->materials->pluck('id');
                $totalMaterials = $materialIds->count();
                $completed      = 0;

                if ($totalMaterials > 0) {
                    $completed = MaterialProgress::where('student_id', Auth::id())
                        ->whereIn('material_id', $materialIds)
                        ->where('is_completed', true)
                        ->count();
                }

                $moduleProgress[$module->id] = [
                    'total'      => $totalMaterials,
                    'completed'  => $completed,
                    'percentage' => $totalMaterials > 0
                        ? round(($completed / $totalMaterials) * 100)
                        : 0,
                ];
            }
        }

        // For each material, check completion status for enrolled student
        $completedMaterialIds = [];
        if ($isEnrolled && !$isPengajar) {
            $allMaterialIds = $modules->flatMap(fn($m) => $m->materials->pluck('id'));
            $completedMaterialIds = MaterialProgress::where('student_id', Auth::id())
                ->whereIn('material_id', $allMaterialIds)
                ->where('is_completed', true)
                ->pluck('material_id')
                ->toArray();
        }

        return view('materials.index', compact(
            'course',
            'modules',
            'isEnrolled',
            'isPengajar',
            'moduleProgress',
            'completedMaterialIds'
        ));
    }

    /**
     * Store a newly created module.
     */
    public function store(Request $request, $courseId)
    {
        if (!Auth::user()->hasPermission('materials.create')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $validator = validator($request->all(), [
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'order'       => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $module = CourseModule::create([
            'course_id'   => $courseId,
            'title'       => $request->title,
            'description' => $request->description,
            'order'       => $request->order,
        ]);

        ActivityLog::log('Bab baru ditambahkan: ' . $module->title, 'materials', $module);

        return response()->json([
            'message'  => 'Bab berhasil ditambahkan.',
            'redirect' => route('courses.materials.index', $courseId),
        ]);
    }

    /**
     * Update the specified module.
     */
    public function update(Request $request, $courseId, CourseModule $module)
    {
        if (!Auth::user()->hasPermission('materials.edit')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $validator = validator($request->all(), [
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'order'       => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $module->update([
            'title'       => $request->title,
            'description' => $request->description,
            'order'       => $request->order,
        ]);

        ActivityLog::log('Bab diperbarui: ' . $module->title, 'materials', $module);

        return response()->json([
            'message'  => 'Bab berhasil diperbarui.',
            'redirect' => route('courses.materials.index', $courseId),
        ]);
    }

    /**
     * Remove the specified module (cascade deletes materials via FK).
     */
    public function destroy($courseId, CourseModule $module)
    {
        if (!Auth::user()->hasPermission('materials.delete')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $module->delete();

        ActivityLog::log('Bab dihapus: ' . $module->title, 'materials');

        return response()->json(['message' => 'Bab berhasil dihapus.']);
    }
}
