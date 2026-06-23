@extends('main')

@php use Illuminate\Support\Str; @endphp

@section('title', 'Dashboard')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold">Dashboard</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        {{-- 1. BAGIAN SAMBUTAN & MOTIVASI (VERSI GENERAL) --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow-lg border-0" style="border-radius: 20px; background: linear-gradient(135deg, #4f46e5 0%, #a78bfa 100%); color: white;">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="font-weight-bold">Halo, {{ explode(' ', Auth::user()->name)[0] }}! 👋</h2>
                                <p class="mb-4" style="font-size: 1.1rem; opacity: 0.9; line-height: 1.6;">
                                    "Setiap langkah kecil yang kamu ambil hari ini adalah investasi besar untuk masa depanmu." <br>
                                    Ayo selesaikan modulmu hari ini dan pertahankan performa belajarmu!
                                </p>
                                <div class="d-flex" style="gap: 15px;">
                                    <button class="btn btn-light rounded-pill px-4 text-primary font-weight-bold shadow-sm">Lanjut Belajar</button>
                                    <button class="btn btn-outline-light rounded-pill px-4 text-white">Lihat Progress</button>
                                </div>
                            </div>
                            <div class="col-md-4 text-center border-left d-none d-md-block" style="border-color: rgba(255,255,255,0.2) !important;">
                                <small class="text-uppercase" style="opacity: 0.8; letter-spacing: 1px;">Progres Mingguan</small>
                                <h1 class="display-3 font-weight-bold mt-2">84%</h1>
                                <div class="progress rounded-pill mx-auto" style="height: 10px; width: 80%; background: rgba(255,255,255,0.2);">
                                    <div class="progress-bar bg-white rounded-pill" style="width: 84%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. KONTEN UTAMA: MATA PELAJARAN --}}
        <div class="row">
            <div class="col-md-8">
                {{-- 2. PENGUMUMAN UNTUK PELAJAR --}}
                @if(Auth::user()->hasRole('pelajar'))
                    <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px; overflow: hidden;">
                        <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center w-100">
                            <div>
                                <h5 class="font-weight-bold mb-0">Pengumuman Terbaru</h5>
                                <small class="text-muted">Pengumuman terbaru untuk kamu ikuti.</small>
                            </div>
                            <a href="{{ route('announcements.index') }}" class="btn btn-sm btn-outline-primary ml-auto">Lihat Semua</a>
                        </div>
                        <div class="card-body px-4 pb-4 pt-0">
                            @forelse($announcements as $announcement)
                                <div class="d-flex align-items-start border-bottom py-2 {{ $loop->last ? 'border-0 pb-0' : '' }}">
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <div>
                                                <span class="badge badge-light border mr-2 text-dark" style="font-size: 0.7rem;">
                                                    {{ $announcement->course->title ?? 'Pengumuman Umum' }}
                                                </span>
                                                <span class="font-weight-bold" style="font-size: 0.9rem;">{{ $announcement->title }}</span>
                                            </div>
                                            <small class="text-muted" style="font-size: 0.75rem;">{{ $announcement->published_at->format('d M Y') }}</small>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-1">
                                            <p class="text-muted mb-0" style="font-size: 0.8rem; line-height: 1.3;">{{ Str::limit($announcement->content, 120) }}</p>
                                            <a href="{{ route('announcements.show', $announcement) }}" class="btn btn-sm btn-light text-primary rounded-circle flex-shrink-0" style="width: 28px; height: 28px; padding: 0; line-height: 28px; text-align: center; margin-left: 10px;">
                                                <i class="fas fa-chevron-right" style="font-size: 0.8rem;"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="alert alert-info mb-0 mt-3">
                                    Belum ada pengumuman terbaru untuk saat ini.
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endif
                <h5 class="mb-3 font-weight-bold">Kursus Aktif</h5>
                <div class="row">
                   @if(Auth::user()->hasRole('pengajar'))

<div class="col-md-4 mb-4">

    <div class="card shadow-sm border-0 h-100" style="border-radius:15px;">

        <div class="card-body">

            <h6 class="text-uppercase text-muted">

                Kelas Terbit

            </h6>

            <h3 class="font-weight-bold">

                {{ $pengajarStats['published_courses'] }}

            </h3>

            <p class="mb-0 text-muted">

                Kelas aktif yang kamu ajar

            </p>

        </div>

    </div>

</div>


<div class="col-md-4 mb-4">

    <div class="card shadow-sm border-0 h-100" style="border-radius:15px;">

        <div class="card-body">

            <h6 class="text-uppercase text-muted">

                Siswa Aktif

            </h6>

            <h3 class="font-weight-bold">

                {{ $pengajarStats['active_students'] }}

            </h3>

            <p class="mb-0 text-muted">

                Siswa terdaftar di kelas kamu

            </p>

        </div>

    </div>

</div>


<div class="col-md-4 mb-4">

    <div class="card shadow-sm border-0 h-100" style="border-radius:15px;">

        <div class="card-body">

            <h6 class="text-uppercase text-muted">

                Total Enroll

            </h6>

            <h3 class="font-weight-bold">

                {{ $pengajarStats['active_enrollments'] }}

            </h3>

            <p class="mb-0 text-muted">

                Pendaftaran siswa

            </p>

        </div>

    </div>

</div>


<div class="col-md-4 mb-4">

    <div class="card shadow-sm border-0 h-100" style="border-radius:15px;">

        <div class="card-body">

            <h6 class="text-uppercase text-muted">

                Completion Rate

            </h6>

            <h3 class="font-weight-bold">

                {{ $pengajarStats['completion_rate'] }}%

            </h3>

            <div class="progress progress-xs mb-2" style="border-radius: 4px; height: 8px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $pengajarStats['completion_rate'] }}%" aria-valuenow="{{ $pengajarStats['completion_rate'] }}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>

            <p class="mb-0 text-muted">

                Penyelesaian materi

            </p>

        </div>

    </div>

</div>


<div class="col-md-4 mb-4">

    <div class="card shadow-sm border-0 h-100" style="border-radius:15px;">

        <div class="card-body">

            <h6 class="text-uppercase text-muted">

                Total Views Materi

            </h6>

            <h3 class="font-weight-bold">

                {{ $pengajarStats['material_views'] }}

            </h3>

            <p class="mb-0 text-muted">

                Jumlah akses materi

            </p>

        </div>

    </div>

</div>

@endif

                        @if(Auth::user()->hasRole('pelajar') || Auth::user()->hasRole('pengajar'))
                        @if(isset($courses) && $courses->isNotEmpty())
                            @foreach($courses as $course)
                                <div class="col-md-6 mb-4">
                                    <div class="card shadow-sm border-0 h-100" style="border-radius: 15px;">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <span class="badge badge-pill px-3 mb-2" style="background: #e9eefb; color: #1f2937;">
                                                    {{ $course->instructor->name ?? 'Instruktur' }}
                                                </span>
                                                <i class="fas fa-chalkboard text-muted"></i>
                                            </div>
                                            <h5 class="font-weight-bold mt-2">{{ $course->title }}</h5>
                                            <p class="text-muted small">{{ Str::limit($course->description ?? '', 120) }}</p>
                                            <hr>
                                            <a href="{{ route('courses.show', $course) }}" class="btn btn-sm btn-primary">Buka Kelas</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-12">
                                <div class="alert alert-info mb-0">
                                    Belum ada kursus aktif yang sesuai.
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="col-12">
                            <p class="text-muted">Konten kursus akan tampil di sini.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- SIDEBAR --}}
            <div class="col-md-4">
                {{-- Tugas --}}
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h5 class="font-weight-bold mb-0">Tugas Terdekat</h5>
                    </div>
                    <div class="card-body px-4">
                        <div class="d-flex align-items-center mb-3 border-bottom pb-3">
                            <div class="mr-3 text-info"><i class="fas fa-edit fa-lg"></i></div>
                            <div>
                                <h6 class="font-weight-bold mb-0">Latihan Essay Inggris</h6>
                                <small class="text-muted">Batas waktu: Hari ini</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="mr-3 text-secondary"><i class="fas fa-tasks fa-lg"></i></div>
                            <div>
                                <h6 class="font-weight-bold mb-0">Kuis Aljabar</h6>
                                <small class="text-muted">Batas waktu: 2 hari lagi</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Streak --}}
                <div class="card border-0 shadow-sm text-center p-4" style="border-radius: 15px; background: #fffbeb;">
                    <i class="fas fa-fire fa-3x text-warning mb-2"></i>
                    <h5 class="font-weight-bold mb-1">12 Hari Berturut-turut!</h5>
                    <p class="small text-muted mb-0">Disiplin adalah kunci sukses. Keren banget progresnya!</p>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection