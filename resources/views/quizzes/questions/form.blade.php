@extends('main')

@php
$editQuestion = $question ?? null;
@endphp

@section('title', $editQuestion ? 'Edit Soal' : 'Tambah Soal')

@section('content')

<div class="card card-primary">

    <div class="card-header">

        <h3 class="card-title">

            {{ $editQuestion ? 'Edit Soal' : 'Tambah Soal' }}

        </h3>

    </div>

    <form method="POST"
          action="{{ $editQuestion
            ? route('quizzes.questions.update', [$quiz->id, $editQuestion->id])
            : route('quizzes.questions.store', $quiz->id) }}">

        @csrf

        @if($editQuestion)
            @method('PUT')
        @endif

        <div class="card-body">

            <div class="form-group">

                <label>Pertanyaan</label>

                <textarea name="question"
                          rows="4"
                          class="form-control"
                          required>{{ old('question', $editQuestion->question ?? '') }}</textarea>

            </div>

            <div class="form-group">

                <label>Tipe Soal</label>

                <select name="type"
                        class="form-control">

                    <option value="multiple_choice">
                        Pilihan Ganda
                    </option>

                    <option value="true_false">
                        Benar / Salah
                    </option>

                    <option value="short_answer">
                        Jawaban Singkat
                    </option>

                    <option value="essay">
                        Essay
                    </option>

                </select>

            </div>

            <div class="form-group">

                <label>Poin</label>

                <input type="number"
                       name="points"
                       class="form-control"
                       value="{{ old('points', $editQuestion->points ?? 1) }}">

            </div>

            <div class="form-group">

                <label>Pembahasan</label>

                <textarea name="explanation"
                          class="form-control"
                          rows="3">{{ old('explanation', $editQuestion->explanation ?? '') }}</textarea>

            </div>

        </div>

        <div class="card-footer">

            <button type="submit"
                    class="btn btn-primary">

                Simpan

            </button>

            <a href="{{ route('quizzes.questions.index', $quiz->id) }}"
               class="btn btn-secondary">

                Kembali

            </a>

        </div>

    </form>

</div>

@endsection