<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index()
    {
        return view('assignments.index');
    }

    public function show($id)
    {
        return view('assignments.detail-tugas');
    }

}
