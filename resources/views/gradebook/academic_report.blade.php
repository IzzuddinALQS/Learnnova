@extends('main')

@section('title', 'Laporan Akademik')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Laporan Akademik</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Laporan Akademik</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            {{-- Statistik Ringkasan --}}
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $summary['total_students'] }}</h3>
                            <p>Total Pelajar</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-graduate"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $summary['total_courses'] }}</h3>
                            <p>Total Kelas</p>
                        </div>
                        <div class="icon"><i class="fas fa-chalkboard"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $summary['total_assignments'] }}</h3>
                            <p>Total Tugas</p>
                        </div>
                        <div class="icon"><i class="fas fa-tasks"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $summary['total_quizzes'] }}</h3>
                            <p>Total Kuis</p>
                        </div>
                        <div class="icon"><i class="fas fa-question-circle"></i></div>
                    </div>
                </div>
            </div>

            {{-- Tombol Export --}}
            <div class="row mb-3">
                <div class="col-12 d-flex justify-content-end" style="gap: 8px;">
                    <a href="{{ route('academic.report.export.excel') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </a>
                    <a href="{{ route('academic.report.export.pdf') }}" class="btn btn-danger btn-sm">
                        <i class="fas fa-file-pdf mr-1"></i> Export PDF
                    </a>
                </div>
            </div>

            {{-- Tabel Ringkasan Per Course --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-chart-bar mr-2"></i>Ringkasan Per Kelas
                    </h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Nama Kelas</th>
                                <th class="text-center">Pelajar</th>
                                <th class="text-center">Rata-rata Tugas</th>
                                <th class="text-center">Rata-rata Kuis</th>
                                <th class="text-center">Nilai Akhir</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($report as $i => $item)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td class="font-weight-bold">{{ $item['course']->title }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-info">{{ $item['total_students'] }} pelajar</span>
                                    </td>
                                    <td class="text-center">
                                        {{ $item['avg_assignment'] !== null ? $item['avg_assignment'] . '%' : '-' }}
                                    </td>
                                    <td class="text-center">
                                        {{ $item['avg_quiz'] !== null ? $item['avg_quiz'] . '%' : '-' }}
                                    </td>
                                    <td class="text-center">
                                        @if($item['avg_final'] !== null)
                                            <span class="badge px-3 py-2
                                                @if($item['avg_final'] >= 75) badge-success
                                                @elseif($item['avg_final'] >= 60) badge-warning
                                                @else badge-danger
                                                @endif">
                                                {{ $item['avg_final'] }}%
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('gradebook.course', $item['course']->id) }}"
                                            class="btn btn-xs btn-primary">
                                            <i class="fas fa-eye mr-1"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        Belum ada data kelas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Detail Per Course --}}
            @foreach($report as $item)
                <div class="card card-outline card-primary mb-4">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold">
                            <i class="fas fa-chalkboard mr-2"></i>{{ $item['course']->title }}
                        </h3>
                        <div class="card-tools">
                            <span class="badge badge-info px-3 py-2">
                                Bobot Tugas: {{ $item['course']->assignment_weight }}%
                                &nbsp;|&nbsp;
                                Bobot Kuis: {{ $item['course']->quiz_weight }}%
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover table-bordered mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nama Pelajar</th>
                                    @foreach($item['course']->assignments as $assignment)
                                        <th class="text-center">
                                            <small>{{ Str::limit($assignment->title, 12) }}</small><br>
                                            <span class="badge badge-secondary">Tugas</span>
                                        </th>
                                    @endforeach
                                    @foreach($item['course']->quizzes as $quiz)
                                        <th class="text-center">
                                            <small>{{ Str::limit($quiz->title, 12) }}</small><br>
                                            <span class="badge badge-info">Kuis</span>
                                        </th>
                                    @endforeach
                                    <th class="text-center">Rata-rata Tugas</th>
                                    <th class="text-center">Rata-rata Kuis</th>
                                    <th class="text-center">Nilai Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($item['gradebook'] as $i => $row)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td class="font-weight-bold">{{ $row['student']->name }}</td>

                                        @foreach($row['assignment_scores'] as $score)
                                            <td class="text-center">{{ $score ?? '-' }}</td>
                                        @endforeach

                                        @foreach($row['quiz_scores'] as $score)
                                            <td class="text-center">{{ $score ?? '-' }}</td>
                                        @endforeach

                                        <td class="text-center font-weight-bold">
                                            {{ $row['avg_assignment'] !== null ? $row['avg_assignment'] . '%' : '-' }}
                                        </td>
                                        <td class="text-center font-weight-bold">
                                            {{ $row['avg_quiz'] !== null ? $row['avg_quiz'] . '%' : '-' }}
                                        </td>
                                        <td class="text-center">
                                            @if($row['final_score'] !== null)
                                                <span class="badge px-3 py-2
                                                    @if($row['final_score'] >= 75) badge-success
                                                    @elseif($row['final_score'] >= 60) badge-warning
                                                    @else badge-danger
                                                    @endif">
                                                    {{ $row['final_score'] }}%
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="20" class="text-center text-muted py-3">
                                            Belum ada pelajar terdaftar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

        </div>
    </section>
@endsection
