@extends('main')
@section('title', 'Rekap Absensi')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <h1>Rekap Absensi: {{ $schedule->title }}</h1>

            <div>
                <a href="{{ route('attendance.export.csv', $schedule->id) }}" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>

                <a href="{{ route('schedules.index') }}" class="btn btn-secondary">
                    ← Kembali ke Jadwal
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        {{-- Filter Tanggal --}}
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('attendance.report', $schedule->id) }}">
                    <div class="row">
                        <div class="col-md-4">
                            <label>Dari Tanggal</label>
                            <input type="date"
                                   name="from_date"
                                   class="form-control"
                                   value="{{ request('from_date') }}">
                        </div>

                        <div class="col-md-4">
                            <label>Sampai Tanggal</label>
                            <input type="date"
                                   name="to_date"
                                   class="form-control"
                                   value="{{ request('to_date') }}">
                        </div>

                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary mr-2">
                                <i class="fas fa-search"></i> Filter
                            </button>

                            <a href="{{ route('attendance.report', $schedule->id) }}" class="btn btn-secondary">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Rekap Box --}}
        @if($attendances->count() > 0)
            <div class="row">
                @foreach($attendances as $date => $group)
                    @php
                        $hadir = $group->where('status', 'present')->count();
                        $izin  = $group->where('status', 'excused')->count();
                        $sakit = $group->where('status', 'late')->count();
                        $alpa  = $group->where('status', 'absent')->count();
                    @endphp

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h5>{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</h5>
                                <p>Hadir: {{ $hadir }} | Izin: {{ $izin }} | Sakit: {{ $sakit }} | Alpa: {{ $alpa }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card">
                <div class="card-body">
                    @foreach($attendances as $date => $group)
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <h5 class="m-0">Tanggal: {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</h5>

                            <a href="{{ route('attendance.show', $schedule->id) }}?date={{ $date }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Edit Absensi
                            </a>
                        </div>

                        <table class="table table-bordered table-striped mt-2 mb-4">
                            <thead>
                                <tr>
                                    <th>Nama Siswa</th>
                                    <th width="15%">Status</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($group as $item)
                                    @php
                                        $statusMap = [
                                            'present' => ['label' => 'Hadir',  'class' => 'badge-success'],
                                            'absent'  => ['label' => 'Alpa',   'class' => 'badge-danger'],
                                            'late'    => ['label' => 'Sakit',  'class' => 'badge-info'],
                                            'excused' => ['label' => 'Izin',   'class' => 'badge-info'],
                                        ];

                                        $status = $statusMap[$item->status] ?? [
                                            'label' => ucfirst($item->status),
                                            'class' => 'badge-secondary'
                                        ];
                                    @endphp

                                    <tr>
                                        <td>{{ $item->student ? $item->student->name : 'Siswa Tidak Ditemukan' }}</td>

                                        <td>
                                            <span class="badge {{ $status['class'] }}">
                                                {{ $status['label'] }}
                                            </span>
                                        </td>

                                        <td>{{ $item->note ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endforeach
                </div>
            </div>
        @else
            <div class="alert alert-warning">
                Belum ada data absensi untuk jadwal ini atau tanggal yang dipilih.
            </div>
        @endif

    </div>
</section>
@endsection