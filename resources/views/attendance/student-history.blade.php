@extends('main')

@section('title', 'Riwayat Kehadiran')

@section('content')

<div class="content-header">
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center">

            <div>
                <h1 class="m-0">
                    Kehadiran Saya
                </h1>

                <small class="text-muted">
                    {{ $schedule->title }}
                </small>
            </div>

            <a href="{{ route('schedules.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>

        </div>

    </div>
</div>


<section class="content">

    <div class="container-fluid">

        <div class="card">

            <div class="card-header">

                <h3 class="card-title">
                    Riwayat Absensi
                </h3>

            </div>

            <div class="card-body p-0">

                <table class="table table-bordered table-hover">

                    <thead>

                        <tr>
                            <th width="8%">No</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Catatan</th>
                        </tr>

                    </thead>

                    <tbody>

                    @forelse($attendances as $index => $attendance)

                        <tr>

                            <td>
                                {{ $index + 1 }}
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($attendance->attendance_date)->format('d M Y') }}
                            </td>

                            <td>

                                @if($attendance->status == 'present')

                                    <span class="badge badge-success">
                                        Hadir
                                    </span>

                                @elseif($attendance->status == 'late')

                                    <span class="badge badge-warning">
                                        Sakit
                                    </span>

                                @elseif($attendance->status == 'excused')

                                    <span class="badge badge-info">
                                        Izin
                                    </span>

                                @else

                                    <span class="badge badge-danger">
                                        Alpha
                                    </span>

                                @endif

                            </td>

                            <td>

                                {{ $attendance->note ?? '-' }}

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="4" class="text-center">

                                Belum ada riwayat kehadiran.

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