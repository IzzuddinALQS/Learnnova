@extends('main')

@section('title', 'Activity Log')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Activity Log</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Activity Log</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        {{-- Filter Card --}}
        <div class="card card-outline card-primary mb-3">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filter</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('activity-logs.index') }}">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-2">
                            <label class="small font-weight-bold">Cari Deskripsi</label>
                            <input type="text" name="search" class="form-control form-control-sm"
                                   placeholder="Ketik kata kunci..."
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2 col-sm-6 mb-2">
                            <label class="small font-weight-bold">Modul</label>
                            <select name="log_name" class="form-control form-control-sm">
                                <option value="">Semua Modul</option>
                                @foreach($logNames as $name)
                                    <option value="{{ $name }}" {{ request('log_name') == $name ? 'selected' : '' }}>
                                        {{ ucfirst($name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-6 mb-2">
                            <label class="small font-weight-bold">User</label>
                            <select name="user_id" class="form-control form-control-sm">
                                <option value="">Semua User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-6 mb-2">
                            <label class="small font-weight-bold">Dari Tanggal</label>
                            <input type="date" name="date_from" class="form-control form-control-sm"
                                   value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2 col-sm-6 mb-2">
                            <label class="small font-weight-bold">Sampai Tanggal</label>
                            <input type="date" name="date_to" class="form-control form-control-sm"
                                   value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-1 col-sm-6 mb-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-sm btn-block">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    @if(request()->hasAny(['search','log_name','user_id','date_from','date_to']))
                        <a href="{{ route('activity-logs.index') }}" class="btn btn-sm btn-secondary mt-1">
                            <i class="fas fa-times mr-1"></i> Reset Filter
                        </a>
                    @endif
                </form>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history mr-1"></i>
                    Total: <strong>{{ $logs->total() }}</strong> log
                </h3>
                <div class="card-tools">
                    <button id="btn-clear-all" class="btn btn-danger btn-sm">
                        <i class="fas fa-trash mr-1"></i> Hapus Semua Log
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:40px">#</th>
                                <th style="width:130px">Waktu</th>
                                <th style="width:100px">Modul</th>
                                <th>Deskripsi</th>
                                <th style="width:150px">User</th>
                                <th style="width:120px">Subject</th>
                                <th style="width:60px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                            <tr>
                                <td class="text-muted small">{{ $logs->firstItem() + $loop->index }}</td>
                                <td class="small text-nowrap">
                                    {{ $log->created_at?->format('d M Y') }}<br>
                                    <span class="text-muted">{{ $log->created_at?->format('H:i:s') }}</span>
                                </td>
                                <td>
                                    @php
                                        $badgeColor = match($log->log_name) {
                                            'courses'     => 'primary',
                                            'materials'   => 'info',
                                            'users'       => 'warning',
                                            'forum'       => 'success',
                                            'assignments' => 'danger',
                                            'auth'        => 'secondary',
                                            default       => 'dark',
                                        };
                                    @endphp
                                    <span class="badge badge-{{ $badgeColor }}">
                                        {{ $log->log_name ?? 'default' }}
                                    </span>
                                </td>
                                <td>{{ $log->description }}</td>
                                <td>
                                    @if($log->user)
                                        <div class="d-flex align-items-center" style="gap:6px">
                                            <img src="{{ asset('dist/img/user2-160x160.jpg') }}"
                                                 class="img-circle" style="width:22px;height:22px;object-fit:cover">
                                            <span class="small">{{ $log->user->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted small">System</span>
                                    @endif
                                </td>
                                <td class="small text-muted">
                                    @if($log->subject_type)
                                        {{ class_basename($log->subject_type) }}
                                        <span class="text-muted">#{{ $log->subject_id }}</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-xs btn-danger btn-delete-log"
                                            data-url="{{ route('activity-logs.destroy', $log->id) }}"
                                            title="Hapus log ini">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    Tidak ada log aktivitas.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($logs->hasPages())
            <div class="card-footer">
                {{ $logs->links() }}
            </div>
            @endif
        </div>

    </div>
</section>
@endsection

@push('scripts')
<script>
$(function () {

    // Hapus satu log
    $(document).on('click', '.btn-delete-log', function () {
        const url = $(this).data('url');
        const row = $(this).closest('tr');

        Swal.fire({
            title: 'Hapus log ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonText: 'Batal',
            confirmButtonText: 'Ya, hapus',
        }).then(result => {
            if (!result.isConfirmed) return;

            $.ajax({
                url: url,
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success(res) {
                    row.fadeOut(300, () => row.remove());
                    toastSuccess(res.message);
                },
                error() { toastError('Gagal menghapus log.'); }
            });
        });
    });

    // Hapus semua log
    $('#btn-clear-all').on('click', function () {
        Swal.fire({
            title: 'Hapus SEMUA log?',
            text: 'Tindakan ini tidak bisa dibatalkan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonText: 'Batal',
            confirmButtonText: 'Ya, hapus semua',
        }).then(result => {
            if (!result.isConfirmed) return;

            $.ajax({
                url: '{{ route('activity-logs.destroyAll') }}',
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success(res) {
                    $('tbody').html(`
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Tidak ada log aktivitas.
                            </td>
                        </tr>
                    `);
                    toastSuccess(res.message);
                },
                error() { toastError('Gagal menghapus semua log.'); }
            });
        });
    });

    function toastSuccess(msg) {
        Swal.fire({ toast: true, position: 'top-end', icon: 'success',
                    title: msg, showConfirmButton: false, timer: 2500 });
    }
    function toastError(msg) {
        Swal.fire({ toast: true, position: 'top-end', icon: 'error',
                    title: msg, showConfirmButton: false, timer: 3000 });
    }
});
</script>
@endpush
