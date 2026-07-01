@extends('main')

@php $editCourse = $course ?? null; @endphp

@section('title', $editCourse ? 'Edit Kelas' : 'Tambah Kelas')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">{{ $editCourse ? 'Edit Kelas' : 'Tambah Kelas' }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Kelas</a></li>
                    <li class="breadcrumb-item active">{{ $editCourse ? 'Edit' : 'Tambah' }}</li>
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
                    <i class="fas fa-chalkboard-teacher mr-2"></i>
                    {{ $editCourse ? 'Edit Kelas' : 'Form Tambah Kelas' }}
                </h3>
            </div>

            <form id="courseForm"
                action="{{ $editCourse ? route('courses.update', $editCourse->id) : route('courses.store') }}"
                method="POST"
                enctype="multipart/form-data">
                @csrf
                @if($editCourse) @method('PUT') @endif

                <div class="card-body">

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Judul --}}
                    <div class="form-group">
                        <label>Judul Kelas <span class="text-danger">*</span></label>
                        <input type="text" name="title"
                            class="form-control @error('title') is-invalid @enderror"
                            value="{{ old('title', $editCourse->title ?? '') }}"
                            placeholder="Contoh: Pemrograman Web dengan Laravel">
                        @error('title')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="description" rows="4"
                            class="form-control @error('description') is-invalid @enderror"
                            placeholder="Deskripsi singkat tentang kelas ini...">{{ old('description', $editCourse->description ?? '') }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row">
                        {{-- Pengajar (multiple) --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>
                                    Pengajar <span class="text-danger">*</span>
                                    <small class="text-muted font-weight-normal">
                                        — urutan pertama = pengajar utama
                                    </small>
                                </label>
                                @php
                                    $selectedInstructorIds = old(
                                        'instructor_ids',
                                        isset($editCourse) ? $editCourse->instructors->pluck('id')->toArray() : []
                                    );
                                @endphp
                                <select name="instructor_ids[]"
                                    id="instructorSelect"
                                    class="form-control select2-multiple @error('instructor_ids') is-invalid @enderror"
                                    multiple>
                                    @foreach($instructors as $instructor)
                                        <option value="{{ $instructor->id }}"
                                            {{ in_array($instructor->id, $selectedInstructorIds) ? 'selected' : '' }}>
                                            {{ $instructor->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('instructor_ids')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                                {{-- Preview urutan pengajar --}}
                                <div id="instructor-order" class="mt-2"></div>
                            </div>
                        </div>

                        {{-- Silabus --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Silabus</label>
                                <select name="syllabus_id"
                                    class="form-control select2 @error('syllabus_id') is-invalid @enderror">
                                    <option value="">-- Tanpa Silabus --</option>
                                    @foreach($syllabuses as $syllabus)
                                        <option value="{{ $syllabus->id }}"
                                            {{ old('syllabus_id', $editCourse->syllabus_id ?? '') == $syllabus->id ? 'selected' : '' }}>
                                            {{ $syllabus->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('syllabus_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- Status --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Status <span class="text-danger">*</span></label>
                                <select name="status"
                                    class="form-control select2 @error('status') is-invalid @enderror">
                                    <option value="draft"
                                        {{ old('status', $editCourse->status ?? 'draft') === 'draft' ? 'selected' : '' }}>
                                        Draft
                                    </option>
                                    <option value="published"
                                        {{ old('status', $editCourse->status ?? '') === 'published' ? 'selected' : '' }}>
                                        Published
                                    </option>
                                    <option value="archived"
                                        {{ old('status', $editCourse->status ?? '') === 'archived' ? 'selected' : '' }}>
                                        Archived
                                    </option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Durasi --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Durasi (Minggu)</label>
                                <input type="number" name="duration_weeks" min="1"
                                    class="form-control @error('duration_weeks') is-invalid @enderror"
                                    value="{{ old('duration_weeks', $editCourse->duration_weeks ?? '') }}"
                                    placeholder="Contoh: 12">
                                @error('duration_weeks')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Maks. Pelajar --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Maks. Pelajar</label>
                                <input type="number" name="max_students" min="1"
                                    class="form-control @error('max_students') is-invalid @enderror"
                                    value="{{ old('max_students', $editCourse->max_students ?? '') }}"
                                    placeholder="Contoh: 30">
                                @error('max_students')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- Bobot Tugas --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Bobot Tugas (%) <span class="text-danger">*</span></label>
                                <input type="number" name="assignment_weight" min="0" max="100"
                                    class="form-control @error('assignment_weight') is-invalid @enderror"
                                    value="{{ old('assignment_weight', $editCourse->assignment_weight ?? '60') }}">
                                @error('assignment_weight')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Bobot Kuis --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Bobot Kuis (%) <span class="text-danger">*</span></label>
                                <input type="number" name="quiz_weight" min="0" max="100"
                                    class="form-control @error('quiz_weight') is-invalid @enderror"
                                    value="{{ old('quiz_weight', $editCourse->quiz_weight ?? '40') }}">
                                @error('quiz_weight')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Thumbnail --}}
                    <div class="form-group">
                        <label>Thumbnail</label>
                        @if($editCourse && $editCourse->thumbnail)
                            @php
                                $thumb = $editCourse->thumbnail;
                                $thumbUrl = Str::startsWith($thumb, ['http://', 'https://'])
                                    ? $thumb
                                    : (Str::startsWith($thumb, 'img/') ? asset($thumb) : asset('storage/' . ltrim($thumb, '/')));
                            @endphp
                            <div class="mb-2">
                                <img src="{{ $thumbUrl }}" alt="Thumbnail saat ini"
                                    style="height: 100px; object-fit: cover; border-radius: 6px; border: 1px solid #dee2e6;">
                                <small class="d-block text-muted mt-1">Upload baru untuk mengganti.</small>
                            </div>
                        @endif
                        <div class="custom-file">
                            <input type="file" name="thumbnail" class="custom-file-input @error('thumbnail') is-invalid @enderror"
                                id="thumbnailInput" accept="image/*">
                            <label class="custom-file-label" for="thumbnailInput">Pilih gambar...</label>
                        </div>
                        @error('thumbnail')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>
                        {{ $editCourse ? 'Update Kelas' : 'Simpan Kelas' }}
                    </button>
                    <a href="{{ route('courses.index') }}" class="btn btn-secondary ml-2">
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
    // Select2 biasa untuk Silabus & Status
    $('.select2').select2({ theme: 'bootstrap4' });

    // Select2 multiple untuk Pengajar
    $('#instructorSelect').select2({
        theme: 'bootstrap4',
        placeholder: '-- Pilih satu atau lebih pengajar --',
        allowClear: true,
    });

    // Tampilkan urutan pengajar (pertama = utama)
    function updateInstructorOrder() {
        const selected = $('#instructorSelect').select2('data');
        if (selected.length === 0) {
            $('#instructor-order').html('');
            return;
        }
        let html = '<small class="text-muted d-block mb-1">Urutan pengajar:</small>';
        selected.forEach((item, i) => {
            const badge = i === 0
                ? '<span class="badge badge-primary mr-1">Utama</span>'
                : `<span class="badge badge-secondary mr-1">#${i + 1}</span>`;
            html += `<div class="d-inline-block mr-2 mb-1">${badge}${item.text}</div>`;
        });
        $('#instructor-order').html(html);
    }

    $('#instructorSelect').on('change', updateInstructorOrder);
    updateInstructorOrder(); // init saat edit

    // Update label custom-file saat file dipilih
    $('#thumbnailInput').on('change', function () {
        const fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').text(fileName || 'Pilih gambar...');
    });

    // Auto-calculate bobot
    $('input[name="assignment_weight"]').on('input', function() {
        let val = parseInt($(this).val()) || 0;
        if (val > 100) { val = 100; $(this).val(val); }
        if (val < 0) { val = 0; $(this).val(val); }
        $('input[name="quiz_weight"]').val(100 - val);
    });

    $('input[name="quiz_weight"]').on('input', function() {
        let val = parseInt($(this).val()) || 0;
        if (val > 100) { val = 100; $(this).val(val); }
        if (val < 0) { val = 0; $(this).val(val); }
        $('input[name="assignment_weight"]').val(100 - val);
    });
});

ajaxForm('#courseForm');
</script>
@endpush
