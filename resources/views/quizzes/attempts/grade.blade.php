@extends('main')

@section('title', 'Beri/Perbarui Nilai Quiz')

@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0" style="font-size:1.3rem">Koreksi & Nilai Attempt</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Kelas</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('quizzes.index') }}">Quiz</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('quizzes.attempts.index', $quiz->id) }}">Attempts</a></li>
                    <li class="breadcrumb-item active">Koreksi</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-check-double mr-2"></i>
            Koreksi & Nilai Attempt — {{ $quiz->title }}
        </h3>
    </div>

    <div class="card-body">
        <div class="mb-4">
            <table class="table table-sm table-borderless bg-light p-3 rounded">
                <tr>
                    <th style="width: 120px;">Pelajar:</th>
                    <td>{{ $attempt->student->name }}</td>
                    <th style="width: 120px;">Dimulai:</th>
                    <td>{{ $attempt->started_at ? $attempt->started_at->format('d M Y H:i') : '-' }}</td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td>{{ $attempt->student->email }}</td>
                    <th>Selesai:</th>
                    <td>{{ $attempt->finished_at ? $attempt->finished_at->format('d M Y H:i') : '-' }}</td>
                </tr>
            </table>
        </div>

        <form action="{{ route('quizzes.attempts.grade', [$quiz->id, $attempt->id]) }}" method="POST">
            @csrf

            <div class="row">
                <!-- Kolom Kiri: Rincian Soal & Jawaban -->
                <div class="col-md-8">
                    <h5 class="mb-3 text-secondary border-bottom pb-2">Rincian Jawaban & Penilaian Per Soal</h5>

                    @foreach($attempt->answers as $answer)
                        @php
                            $isAutoScored = in_array($answer->question->type, ['multiple_choice', 'true_false']);
                        @endphp
                        <div class="card mb-3 card-outline {{ $isAutoScored ? 'card-secondary' : 'card-primary' }}">
                            <div class="card-header py-2">
                                <span class="badge {{ $isAutoScored ? 'badge-secondary' : 'badge-primary' }} float-right">
                                    {{ $isAutoScored ? 'Pilihan Ganda / TF (Auto)' : 'Esai / Isian (Manual)' }}
                                </span>
                                <h6 class="card-title mb-0 font-weight-bold">
                                    Soal {{ $loop->iteration }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="font-weight-bold text-dark mb-3">{{ $answer->question->question }}</p>

                                @if($isAutoScored)
                                    <div class="p-2 mb-2 rounded bg-light border">
                                        <strong>Jawaban Pelajar:</strong> 
                                        <span class="ml-1 text-primary">{{ $answer->option?->option_text ?? 'Tidak dijawab' }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Hasil Sistem:</strong>
                                        @if($answer->is_correct)
                                            <span class="badge badge-success ml-1"><i class="fas fa-check"></i> Benar</span>
                                        @elseif($answer->is_correct === false)
                                            <span class="badge badge-danger ml-1"><i class="fas fa-times"></i> Salah</span>
                                        @else
                                            <span class="badge badge-warning ml-1">Belum dinilai</span>
                                        @endif
                                        <span class="text-muted ml-3">(Skor Sistem: {{ $answer->points_earned ?? 0 }} / {{ $answer->question->points }})</span>
                                    </div>
                                @else
                                    <div class="mb-3">
                                        <strong class="d-block mb-1">Jawaban Pelajar:</strong>
                                        <div class="border rounded p-3 bg-light text-dark font-italic" style="white-space: pre-line;">{{ $answer->text_answer ?? '-' }}</div>
                                    </div>
                                @endif

                                @if($answer->question->explanation)
                                    <div class="alert alert-info py-2 px-3 text-sm mb-3">
                                        <strong>Penjelasan/Kunci:</strong> {{ $answer->question->explanation }}
                                    </div>
                                @endif

                                <div class="form-group row align-items-center mb-0 mt-3 pt-2 border-top">
                                    <label class="col-sm-4 col-form-label font-weight-bold text-success">Poin Penilaian:</label>
                                    <div class="col-sm-5">
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="0.01" min="0" max="{{ $answer->question->points }}"
                                                   class="form-control question-score-input"
                                                   name="answers_points[{{ $answer->id }}]"
                                                   id="input-answer-{{ $answer->id }}"
                                                   data-is-auto="{{ $isAutoScored ? '1' : '0' }}"
                                                   data-system-score="{{ $isAutoScored ? ($answer->points_earned ?? 0) : '0' }}"
                                                   data-max="{{ $answer->question->points }}"
                                                   value="{{ old('answers_points.'.$answer->id, $answer->points_earned ?? 0) }}">
                                            <div class="input-group-append">
                                                <span class="input-group-text">/ {{ $answer->question->points }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Kolom Kanan: Summary Panel (Sticky) -->
                <div class="col-md-4">
                    <div class="card card-outline card-success sticky-top" style="top: 20px; z-index: 10;">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold text-success">
                                <i class="fas fa-calculator mr-2"></i> Ringkasan Nilai
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <span class="text-muted font-weight-bold">Skor Sistem (Auto):</span>
                                <span id="system-score-display" class="h4 font-weight-bold text-secondary mb-0">0.00</span>
                            </div>

                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <span class="text-muted font-weight-bold">Skor Review (Total):</span>
                                <div>
                                    <span id="review-score-display" class="h3 font-weight-bold text-success mb-0">0.00</span>
                                    <span class="text-muted">/ <span id="total-max-points">0</span></span>
                                </div>
                            </div>

                            <hr>

                            <div class="form-group">
                                <label for="is_passed" class="font-weight-bold">Status Kelulusan</label>
                                <select name="is_passed" id="is_passed" class="form-control">
                                    <option value="1" {{ old('is_passed', $attempt->is_passed) == 1 ? 'selected' : '' }}>Lulus</option>
                                    <option value="0" {{ old('is_passed', $attempt->is_passed) == 0 ? 'selected' : '' }}>Tidak Lulus</option>
                                </select>
                                <div class="mt-2 text-sm text-muted">
                                    Passing Score: <strong>{{ $quiz->passing_score }}%</strong>
                                </div>
                            </div>

                            <div class="mt-4 d-flex justify-content-end">
                                <a href="{{ route('quizzes.attempts.show', [$quiz->id, $attempt->id]) }}" class="btn btn-default mr-2">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save mr-1"></i> Perbarui Nilai
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputs = document.querySelectorAll('.question-score-input');
    const systemScoreDisplay = document.getElementById('system-score-display');
    const reviewScoreDisplay = document.getElementById('review-score-display');
    const totalMaxPointsDisplay = document.getElementById('total-max-points');
    const isPassedSelect = document.getElementById('is_passed');
    const passingScorePercent = {{ $quiz->passing_score }};
    
    let userManuallyToggledPassed = false;

    isPassedSelect.addEventListener('change', function() {
        userManuallyToggledPassed = true;
    });

    function calculateScores() {
        let systemScoreSum = 0;
        let reviewScoreSum = 0;
        let maxPointsSum = 0;

        inputs.forEach(input => {
            const max = parseFloat(input.dataset.max) || 0;
            let val = parseFloat(input.value);
            
            // Validate boundaries
            if (isNaN(val) || val < 0) {
                val = 0;
            } else if (val > max) {
                val = max;
                input.value = max;
            }

            reviewScoreSum += val;
            maxPointsSum += max;

            if (input.dataset.isAuto === '1') {
                systemScoreSum += parseFloat(input.dataset.systemScore) || 0;
            }
        });

        systemScoreDisplay.textContent = systemScoreSum.toFixed(2);
        reviewScoreDisplay.textContent = reviewScoreSum.toFixed(2);
        totalMaxPointsDisplay.textContent = maxPointsSum.toFixed(0);

        if (!userManuallyToggledPassed) {
            const percentage = maxPointsSum > 0 ? (reviewScoreSum / maxPointsSum * 100) : 0;
            if (percentage >= passingScorePercent) {
                isPassedSelect.value = '1';
            } else {
                isPassedSelect.value = '0';
            }
        }
    }

    inputs.forEach(input => {
        input.addEventListener('input', calculateScores);
        input.addEventListener('change', calculateScores);
    });

    calculateScores();
});
</script>
@endpush
