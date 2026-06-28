@extends('main')

@section('title', 'Absensi')

@section('content')

<div class="content-header">
    <div class="container-fluid">
        <h1>Absensi: {{ $schedule->title }}</h1>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Input Kehadiran Siswa</h3>
            </div>

            <form action="{{ route('attendance.store') }}" method="POST">
                @csrf

                <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">

                <div class="card-body">
                    <div class="form-group mb-4" style="max-width: 300px;">
                        <label>Pilih Tanggal Absensi:</label>
                        <input 
                            type="date" 
                            name="attendance_date" 
                            id="attendance_date" 
                            class="form-control" 
                            value="{{ $date }}" 
                            required
                        >
                    </div>

                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Nama Siswa</th>
                                <th width="20%">Status</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($students as $student)
                                @php
                                    $oldStatus = $existingAttendances[$student->id]->status ?? 'present';
                                    $oldNote = $existingAttendances[$student->id]->note ?? '';
                                @endphp

                                <tr>
                                    <td>{{ $student->name }}</td>

                                    <td>
                                        <select name="attendances[{{ $student->id }}][status]" class="form-control form-control-sm">
                                            <option value="present" {{ $oldStatus == 'present' ? 'selected' : '' }}>
                                                Hadir
                                            </option>

                                            <option value="excused" {{ $oldStatus == 'excused' ? 'selected' : '' }}>
                                                Izin
                                            </option>

                                            <option value="late" {{ $oldStatus == 'late' ? 'selected' : '' }}>
                                                Sakit
                                            </option>

                                            <option value="absent" {{ $oldStatus == 'absent' ? 'selected' : '' }}>
                                                Alpa
                                            </option>
                                        </select>
                                    </td>

                                    <td>
                                        <input 
                                            type="text" 
                                            name="attendances[{{ $student->id }}][note]" 
                                            class="form-control form-control-sm" 
                                            placeholder="Opsional..."
                                            value="{{ $oldNote }}"
                                        >
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Absensi
                    </button>

                    <a href="{{ route('attendance.report', $schedule->id) }}" class="btn btn-secondary">
                        Kembali ke Rekap
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>

@endsection