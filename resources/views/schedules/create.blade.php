@extends('main')

@section('title', 'Tambah Jadwal')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <h1>Tambah Jadwal Pembelajaran</h1>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <form action="{{ route('schedules.store') }}" method="POST">
                @csrf

                <div class="card-body">
                    <div class="form-group">
                        <label>Kelas / Course</label>
                        <select name="course_id" class="form-control" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Pengajar</label>
                        <select name="user_id" class="form-control" required>
                            <option value="">-- Pilih Pengajar --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Hari</label>
                        <select name="day" class="form-control" required>
                            <option value="">-- Pilih Hari --</option>
                            @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $day)
                                <option value="{{ $day }}">{{ $day }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Judul Jadwal</label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: Pemrograman Web" required>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Opsional..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>Waktu Mulai</label>
                        <input type="datetime-local" name="start_time" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Waktu Selesai</label>
                        <input type="datetime-local" name="end_time" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Ruang Kelas / Link Online</label>
                        <input type="text" name="location" class="form-control" placeholder="Contoh: Lab Komputer A / Link Zoom">
                    </div>

                    <div class="form-group">
                        <label>Tipe Pertemuan</label>
                        <select name="type" class="form-control" required>
                            <option value="offline">Offline</option>
                            <option value="online">Online</option>
                            <option value="hybrid">Hybrid</option>
                        </select>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>

                    <a href="{{ route('schedules.index') }}" class="btn btn-secondary">
                        Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection