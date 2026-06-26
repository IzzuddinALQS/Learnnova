@extends('main')

@php $editAssignment = $assignment ?? null; @endphp

@section('title', $editAssignment ? 'Edit Tugas' : 'Tambah Tugas')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ $editAssignment ? 'Edit Tugas' : 'Tambah Tugas' }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('assignments.index') }}">Tugas</a></li>
                        <li class="breadcrumb-item active">{{ $editAssignment ? 'Edit' : 'Tambah' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tasks mr-2"></i>
                        {{ $editAssignment ? 'Edit Tugas' : 'Form Tambah Tugas' }}
                    </h3>
                </div>

                <form id="assignmentForm"
                    action="{{ $editAssignment ? route('assignments.update', $editAssignment->id) : route('assignments.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if ($editAssignment)
                        @method('PUT')
                    @endif

                    <div class="card-body">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Kelas --}}
                        <div class="form-group">
                            <label>Kelas <span class="text-danger">*</span></label>
                            <select name="course_id" class="form-control select2 @error('course_id') is-invalid @enderror">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}"
                                        {{ old('course_id', $editAssignment->course_id ?? '') == $course->id ? 'selected' : '' }}>
                                        {{ $course->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Judul & Nilai Maks --}}
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Judul Tugas <span class="text-danger">*</span></label>
                                    <input type="text" name="title"
                                        class="form-control @error('title') is-invalid @enderror"
                                        value="{{ old('title', $editAssignment->title ?? '') }}"
                                        placeholder="Contoh: Tugas 1 - HTML Dasar">
                                    @error('title')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Nilai Maksimal <span class="text-danger">*</span></label>
                                    <input type="number" name="max_score" min="1" max="100"
                                        class="form-control @error('max_score') is-invalid @enderror"
                                        value="{{ old('max_score', $editAssignment->max_score ?? 100) }}">
                                    @error('max_score')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Deadline --}}
                        <div class="form-group">
                            <label>Deadline <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="due_date"
                                class="form-control @error('due_date') is-invalid @enderror"
                                value="{{ old('due_date', $editAssignment?->due_date?->format('Y-m-d\TH:i') ?? '') }}">
                            @error('due_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Deskripsi --}}
                        <div class="form-group">
                            <label>Deskripsi / Instruksi <span class="text-danger">*</span></label>
                            <textarea name="description" rows="5"
                                class="form-control @error('description') is-invalid @enderror"
                                placeholder="Jelaskan instruksi tugas ini...">{{ old('description', $editAssignment->description ?? '') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- File Lampiran --}}
                        <div class="form-group">
                            <label>File Lampiran</label>
                            <div class="custom-file">
                                <input type="file" name="file" id="assignmentFile"
                                    class="custom-file-input @error('file') is-invalid @enderror">
                                <label class="custom-file-label" for="assignmentFile">Pilih file...</label>
                            </div>
                            <small class="text-muted">Format: PDF, DOC, DOCX, PPT, PPTX, PNG, JPG. Maks 2MB.</small>
                            @error('file')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror

                            {{-- File lama --}}
                            @if ($editAssignment?->file)
                                <div class="mt-2">
                                    <span class="text-muted">File saat ini: </span>
                                    <a href="{{ asset('storage/' . $editAssignment->file) }}" target="_blank">
                                        <i class="fas fa-paperclip mr-1"></i>Lihat File
                                    </a>
                                </div>
                            @endif
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>
                            {{ $editAssignment ? 'Update Tugas' : 'Simpan Tugas' }}
                        </button>
                        <a href="{{ $editAssignment ? route('assignments.show', $editAssignment->id) : route('assignments.index') }}"
                            class="btn btn-secondary ml-2">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('select[name="course_id"]').select2({
                theme: 'bootstrap4',
                placeholder: '-- Pilih Kelas --'
            }).val('{{ old('course_id', $editAssignment->course_id ?? '') }}').trigger('change');

            $('#assignmentFile').on('change', function () {
                const file = this.files[0];
                $('.custom-file-label').text(file ? file.name : 'Pilih file...');
            });
        });

        ajaxForm('#assignmentForm');
    </script>
@endpush