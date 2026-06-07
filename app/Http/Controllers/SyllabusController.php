<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Syllabus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class SyllabusController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermission('syllabus.view')) {
            abort(403, 'Unauthorized action.');
        }

        $data_syllabi = Syllabus::withCount('courses')
            ->with('instructor')
            ->latest()
            ->get();
        return view('syllabus.index', compact('data_syllabi'));
    }

    public function create()
    {
        if (!Auth::user()->hasPermission('syllabus.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $images = array_values(array_diff(scandir(public_path('img')), ['.', '..']));
        $instructors = User::all(); 

        return view('syllabus.form', compact('images', 'instructors'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('syllabus.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = validator($request->all(), [
            'name'           => 'required|string|max:255',
            'theme'          => 'nullable|string|max:255',
            'description'    => 'required|string',
            'duration_weeks' => 'required|integer|min:1',
            'instructor_id'  => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $syllabus = Syllabus::create(array_merge($validator->validated(), [
            'created_by' => Auth::id(),
        ]));

        ActivityLog::log('Silabus baru dibuat: ' . $syllabus->name, 'syllabus', $syllabus);

        return response()->json([
            'message'  => 'Silabus berhasil dibuat.',
            'redirect' => route('syllabus.index'),
        ]);
    }

    public function show(Syllabus $syllabu)
    {
        if (!Auth::user()->hasPermission('syllabus.view')) {
            abort(403, 'Unauthorized action.');
        }

        $syllabu->loadCount('courses');
        $syllabu->load('instructor');
        return view('syllabus.show', ['syllabus' => $syllabu]);
    }

    public function edit(Syllabus $syllabu)
    {
        if (!Auth::user()->hasPermission('syllabus.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $images = array_values(array_diff(scandir(public_path('img')), ['.', '..']));
        $instructors = User::all(); 

        return view('syllabus.form', [
            'syllabus'    => $syllabu,
            'images'      => $images,
            'instructors' => $instructors, 
        ]);
    }

    public function update(Request $request, Syllabus $syllabu)
    {
        if (!Auth::user()->hasPermission('syllabus.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = validator($request->all(), [
            'name'           => 'required|string|max:255',
            'theme'          => 'nullable|string|max:255',
            'description'    => 'required|string',
            'duration_weeks' => 'required|integer|min:1',
            'instructor_id'  => 'required|exists:users,id', 
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $syllabu->update($validator->validated());

        ActivityLog::log('Silabus diperbarui: ' . $syllabu->name, 'syllabus', $syllabu);

        return response()->json([
            'message'  => 'Silabus berhasil diperbarui.',
            'redirect' => route('syllabus.show', $syllabu->id),
        ]);
    }

    public function destroy(Syllabus $syllabu)
    {
        if (!Auth::user()->hasPermission('syllabus.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $syllabu->delete();

        ActivityLog::log('Silabus dihapus: ' . $syllabu->name, 'syllabus');

        return response()->json(['message' => 'Silabus berhasil dihapus.']);
    }
}
