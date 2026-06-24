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
                <td>
                    @if(Auth::user()->hasRole('pelajar'))
                        {{ $attemptUsed }} / {{ $quiz->max_attempts }}
                    @else

                        {{ $quiz->max_attempts}}

                        @endif
                </td>
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

    @if($quiz->max_attempts && $attemptUsed >= $quiz->max_attempts)

        <button class="btn btn-secondary" disabled>

            <i class="fas fa-ban"></i>

            Batas Percobaan Habis

        </button>

    @else

        <a href="{{ route('quizzes.attempts.take', $quiz->id) }}"
           class="btn btn-success">

            <i class="fas fa-play"></i>

            Ikuti Quiz

        </a>

    @endif

@endif

        @if(auth()->user()->hasrole('pengajar'))
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

@if(Auth::user()->hasRole('pelajar'))

<div class="card mt-4">

    <div class="card-header">

        <h3 class="card-title">

            Riwayat Pengerjaan Quiz

        </h3>

    </div>

    <div class="card-body">

        @if($attempts->isEmpty())

            <div class="alert alert-info">

                Belum ada riwayat pengerjaan.

            </div>

        @else

            <table class="table table-bordered">

                <thead>

                    <tr>

                        <th>#</th>

                        <th>Tanggal</th>

                        <th>Skor</th>

                        <th>Status</th>

                        <th>Aksi</th>

                    </tr>

                </thead>

                <tbody>

                @foreach($attempts as $attempt)

                    <tr>

                        <td>

                            {{ $loop->iteration }}

                        </td>

                        <td>

                            {{ $attempt->created_at->format('d M Y H:i') }}

                        </td>

                        <td>

                            {{ $attempt->score ?? '-' }}

                        </td>

                        <td>

                            @if($attempt->is_passed === true)

                                <span class="badge badge-success">

                                    Lulus

                                </span>

                            @elseif($attempt->is_passed === false)

                                <span class="badge badge-danger">

                                    Tidak Lulus

                                </span>

                            @else

                                <span class="badge badge-warning">

                                    Menunggu Penilaian

                                </span>

                            @endif

                        </td>

                        <td>

                            <a href="{{ route('quizzes.attempts.show', [$quiz->id, $attempt->id]) }}"

                               class="btn btn-info btn-sm">

                                <i class="fas fa-eye"></i>

                                Lihat Review

                            </a>

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        @endif

    </div>

</div>

@endif

@endsection