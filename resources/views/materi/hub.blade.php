@extends('main')

@section('title', 'Materi')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Materi</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Materi</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        @if($role === 'pengajar')
            <div class="alert alert-info py-2 mb-3">
                <i class="fas fa-chalkboard-teacher mr-2"></i>
                Pilih kelas untuk mengelola materi dan bab.
            </div>
        @elseif($role === 'pelajar')
            <div class="alert alert-info py-2 mb-3">
                <i class="fas fa-book-reader mr-2"></i>
                Pilih kelas untuk mulai belajar.
            </div>
        @endif

        @if($courses->isEmpty())
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-book-open fa-3x text-muted mb-3 d-block"></i>
                    @if($role === 'pelajar')
                        <p class="text-muted">Kamu belum terdaftar di kelas manapun.</p>
                        <small class="text-muted">Hubungi staf akademik untuk didaftarkan ke kelas.</small>
                    @elseif($role === 'pengajar')
                        <p class="text-muted">Kamu belum mengajar di kelas manapun.</p>
                    @else
                        <p class="text-muted">Belum ada kelas yang tersedia.</p>
                        <a href="{{ route('courses.create') }}" class="btn btn-success btn-sm mt-2">
                            <i class="fas fa-plus mr-1"></i> Tambah Kelas
                        </a>
                    @endif
                </div>
            </div>
        @else
            <div class="row">
                @foreach($courses as $course)
                    @php
                        $thumb = $course->thumbnail;
                        $thumbUrl = $thumb
                            ? (Str::startsWith($thumb, ['http://', 'https://'])
                                ? $thumb
                                : (Str::startsWith($thumb, 'img/') ? asset($thumb) : asset('storage/' . ltrim($thumb, '/'))))
                            : asset('img/images.jpg');

                        $sc = $course->status === 'published' ? 'success'
                            : ($course->status === 'archived' ? 'secondary' : 'warning');
                    @endphp

                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm">
                            <img src="{{ $thumbUrl }}" class="card-img-top"
                                style="height: 160px; object-fit: cover;" alt="{{ $course->title }}">

                            <div class="card-body d-flex flex-column">
                                <div class="mb-2">
                                    <span class="badge badge-{{ $sc }}">{{ ucfirst($course->status) }}</span>
                                    @if($course->duration_weeks)
                                        <span class="badge badge-info">{{ $course->duration_weeks }} minggu</span>
                                    @endif
                                </div>

                                <h5 class="card-title font-weight-bold mb-1">{{ $course->title }}</h5>

                                <small class="text-muted mb-3">
                                    <i class="fas fa-chalkboard-teacher mr-1"></i>
                                    @forelse($course->instructors as $inst)
                                        {{ $inst->name }}{{ !$loop->last ? ', ' : '' }}
                                    @empty
                                        —
                                    @endforelse
                                </small>

                                <div class="mt-auto">
                                    <a href="{{ route('courses.materials.index', $course->id) }}"
                                        class="btn btn-primary btn-block">
                                        <i class="fas fa-book-open mr-1"></i>
                                        {{ $role === 'pengajar' ? 'Kelola Materi' : 'Buka Materi' }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</section>
@endsection
