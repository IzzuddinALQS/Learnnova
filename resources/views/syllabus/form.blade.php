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
                    method="POST"
                    enctype="multipart/form-data">
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

                        {{-- Gambar Cover --}}
                        <div class="form-group">
                            <label>Gambar Cover</label>
                            <div class="custom-file">
                                <input type="file" name="theme" id="themeCover" accept="image/*"
                                    class="custom-file-input @error('theme') is-invalid @enderror">
                                <label class="custom-file-label" for="themeCover">Pilih gambar...</label>
                            </div>
                            <small class="text-muted">Format: JPG, JPEG, PNG, WEBP. Maks 2MB.</small>
                            @error('theme')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror

                            {{-- Preview gambar --}}
                            <div id="theme-preview" class="mt-2"
                                style="{{ $editSyllabus?->theme ? '' : 'display:none;' }}">
                                <img id="preview-img"
                                    src="{{ $editSyllabus?->theme ? asset('storage/' . $editSyllabus->theme) : '' }}"
                                    alt="Preview"
                                    style="height: 120px; object-fit: cover; border-radius: 6px; border: 1px solid #dee2e6;">
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="form-group">
                            <label>Deskripsi <span class="text-danger">*</span></label>
                            <textarea name="description" rows="5"
                                class="form-control @error('description') is-invalid @enderror"
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
        $(document).ready(function () {
            $('#themeCover').on('change', function () {
                const file = this.files[0];
                if (file) {
                    $('#preview-img').attr('src', URL.createObjectURL(file));
                    $('#theme-preview').show();
                    $('.custom-file-label').text(file.name);
                } else {
                    $('#theme-preview').hide();
                    $('.custom-file-label').text('Pilih gambar...');
                }
            });
        });

        ajaxForm('#syllabusForm');
    </script>
@endpush