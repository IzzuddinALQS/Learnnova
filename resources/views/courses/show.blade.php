@extends('main')

@section('title', 'Detail Kelas — ' . $course->title)

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Detail Kelas</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Kelas</a></li>
                        <li class="breadcrumb-item active">{{ Str::limit($course->title, 30) }}</li>
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
                            <h3 class="card-title font-weight-bold">{{ $course->title }}</h3>
                            <div class="card-tools">
                                @php
                                    $sc =
                                        $course->status === 'published'
                                            ? 'success'
                                            : ($course->status === 'archived'
                                                ? 'secondary'
                                                : 'warning');
                                @endphp
                                <span
                                    class="badge badge-{{ $sc }} px-3 py-2">{{ ucfirst($course->status) }}</span>
                                @if ($course->duration_weeks)
                                    <span class="badge badge-info px-3 py-2 ml-1">
                                        {{ $course->duration_weeks }} minggu
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- Thumbnail --}}
                            @php
                                $thumb = $course->thumbnail;
                                $thumbUrl = $thumb
                                    ? (Str::startsWith($thumb, ['http://', 'https://'])
                                        ? $thumb
                                        : (Str::startsWith($thumb, 'img/')
                                            ? asset($thumb)
                                            : asset('storage/' . ltrim($thumb, '/'))))
                                    : asset('img/images.jpg');
                            @endphp
                            <img src="{{ $thumbUrl }}" alt="{{ $course->title }}"
                                class="img-fluid rounded mb-4 shadow-sm"
                                style="width: 100%; max-height: 360px; object-fit: cover;">

                            {{-- Deskripsi --}}
                            <h6 class="font-weight-bold text-muted text-uppercase mb-2">Deskripsi</h6>
                            <p style="line-height: 1.8;">
                                {{ $course->description ?: 'Deskripsi kelas belum tersedia.' }}
                            </p>

                            {{-- Aksi --}}
                            <div class="mt-4 pt-3 border-top">
                                <a href="{{ route('courses.materials.index', $course->id) }}"
                                    class="btn btn-primary rounded-pill px-4 mr-2">
                                    <i class="fas fa-book-open mr-1"></i> Materi
                                </a>
                                <a href="{{ route('assignments.index') }}"
                                    class="btn btn-outline-secondary rounded-pill px-4 mr-2">
                                    <i class="fas fa-tasks mr-1"></i> Tugas
                                </a>
                                <a href="{{ route('forum.index', $course->id) }}"
                                    class="btn btn-outline-primary rounded-pill px-4 mr-2">
                                    <i class="fas fa-comments mr-1"></i> Forum
                                </a>

                                {{-- Tombol Nilai untuk Pelajar --}}
                                @if (Auth::user()->hasRole('pelajar'))
                                    <a href="{{ route('gradebook.index') }}?course_id={{ $course->id }}"
                                        class="btn btn-outline-success rounded-pill px-4">
                                        <i class="fas fa-chart-bar mr-1"></i> Nilai Saya
                                    </a>
                                @endif

                                {{-- Tombol Rekap Nilai untuk Pengajar & Admin --}}
                                @if (Auth::user()->hasPermission('reports.view') && !Auth::user()->hasRole('pelajar'))
                                    <a href="{{ route('gradebook.course', $course->id) }}"
                                        class="btn btn-outline-success rounded-pill px-4">
                                        <i class="fas fa-chart-bar mr-1"></i> Rekap Nilai
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <a href="{{ route('courses.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </a>
                            @if (auth()->user()->hasPermission('courses.edit'))
                                <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-warning ml-2">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>
                                <a href="{{ route('enrollments.index', $course->id) }}" class="btn btn-info ml-2">
                                    <i class="fas fa-users mr-1"></i> Kelola Pelajar
                                </a>
                            @endif
                            @if (auth()->user()->hasPermission('courses.delete'))
                                <button class="btn btn-danger ml-2 btn-delete"
                                    data-url="{{ route('courses.destroy', $course->id) }}"
                                    data-redirect="{{ route('courses.index') }}">
                                    <i class="fas fa-trash mr-1"></i> Hapus
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Kolom Info --}}
                <div class="col-lg-4 col-md-12">
                    {{-- Profile Pengajar --}}
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chalkboard-teacher mr-2"></i>Pengajar</h3>
                        </div>
                        <div class="card-body p-0">
                            @forelse($course->instructors as $inst)
                                <div class="d-flex align-items-center px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <img src="{{ 'https://ui-avatars.com/api/?name=' . urlencode($inst->name) . '&background=4f46e5&color=fff&size=64' }}"
                                        alt="{{ $inst->name }}" class="img-circle mr-3"
                                        style="width: 42px; height: 42px; object-fit: cover;">
                                    <div>
                                        <div class="font-weight-bold" style="font-size: 0.95rem;">{{ $inst->name }}</div>
                                        <small class="text-{{ $inst->pivot->is_primary ? 'primary' : 'muted' }}">
                                            @if ($inst->pivot->is_primary)
                                                <i class="fas fa-star mr-1"></i>Pengajar Utama
                                            @else
                                                <i class="fas fa-user-tie mr-1"></i>Pengajar
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            @empty
                                <div class="px-3 py-2 text-muted">Belum ada pengajar.</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Info Kelas --}}
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i>Info Kelas</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted pl-3" style="width: 45%;">Status</td>
                                    <td>
                                        <span
                                            class="badge badge-{{ $sc }}">{{ ucfirst($course->status) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted pl-3">Durasi</td>
                                    <td class="font-weight-bold">
                                        {{ $course->duration_weeks ? $course->duration_weeks . ' minggu' : '—' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted pl-3">Maks. Pelajar</td>
                                    <td class="font-weight-bold">{{ $course->max_students ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted pl-3">Dipublish</td>
                                    <td>{{ $course->published_at ? $course->published_at->format('d M Y') : '—' }}</td>
                                </tr>
                                @if ($course->enrollments_count ?? false)
                                    <tr>
                                        <td class="text-muted pl-3">Pelajar</td>
                                        <td class="font-weight-bold">{{ $course->enrollments_count }}</td>
                                    </tr>
                                @endif
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
            const redirect = $(this).data('redirect') || '{{ route('courses.index') }}';

            Swal.fire({
                title: 'Hapus Kelas?',
                text: 'Semua data terkait (modul, materi, enrollment) juga akan dihapus.',
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
                        Swal.fire('Error', 'Gagal menghapus kelas.', 'error');
                    }
                });
            });
        });
    </script>
@endpush
