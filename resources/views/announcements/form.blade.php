@extends('main')

@php
    $editAnnouncement = $announcement ?? null;
@endphp

@section('title', $editAnnouncement ? 'Edit Pengumuman' : 'Tambah Pengumuman')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">{{ $editAnnouncement ? 'Edit Pengumuman' : 'Tambah Pengumuman' }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('announcements.index') }}">Pengumuman</a></li>
                    <li class="breadcrumb-item active">{{ $editAnnouncement ? 'Edit' : 'Tambah' }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-primary shadow-sm">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bullhorn mr-2"></i>
                    {{ $editAnnouncement ? 'Edit Pengumuman' : 'Form Tambah Pengumuman' }}
                </h3>
            </div>

            <form action="{{ $editAnnouncement ? route('announcements.update', $editAnnouncement) : route('announcements.store') }}" method="POST">
                @csrf
                @if($editAnnouncement)
                    @method('PUT')
                @endif

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

                    <div class="form-group">
                        <label>Judul Pengumuman <span class="text-danger">*</span></label>
                        <input type="text" name="title"
                               class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $editAnnouncement->title ?? '') }}"
                               placeholder="Contoh: Ujian Tengah Semester">
                        @error('title')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Isi Pengumuman <span class="text-danger">*</span></label>
                        <textarea name="content" rows="6"
                                  class="form-control @error('content') is-invalid @enderror"
                                  placeholder="Tuliskan pengumuman untuk siswa...">{{ old('content', $editAnnouncement->content ?? '') }}</textarea>
                        @error('content')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kelas</label>
                                <select name="course_id" class="form-control @error('course_id') is-invalid @enderror">
                                    <option value="">-- Umum untuk Semua Siswa --</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}"
                                            {{ old('course_id', $editAnnouncement->course_id ?? '') == $course->id ? 'selected' : '' }}>
                                            {{ $course->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="d-block">Tayangkan</label>
                                <div class="form-check">
                                    <input type="checkbox" name="is_published" value="1"
                                           id="isPublished"
                                           class="form-check-input"
                                           {{ old('is_published', $editAnnouncement->is_published ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isPublished">Aktifkan sekarang</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tanggal Publikasi</label>
                                <input type="datetime-local" name="published_at"
                                       class="form-control @error('published_at') is-invalid @enderror"
                                       value="{{ old('published_at', optional($editAnnouncement)->published_at ? optional($editAnnouncement->published_at)->format('Y-m-d\TH:i') : '') }}">
                                @error('published_at')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>
                        {{ $editAnnouncement ? 'Perbarui Pengumuman' : 'Simpan Pengumuman' }}
                    </button>
                    <a href="{{ route('announcements.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
