@extends('main')

@section('title', 'Jadwal')

@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <h1 class="m-0" style="font-size:1.3rem">Jadwal Pembelajaran</h1>
                <small class="text-muted">Daftar jadwal kelas dan pertemuan pembelajaran</small>
            </div>

            @if(auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('akademik'))
                <a href="{{ route('schedules.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Jadwal
                </a>
            @endif
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
                            <th>Kelas / Course</th>
                            <th>Pengajar</th>
                            <th>Hari</th>
                            <th>Jam</th>
                            <th>Ruangan</th>
                            <th>Tipe</th>

                            @if(!auth()->user()->hasRole('pelajar'))
                                <th width="25%">Aksi</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($schedules as $index => $schedule)
                            <tr>
                                <td>{{ $index + 1 }}</td>

                                <td>
                                    <strong>{{ $schedule->course->title ?? $schedule->title }}</strong><br>
                                    <small class="text-muted">{{ $schedule->description ?? '-' }}</small>
                                </td>

                                <td>{{ $schedule->teacher->name ?? '-' }}</td>

                                <td>{{ $schedule->day ?? '-' }}</td>

                                <td>
                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                                    -
                                    {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                </td>

                                <td>{{ $schedule->location ?? '-' }}</td>

                                <td>
                                    @if($schedule->type == 'online')
                                        <span class="badge badge-info">Online</span>
                                    @elseif($schedule->type == 'offline')
                                        <span class="badge badge-success">Offline</span>
                                    @else
                                        <span class="badge badge-warning">Hybrid</span>
                                    @endif
                                </td>

                                @if(!auth()->user()->hasRole('pelajar'))
                                    <td>
                                        @if(auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('akademik'))
                                            <a href="{{ route('schedules.edit', $schedule->id) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>

                                            <form action="{{ route('schedules.destroy', $schedule->id) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </button>
                                            </form>
                                        @endif

                                        @if(auth()->user()->hasRole('pengajar') || auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('akademik'))
                                            <a href="{{ route('attendance.show', $schedule->id) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-user-check"></i> Absensi
                                            </a>

                                            <a href="{{ route('attendance.report', $schedule->id) }}" class="btn btn-success btn-sm">
                                                <i class="fas fa-list"></i> Rekap
                                            </a>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->hasRole('pelajar') ? 7 : 8 }}" class="text-center">
                                    Belum ada jadwal
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

@endsection