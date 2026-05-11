@extends('main')
@section('title', 'Detail Silabus - ' . $syllabus->name)

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Detail Silabus</h4>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <!-- Kolom Gambar/Tema -->
                            <div class="col-md-4 text-center mb-4">
                                <img src="{{ Str::startsWith($syllabus->theme, ['http://', 'https://']) ? $syllabus->theme : asset('img/' . $syllabus->theme) }}"
                                    class="img-fluid rounded shadow" alt="{{ $syllabus->name }}"
                                    style="max-height: 300px; width: 100%; object-fit: cover;">

                                <div class="mt-3">
                                    <span class="badge bg-info text-dark p-2">
                                        <i class="fas fa-clock"></i> Durasi: {{ $syllabus->duration_weeks }} Minggu
                                    </span>
                                </div>
                            </div>

                            <!-- Kolom Informasi Detail -->
                            <div class="col-md-8">
                                <h2 class="font-weight-bold text-primary">{{ $syllabus->name }}</h2>
                                <p class="text-muted mb-4">
                                    <i class="fas fa-user-tie"></i> Pengajar: <strong>{{ $syllabus->instructor }}</strong>
                                </p>

                                <hr>

                                <h5 class="text-secondary font-weight-bold">Deskripsi Silabus</h5>
                                <p class="text-justify" style="line-height: 1.6;">
                                    {{ $syllabus->description ?: 'Tidak ada deskripsi untuk silabus ini.' }}
                                </p>

                                <div class="mt-5 p-3 bg-light rounded">
                                    <h6 class="text-muted small uppercase font-weight-bold">Informasi Tambahan</h6>
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="d-block text-muted">Dibuat Oleh:</small>
                                            <span>{{ $syllabus->creator->name ?? 'Sistem' }}</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="d-block text-muted">Terakhir Diperbarui:</small>
                                            <span>{{ $syllabus->updated_at->format('d M Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white d-flex justify-content-end align-items-center">
                        {{-- Menggunakan d-flex dan ms-2 (margin start) untuk memberi jarak antar elemen --}}
                        <div class="d-flex gap-2">
                            <a href="{{ route('syllabus.index') }}" class="btn btn-secondary shadow-sm">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>

                            <a href="{{ route('syllabus.edit', $syllabus->id) }}"
                                class="btn btn-warning text-dark shadow-sm">
                                <i class="fas fa-edit"></i> Edit Data
                            </a>

                            <form action="{{ route('syllabus.destroy', $syllabus->id) }}" method="POST"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger shadow-sm">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
