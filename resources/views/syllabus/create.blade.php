@extends('main')
@section('title', 'Tambah Silabus')
@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Tambah Silabus Baru</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('syllabus.store') }}" method="POST">
                            @csrf {{-- Penting: Token keamanan Laravel --}}

                            <div class="form-group mb-3">
                                <label>Nama Silabus</label>
                                <input type="text" name="name" class="form-control"
                                    placeholder="Contoh: Belajar Laravel Dasar" required>
                            </div>

                            <div class="form-group mb-3">
                                <label>Nama Pengajar</label>
                                <input type="text" name="instructor" class="form-control"
                                    placeholder="Contoh: Budi Santoso" required>
                            </div>

                            <div class="form-group mb-3">
                                <label>Durasi</label>
                                <input type="number" name="duration_weeks" class="form-control" placeholder="Contoh: 12"
                                    required>
                            </div>

                            <select name="theme" class="form-control">
                                @foreach ($images as $image)
                                    <option value="{{ $image }}"
                                        {{ old('theme', $syllabus->theme ?? '') == $image ? 'selected' : '' }}>
                                        {{ $image }}
                                    </option>
                                @endforeach
                            </select>

                            <div class="form-group mb-3">
                                <label>Deskripsi</label>
                                <textarea name="description" class="form-control" rows="4" placeholder="Jelaskan isi silabus..." required></textarea>
                            </div>


                            <div class="d-flex justify-content-between">
                                <a href="{{ route('syllabus.index') }}" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-success">Simpan Data</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
