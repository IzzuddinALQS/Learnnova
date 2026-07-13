@extends('main')

@section('title', 'Hasil Attempt Quiz')

@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0" style="font-size:1.3rem">Hasil Attempt Quiz</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Kelas</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('quizzes.index') }}">Quiz</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('quizzes.attempts.index', $quiz->id) }}">Attempts</a></li>
                    <li class="breadcrumb-item active">Hasil</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-clipboard-list mr-2"></i>
            Hasil Attempt — {{ $quiz->title }}
        </h3>
    </div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row mb-4">
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th>Pelajar</th>
                        <td>{{ $attempt->student->name }}</td>
                    </tr>
                    <tr>
                        <th>Dimulai</th>
                        <td>{{ $attempt->started_at ? $attempt->started_at->format('d M Y H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Selesai</th>
                        <td>{{ $attempt->finished_at ? $attempt->finished_at->format('d M Y H:i') : '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th>Skor</th>
                        <td>{{ $attempt->score !== null ? $attempt->score : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($attempt->score === null)
                                <span class="badge badge-warning">Menunggu Nilai</span>
                            @else
                                <span class="badge badge-{{ $attempt->is_passed ? 'success' : 'danger' }}">
                                    {{ $attempt->is_passed ? 'Lulus' : 'Tidak Lulus' }}
                                </span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        @if(Auth::user()->hasPermission('quizzes.grade'))
            <a href="{{ route('quizzes.attempts.grade.form', [$quiz->id, $attempt->id]) }}" class="btn btn-warning btn-sm mb-4">
                <i class="fas fa-check"></i> Nilai / Perbarui Nilai
            </a>
        @endif

        <div class="mb-4">
            <h5>Rincian Jawaban</h5>
        </div>

        @foreach($attempt->answers as $answer)
            <div class="card mb-3">
                <div class="card-body">
                    <p class="font-weight-bold">Soal {{ $loop->iteration }}: {{ $answer->question->question }}</p>

                    @if($answer->question->type === 'multiple_choice' || $answer->question->type === 'true_false')
                        <p><strong>Jawaban:</strong> {{ $answer->option?->option_text ?? 'Tidak dijawab' }}</p>
                        <p><strong>Hasil:</strong>
                            @if($answer->is_correct)
                                <span class="text-success">Benar</span>
                            @elseif($answer->is_correct === false)
                                <span class="text-danger">Salah</span>
                            @else
                                <span class="text-muted">Belum dinilai</span>
                            @endif
                        </p>
                        <p><strong>Point diperoleh:</strong> {{ $answer->points_earned ?? 0 }} / {{ $answer->question->points }}</p>
                    @else
                        <p><strong>Jawaban:</strong></p>
                        <div class="border rounded p-3 mb-2">{{ $answer->text_answer ?? '-' }}</div>
                        <p><strong>Point yang diakui:</strong> {{ $answer->points_earned ?? '-' }} / {{ $answer->question->points }}</p>
                    @endif

                    @if($answer->question->explanation)
                        <p><strong>Penjelasan:</strong> {{ $answer->question->explanation }}</p>
                    @endif
                </div>
            </div>
        @endforeach

    </div>
    <div class="card-footer">
        <a href="{{ route('quizzes.attempts.index', $quiz->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

@endsection
