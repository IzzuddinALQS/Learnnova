@extends('main')

@section('title', auth()->user()->hasRole('super_admin') ? 'Dashboard Super Admin' : (auth()->user()->hasRole('akademik') ? 'Dashboard Staf Akademik' : (auth()->user()->hasRole('pengajar') ? 'Dashboard Pengajar' : 'Dashboard Pelajar')))

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold">
                    @if(auth()->user()->hasRole('super_admin'))
                        Dashboard Super Admin
                    @elseif(auth()->user()->hasRole('akademik'))
                        Dashboard Staf Akademik
                    @elseif(auth()->user()->hasRole('pengajar'))
                        Dashboard Pengajar
                    @else
                        Dashboard Pelajar
                    @endif
                </h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        {{-- ========================================================== --}}
        {{-- 1. BLOK SUPER ADMIN --}}
        {{-- ========================================================== --}}
        @if(auth()->user()->hasRole('super_admin'))
            <div class="row">
                {{-- Box Total Users --}}
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-info shadow-sm" style="border-radius: 15px;">
                        <div class="inner p-3">
                            <h3>{{ $stats['total_users'] ?? 0 }}</h3>
                            <p>Total Pengguna Sistem</p>
                        </div>
                        <div class="icon"><i class="fas fa-users"></i></div>
                        <a href="{{ route('users.index') }}" class="small-box-footer rounded-bottom" style="border-bottom-left-radius: 15px !important; border-bottom-right-radius: 15px !important;">
                            Manajemen User <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                {{-- Box Total Kelas Sistem --}}
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-success shadow-sm" style="border-radius: 15px;">
                        <div class="inner p-3">
                            <h3>{{ $stats['total_courses'] ?? 0 }}</h3>
                            <p>Total Seluruh Kelas</p>
                        </div>
                        <div class="icon"><i class="fas fa-chalkboard"></i></div>
                        <a href="{{ route('courses.index') }}" class="small-box-footer" style="border-bottom-left-radius: 15px !important; border-bottom-right-radius: 15px !important;">
                            Kelola Kelas Master <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                {{-- Box Konfigurasi Hak Akses --}}
                <div class="col-lg-4 col-12">
                    <div class="small-box bg-secondary shadow-sm" style="border-radius: 15px;">
                        <div class="inner p-3">
                            <h3>Roles</h3>
                            <p>Hak Akses & Log Sistem</p>
                        </div>
                        <div class="icon"><i class="fas fa-shield-alt"></i></div>
                        <a href="{{ route('roles.index') }}" class="small-box-footer" style="border-bottom-left-radius: 15px !important; border-bottom-right-radius: 15px !important;">
                            Pengaturan Hak Akses <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card shadow-sm border-0 p-4 text-center" style="border-radius: 15px; background: #f8fafc;">
                        <h4 class="font-weight-bold text-primary">Selamat Datang di Panel Akses Super Admin 🔐</h4>
                        <p class="text-muted mb-0">Anda memiliki otoritas penuh terhadap menu Administrasi, Manajemen Akun Pengguna, Roles & Permissions, serta rekaman Activity Log.</p>
                    </div>
                </div>
            </div>

        {{-- ========================================================== --}}
        {{-- 2. BLOK STAF AKADEMIK --}}
        {{-- ========================================================== --}}
        @elseif(auth()->user()->hasRole('akademik'))
            <div class="row">
                <div class="col-lg-4 col-12 mb-3">
                    <div class="small-box bg-success shadow-sm" style="border-radius: 15px;">
                        <div class="inner p-3">
                            <h3>{{ $stats['total_courses'] ?? 0 }}</h3>
                            <p>Total Kelas Aktif</p>
                        </div>
                        <div class="icon"><i class="fas fa-chalkboard-teacher"></i></div>
                        <a href="{{ route('courses.index') }}" class="small-box-footer" style="border-bottom-left-radius: 15px !important; border-bottom-right-radius: 15px !important;">
                            Kelola Master Kelas <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-4 col-6 mb-3">
                    <div class="small-box bg-warning shadow-sm" style="border-radius: 15px;">
                        <div class="inner p-3 text-white">
                            <h3>{{ $stats['total_syllabus'] ?? 0 }}</h3>
                            <p>Dokumen Silabus</p>
                        </div>
                        <div class="icon"><i class="fas fa-book-open"></i></div>
                        <a href="{{ route('syllabus.index') }}" class="small-box-footer text-white" style="border-bottom-left-radius: 15px !important; border-bottom-right-radius: 15px !important;">
                            Kelola Master Silabus <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-4 col-6 mb-3">
                    <div class="small-box bg-purple shadow-sm" style="border-radius: 15px; background-color: #6f42c1 !important; color: white;">
                        <div class="inner p-3">
                            <h3>Peserta</h3>
                            <p>Manajemen Peserta Kelas</p>
                        </div>
                        <div class="icon" style="color: rgba(255,255,255,0.15);"><i class="fas fa-user-graduate"></i></div>
                        <a href="{{ route('students.overview') }}" class="small-box-footer text-white" style="border-bottom-left-radius: 15px !important; border-bottom-right-radius: 15px !important;">
                            Lihat Daftar Pelajar <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

        {{-- ========================================================== --}}
        {{-- 3. BLOK PENGAJAR --}}
        {{-- ========================================================== --}}
        @elseif(auth()->user()->hasRole('pengajar'))
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card shadow-sm border-0 p-4" style="border-radius: 15px; background: #f0fdf4; border-left: 5px solid #16a34a !important;">
                        <h4 class="font-weight-bold text-success">Ruang Kerja Pendidik 👨‍🏫</h4>
                        <p class="text-muted mb-0">Selamat datang kembali. Melalui panel ini Anda dapat mengunggah materi, membuat quiz, dan memeriksa tugas masuk.</p>
                    </div>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-4 col-12">
                    <div class="info-box shadow-sm border-0" style="border-radius: 10px;">
                        <span class="info-box-icon bg-success"><i class="fas fa-graduation-cap"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Kelas Anda Ampu</span>
                            <span class="info-box-number">{{ isset($taughtCourses) ? count($taughtCourses) : 0 }} Kelas</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-6">
                    <div class="info-box shadow-sm border-0" style="border-radius: 10px;">
                        <span class="info-box-icon bg-info"><i class="fas fa-file-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Materi</span>
                            <span class="info-box-number">{{ $stats['teacher_materials_count'] ?? 0 }} File</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-6">
                    <div class="info-box shadow-sm border-0" style="border-radius: 10px;">
                        <span class="info-box-icon bg-danger"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Perlu Diperiksa</span>
                            <span class="info-box-number">{{ $stats['pending_grading_count'] ?? 0 }} Tugas</span>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="font-weight-bold text-dark mt-4 mb-3">Daftar Kelas yang Anda Ampu</h5>
            <div class="row">
                @if(isset($taughtCourses) && count($taughtCourses) > 0)
                    @foreach($taughtCourses as $course)
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm border-0 h-100" style="border-radius: 15px;">
                                <div class="card-body p-4 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="text-success mb-2"><i class="fas fa-chalkboard fa-2x"></i></div>
                                        <h5 class="font-weight-bold text-dark mb-1">{{ $course->name }}</h5>
                                        <p class="text-muted small">{{ Str::limit($course->description, 80, '...') }}</p>
                                    </div>
                                    <div>
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <a href="{{ route('courses.materials.index', ['courseId' => $course->id]) }}" class="btn btn-xs btn-outline-primary rounded-pill px-3">Kelola Materi</a>
                                            <a href="{{ route('assignments.index') }}" class="btn btn-xs btn-outline-danger rounded-pill px-3">Kelola Tugas</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="card p-4 text-center text-muted shadow-sm" style="border-radius: 15px;">Belum ada kelas yang diampu.</div>
                    </div>
                @endif
            </div>

        {{-- ========================================================== --}}
        {{-- 4. BLOK PELAJAR --}}
        {{-- ========================================================== --}}
        @else
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card shadow-lg border-0" style="border-radius: 20px; background: linear-gradient(135deg, #4f46e5 0%, #a78bfa 100%); color: white;">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h2 class="font-weight-bold">Halo, Pelajar! 👋</h2>
                                    <p class="mb-4" style="font-size: 1.1rem; opacity: 0.9;">Jadikan setiap hari sebagai kesempatan untuk tumbuh.</p>
                                    <a href="{{ route('courses.index') }}" class="btn btn-light rounded-pill px-5 text-primary font-weight-bold shadow-sm">Mulai Belajar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PENGUMUMAN TERBARU --}}
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card shadow-sm border-0" style="border-radius: 15px;">
                        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3 pb-0">
                            <h5 class="font-weight-bold text-dark mb-0">Pengumuman Terbaru</h5>
                            <a href="{{ route('announcements.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Lihat Semua</a>
                        </div>
                        <div class="card-body">
                            @if(isset($latestAnnouncements) && count($latestAnnouncements) > 0)
                                @foreach($latestAnnouncements as $announcement)
                                    <div class="d-flex align-items-center border-bottom pb-2 mb-2">
                                        <div class="mr-3 text-primary"><i class="fas fa-bullhorn"></i></div>
                                        <div>
                                            <h6 class="font-weight-bold mb-0">{{ $announcement->title }}</h6>
                                            <small class="text-muted">{{ $announcement->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="bg-info p-3 text-white" style="border-radius: 10px;">
                                    Belum ada pengumuman terbaru untuk saat ini.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8" id="kursus-aktif">
                    <h5 class="mb-3 font-weight-bold text-dark">Kelas yang Anda Ikuti</h5>
                    <div class="row">
                        @if(isset($activeCourses))
                            @forelse($activeCourses as $course)
                                <div class="col-md-6 mb-4">
                                    <div class="card shadow-sm border-0 h-100" style="border-radius: 15px;">
                                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                                            <div>
                                                <h5 class="font-weight-bold text-primary mb-1">{{ $course->name }}</h5>
                                                <p class="text-muted small">{{ Str::limit($course->description, 80, '...') }}</p>
                                            </div>
                                            <div>
                                                <hr>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <a href="{{ route('courses.show', ['course' => $course->id]) }}" class="btn btn-sm btn-primary rounded-pill px-3">Masuk Kelas</a>
                                                    <small class="text-muted"><i class="fas fa-book mr-1"></i> Lihat Materi</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="card p-4 text-center text-muted shadow-sm" style="border-radius: 15px;">Belum ada kelas yang diikuti.</div>
                                </div>
                            @endforelse
                        @endif
                    </div>
                </div>

                {{-- TUGAS TERDEKAT PELAJAR --}}
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
                        <div class="card-header bg-transparent border-0 pt-4 px-4">
                            <h5 class="font-weight-bold mb-0 text-danger"><i class="fas fa-exclamation-circle mr-1"></i> Tugas Terdekat</h5>
                        </div>
                        <div class="card-body px-4 pt-2">
                            @if(isset($upcomingAssignments))
                                @forelse($upcomingAssignments as $assignment)
                                    <a href="{{ route('assignments.show', ['assignment' => $assignment->id]) }}" class="text-decoration-none text-dark">
                                        <div class="d-flex align-items-center mb-3 border-bottom pb-3">
                                            <div class="mr-3 text-danger"><i class="fas fa-edit fa-lg"></i></div>
                                            <div>
                                                <h6 class="font-weight-bold mb-0 text-dark">{{ $assignment->title ?? $assignment->name }}</h6>
                                                <small class="text-danger font-weight-bold">Batas: {{ $assignment->due_date }}</small>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <p class="small mb-0">Hore! Tidak ada tugas terdekat yang perlu dikerjakan.</p>
                                    </div>
                                @endforelse
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection