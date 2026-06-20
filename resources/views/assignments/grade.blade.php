@extends('main')

@section('title', 'Beri Nilai — ' . $submission->student->name)

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Beri Nilai</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('assignments.index') }}">Tugas</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('assignments.show', $assignment->id) }}">{{ Str::limit($assignment->title, 20) }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('assignments.submissions', $assignment->id) }}">Submission</a></li>
                        <li class="breadcrumb-item active">{{ $submission->student->name }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">

                {{-- Kolom Jawaban Pelajar --}}
                <div class="col-lg-8 col-md-12 mb-4">

                    {{-- Jawaban Teks --}}
                    @if($submission->text_answer)
                        <div class="card card-primary card-outline mb-4">
                            <div class="card-header">
                                <h3 class="card-title font-weight-bold">
                                    <i class="fas fa-file-alt mr-2"></i>Jawaban Teks
                                </h3>
                            </div>
                            <div class="card-body">
                                <p style="line-height: 1.8;">{{ $submission->text_answer }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- File Jawaban --}}
                    @if($submission->file_path)
                        <div class="card card-info card-outline mb-4">
                            <div class="card-header">
                                <h3 class="card-title font-weight-bold">
                                    <i class="fas fa-paperclip mr-2"></i>File Jawaban
                                </h3>
                            </div>
                            <div class="card-body">
                                <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank"
                                    class="btn btn-info btn-sm">
                                    <i class="fas fa-download mr-1"></i> Unduh / Lihat File
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Catatan Pelajar --}}
                    @if($submission->note)
                        <div class="card card-warning card-outline mb-4">
                            <div class="card-header">
                                <h3 class="card-title font-weight-bold">
                                    <i class="fas fa-sticky-note mr-2"></i>Catatan Pelajar
                                </h3>
                            </div>
                            <div class="card-body">
                                <p>{{ $submission->note }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Tidak ada jawaban --}}
                    @if(!$submission->text_answer && !$submission->file_path)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Pelajar belum mengisi jawaban teks maupun file.
                        </div>
                    @endif

                    {{-- Form Grading --}}
                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold">
                                <i class="fas fa-star mr-2"></i>Form Penilaian
                            </h3>
                        </div>
                        <form id="gradeForm"
                            action="{{ route('assignments.grade', [$assignment->id, $submission->id]) }}"
                            method="POST">
                            @csrf
                            <div class="card-body">

                                {{-- Nilai --}}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nilai <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" name="score"
                                                    class="form-control @error('score') is-invalid @enderror"
                                                    min="0" max="{{ $assignment->max_score }}"
                                                    value="{{ old('score', $submission->score ?? '') }}"
                                                    placeholder="0 - {{ $assignment->max_score }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">/ {{ $assignment->max_score }}</span>
                                                </div>
                                            </div>
                                            @error('score')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Status <span class="text-danger">*</span></label>
                                            <select name="status"
                                                class="form-control @error('status') is-invalid @enderror">
                                                <option value="graded"
                                                    {{ old('status', $submission->status) === 'graded' ? 'selected' : '' }}>
                                                    Dinilai
                                                </option>
                                                <option value="returned"
                                                    {{ old('status', $submission->status) === 'returned' ? 'selected' : '' }}>
                                                    Kembalikan (Minta Revisi)
                                                </option>
                                            </select>
                                            @error('status')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- Feedback --}}
                                <div class="form-group">
                                    <label>Feedback <small class="text-muted">(opsional)</small></label>
                                    <textarea name="feedback" rows="4" class="form-control"
                                        placeholder="Berikan komentar atau masukan untuk pelajar...">{{ old('feedback', $submission->feedback ?? '') }}</textarea>
                                </div>

                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save mr-1"></i> Simpan Nilai
                                </button>
                                <a href="{{ route('assignments.submissions', $assignment->id) }}"
                                    class="btn btn-secondary ml-2">
                                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>

                </div>

                {{-- Kolom Info --}}
                <div class="col-lg-4 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i>Informasi</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted pl-3" style="width: 40%;">Pelajar</td>
                                    <td class="font-weight-bold">{{ $submission->student->name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted pl-3">Tugas</td>
                                    <td class="font-weight-bold">{{ $assignment->title }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted pl-3">Kelas</td>
                                    <td class="font-weight-bold">{{ $assignment->course->title }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted pl-3">Deadline</td>
                                    <td>{{ $assignment->due_date->format('d M Y, H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted pl-3">Dikumpulkan</td>
                                    <td>
                                        @if($submission->submitted_at)
                                            {{ $submission->submitted_at->format('d M Y, H:i') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted pl-3">Status</td>
                                    <td>
                                        @switch($submission->status)
                                            @case('draft')
                                                <span class="badge badge-warning">Draft</span>
                                                @break
                                            @case('submitted')
                                                <span class="badge badge-primary">Dikumpulkan</span>
                                                @break
                                            @case('graded')
                                                <span class="badge badge-success">Sudah Dinilai</span>
                                                @break
                                            @case('returned')
                                                <span class="badge badge-info">Dikembalikan</span>
                                                @break
                                        @endswitch
                                    </td>
                                </tr>
                                @if($submission->graded_at)
                                    <tr>
                                        <td class="text-muted pl-3">Dinilai</td>
                                        <td>{{ $submission->graded_at->format('d M Y, H:i') }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        ajaxForm('#gradeForm');
    </script>
@endpush