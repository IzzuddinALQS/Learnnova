@extends('main')

@section('title', 'Ikuti Quiz')

@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0" style="font-size:1.3rem">Kerjakan Quiz</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Kelas</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('quizzes.index') }}">Quiz</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('quizzes.show', $quiz->id) }}">Detail</a></li>
                    <li class="breadcrumb-item active">Kerjakan</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-play mr-2"></i>
            Ikuti Quiz: {{ $quiz->title }}
        </h3>
    </div>

    <div class="card-body">

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="mb-4">
            <p><strong>Kelas:</strong> {{ $quiz->course->title ?? '-' }}</p>
            <p><strong>Deadline:</strong> {{ $quiz->deadline ? $quiz->deadline->format('d M Y H:i') : '-' }}</p>
            <p><strong>Durasi:</strong> {{ $quiz->duration_minutes ?? '-' }} menit</p>
            <p><strong>Jumlah percobaan:</strong> {{ $attemptCount + 1 }} / {{ $quiz->max_attempts ?? 'Tak terbatas' }}</p>
        </div>

        <form action="{{ route('quizzes.attempts.store', $quiz->id) }}" method="POST">
            @csrf

            @foreach($quiz->questions as $question)
                <div class="card card-outline card-info mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Soal {{ $loop->iteration }}</h5>
                    </div>
                    <div class="card-body">
                        <p class="font-weight-bold">{{ $question->question }}</p>

                        @if(in_array($question->type, ['multiple_choice', 'true_false']))
                            @foreach($question->options as $option)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio"
                                           name="answers[{{ $question->id }}]"
                                           id="question-{{ $question->id }}-option-{{ $option->id }}"
                                           value="{{ $option->id }}">
                                    <label class="form-check-label" for="question-{{ $question->id }}-option-{{ $option->id }}">
                                        {{ $option->option_text }}
                                    </label>
                                </div>
                            @endforeach
                        @else
                            <div class="form-group">
                                <textarea name="answers_text[{{ $question->id }}]" class="form-control" rows="4" placeholder="Jawaban Anda...">{{ old('answers_text.' . $question->id) }}</textarea>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            <button type="submit" class="btn btn-success">
                <i class="fas fa-paper-plane"></i> Kirim Jawaban
            </button>
            <a href="{{ route('quizzes.show', $quiz->id) }}" class="btn btn-secondary">
                Batal
            </a>
        </form>
    </div>
</div>

@endsection
