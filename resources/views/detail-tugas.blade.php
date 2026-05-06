@extends('main')

@section('title', 'Detail Tugas')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold">Detail Tugas</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('detail-kelas.index') }}" class="btn btn-outline-secondary rounded-pill btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali ke Kelas</a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            {{-- Bagian Tengah --}}
            <div class="col-lg-8 col-md-12 mb-4">
                <div class="card shadow-sm border-0" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        {{-- 1. Judul Tugas --}}
                        <div class="mb-3">
                            <span class="badge badge-danger px-3 py-2 mr-2 mb-2" style="border-radius: 20px;"><i class="fas fa-clock mr-1"></i>Tenggat: 3 Hari Lagi</span>
                            <span class="badge badge-warning px-3 py-2 mb-2" style="border-radius: 20px;">Wajib</span>
                        </div>
                        <h2 class="font-weight-bold text-dark mb-3">Tugas 1: Menulis Karangan Bebas</h2>
                        
                        {{-- 2. Deskripsi singkat Tugas --}}
                        <p class="text-muted" style="font-size: 1.1rem; line-height: 1.6;">
                            Buatlah sebuah karangan bebas bertema "Dampak Teknologi dalam Pendidikan". Karangan harus memuat minimal 500 kata, memperhatikan ejaan yang disempurnakan (EYD), serta menggunakan struktur pendahuluan, isi, dan penutup yang jelas.
                        </p>

                        {{-- 3. Area Drop File --}}
                        <div class="mt-4">
                            <h5 class="font-weight-bold mb-3">Area Pengumpulan</h5>
                            <div class="p-5 text-center bg-light border position-relative" style="border-radius: 15px; border: 2px dashed #94a3b8 !important; transition: all 0.3s ease; cursor: pointer;" onmouseover="this.style.backgroundColor='#f1f5f9'; this.style.borderColor='#4f46e5'" onmouseout="this.style.backgroundColor='#f8f9fa'; this.style.borderColor='#94a3b8'" onclick="document.getElementById('fileUpload').click()">
                                <i class="fas fa-cloud-upload-alt fa-4x text-primary mb-3" style="opacity: 0.8;"></i>
                                <h5 class="font-weight-bold text-dark mb-2">Tarik & Lepas File Tugas Anda Di Sini</h5>
                                <p class="text-muted mb-3">Atau klik area ini untuk menelusuri file dari perangkat Anda.</p>
                                <div class="d-flex justify-content-center gap-2" style="gap: 5px;">
                                    <span class="badge badge-pill badge-secondary px-3 py-2">PDF</span>
                                    <span class="badge badge-pill badge-secondary px-3 py-2">ZIP</span>
                                    <span class="badge badge-pill badge-secondary px-3 py-2">XLSX</span>
                                    <span class="badge badge-pill badge-secondary px-3 py-2">DOC/DOCX</span>
                                </div>
                                <input type="file" class="d-none" id="fileUpload" accept=".pdf,.zip,.xlsx,.doc,.docx">
                            </div>
                        </div>
                        
                        <div class="mt-4 pt-3 border-top">
                            <button class="btn btn-primary rounded-pill px-4 py-2 font-weight-bold shadow-sm mr-2" onclick="document.getElementById('fileUpload').click()">
                                <i class="fas fa-paper-plane mr-2"></i> Kumpulkan Tugas
                            </button>
                            <button class="btn btn-outline-danger rounded-pill px-4 py-2 font-weight-bold">
                                Batal
                            </button>
                        </div>

                        {{-- Form Feedback dari Tutor --}}
                        <div class="mt-5 bg-white border border-info shadow-sm" style="border-radius: 15px; overflow: hidden;">
                            <div class="bg-info text-white px-4 py-3 d-flex align-items-center">
                                <i class="fas fa-comment-dots fa-lg mr-2"></i>
                                <h6 class="font-weight-bold mb-0">Umpan Balik (Feedback) Tutor</h6>
                            </div>
                            <div class="p-4 bg-light">
                                <div class="d-flex mb-0">
                                    <img src="https://ui-avatars.com/api/?name=Asep+Khoir&background=4f46e5&color=fff&size=64" alt="Tutor" class="img-circle shadow-sm mr-3" style="width: 50px; height: 50px; object-fit: cover;">
                                    <div class="bg-white p-3 shadow-sm" style="border-radius: 0 15px 15px 15px !important; width: 100%; border: 1px solid #e2e8f0;">
                                        <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                                            <h6 class="font-weight-bold text-dark mb-0">Asep Khoir <span class="badge badge-success ml-1">Tutor</span></h6>
                                            <span class="badge badge-pill badge-secondary px-2 py-1"><i class="far fa-clock mr-1"></i>Menunggu Pengumpulan</span>
                                        </div>
                                        <p class="text-muted mb-0 font-italic text-center py-4" style="font-size: 0.95rem;">
                                            <i class="fas fa-inbox fa-3x mb-3 text-light"></i><br>
                                            Umpan balik atau revisi tugas dari tutor akan muncul di area ini setelah tugas Anda diperiksa.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sisi Kanan (Tugas Lainnya) --}}
            <div class="col-lg-4 col-md-12">
                <div class="card shadow-sm border-0 sticky-top" style="border-radius: 15px; top: 20px;">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                        <h5 class="font-weight-bold mb-0"><i class="fas fa-tasks text-primary mr-2"></i>Tugas Lainnya</h5>
                        <p class="small text-muted mt-1 mb-0">Menunggu untuk dikerjakan</p>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush mt-2">
                            {{-- Item Tugas 1 --}}
                            <a href="#" class="list-group-item list-group-item-action border-0 px-4 py-3" style="transition: all 0.2s;">
                                <div class="d-flex w-100 justify-content-between align-items-start mb-1">
                                    <h6 class="font-weight-bold text-dark mb-1">Matematika</h6>
                                    <span class="badge badge-danger rounded-pill">Besok</span>
                                </div>
                                <p class="small text-muted mb-2">Latihan Persamaan Kuadrat</p>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-light" role="progressbar" style="width: 100%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </a>

                            {{-- Item Tugas 2 --}}
                            <a href="#" class="list-group-item list-group-item-action border-0 px-4 py-3" style="transition: all 0.2s;">
                                <div class="d-flex w-100 justify-content-between align-items-start mb-1">
                                    <h6 class="font-weight-bold text-dark mb-1">IPS</h6>
                                    <span class="badge badge-warning rounded-pill">3 Hari</span>
                                </div>
                                <p class="small text-muted mb-2">Makalah Sejarah Kemerdekaan</p>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-light" role="progressbar" style="width: 100%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </a>

                            {{-- Item Tugas 3 --}}
                            <a href="#" class="list-group-item list-group-item-action border-0 px-4 py-3" style="transition: all 0.2s;">
                                <div class="d-flex w-100 justify-content-between align-items-start mb-1">
                                    <h6 class="font-weight-bold text-dark mb-1">Bahasa Inggris</h6>
                                    <span class="badge badge-secondary rounded-pill">Mgg Depan</span>
                                </div>
                                <p class="small text-muted mb-2">Essay Grammar & Vocabulary</p>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-light" role="progressbar" style="width: 100%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </a>
                        </div>
                        
                        <div class="p-4 text-center border-top">
                            <button class="btn btn-outline-primary btn-sm rounded-pill px-4 font-weight-bold"><i class="fas fa-arrow-right mr-2"></i>Lihat Semua Tugas</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
