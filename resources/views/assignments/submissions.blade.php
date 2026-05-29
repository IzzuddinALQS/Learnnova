@extends('main')

@section('title', 'Daftar Submission — ' . $assignment->title)

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Daftar Submission</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('assignments.index') }}">Tugas</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('assignments.show', $assignment->id) }}">{{ Str::limit($assignment->title, 20) }}</a>
                        </li>
                        <li class="breadcrumb-item active">Submission</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            {{-- Info Tugas --}}
            <div class="card card-primary card-outline mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="font-weight-bold mb-1">{{ $assignment->title }}</h5>
                            <span class="text-muted"><i
                                    class="fas fa-chalkboard mr-1"></i>{{ $assignment->course->title }}</span>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <span class="badge badge-info px-3 py-2 mr-2">
                                <i class="fas fa-users mr-1"></i>{{ $submissions->count() }} Submission
                            </span>
                            @if ($assignment->due_date->isPast())
                                <span class="badge badge-danger px-3 py-2">
                                    <i class="fas fa-clock mr-1"></i>Deadline Lewat
                                </span>
                            @else
                                <span class="badge badge-success px-3 py-2">
                                    <i class="fas fa-clock mr-1"></i>{{ $assignment->due_date->format('d M Y, H:i') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabel Submission --}}
            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Pelajar</th>
                                <th>Dikumpulkan</th>
                                <th>Status</th>
                                <th>Nilai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($submissions as $i => $item)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        <div class="font-weight-bold">{{ $item->student->name }}</div>
                                        <small class="text-muted">{{ $item->student->email }}</small>
                                    </td>
                                    <td>
                                        @if ($item->submitted_at)
                                            {{ $item->submitted_at->format('d M Y, H:i') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($item->status)
                                            @case('draft')
                                                <span class="badge badge-warning">Draft</span>
                                            @break

                                            @case('submitted')
                                                <span class="badge badge-primary">Dikumpulkan</span>
                                            @break

                                            @case('graded')
                                                <span class="badge badge-success">Sudah Dinilai</span>
                                            @break

                                            @case('returned')
                                                <span class="badge badge-info">Dikembalikan</span>
                                            @break
                                        @endswitch
                                    </td>
                                    <td>
                                        @if ($item->score !== null)
                                            <strong>{{ $item->score }}</strong> / {{ $assignment->max_score }}
                                        @else
                                            <span class="text-muted">Belum dinilai</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('assignments.grade.form', [$assignment->id, $item->id]) }}"
                                            class="btn btn-xs btn-primary">
                                            <i class="fas fa-{{ $item->score !== null ? 'edit' : 'star' }} mr-1"></i>
                                            {{ $item->score !== null ? 'Edit Nilai' : 'Beri Nilai' }}
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            Belum ada pelajar yang mengumpulkan tugas ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <a href="{{ route('assignments.index') }}" class="btn btn-secondary mt-3">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>

            </div>
        </section>
    @endsection
