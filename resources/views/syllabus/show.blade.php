@extends('main')

@section('title', 'Detail Silabus — ' . $syllabus->name)

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Detail Silabus</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('syllabus.index') }}">Silabus</a></li>
                        <li class="breadcrumb-item active">{{ Str::limit($syllabus->name, 30) }}</li>
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
                            <h3 class="card-title font-weight-bold">{{ $syllabus->name }}</h3>
                            <div class="card-tools">
                                <span class="badge badge-info px-3 py-2">
                                    <i class="fas fa-clock mr-1"></i>{{ $syllabus->duration_weeks }} Minggu
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            @php
                                $imgUrl = $syllabus->theme
                                    ? asset('storage/' . $syllabus->theme)
                                    : asset('img/images.jpg');
                            @endphp
                            <img src="{{ $imgUrl }}" alt="{{ $syllabus->name }}"
                                class="img-fluid rounded mb-4 shadow-sm"
                                style="width: 100%; max-height: 320px; object-fit: cover;">

                            <h6 class="font-weight-bold text-muted text-uppercase mb-2">Deskripsi</h6>
                            <p style="line-height: 1.8;">
                                {{ $syllabus->description ?: 'Tidak ada deskripsi untuk silabus ini.' }}
                            </p>
                        </div>
                        <div class="card-footer bg-white">
                            <a href="{{ route('syllabus.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </a>
                            @if (auth()->user()->hasPermission('syllabus.manage'))
                                <a href="{{ route('syllabus.edit', $syllabus->id) }}" class="btn btn-warning ml-2">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>
                                <button class="btn btn-danger ml-2 btn-delete"
                                    data-url="{{ route('syllabus.destroy', $syllabus->id) }}"
                                    data-redirect="{{ route('syllabus.index') }}">
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
                                    <td class="text-muted pl-3" style="width: 40%;">Durasi</td>
                                    <td class="font-weight-bold">{{ $syllabus->duration_weeks }} minggu</td>
                                </tr>
                                <tr>
                                    <td class="text-muted pl-3">Digunakan</td>
                                    <td class="font-weight-bold">{{ $syllabus->courses_count }} kelas</td>
                                </tr>
                                <tr>
                                    <td class="text-muted pl-3">Dibuat oleh</td>
                                    <td class="font-weight-bold">{{ optional($syllabus->creator)->name ?? 'Sistem' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted pl-3">Dibuat</td>
                                    <td>{{ $syllabus->created_at->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted pl-3">Diperbarui</td>
                                    <td>{{ $syllabus->updated_at->format('d M Y') }}</td>
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
            const redirect = $(this).data('redirect') || '{{ route('syllabus.index') }}';

            Swal.fire({
                title: 'Hapus Silabus?',
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
                            })
                            .then(() => window.location.href = redirect);
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal menghapus data.', 'error');
                    }
                });
            });
        });
    </script>
@endpush
