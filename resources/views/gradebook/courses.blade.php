@extends('main')

@section('title', 'Rekap Nilai')

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

            <div class="row mb-3">
                <div class="col-12">
                    <div class="text-muted">
                        Total: <strong>{{ $courses->count() }}</strong> kelas
                    </div>
                </div>
            </div>

            <div class="row">
                @forelse($courses as $course)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title font-weight-bold mb-1">{{ $course->title }}</h5>
                                <small class="text-muted mb-3">
                                    <i class="fas fa-users mr-1"></i>
                                    {{ $course->enrollments()->where('status', 'active')->count() }} pelajar aktif
                                    &nbsp;·&nbsp;
                                    <i class="fas fa-tasks mr-1"></i>
                                    {{ $course->assignments->count() }} tugas
                                    &nbsp;·&nbsp;
                                    <i class="fas fa-question-circle mr-1"></i>
                                    {{ $course->quizzes->count() }} kuis
                                </small>

                                <div class="mt-auto">
                                    <a href="{{ route('gradebook.course', $course->id) }}"
                                        class="btn btn-primary btn-block">
                                        <i class="fas fa-chart-bar mr-1"></i> Lihat Rekap Nilai
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">
                            Belum ada kelas yang Anda ajar.
                        </div>
                    </div>
                @endforelse
            </div>

        </div>
    </section>
@endsection