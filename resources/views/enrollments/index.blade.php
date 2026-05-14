@extends('main')

@section('title', 'Manajemen Pelajar — ' . $course->title)

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Manajemen Pelajar</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Kelas</a></li>
                    <li class="breadcrumb-item active">Pelajar</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        {{-- Info Kelas --}}
        <div class="card card-primary card-outline mb-4">
            <div class="card-body py-3">
                <div class="d-flex align-items-center flex-wrap" style="gap: 12px;">
                    <div>
                        <h5 class="mb-0 font-weight-bold">{{ $course->title }}</h5>
                        <small class="text-muted">
                            Pengajar: <strong>{{ optional($course->instructor)->name ?? '—' }}</strong>
                            &nbsp;|&nbsp;
                            @php $sc = $course->status === 'published' ? 'success' : ($course->status === 'archived' ? 'secondary' : 'warning'); @endphp
                            <span class="badge badge-{{ $sc }}">{{ ucfirst($course->status) }}</span>
                        </small>
                    </div>
                    <div class="ml-auto">
                        <span class="badge badge-info px-3 py-2" style="font-size: 0.9rem;">
                            {{ $enrollments->count() }} Pelajar Terdaftar
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Daftar Pelajar Terdaftar --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-users mr-2"></i>Pelajar Terdaftar</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Terdaftar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($enrollments as $enrollment)
                                    <tr id="row-{{ $enrollment->id }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ optional($enrollment->student)->name }}</td>
                                        <td>{{ optional($enrollment->student)->email }}</td>
                                        <td>
                                            <select class="form-control form-control-sm status-select"
                                                data-id="{{ $enrollment->id }}"
                                                style="width: auto; min-width: 110px;">
                                                <option value="active"    {{ $enrollment->status === 'active'    ? 'selected' : '' }}>Aktif</option>
                                                <option value="completed" {{ $enrollment->status === 'completed' ? 'selected' : '' }}>Selesai</option>
                                                <option value="dropped"   {{ $enrollment->status === 'dropped'   ? 'selected' : '' }}>Keluar</option>
                                            </select>
                                        </td>
                                        <td>{{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('d M Y') : '—' }}</td>
                                        <td>
                                            <button class="btn btn-danger btn-xs btn-remove"
                                                data-id="{{ $enrollment->id }}"
                                                data-name="{{ optional($enrollment->student)->name }}">
                                                <i class="fas fa-user-minus"></i> Keluarkan
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            Belum ada pelajar yang terdaftar di kelas ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Form Tambah Pelajar --}}
            <div class="col-lg-4">
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user-plus mr-2"></i>Daftarkan Pelajar</h3>
                    </div>
                    <div class="card-body">
                        @if($availableStudents->isEmpty())
                            <div class="alert alert-info mb-0">
                                Semua pelajar aktif sudah terdaftar di kelas ini.
                            </div>
                        @else
                            <div id="enroll-alert" class="alert d-none"></div>
                            <form id="enroll-form">
                                @csrf
                                <div class="form-group">
                                    <label>Pilih Pelajar</label>
                                    <small class="d-block text-muted mb-2">Tahan Ctrl untuk memilih lebih dari satu.</small>
                                    <select name="student_ids[]" id="student-select" class="form-control" multiple
                                        style="height: 200px;">
                                        @foreach($availableStudents as $student)
                                            <option value="{{ $student->id }}">{{ $student->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success btn-block" id="btn-enroll">
                                    <i class="fas fa-user-plus mr-1"></i> Daftarkan
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body text-center">
                        <a href="{{ route('courses.index') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Kelas
                        </a>
                        <a href="{{ route('teachers.overview') }}" class="btn btn-outline-info btn-block mt-2">
                            <i class="fas fa-chalkboard-teacher mr-1"></i> Lihat Semua Pengajar
                        </a>
                        <a href="{{ route('students.overview') }}" class="btn btn-outline-primary btn-block mt-2">
                            <i class="fas fa-users mr-1"></i> Lihat Semua Pelajar
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection

@push('scripts')
<script>
const courseId = {{ $course->id }};

// Daftarkan pelajar
$('#enroll-form').on('submit', function(e) {
    e.preventDefault();
    const selected = $('#student-select').val();
    if (!selected || selected.length === 0) {
        showAlert('warning', 'Pilih minimal satu pelajar.');
        return;
    }

    const $btn = $('#btn-enroll');
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Mendaftarkan...');

    $.ajax({
        url: `/courses/${courseId}/enrollments`,
        method: 'POST',
        data: { _token: $('meta[name="csrf-token"]').attr('content'), student_ids: selected },
        success: function(res) {
            showAlert('success', res.message);
            setTimeout(() => location.reload(), 1200);
        },
        error: function(xhr) {
            $btn.prop('disabled', false).html('<i class="fas fa-user-plus mr-1"></i> Daftarkan');
            const msg = xhr.responseJSON?.message || 'Terjadi kesalahan.';
            showAlert('danger', msg);
        }
    });
});

// Update status enrollment
$(document).on('change', '.status-select', function() {
    const id = $(this).data('id');
    const status = $(this).val();
    $.ajax({
        url: `/courses/${courseId}/enrollments/${id}`,
        method: 'POST',
        data: { _token: $('meta[name="csrf-token"]').attr('content'), _method: 'PUT', status: status },
        success: function(res) {
            toastr.success(res.message);
        },
        error: function() {
            toastr.error('Gagal memperbarui status.');
        }
    });
});

// Keluarkan pelajar
$(document).on('click', '.btn-remove', function() {
    const id = $(this).data('id');
    const name = $(this).data('name');
    if (!confirm(`Keluarkan "${name}" dari kelas ini?`)) return;

    $.ajax({
        url: `/courses/${courseId}/enrollments/${id}`,
        method: 'POST',
        data: { _token: $('meta[name="csrf-token"]').attr('content'), _method: 'DELETE' },
        success: function(res) {
            $(`#row-${id}`).fadeOut(300, function() { $(this).remove(); });
            toastr.success(res.message);
        },
        error: function() {
            toastr.error('Gagal mengeluarkan pelajar.');
        }
    });
});

function showAlert(type, msg) {
    $('#enroll-alert').removeClass('d-none alert-success alert-warning alert-danger')
        .addClass(`alert-${type}`).text(msg);
}
</script>
@endpush
