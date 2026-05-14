@extends('main')

@section('title', 'Daftar Pelajar & Kelas')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Daftar Pelajar</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Pelajar</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-users mr-2"></i>Semua Pelajar & Kelas yang Diikuti</h3>
                <div class="card-tools">
                    <a href="{{ route('teachers.overview') }}" class="btn btn-sm btn-outline-info">
                        <i class="fas fa-chalkboard-teacher mr-1"></i> Lihat Pengajar
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Pelajar</th>
                            <th>Email</th>
                            <th>Kelas yang Diikuti</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $student->name }}</strong>
                                    @if(!$student->is_active)
                                        <span class="badge badge-danger ml-1">Nonaktif</span>
                                    @endif
                                </td>
                                <td>{{ $student->email }}</td>
                                <td>
                                    @forelse($student->enrollments as $enrollment)
                                        @if($enrollment->course)
                                            <span class="badge badge-{{ $enrollment->status === 'active' ? 'primary' : ($enrollment->status === 'completed' ? 'success' : 'secondary') }} mb-1">
                                                {{ Str::limit($enrollment->course->title, 30) }}
                                                @if($enrollment->status !== 'active')
                                                    ({{ $enrollment->status }})
                                                @endif
                                            </span>
                                        @endif
                                    @empty
                                        <span class="text-muted">Belum ikut kelas</span>
                                    @endforelse
                                </td>
                                <td>
                                    <a href="{{ route('courses.index') }}" class="btn btn-xs btn-info">
                                        <i class="fas fa-plus"></i> Daftarkan ke Kelas
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada pelajar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $students->links() }}
            </div>
        </div>

    </div>
</section>
@endsection
