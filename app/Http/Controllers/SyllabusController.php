<?php

namespace App\Http\Controllers;

use App\Models\Syllabus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SyllabusController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermission('syllabus.view')) {
            abort(403, 'Unauthorized action.');
        }

        $data_syllabi = Syllabus::withCount('courses')->latest()->get();
        return view('syllabus.index', compact('data_syllabi'));
    }

    public function create()
    {
        if (!Auth::user()->hasPermission('syllabus.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $images = array_values(array_diff(scandir(public_path('img')), ['.', '..']));
        return view('syllabus.form', compact('images'));
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
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Syllabus::create(array_merge($validator->validated(), [
            'created_by' => Auth::id(),
        ]));

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
        return view('syllabus.show', ['syllabus' => $syllabu]);
    }

    public function edit(Syllabus $syllabu)
    {
        if (!Auth::user()->hasPermission('syllabus.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $images = array_values(array_diff(scandir(public_path('img')), ['.', '..']));
        return view('syllabus.form', ['syllabus' => $syllabu, 'images' => $images]);
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
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $syllabu->update($validator->validated());

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

        return response()->json(['message' => 'Silabus berhasil dihapus.']);
    }
}
