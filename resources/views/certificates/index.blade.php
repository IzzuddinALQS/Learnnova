@extends('main')

@section('title', 'Validasi Sertifikat')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Validasi Sertifikat</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Sertifikat</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-certificate mr-2"></i>Validasi Kelulusan Pelajar
                </h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('certificates.index') }}" class="row align-items-end">
                    <div class="col-md-6">
                        <label for="course_id">Pilih Kelas</label>
                        <select name="course_id" id="course_id" class="form-control select2">
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ (int) $selectedCourseId === $course->id ? 'selected' : '' }}>
                                    {{ $course->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mt-3 mt-md-0">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter mr-1"></i> Tampilkan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if(!$selectedCourse)
            <div class="alert alert-info">
                Belum ada kelas dengan pelajar terdaftar.
            </div>
        @else
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-signature mr-2"></i>Penandatangan Sertifikat
                    </h3>
                    <div class="card-tools">
                        @if($selectedCourse->certificateSigner)
                            <span class="badge badge-success">Sudah diatur</span>
                        @else
                            <span class="badge badge-warning">Belum diatur</span>
                        @endif
                    </div>
                </div>
                <form id="certificate-signer-form" enctype="multipart/form-data">
                    <div class="card-body">
                        <input type="hidden" name="course_id" value="{{ $selectedCourse->id }}">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Nama Penandatangan</label>
                                    <input type="text"
                                           name="name"
                                           class="form-control"
                                           value="{{ $selectedCourse->certificateSigner->name ?? '' }}"
                                           placeholder="Contoh: Budi Santoso, S.Kom"
                                           required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Jabatan / Sebagai</label>
                                    <input type="text"
                                           name="position"
                                           class="form-control"
                                           value="{{ $selectedCourse->certificateSigner->position ?? '' }}"
                                           placeholder="Contoh: Pengajar Utama"
                                           required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Upload TTD</label>
                                    <input type="file"
                                           name="signature"
                                           class="form-control"
                                           accept="image/png,image/jpeg,image/webp"
                                           {{ $selectedCourse->certificateSigner ? '' : 'required' }}>
                                    <small class="text-muted">Format: JPG, PNG, WEBP. Maksimal 2MB.</small>
                                </div>
                            </div>
                        </div>

                        @if($selectedCourse->certificateSigner)
                            <div class="border rounded p-3 bg-light d-flex align-items-center flex-wrap" style="gap: 16px;">
                                <img src="{{ asset('storage/' . $selectedCourse->certificateSigner->signature_path) }}"
                                     alt="TTD {{ $selectedCourse->certificateSigner->name }}"
                                     style="height: 72px; max-width: 220px; object-fit: contain;">
                                <div>
                                    <div class="font-weight-bold">{{ $selectedCourse->certificateSigner->name }}</div>
                                    <div class="text-muted">{{ $selectedCourse->certificateSigner->position }}</div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning mb-0">
                                Atur penandatangan terlebih dahulu. Sertifikat tidak bisa diterbitkan sebelum TTD tersedia.
                            </div>
                        @endif
                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Simpan Penandatangan
                        </button>
                    </div>
                </form>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $rows->count() }}</h3>
                            <p>Pelajar Terdaftar</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-graduate"></i></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $rows->where('is_eligible', true)->count() }}</h3>
                            <p>Layak Sertifikat</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3>{{ $rows->whereNotNull('certificate')->count() }}</h3>
                            <p>Sertifikat Terbit</p>
                        </div>
                        <div class="icon"><i class="fas fa-award"></i></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list mr-2"></i>{{ $selectedCourse->title }}
                    </h3>
                    <div class="card-tools text-muted">
                        Syarat: progress materi 100%
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Pelajar</th>
                                <th>Status Enrollment</th>
                                <th>Progress Materi</th>
                                <th>Sertifikat</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $row)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $row['student']->name ?? '-' }}</strong><br>
                                        <small class="text-muted">{{ $row['student']->email ?? '-' }}</small>
                                    </td>
                                    <td>
                                        @php
                                            $status = $row['enrollment']->status;
                                            $badge = $status === 'completed' ? 'success' : ($status === 'active' ? 'primary' : 'secondary');
                                        @endphp
                                        <span class="badge badge-{{ $badge }}">{{ ucfirst($status) }}</span>
                                    </td>
                                    <td style="min-width: 220px;">
                                        <div class="progress progress-sm mb-1">
                                            <div class="progress-bar bg-{{ $row['is_eligible'] ? 'success' : 'warning' }}"
                                                 style="width: {{ $row['progress_percent'] }}%"></div>
                                        </div>
                                        <small>
                                            {{ $row['completed_materials'] }}/{{ $row['total_materials'] }} materi
                                            ({{ $row['progress_percent'] }}%)
                                        </small>
                                    </td>
                                    <td>
                                        @if($row['certificate'])
                                            <span class="badge badge-success">Terbit</span><br>
                                            <small class="text-muted">{{ $row['certificate']->certificate_no }}</small>
                                        @elseif($row['is_eligible'])
                                            <span class="badge badge-info">Siap diterbitkan</span>
                                        @else
                                            <span class="badge badge-warning">Belum layak</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if($row['certificate'])
                                            <a href="{{ route('certificates.print', $row['certificate']->id) }}"
                                               target="_blank"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-print mr-1"></i> Cetak
                                            </a>
                                        @else
                                            <button type="button"
                                                    class="btn btn-sm btn-success btn-issue-certificate"
                                                    data-course-id="{{ $selectedCourse->id }}"
                                                    data-student-id="{{ $row['student']->id }}"
                                                    {{ $row['is_eligible'] && $selectedCourse->certificateSigner ? '' : 'disabled' }}>
                                                <i class="fas fa-certificate mr-1"></i> Terbitkan
                                            </button>
                                            @if($row['is_eligible'] && !$selectedCourse->certificateSigner)
                                                <br><small class="text-danger">TTD belum diatur</small>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Belum ada pelajar terdaftar di kelas ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection

@push('scripts')
<script>
$(function () {
    $('.select2').select2({ theme: 'bootstrap4' });
});

$(document).on('submit', '#certificate-signer-form', function (event) {
    event.preventDefault();

    const form = this;
    const formData = new FormData(form);
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

    Swal.fire({
        title: 'Simpan penandatangan?',
        text: 'Data ini akan digunakan untuk sertifikat yang diterbitkan setelahnya.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, simpan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#007bff',
        reverseButtons: true,
    }).then((result) => {
        if (!result.isConfirmed) return;

        Swal.fire({
            title: 'Menyimpan...',
            text: 'Sedang menyimpan data penandatangan.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => Swal.showLoading(),
        });

        $.ajax({
            url: '{{ route('certificates.signer.save') }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false,
                }).then(() => location.reload());
            },
            error: function (xhr) {
                const errors = xhr.responseJSON?.errors;
                const message = errors
                    ? Object.values(errors).flat().join('\n')
                    : (xhr.responseJSON?.message || 'Gagal menyimpan penandatangan.');

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: message,
                });
            }
        });
    });
});

$(document).on('click', '.btn-issue-certificate', function () {
    const button = $(this);

    Swal.fire({
        title: 'Terbitkan sertifikat?',
        text: 'Sertifikat akan dibuat untuk pelajar ini dan bisa langsung dicetak.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, terbitkan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#28a745',
        reverseButtons: true,
    }).then((result) => {
        if (!result.isConfirmed) return;

        button.prop('disabled', true);

        Swal.fire({
            title: 'Memproses...',
            text: 'Sedang menerbitkan sertifikat.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => Swal.showLoading(),
        });

        $.ajax({
            url: '{{ route('certificates.issue') }}',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                course_id: button.data('course-id'),
                student_id: button.data('student-id')
            },
            success: function (res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message,
                    showCancelButton: true,
                    confirmButtonText: 'Cetak Sertifikat',
                    cancelButtonText: 'Tutup',
                    confirmButtonColor: '#007bff',
                }).then((successResult) => {
                    if (successResult.isConfirmed) {
                        window.open(res.print_url, '_blank');
                    }
                    location.reload();
                });
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: xhr.responseJSON?.message || 'Gagal menerbitkan sertifikat.',
                });
                button.prop('disabled', false);
            }
        });
    });
});
</script>
@endpush
