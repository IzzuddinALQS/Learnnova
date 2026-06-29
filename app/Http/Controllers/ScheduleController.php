<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermission('schedules.view')) {
            abort(403, 'Unauthorized action.');
        }

        $user = Auth::user();

        if ($user->hasRole('pelajar')) {
            $schedules = Schedule::with(['course', 'teacher'])
                ->whereHas('course.enrollments', function ($query) use ($user) {
                    $query->where('student_id', $user->id)
                          ->where('status', 'active');
                })
                ->orderBy('day')
                ->orderBy('start_time')
                ->get();
        } elseif ($user->hasRole('pengajar')) {
            $schedules = Schedule::with(['course', 'teacher'])
                ->where('user_id', $user->id)
                ->orderBy('day')
                ->orderBy('start_time')
                ->get();
        } else {
            $schedules = Schedule::with(['course', 'teacher'])
                ->orderBy('day')
                ->orderBy('start_time')
                ->get();
        }

        return view('schedules.index', compact('schedules'));
    }


    public function show(Schedule $schedule)
    {   
    return redirect()->route('schedules.index');
        }

    public function create()
    {
        if (!Auth::user()->hasRole('super_admin') && !Auth::user()->hasRole('akademik')) {
            abort(403, 'Unauthorized action.');
        }

        $courses = Course::orderBy('title')->get();

        $teachers = User::whereHas('roles', function ($query) {
            $query->where('name', 'pengajar');
        })->orderBy('name')->get();

        return view('schedules.create', compact('courses', 'teachers'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasRole('super_admin') && !Auth::user()->hasRole('akademik')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'course_id'   => 'required|exists:courses,id',
            'user_id'     => 'required|exists:users,id',
            'day'         => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time'  => 'required|date',
            'end_time'    => 'required|date|after:start_time',
            'location'    => 'nullable|string|max:255',
            'type'        => 'required|in:online,offline,hybrid',
        ]);

        Schedule::create($request->all());

        return redirect()
            ->route('schedules.index')
            ->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(Schedule $schedule)
    {
        if (!Auth::user()->hasRole('super_admin') && !Auth::user()->hasRole('akademik')) {
            abort(403, 'Unauthorized action.');
        }

        $courses = Course::orderBy('title')->get();

        $teachers = User::whereHas('roles', function ($query) {
            $query->where('name', 'pengajar');
        })->orderBy('name')->get();

        return view('schedules.edit', compact('schedule', 'courses', 'teachers'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        if (!Auth::user()->hasRole('super_admin') && !Auth::user()->hasRole('akademik')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'course_id'   => 'required|exists:courses,id',
            'user_id'     => 'required|exists:users,id',
            'day'         => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time'  => 'required|date',
            'end_time'    => 'required|date|after:start_time',
            'location'    => 'nullable|string|max:255',
            'type'        => 'required|in:online,offline,hybrid',
        ]);

        $schedule->update($request->all());

        return redirect()
            ->route('schedules.index')
            ->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Schedule $schedule)
    {
    if (!Auth::user()->hasRole('super_admin') && !Auth::user()->hasRole('akademik')) {
        abort(403, 'Unauthorized action.');
    }

    DB::transaction(function () use ($schedule) {
        Attendance::where('schedule_id', $schedule->id)->delete();
        $schedule->delete();
    });

    return redirect()
        ->route('schedules.index')
        ->with('success', 'Jadwal berhasil dihapus.');
    }
}