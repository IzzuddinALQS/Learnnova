<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermission('assignments.view')) {
            abort(403, 'Unauthorized action.');
        }

        return view('assignments.index');
    }

    public function show($id)
    {
        if (!Auth::user()->hasPermission('assignments.view')) {
            abort(403, 'Unauthorized action.');
        }

        return view('assignments.detail-tugas');
    }

}
