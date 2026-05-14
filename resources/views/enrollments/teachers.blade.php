@extends('main')

@section('title', 'Daftar Pengajar & Kelas')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Daftar Pengajar</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Pengajar</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chalkboard-teacher mr-2"></i>Semua Pengajar & Kelas yang Diajar</h3>
                <div class="card-tools">
                    <a href="{{ route('students.overview') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-users mr-1"></i> Lihat Pelajar
                    </a>
                    @if(auth()->user()->hasPermission('courses.create'))
                        <a href="{{ route('courses.create') }}" class="btn btn-sm btn-success ml-1">
                            <i class="fas fa-plus mr-1"></i> Tambah Kelas
                        </a>
                    @endif
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Pengajar</th>
                            <th>Email</th>
                            <th>Kelas yang Diajar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teachers as $teacher)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $teacher->name }}</strong>
                                    @if(!$teacher->is_active)
                                        <span class="badge badge-danger ml-1">Nonaktif</span>
                                    @endif
                                </td>
                                <td>{{ $teacher->email }}</td>
                                <td>
                                    @forelse($teacher->taughtCourses as $course)
                                        <span class="badge badge-{{ $course->status === 'published' ? 'success' : ($course->status === 'archived' ? 'secondary' : 'warning') }} mb-1">
                                            {{ Str::limit($course->title, 30) }}
                                        </span>
                                    @empty
                                        <span class="text-muted">Belum mengajar kelas</span>
                                    @endforelse
                                </td>
                                <td>
                                    @if(auth()->user()->hasPermission('courses.create'))
                                        <a href="{{ route('courses.create') }}" class="btn btn-xs btn-success">
                                            <i class="fas fa-plus"></i> Buat Kelas
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada pengajar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $teachers->links() }}
            </div>
        </div>

    </div>
</section>
@endsection
