@extends('main')

@section('title', 'Edit Jadwal')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <h1>Edit Jadwal Pembelajaran</h1>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <form action="{{ route('schedules.update', $schedule->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card-body">
                    <div class="form-group">
                        <label>Kelas / Course</label>
                        <select name="course_id" class="form-control" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ $schedule->course_id == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Pengajar</label>
                        <select name="user_id" class="form-control" required>
                            <option value="">-- Pilih Pengajar --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ $schedule->user_id == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Hari</label>
                        <select name="day" class="form-control" required>
                            <option value="">-- Pilih Hari --</option>
                            @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $day)
                                <option value="{{ $day }}" {{ $schedule->day == $day ? 'selected' : '' }}>
                                    {{ $day }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Judul Jadwal</label>
                        <input type="text" name="title" class="form-control"
                               value="{{ $schedule->title }}" required>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3">{{ $schedule->description }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Waktu Mulai</label>
                        <input type="datetime-local" name="start_time" class="form-control"
                               value="{{ \Carbon\Carbon::parse($schedule->start_time)->format('Y-m-d\TH:i') }}"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Waktu Selesai</label>
                        <input type="datetime-local" name="end_time" class="form-control"
                               value="{{ \Carbon\Carbon::parse($schedule->end_time)->format('Y-m-d\TH:i') }}"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Ruang Kelas / Link Online</label>
                        <input type="text" name="location" class="form-control"
                               value="{{ $schedule->location }}">
                    </div>

                    <div class="form-group">
                        <label>Tipe Pertemuan</label>
                        <select name="type" class="form-control" required>
                            <option value="offline" {{ $schedule->type == 'offline' ? 'selected' : '' }}>Offline</option>
                            <option value="online" {{ $schedule->type == 'online' ? 'selected' : '' }}>Online</option>
                            <option value="hybrid" {{ $schedule->type == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                        </select>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update
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