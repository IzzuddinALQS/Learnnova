@extends('main')

@section('title', 'Sertifikat Saya')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Sertifikat Saya</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Sertifikat Saya</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            @forelse($certificates as $certificate)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <div class="text-center mb-3">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning"
                                      style="width:64px;height:64px;">
                                    <i class="fas fa-certificate fa-2x text-white"></i>
                                </span>
                            </div>
                            <h5 class="font-weight-bold text-center">{{ $certificate->course->title ?? '-' }}</h5>
                            <p class="text-muted text-center mb-2">
                                Nomor: <strong>{{ $certificate->certificate_no }}</strong>
                            </p>
                            <p class="text-muted text-center">
                                Diterbitkan:
                                {{ $certificate->issued_at?->format('d M Y') ?? '-' }}
                            </p>
                            <a href="{{ route('certificates.print', $certificate->id) }}"
                               target="_blank"
                               class="btn btn-primary btn-block mt-auto">
                                <i class="fas fa-print mr-1"></i> Lihat / Cetak
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        Sertifikat belum tersedia. Selesaikan seluruh materi kelas, lalu tunggu validasi akademik.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
