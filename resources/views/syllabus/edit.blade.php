@extends('main')
@section('title', 'Edit Silabus')
@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow border-0">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0 font-weight-bold">Edit Silabus</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('syllabus.update', $syllabus->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            {{-- Judul Silabus --}}
                            <div class="form-group mb-3">
                                <label>Judul Silabus</label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $syllabus->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Nama Pengajar --}}
                            <div class="form-group mb-3">
                                <label>Nama Pengajar</label>
                                <input type="text" name="instructor"
                                    class="form-control @error('instructor') is-invalid @enderror"
                                    value="{{ old('instructor', $syllabus->instructor) }}" required>
                                @error('instructor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Durasi --}}
                            <div class="form-group mb-3">
                                <label>Durasi (Minggu)</label>
                                <input type="number" name="duration_weeks"
                                    class="form-control @error('duration_weeks') is-invalid @enderror"
                                    value="{{ old('duration_weeks', $syllabus->duration_weeks) }}" required>
                                @error('duration_weeks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Tema/Gambar --}}
                            <div class="form-group mb-3">
                                <select name="theme" class="form-control">
                                    @foreach ($images as $image)
                                        <option value="{{ $image }}"
                                            {{ old('theme', $syllabus->theme ?? '') == $image ? 'selected' : '' }}>
                                            {{ $image }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('theme')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Deskripsi --}}
                            <div class="form-group mb-3">
                                <label>Deskripsi</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" required>{{ old('description', $syllabus->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('syllabus.show', $syllabus->id) }}" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary shadow-sm">
                                    <i class="fas fa-save"></i> Perbarui Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
