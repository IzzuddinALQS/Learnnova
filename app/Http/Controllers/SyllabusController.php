<?php

namespace App\Http\Controllers;

use App\Models\Syllabus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\ActivityLog;

class SyllabusController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermission('syllabus.view')) {
            abort(403, 'Unauthorized action.');
        }

        $data_syllabi = Syllabus::withCount('courses')
            ->latest()
            ->get();
        return view('syllabus.index', compact('data_syllabi'));
    }

    public function create()
    {
        if (!Auth::user()->hasPermission('syllabus.manage')) {
            abort(403, 'Unauthorized action.');
        }

        return view('syllabus.form');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('syllabus.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = validator($request->all(), [
            'name'           => 'required|string|max:255',
            'theme'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'description'    => 'required|string',
            'duration_weeks' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = array_merge($validator->validated(), ['created_by' => Auth::id()]);

        if ($request->hasFile('theme')) {
            $data['theme'] = $request->file('theme')->store('syllabus/covers', 'public');
        }

        $syllabus = Syllabus::create($data);

        ActivityLog::log("Membuat silabus baru: {$syllabus->name}", $syllabus, $syllabus->toArray(), 'syllabus');

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

        return view('syllabus.form', ['syllabus' => $syllabu]);
    }

    public function update(Request $request, Syllabus $syllabu)
    {
        if (!Auth::user()->hasPermission('syllabus.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = validator($request->all(), [
            'name'           => 'required|string|max:255',
            'theme'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'description'    => 'required|string',
            'duration_weeks' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        if ($request->hasFile('theme')) {
            if ($syllabu->theme && Storage::disk('public')->exists($syllabu->theme)) {
                Storage::disk('public')->delete($syllabu->theme);
            }
            $data['theme'] = $request->file('theme')->store('syllabus/covers', 'public');
        } else {
            unset($data['theme']);
        }

        $syllabu->update($data);

        ActivityLog::log("Memperbarui silabus: {$syllabu->name}", $syllabu, $syllabu->getChanges(), 'syllabus');

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

        if ($syllabu->theme && Storage::disk('public')->exists($syllabu->theme)) {
            Storage::disk('public')->delete($syllabu->theme);
        }

        $name = $syllabu->name;
        $syllabu->delete();

        ActivityLog::log("Menghapus silabus: {$name}", $syllabu, ['name' => $name], 'syllabus');

        return response()->json(['message' => 'Silabus berhasil dihapus.']);
    }
}