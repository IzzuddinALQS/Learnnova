@extends('main')

@section('title', 'Daftar Attempt Quiz')

@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-tasks mr-2"></i>
            {{ Auth::user()->hasPermission('quizzes.grade') ? 'Daftar Attempt Quiz' : 'Attempt Saya' }} — {{ $quiz->title }}
        </h3>
    </div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="mb-3">
            <a href="{{ route('quizzes.show', $quiz->id) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali ke Detail Quiz
            </a>
            @if(Auth::user()->hasPermission('quizzes.take'))
                <a href="{{ route('quizzes.attempts.take', $quiz->id) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-play"></i> Ikuti Quiz
                </a>
            @endif
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    @if(Auth::user()->hasPermission('quizzes.grade'))
                        <th>Pelajar</th>
                    @endif
                    <th>Dimulai</th>
                    <th>Selesai</th>
                    <th>Skor</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attempts as $attempt)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        @if(Auth::user()->hasPermission('quizzes.grade'))
                            <td>{{ $attempt->student->name }}</td>
                        @endif
                        <td>{{ $attempt->started_at ? $attempt->started_at->format('d M Y H:i') : '-' }}</td>
                        <td>{{ $attempt->finished_at ? $attempt->finished_at->format('d M Y H:i') : '-' }}</td>
                        <td>{{ $attempt->score !== null ? $attempt->score : '-' }}</td>
                        <td>
                            @if($attempt->score === null)
                                <span class="badge badge-warning">Menunggu Nilai</span>
                            @else
                                <span class="badge badge-{{ $attempt->is_passed ? 'success' : 'danger' }}">
                                    {{ $attempt->is_passed ? 'Lulus' : 'Tidak Lulus' }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('quizzes.attempts.show', [$quiz->id, $attempt->id]) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Belum ada attempt untuk quiz ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
