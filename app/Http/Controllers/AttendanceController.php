<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function show(Request $request, $scheduleId)
{
    $schedule = Schedule::findOrFail($scheduleId);

    $date = $request->input('date', date('Y-m-d'));

    $students = User::whereHas('roles', function($query) {
        $query->where('name', 'pelajar');
    })->get();

    $existingAttendances = Attendance::where('schedule_id', $scheduleId)
        ->where('attendance_date', $date)
        ->get()
        ->keyBy('student_id');

    return view('attendance.show', compact('schedule', 'students', 'date', 'existingAttendances'));
}

   public function store(Request $request)
{
    $tanggal = $request->input('attendance_date');
    $scheduleId = $request->schedule_id;

    $schedule = \App\Models\Schedule::findOrFail($scheduleId);

    foreach ($request->attendances as $studentId => $data) {
        \App\Models\Attendance::updateOrCreate(
            [
                
                'schedule_id'     => $scheduleId,
                'student_id'      => $studentId,
                'attendance_date' => $tanggal, 
            ],
            [
                
                'course_id'       => $schedule->course_id,
                'status'          => $data['status'],
                'note'            => $data['note'] ?? null,
                'recorded_at'     => now(),
            ]
        );
    }

    return redirect()->route('schedules.index')
                     ->with('success', 'Absensi tanggal ' . $tanggal . ' berhasil disimpan!');
}


    public function report(Request $request, $scheduleId)
{
    $schedule = Schedule::findOrFail($scheduleId);

    $query = Attendance::where('schedule_id', $scheduleId);

    if ($request->filled('from_date')) {
        $query->whereDate('attendance_date', '>=', $request->from_date);
    }

    if ($request->filled('to_date')) {
        $query->whereDate('attendance_date', '<=', $request->to_date);
    }

    $attendances = $query
        ->orderBy('attendance_date', 'desc')
        ->get()
        ->groupBy('attendance_date');

    return view('attendance.report', compact('schedule', 'attendances'));
}


    public function exportCsv($scheduleId)
{
    $schedule = Schedule::findOrFail($scheduleId);

    $attendances = Attendance::with('student')
        ->where('schedule_id', $scheduleId)
        ->orderBy('attendance_date', 'desc')
        ->get();

    $filename = 'rekap_absensi_' . str_replace(' ', '_', strtolower($schedule->title)) . '.csv';

    $headers = [
        "Content-Type" => "text/csv",
        "Content-Disposition" => "attachment; filename=$filename",
    ];

    $callback = function () use ($attendances, $schedule) {
        $file = fopen('php://output', 'w');

        fputcsv($file, ['Rekap Absensi']);
        fputcsv($file, ['Kelas', $schedule->title]);
        fputcsv($file, []);

        fputcsv($file, ['Tanggal', 'Nama Siswa', 'Status', 'Catatan']);

        foreach ($attendances as $item) {
            $statusMap = [
                'present' => 'Hadir',
                'absent'  => 'Alpa',
                'late'    => 'Sakit',
                'excused' => 'Izin',
            ];

            fputcsv($file, [
                $item->attendance_date,
                $item->student ? $item->student->name : 'Siswa Tidak Ditemukan',
                $statusMap[$item->status] ?? $item->status,
                $item->note ?? '-',
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
}