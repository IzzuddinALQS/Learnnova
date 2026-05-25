@extends('main')

@php $editSyllabus = $syllabus ?? null; @endphp

@section('title', $editSyllabus ? 'Edit Silabus' : 'Tambah Silabus')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ $editSyllabus ? 'Edit Silabus' : 'Tambah Silabus' }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('syllabus.index') }}">Silabus</a></li>
                        <li class="breadcrumb-item active">{{ $editSyllabus ? 'Edit' : 'Tambah' }}</li>
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
                        <i class="fas fa-book-open mr-2"></i>
                        {{ $editSyllabus ? 'Edit Silabus' : 'Form Tambah Silabus' }}
                    </h3>
                </div>

                <form id="syllabusForm"
                    action="{{ $editSyllabus ? route('syllabus.update', $editSyllabus->id) : route('syllabus.store') }}"
                    method="POST">
                    @csrf
                    @if ($editSyllabus)
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

                        {{-- Nama & Durasi --}}
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Nama Silabus <span class="text-danger">*</span></label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $editSyllabus->name ?? '') }}"
                                        placeholder="Contoh: Pemrograman Web Dasar">
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Durasi (Minggu) <span class="text-danger">*</span></label>
                                    <input type="number" name="duration_weeks" min="1"
                                        class="form-control @error('duration_weeks') is-invalid @enderror"
                                        value="{{ old('duration_weeks', $editSyllabus->duration_weeks ?? '') }}"
                                        placeholder="Contoh: 12">
                                    @error('duration_weeks')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Instruktur --}}
                        <div class="form-group">
                            <label>Instruktur <span class="text-danger">*</span></label>
                            <select name="instructor_id"
                                class="form-control select2 @error('instructor_id') is-invalid @enderror">
                                <option value="">-- Pilih Instruktur --</option>
                                @foreach ($instructors as $instructor)
                                    <option value="{{ $instructor->id }}"
                                        {{ old('instructor_id', $editSyllabus->instructor_id ?? '') == $instructor->id ? 'selected' : '' }}>
                                        {{ $instructor->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('instructor_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Tema / Cover --}}
                        <div class="form-group">
                            <label>Tema / Gambar Cover</label>
                            <select name="theme" class="form-control select2 @error('theme') is-invalid @enderror">
                                <option value="">-- Pilih Gambar --</option>
                                @foreach ($images as $image)
                                    <option value="{{ $image }}"
                                        {{ old('theme', $editSyllabus->theme ?? '') == $image ? 'selected' : '' }}>
                                        {{ $image }}
                                    </option>
                                @endforeach
                            </select>
                            @error('theme')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror

                            {{-- Preview gambar --}}
                            <div id="theme-preview" class="mt-2"
                                style="{{ old('theme', $editSyllabus->theme ?? '') ? '' : 'display:none;' }}">
                                <img id="preview-img"
                                    src="{{ old('theme', $editSyllabus->theme ?? '') ? asset('img/' . old('theme', $editSyllabus->theme ?? '')) : '' }}"
                                    alt="Preview"
                                    style="height: 120px; object-fit: cover; border-radius: 6px; border: 1px solid #dee2e6;">
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="form-group">
                            <label>Deskripsi <span class="text-danger">*</span></label>
                            <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror"
                                placeholder="Jelaskan isi dan tujuan silabus ini...">{{ old('description', $editSyllabus->description ?? '') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>
                            {{ $editSyllabus ? 'Update Silabus' : 'Simpan Silabus' }}
                        </button>
                        <a href="{{ $editSyllabus ? route('syllabus.show', $editSyllabus->id) : route('syllabus.index') }}"
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
        $(document).ready(function() {
            $('select[name="instructor_id"]').select2({
                theme: 'bootstrap4',
                placeholder: '-- Pilih Instruktur --'
            });

            $('select[name="theme"]').select2({
                theme: 'bootstrap4',
                placeholder: '-- Pilih Gambar --'
            });

            $('select[name="theme"]').on('change', function() {
                const val = $(this).val();
                if (val) {
                    $('#preview-img').attr('src', '{{ asset('img') }}/' + val);
                    $('#theme-preview').show();
                } else {
                    $('#theme-preview').hide();
                }
            });
        });

        ajaxForm('#syllabusForm');
    </script>
@endpush
