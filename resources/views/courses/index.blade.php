@extends('main')

@section('title', 'Kelas')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Daftar Kelas</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Kelas</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
                <div class="text-muted">
                    Total: <strong>{{ $courses->count() }}</strong> kelas
                </div>
                @if(auth()->check() && auth()->user()->hasPermission('courses.create'))
                    <a href="{{ route('courses.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus mr-1"></i> Tambah Kelas
                    </a>
                @endif
            </div>
        </div>

        <div class="row">
            @forelse($courses as $course)
                @php
                    $thumb = $course->thumbnail;
                    $thumbUrl = $thumb
                        ? (Str::startsWith($thumb, ['http://', 'https://'])
                            ? $thumb
                            : (Str::startsWith($thumb, ['img/', 'storage/']) ? asset($thumb) : asset('storage/' . ltrim($thumb, '/'))))
                        : asset('img/images.jpg');
                @endphp

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="{{ $thumbUrl }}" class="card-img-top" style="height: 180px; object-fit: cover;" alt="{{ $course->title }}">

                        <div class="card-body d-flex flex-column">
                            <div class="mb-2">
                                @php
                                    $statusClass = $course->status === 'published' ? 'success' : ($course->status === 'archived' ? 'secondary' : 'warning');
                                @endphp
                                <span class="badge badge-{{ $statusClass }}">{{ ucfirst($course->status) }}</span>
                                @if($course->duration_weeks)
                                    <span class="badge badge-info">{{ $course->duration_weeks }} minggu</span>
                                @endif
                            </div>

                            <h5 class="card-title font-weight-bold mb-1">{{ $course->title }}</h5>
                            <small class="text-muted mb-2">
                                Pengajar:
                                @forelse($course->instructors as $inst)
                                    <strong>{{ $inst->name }}</strong>@if($inst->pivot->is_primary) <span class="badge badge-primary" style="font-size:0.65rem;">Utama</span>@endif{{ !$loop->last ? ', ' : '' }}
                                @empty
                                    <strong>—</strong>
                                @endforelse
                            </small>

                            @if($course->description)
                                <p class="card-text text-muted mb-3">{{ Str::limit($course->description, 120) }}</p>
                            @endif

                            <div class="mt-auto">
                                <a href="{{ route('courses.show', $course->id) }}" class="btn btn-primary btn-block">
                                    <i class="fas fa-eye mr-1"></i> Lihat Kelas
                                </a>
                                @if(auth()->user()->hasPermission('courses.edit'))
                                    <div class="btn-group btn-block mt-1">
                                        <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </a>
                                        <a href="{{ route('enrollments.index', $course->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-users mr-1"></i> Pelajar
                                        </a>
                                        <button class="btn btn-danger btn-sm btn-delete-course"
                                            data-url="{{ route('courses.destroy', $course->id) }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info mb-0">
                        Belum ada data kelas.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
$(document).on('click', '.btn-delete-course', function() {
    const url = $(this).data('url');
    if (!confirm('Hapus kelas ini? Data terkait (modul, materi, enrollment) juga akan dihapus.')) return;
    $.ajax({
        url: url,
        method: 'POST',
        data: { _token: $('meta[name="csrf-token"]').attr('content'), _method: 'DELETE' },
        success: function(res) {
            toastr.success(res.message);
            setTimeout(() => location.reload(), 1000);
        },
        error: function() { toastr.error('Gagal menghapus kelas.'); }
    });
});
</script>
@endpush

