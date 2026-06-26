@extends('main')

@section('title', 'Rekap Nilai — ' . $course->title)

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Rekap Nilai</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Rekap Nilai</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            {{-- Info Course --}}
            <div class="card card-primary card-outline mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="font-weight-bold mb-1">{{ $course->title }}</h5>
                            <span class="text-muted">
                                <i class="fas fa-users mr-1"></i>{{ $students->count() }} Pelajar
                            </span>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <span class="badge badge-info px-3 py-2">
                                Bobot Tugas: {{ $course->assignment_weight }}% &nbsp;|&nbsp;
                                Bobot Kuis: {{ $course->quiz_weight }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tombol Export --}}
            <div class="row mb-3">
                <div class="col-12 d-flex justify-content-end" style="gap: 8px;">
                    <a href="{{ route('gradebook.export.excel.course', $course->id) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </a>
                    <a href="{{ route('gradebook.export.pdf.course', $course->id) }}" class="btn btn-danger btn-sm">
                        <i class="fas fa-file-pdf mr-1"></i> Export PDF
                    </a>
                </div>
            </div>

            {{-- Tabel Rekap --}}
            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-hover table-bordered mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Nama Pelajar</th>
                                {{-- Header Tugas --}}
                                @foreach($course->assignments as $assignment)
                                    <th class="text-center">
                                        <small>{{ Str::limit($assignment->title, 15) }}</small><br>
                                        <span class="badge badge-secondary">Tugas</span>
                                    </th>
                                @endforeach
                                {{-- Header Kuis --}}
                                @foreach($course->quizzes as $quiz)
                                    <th class="text-center">
                                        <small>{{ Str::limit($quiz->title, 15) }}</small><br>
                                        <span class="badge badge-info">Kuis</span>
                                    </th>
                                @endforeach
                                <th class="text-center">Rata-rata Tugas</th>
                                <th class="text-center">Rata-rata Kuis</th>
                                <th class="text-center">Nilai Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($gradebook as $i => $item)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td class="font-weight-bold">{{ $item['student']->name }}</td>

                                    {{-- Nilai per Tugas --}}
                                    @foreach($item['assignment_scores'] as $score)
                                        <td class="text-center">
                                            {{ $score !== null ? $score : '-' }}
                                        </td>
                                    @endforeach

                                    {{-- Nilai per Kuis --}}
                                    @foreach($item['quiz_scores'] as $score)
                                        <td class="text-center">
                                            {{ $score !== null ? $score : '-' }}
                                        </td>
                                    @endforeach

                                    {{-- Rata-rata Tugas --}}
                                    <td class="text-center font-weight-bold">
                                        {{ $item['avg_assignment'] !== null ? $item['avg_assignment'] . '%' : '-' }}
                                    </td>

                                    {{-- Rata-rata Kuis --}}
                                    <td class="text-center font-weight-bold">
                                        {{ $item['avg_quiz'] !== null ? $item['avg_quiz'] . '%' : '-' }}
                                    </td>

                                    {{-- Nilai Akhir --}}
                                    <td class="text-center">
                                        @if($item['final_score'] !== null)
                                            <span class="badge px-3 py-2
                                                @if($item['final_score'] >= 75) badge-success
                                                @elseif($item['final_score'] >= 60) badge-warning
                                                @else badge-danger
                                                @endif">
                                                {{ $item['final_score'] }}%
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="20" class="text-center text-muted py-4">
                                        Belum ada pelajar yang terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <a href="{{ route('dashboard') }}" class="btn btn-secondary mt-3">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>

        </div>
    </section>
@endsection