@extends('main')

@section('title', 'Jadwal')

@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0" style="font-size:1.3rem">Jadwal Kelas</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Jadwal & Absensi</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Data Jadwal Pembelajaran</h3>
            </div>
            
            <div class="card-body p-0">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Mata Pelajaran</th>
                            <th>Jam</th>
                            <th>Ruangan</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedules as $index => $schedule)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $schedule->title }}</strong><br>
                                <small class="text-muted">{{ $schedule->description }}</small>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                            <td>{{ $schedule->location }}</td>
                            <td>
                                <a href="{{ route('attendance.show', $schedule->id) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-user-check"></i> Absensi
                                </a>
                                <a href="{{ route('attendance.report', $schedule->id) }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-list"></i> Rekap
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada jadwal</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

@endsection