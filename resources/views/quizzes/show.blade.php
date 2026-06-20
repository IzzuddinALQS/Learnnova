@extends('main')

@section('title', 'Detail Quiz')

@section('content')

<div class="card">

    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-eye mr-2"></i>
            Detail Quiz
        </h3>
    </div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <table class="table table-bordered">

            <tr>
                <th width="25%">Kelas</th>
                <td>{{ $quiz->course->title ?? '-' }}</td>
            </tr>

            <tr>
                <th>Judul Quiz</th>
                <td>{{ $quiz->title }}</td>
            </tr>

            <tr>
                <th>Deskripsi</th>
                <td>{{ $quiz->description ?: '-' }}</td>
            </tr>

            <tr>
                <th>Deadline</th>
                <td>
                    {{ $quiz->deadline
                        ? \Carbon\Carbon::parse($quiz->deadline)->format('d M Y H:i')
                        : '-' }}
                </td>
            </tr>

            <tr>
                <th>Durasi</th>
                <td>{{ $quiz->duration_minutes ?? '-' }} Menit</td>
            </tr>

            <tr>
                <th>Passing Score</th>
                <td>{{ $quiz->passing_score ?? '-' }}</td>
            </tr>

            <tr>
                <th>Maksimal Percobaan</th>
                <td>{{ $quiz->max_attempts ?? '-' }}</td>
            </tr>

        </table>

    </div>

    <div class="card-footer">

        <a href="{{ route('quizzes.index') }}"
           class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Kembali
        </a>

        @if(auth()->user()->hasPermission('quizzes.take'))
            <a href="{{ route('quizzes.attempts.take', $quiz->id) }}"
               class="btn btn-success">
                <i class="fas fa-play"></i>
                Ikuti Quiz
            </a>
        @endif

        @if(auth()->user()->hasPermission('quizzes.grade'))
            <a href="{{ route('quizzes.attempts.index', $quiz->id) }}"
               class="btn btn-primary">
                <i class="fas fa-clipboard-list"></i>
                Daftar Attempt
            </a>
        @endif

        @if(
            auth()->user()->hasRole('super_admin') ||
            auth()->user()->hasRole('pengajar') ||
            auth()->user()->hasRole('akademik')
        )
            <a href="{{ route('quizzes.questions.index', $quiz->id) }}"
               class="btn btn-info">
                <i class="fas fa-list"></i>
                Kelola Soal
            </a>
        @endif

    </div>

</div>

@endsection