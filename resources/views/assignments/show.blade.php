@extends('main')

@section('title', 'Detail Tugas — ' . $assignment->title)

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Detail Tugas</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('assignments.index') }}">Tugas</a></li>
                        <li class="breadcrumb-item active">{{ Str::limit($assignment->title, 30) }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">

                {{-- Kolom Utama --}}
                <div class="col-lg-8 col-md-12 mb-4">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold">{{ $assignment->title }}</h3>
                            <div class="card-tools">
                                @if ($assignment->due_date->isPast())
                                    <span class="badge badge-danger px-3 py-2">
                                        <i class="fas fa-clock mr-1"></i>Deadline Lewat
                                    </span>
                                @else
                                    <span class="badge badge-success px-3 py-2">
                                        <i class="fas fa-clock mr-1"></i>Aktif
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="font-weight-bold text-muted text-uppercase mb-2">Deskripsi / Instruksi</h6>
                            <p style="line-height: 1.8;">
                                {{ $assignment->description ?: 'Tidak ada deskripsi.' }}
                            </p>

                            @if ($assignment->file)
                                <div class="mt-3">
                                    <h6 class="font-weight-bold text-muted text-uppercase mb-2">File Lampiran</h6>
                                    <a href="{{ asset('storage/' . $assignment->file) }}" target="_blank"
                                        class="btn btn-info btn-sm">
                                        <i class="fas fa-paperclip mr-1"></i> Unduh / Lihat File
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer bg-white">
                            <a href="{{ route('assignments.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </a>
                            @if (Auth::user()->hasPermission('assignments.edit'))
                                <a href="{{ route('assignments.edit', $assignment->id) }}" class="btn btn-warning ml-2">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>
                            @endif
                            @if (Auth::user()->hasPermission('assignments.grade'))
                                <a href="{{ route('assignments.submissions', $assignment->id) }}"
                                    class="btn btn-primary ml-2">
                                    <i class="fas fa-users mr-1"></i> Lihat Submission
                                    @if ($assignment->submissions_count > 0)
                                        <span class="badge badge-light ml-1">{{ $assignment->submissions_count }}</span>
                                    @endif
                                </a>
                            @endif
                            @if (Auth::user()->hasPermission('assignments.delete'))
                                <button class="btn btn-danger ml-2 btn-delete"
                                    data-url="{{ route('assignments.destroy', $assignment->id) }}"
                                    data-redirect="{{ route('assignments.index') }}">
                                    <i class="fas fa-trash mr-1"></i> Hapus
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Kolom Info --}}
                <div class="col-lg-4 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i>Informasi</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted pl-3" style="width: 40%;">Kelas</td>
                                    <td class="font-weight-bold">{{ $assignment->course->title ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted pl-3">Deadline</td>
                                    <td class="font-weight-bold">{{ $assignment->due_date->format('d M Y, H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted pl-3">Nilai Maks</td>
                                    <td class="font-weight-bold">{{ $assignment->max_score }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted pl-3">Pengumpulan</td>
                                    <td class="font-weight-bold">{{ $assignment->submissions_count }} siswa</td>
                                </tr>
                                <tr>
                                    <td class="text-muted pl-3">Dibuat oleh</td>
                                    <td class="font-weight-bold">{{ optional($assignment->creator)->name ?? 'Sistem' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted pl-3">Dibuat</td>
                                    <td>{{ $assignment->created_at->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted pl-3">Diperbarui</td>
                                    <td>{{ $assignment->updated_at->format('d M Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).on('click', '.btn-delete', function() {
            const url = $(this).data('url');
            const redirect = $(this).data('redirect') || '{{ route('assignments.index') }}';

            Swal.fire({
                title: 'Hapus Tugas?',
                text: 'Data yang dihapus tidak bisa dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash mr-1"></i> Ya, Hapus',
                cancelButtonText: 'Batal',
            }).then(result => {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Dihapus!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => window.location.href = redirect);
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal menghapus data.', 'error');
                    }
                });
            });
        });
    </script>
@endpush
