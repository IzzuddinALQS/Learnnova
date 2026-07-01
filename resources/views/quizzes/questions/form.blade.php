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
                <select name="type" class="form-control">
                    @php $type = old('type', $editQuestion->type ?? 'multiple_choice'); @endphp
                    <option value="multiple_choice" {{ $type == 'multiple_choice' ? 'selected' : '' }}>Pilihan Ganda</option>
                    <option value="true_false" {{ $type == 'true_false' ? 'selected' : '' }}>Benar / Salah</option>
                    <option value="short_answer" {{ $type == 'short_answer' ? 'selected' : '' }}>Jawaban Singkat</option>
                    <option value="essay" {{ $type == 'essay' ? 'selected' : '' }}>Essay</option>
                </select>
            </div>

            <div class="form-group" id="true_false_answer" style="display: none;">
                <label>Kunci Jawaban</label>
                <select name="correct_answer_tf" class="form-control">
                    @php
                        $isTrue = old('correct_answer_tf') == '1';
                        if (!old('correct_answer_tf') && $editQuestion && $editQuestion->type === 'true_false') {
                            $trueOption = $editQuestion->options()->where('option_text', 'Benar')->first();
                            $isTrue = $trueOption && $trueOption->is_correct;
                        }
                    @endphp
                    <option value="1" {{ $isTrue ? 'selected' : '' }}>Benar</option>
                    <option value="0" {{ !$isTrue && $editQuestion && $editQuestion->type === 'true_false' ? 'selected' : '' }}>Salah</option>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.querySelector('select[name="type"]');
        const tfAnswerGroup = document.getElementById('true_false_answer');
        
        function toggleTfAnswer() {
            if (typeSelect.value === 'true_false') {
                tfAnswerGroup.style.display = 'block';
            } else {
                tfAnswerGroup.style.display = 'none';
            }
        }
        
        typeSelect.addEventListener('change', toggleTfAnswer);
        toggleTfAnswer(); // Initial check
    });
</script>
@endpush
@endsection