@extends('main')

@section('title', 'Daftar Tugas')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Daftar Tugas</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Tugas</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            <div class="row mb-3">
                <div class="col-12">
                    <div class="text-muted">
                        Total: <strong>{{ $assignments->count() }}</strong> tugas
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Judul Tugas</th>
                                <th>Kelas</th>
                                <th>Deadline</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignments as $i => $item)
                                @php
                                    $submission = $item->submissions->first();
                                    $status     = $submission?->status ?? 'belum';
                                    $isPast     = $item->due_date->isPast();
                                @endphp
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td class="font-weight-bold">{{ $item->title }}</td>
                                    <td>{{ $item->course->title ?? '-' }}</td>
                                    <td>
                                        @if($isPast)
                                            <span class="badge badge-danger">
                                                <i class="fas fa-clock mr-1"></i>{{ $item->due_date->format('d M Y, H:i') }}
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">
                                                <i class="fas fa-clock mr-1"></i>{{ $item->due_date->format('d M Y, H:i') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($status)
                                            @case('belum')
                                                <span class="badge badge-warning">Belum Dikumpulkan</span>
                                                @break
                                            @case('draft')
                                                <span class="badge badge-warning">Draft</span>
                                                @break
                                            @case('submitted')
                                                <span class="badge badge-success">Dikumpulkan</span>
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
                                        <a href="{{ route('assignments.show', $item->id) }}"
                                            class="btn btn-xs btn-primary">
                                            <i class="fas fa-eye mr-1"></i>
                                            {{ $submission ? 'Lihat' : 'Kumpulkan' }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Belum ada tugas.
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