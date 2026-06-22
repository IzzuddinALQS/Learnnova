@extends('main')

@section('title', 'Buku Nilai')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Buku Nilai</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Buku Nilai</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            {{-- Tombol Export --}}
            <div class="row mb-3">
                <div class="col-12 d-flex justify-content-end" style="gap: 8px;">
                    <a href="{{ route('gradebook.export.excel') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </a>
                    <a href="{{ route('gradebook.export.pdf') }}" class="btn btn-danger btn-sm">
                        <i class="fas fa-file-pdf mr-1"></i> Export PDF
                    </a>
                </div>
            </div>

            @forelse($gradebook as $item)
                <div class="card card-outline card-primary mb-4">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold">
                            <i class="fas fa-chalkboard mr-2"></i>{{ $item['course']->title }}
                        </h3>
                        <div class="card-tools">
                            <span class="badge badge-info px-3 py-2">
                                Bobot Tugas: {{ $item['assignment_weight'] }}% &nbsp;|&nbsp;
                                Bobot Kuis: {{ $item['quiz_weight'] }}%
                            </span>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="row">

                            {{-- Tabel Tugas --}}
                            <div class="col-md-6 mb-3">
                                <h6 class="font-weight-bold text-muted text-uppercase mb-2">
                                    <i class="fas fa-tasks mr-1"></i> Tugas
                                </h6>
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Judul</th>
                                            <th>Nilai</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($item['assignments'] as $assignment)
                                            <tr>
                                                <td>{{ $assignment['title'] }}</td>
                                                <td>
                                                    @if($assignment['score'] !== null)
                                                        {{ $assignment['score'] }} / {{ $assignment['max'] }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @switch($assignment['status'])
                                                        @case('belum')
                                                            <span class="badge badge-warning">Belum</span>
                                                            @break
                                                        @case('submitted')
                                                            <span class="badge badge-primary">Dikumpulkan</span>
                                                            @break
                                                        @case('graded')
                                                            <span class="badge badge-success">Dinilai</span>
                                                            @break
                                                        @case('returned')
                                                            <span class="badge badge-info">Dikembalikan</span>
                                                            @break
                                                        @default
                                                            <span class="badge badge-secondary">-</span>
                                                    @endswitch
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">Belum ada tugas</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    @if(count($item['assignments']) > 0)
                                        <tfoot>
                                            <tr class="table-light">
                                                <td colspan="2" class="font-weight-bold">Rata-rata Tugas</td>
                                                <td class="font-weight-bold">
                                                    {{ $item['avg_assignment'] !== null ? $item['avg_assignment'] . '%' : '-' }}
                                                </td>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>

                            {{-- Tabel Kuis --}}
                            <div class="col-md-6 mb-3">
                                <h6 class="font-weight-bold text-muted text-uppercase mb-2">
                                    <i class="fas fa-question-circle mr-1"></i> Kuis
                                </h6>
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Judul</th>
                                            <th>Nilai</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($item['quizzes'] as $quiz)
                                            <tr>
                                                <td>{{ $quiz['title'] }}</td>
                                                <td>
                                                    @if($quiz['score'] !== null)
                                                        {{ $quiz['score'] }} / {{ $quiz['max'] }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($quiz['is_passed'] === null)
                                                        <span class="badge badge-warning">Belum</span>
                                                    @elseif($quiz['is_passed'])
                                                        <span class="badge badge-success">Lulus</span>
                                                    @else
                                                        <span class="badge badge-danger">Tidak Lulus</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">Belum ada kuis</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    @if(count($item['quizzes']) > 0)
                                        <tfoot>
                                            <tr class="table-light">
                                                <td colspan="2" class="font-weight-bold">Rata-rata Kuis</td>
                                                <td class="font-weight-bold">
                                                    {{ $item['avg_quiz'] !== null ? $item['avg_quiz'] . '%' : '-' }}
                                                </td>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>

                        </div>

                        {{-- Nilai Akhir --}}
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="alert mb-0
                                    @if($item['final_score'] === null) alert-secondary
                                    @elseif($item['final_score'] >= 75) alert-success
                                    @elseif($item['final_score'] >= 60) alert-warning
                                    @else alert-danger
                                    @endif">
                                    <strong>Nilai Akhir:</strong>
                                    @if($item['final_score'] !== null)
                                        <span class="font-weight-bold" style="font-size: 1.2rem;">
                                            {{ $item['final_score'] }}%
                                        </span>
                                        @if($item['final_score'] >= 75)
                                            <span class="badge badge-success ml-2">Baik</span>
                                        @elseif($item['final_score'] >= 60)
                                            <span class="badge badge-warning ml-2">Cukup</span>
                                        @else
                                            <span class="badge badge-danger ml-2">Perlu Peningkatan</span>
                                        @endif
                                    @else
                                        <span class="text-muted">Belum ada nilai</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            @empty
                <div class="alert alert-info">
                    Anda belum terdaftar di kelas manapun.
                </div>
            @endforelse

        </div>
    </section>
@endsection