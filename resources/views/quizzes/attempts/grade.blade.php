@extends('main')

@section('title', 'Beri Nilai Quiz')

@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-star mr-2"></i>
            Beri Nilai — {{ $quiz->title }}
        </h3>
    </div>

    <div class="card-body">

        <div class="mb-4">
            <p><strong>Pelajar:</strong> {{ $attempt->student->name }}</p>
            <p><strong>Dimulai:</strong> {{ $attempt->started_at ? $attempt->started_at->format('d M Y H:i') : '-' }}</p>
            <p><strong>Selesai:</strong> {{ $attempt->finished_at ? $attempt->finished_at->format('d M Y H:i') : '-' }}</p>
        </div>

        <form action="{{ route('quizzes.attempts.grade', [$quiz->id, $attempt->id]) }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="score">Skor Akhir</label>
                <input type="number" step="0.01" min="0" name="score" id="score"
                       class="form-control @error('score') is-invalid @enderror"
                       value="{{ old('score', $attempt->score ?? 0) }}">
                @error('score')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="is_passed">Status Kelulusan</label>
                <select name="is_passed" id="is_passed" class="form-control">
                    <option value="1" {{ old('is_passed', $attempt->is_passed) == 1 ? 'selected' : '' }}>Lulus</option>
                    <option value="0" {{ old('is_passed', $attempt->is_passed) == 0 ? 'selected' : '' }}>Tidak Lulus</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Nilai
            </button>
            <a href="{{ route('quizzes.attempts.show', [$quiz->id, $attempt->id]) }}" class="btn btn-secondary">
                Batal
            </a>
        </form>
    </div>
</div>

@endsection
