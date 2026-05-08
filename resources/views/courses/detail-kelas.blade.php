@extends('main')

@section('title', 'Detail Kelas')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold">Detail Kelas</h1>
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
                        {{-- 1. Judul Kelas --}}
                        <div class="mb-3">
                            <span class="badge badge-primary px-3 py-2 mr-2 mb-2" style="border-radius: 20px;">Bahasa</span>
                            <span class="badge badge-success px-3 py-2 mb-2" style="border-radius: 20px;">Semua Tingkatan</span>
                        </div>
                        <h2 class="font-weight-bold text-dark mb-3">Kelas Bahasa Indonesia</h2>
                        
                        {{-- 2. Deskripsi singkat Kelas --}}
                        <p class="text-muted" style="font-size: 1.1rem; line-height: 1.6;">
                            Pelajari tata bahasa, kosa kata, dan kaidah penulisan Bahasa Indonesia yang baik dan benar sesuai EYD. Kelas ini dirancang interaktif untuk meningkatkan keterampilan komunikasi verbal dan tertulis Anda.
                        </p>

                        {{-- 3. Pemutar Video (Dummy) --}}
                        <div class="mt-4">
                            <div class="embed-responsive embed-responsive-16by9 rounded shadow-sm" style="border-radius: 15px; overflow: hidden; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); position: relative;">
                                <div class="d-flex justify-content-center align-items-center h-100 w-100" style="position: absolute; top: 0; left: 0;">
                                    <div class="text-center text-white">
                                        <i class="fas fa-play-circle fa-4x mb-3 text-primary" style="opacity: 0.9; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'"></i>
                                        <h5 class="font-weight-bold">Video Player (Dummy)</h5>
                                        <p class="small text-muted">Klik untuk memutar video materi</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 pt-3 border-top">
                            <a href="{{ route('detail-tugas.index') }}" class="btn btn-primary rounded-pill px-4 py-2 font-weight-bold shadow-sm mr-2">
                                <i class="fas fa-book-open mr-2"></i> Tugas
                            </a>
                            <button class="btn btn-outline-secondary rounded-pill px-4 py-2 font-weight-bold">
                                Mulai Materi
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sisi Kanan (Profile Pengajar) --}}
            <div class="col-lg-4 col-md-12">
                <div class="card shadow-sm border-0 sticky-top" style="border-radius: 15px; top: 20px;">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 text-center">
                        <h5 class="font-weight-bold mb-0">Profile Pengajar</h5>
                    </div>
                    <div class="card-body px-4 text-center">
                        <div class="position-relative d-inline-block mb-3">
                            <img src="https://ui-avatars.com/api/?name=Asep+Khoir&background=4f46e5&color=fff&size=128" alt="Tutor" class="img-circle shadow" style="width: 120px; height: 120px; object-fit: cover; border: 4px solid #fff;">
                            <span class="position-absolute bottom-0 right-0 bg-success border border-white rounded-circle" style="width: 20px; height: 20px; right: 10px; bottom: 5px;" title="Online"></span>
                        </div>
                        
                        <h4 class="font-weight-bold mb-1">Asep Khoir</h4>
                        <p class="text-primary font-weight-bold mb-3"><i class="fas fa-check-circle mr-1"></i>Tutor Bahasa Terpadu</p>
                        
                        <p class="text-muted text-justify mb-4" style="font-size: 0.9rem; line-height: 1.5;">
                            Berpengalaman lebih dari 5 tahun sebagai pengajar Bahasa Indonesia dan Sastra. Aktif membimbing siswa untuk meningkatkan literasi, public speaking, dan penulisan kreatif di berbagai platform e-learning.
                        </p>

                        <hr class="mb-4">

                        <div class="row text-center mb-4">
                            <div class="col-6 border-right">
                                <h5 class="font-weight-bold mb-0">15</h5>
                                <small class="text-muted">Kelas</small>
                            </div>
                            <div class="col-6">
                                <h5 class="font-weight-bold mb-0">4.9/5</h5>
                                <small class="text-muted">Rating</small>
                            </div>
                        </div>

                        {{-- Dummy PDF dari Tutor --}}
                        <div class="mt-4 pt-3 border-top text-left">
                            <h6 class="font-weight-bold mb-3"><i class="fas fa-paperclip text-secondary mr-2"></i>Materi Tambahan</h6>
                            <div class="d-flex align-items-center justify-content-between bg-light p-3 shadow-sm border" style="border-radius: 12px; transition: all 0.3s ease;">
                                <div class="d-flex align-items-center overflow-hidden">
                                    <div class="mr-3 text-danger">
                                        <i class="fas fa-file-pdf fa-2x"></i>
                                    </div>
                                    <div class="text-truncate">
                                        <p class="mb-0 font-weight-bold text-dark text-truncate" style="font-size: 0.9rem;">Modul_Bahasa_Dasar.pdf</p>
                                        <small class="text-muted">2.4 MB &bull; PDF</small>
                                    </div>
                                </div>
                                <button class="btn btn-primary btn-sm rounded-circle ml-2 shadow-sm flex-shrink-0" title="Unduh Materi">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
