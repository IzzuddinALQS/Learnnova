<?php

namespace App\Http\Controllers;

use App\Models\Syllabus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SyllabusController extends Controller
{
    public function index()
    {
        $data_syllabi = Syllabus::all();
        return view('syllabus.index', compact('data_syllabi'));
    } 

    public function create()
    {
        $images = array_diff(scandir(public_path('img')), array('.', '..'));
        return view('syllabus.create', compact('images'));
        // return view('syllabus.create');
    } 

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'           => 'required|string|max:255',
            'instructor'     => 'required|string|max:255', 
            'theme'          => 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'duration_weeks' => 'required|integer|min:1',
            // 'created_by'     => 'required|integer|exists:users,id',
        ]);

        Syllabus::create($validatedData);
        return redirect()->route('syllabus.index')->with('success', 'Silabus berhasil dibuat!');
    }


    public function show(Syllabus $syllabu)
    {
        $syllabus = $syllabu;
        return view('syllabus.show', compact('syllabus'));
    }

    public function edit(Syllabus $syllabu) 
    {
        $syllabus = $syllabu;
        $images = array_diff(scandir(public_path('img')), array('.', '..'));
        return view('syllabus.edit', compact('syllabus', 'images'));
        // return view('syllabus.edit', compact('syllabus'));
    }

    public function update(Request $request, Syllabus $syllabu)
    {
        $validatedData = $request->validate([
            'name'           => 'required|string|max:255',
            'instructor'     => 'required|string|max:255', 
            'theme'          => 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'duration_weeks' => 'required|integer|min:1',
        ]);

        $syllabu->update($validatedData);

        return redirect()->route('syllabus.show', $syllabu->id)->with('success', 'Silabus berhasil diperbarui!');
    }

    public function destroy(Syllabus $syllabu)
    {
        $syllabu->delete();
        return redirect()->route('syllabus.index')->with('success', 'Silabus berhasil dihapus!');
    }

}
