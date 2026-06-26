<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermission('schedules.view')) {
            abort(403, 'Unauthorized action.');
        }

        return view('schedules.index');
    }
}
