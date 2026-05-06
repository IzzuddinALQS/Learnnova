@extends('main')

@section('title', 'Dashboard Pelajar')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold">Dashboard Pelajar</h1>
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

        {{-- 2. KONTEN UTAMA: MATA PELAJARAN --}}
        <div class="row">
            <div class="col-md-8">
                <h5 class="mb-3 font-weight-bold">Kursus Aktif</h5>
                <div class="row">
                    {{-- Card Bahasa Inggris --}}
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm border-0 h-100" style="border-radius: 15px;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <span class="badge badge-pill px-3 mb-2" style="background: #e0e7ff; color: #4338ca;">Bahasa</span>
                                    <i class="fas fa-globe-americas text-muted"></i>
                                </div>
                                <h5 class="font-weight-bold mt-2">Bahasa Inggris</h5>
                                <p class="text-muted small">Materi: Business Communication</p>
                                <hr>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>Progres</span>
                                    <span class="font-weight-bold text-primary">65%</span>
                                </div>
                                <div class="progress rounded-pill" style="height: 8px;">
                                    <div class="progress-bar bg-primary" style="width: 65%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card Bahasa Arab --}}
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm border-0 h-100" style="border-radius: 15px;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <span class="badge badge-pill px-3 mb-2" style="background: #fef3c7; color: #b45309;">Bahasa</span>
                                    <i class="fas fa-language text-muted"></i>
                                </div>
                                <h5 class="font-weight-bold mt-2">Bahasa Arab</h5>
                                <p class="text-muted small">Materi: Dasar Percakapan</p>
                                <hr>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>Progres</span>
                                    <span class="font-weight-bold text-warning">40%</span>
                                </div>
                                <div class="progress rounded-pill" style="height: 8px;">
                                    <div class="progress-bar bg-warning" style="width: 40%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card Matematika --}}
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm border-0 h-100" style="border-radius: 15px;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <span class="badge badge-pill px-3 mb-2" style="background: #d1fae5; color: #059669;">Sains</span>
                                    <i class="fas fa-square-root-alt text-muted"></i>
                                </div>
                                <h5 class="font-weight-bold mt-2">Matematika</h5>
                                <p class="text-muted small">Materi: Statistik Dasar</p>
                                <hr>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>Progres</span>
                                    <span class="font-weight-bold text-success">92%</span>
                                </div>
                                <div class="progress rounded-pill" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: 92%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
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