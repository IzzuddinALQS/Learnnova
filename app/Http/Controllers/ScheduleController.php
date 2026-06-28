<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermission('schedules.view')) {
            abort(403, 'Unauthorized action.');
        }

        // Ambil semua jadwal dari database
        $schedules = Schedule::all();

        // Kirim data $schedules ke view 'schedules.index'
        return view('schedules.index', compact('schedules'));
    }
}