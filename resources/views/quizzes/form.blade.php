@extends('main')

@php
    $editQuiz = $quiz ?? null;
@endphp

@section('title', $editQuiz ? 'Edit Quiz' : 'Tambah Quiz')

@section('content')

<div class="container-fluid">

    <div class="card card-primary">

        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-question-circle mr-2"></i>
                {{ $editQuiz ? 'Edit Quiz' : 'Tambah Quiz' }}
            </h3>
        </div>

        <form id="quizForm"
              action="{{ $editQuiz ? route('quizzes.update', $editQuiz->id) : route('quizzes.store') }}"
              method="POST">

            @csrf

            @if($editQuiz)
                @method('PUT')
            @endif

            <div class="card-body">

                <div class="form-group">
                    <label>Kelas <span class="text-danger">*</span></label>

                    <select name="course_id" class="form-control" required>
                        <option value="">-- Pilih Kelas --</option>

                        @foreach($courses as $course)
                            <option value="{{ $course->id }}"
                                {{ old('course_id', $editQuiz->course_id ?? '') == $course->id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <div class="form-group">
                    <label>Judul Quiz</label>

                    <input type="text"
                           name="title"
                           class="form-control"
                           value="{{ old('title', $editQuiz->title ?? '') }}"
                           required>
                </div>

                <div class="form-group">
                    <label>Deskripsi</label>

                    <textarea name="description"
                              class="form-control"
                              rows="4">{{ old('description', $editQuiz->description ?? '') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Deadline</label>

                    <input type="datetime-local"
                           name="deadline"
                           class="form-control"
                           value="{{ old(
                                'deadline',
                                isset($editQuiz->deadline)
                                    ? \Carbon\Carbon::parse($editQuiz->deadline)->format('Y-m-d\TH:i')
                                    : ''
                           ) }}">
                </div>

                <div class="row">

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Durasi (Menit)</label>

                            <input type="number"
                                   name="duration_minutes"
                                   class="form-control"
                                   value="{{ old('duration_minutes', $editQuiz->duration_minutes ?? 60) }}">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Maksimal Percobaan</label>

                            <input type="number"
                                   name="max_attempts"
                                   class="form-control"
                                   value="{{ old('max_attempts', $editQuiz->max_attempts ?? 1) }}">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Passing Score</label>

                            <input type="number"
                                   name="passing_score"
                                   class="form-control"
                                   value="{{ old('passing_score', $editQuiz->passing_score ?? 70) }}">
                        </div>
                    </div>

                </div>

            </div>

            <div class="card-footer">

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i>
                    {{ $editQuiz ? 'Update Quiz' : 'Simpan Quiz' }}
                </button>

                <a href="{{ route('quizzes.index') }}"
                   class="btn btn-secondary">
                    Kembali
                </a>

            </div>

        </form>

    </div>

</div>

@endsection

@push('scripts')
<script>
    ajaxForm('#quizForm');
</script>
@endpush