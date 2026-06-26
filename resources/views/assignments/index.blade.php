{{-- @extends('main')
@section('title', 'Daftar Tugas')
@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Daftar Tugas</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Tugas</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<section>
<div class="container mt-4">

  <div class="row g-3">
    <div class="col-md-6">
      <div class="card p-3 shadow-sm h-80">
        <div class="mb-2">
          <span class="badge bg-warning text-dark">Bahasa Arab</span>
        </div>

        <!-- Judul -->
        <h5 class="fw-bold">
          <a href="{{ route('assignments.show', 1) }}" class="text-dark text-decoration-none">Tugas Gramatika</a>
        </h5>

        <!-- Deadline -->
        <p class="text-muted mb-2">Tenggat: 27 April 2026</p>
        <p class="text-muted small">
          Tugas ditulis tangan. Langsung dikerjakan di buku, difoto dan diunggah
        </p>
        <span class="badge bg-success mb-3">2 Hari Lagi</span>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card p-3 shadow-sm h-80">
        <div class="mb-2">
          <span class="badge bg-primary">Bahasa Inggris</span>
        </div>
        <h5 class="fw-bold">
          <a href="{{ route('assignments.show', 2) }}" class="text-dark text-decoration-none">Essay Grammar Struktur Kalimat</a>
        </h5>
        <p class="text-muted mb-2">Tenggat: 27 April 2026</p>
        <p class="text-muted small">
          Buat sebuah essay pendek tentang struktur kalimat dalam bahasa Inggris.
        </p>
        <span class="badge bg-success mb-3">4 Hari Lagi</span>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card p-3 shadow-sm h-80">
        <div class="mb-2">
          <span class="badge bg-primary">Matetika</span>
        </div>
        <h5 class="fw-bold">
          <a href="{{ route('assignments.show', 3) }}" class="text-dark text-decoration-none">Essay Grammar Struktur Kalimat</a>
        </h5>
        <p class="text-muted mb-2">Tenggat: 27 April 2026</p>
        <p class="text-muted small">
          Buat sebuah essay pendek tentang struktur kalimat dalam bahasa Inggris.
        </p>
        <span class="badge bg-success mb-3">4 Hari Lagi</span>
      </div>
    </div>

  </div>
</div>

</section>
@endsection --}}

@extends('main')

@section('title', 'Daftar Tugas')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Daftar Tugas</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Tugas</li>
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
                        Total: <strong>{{ $assignments->count() }}</strong> tugas
                    </div>
                    @if (Auth::user()->hasPermission('assignments.create'))
                        <a href="{{ route('assignments.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus mr-1"></i> Tambah Tugas
                        </a>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Judul</th>
                                <th>Kelas</th>
                                <th>Deadline</th>
                                <th>Nilai Maks</th>
                                <th>Submissions</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignments as $i => $item)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td class="font-weight-bold">{{ $item->title }}</td>
                                    <td>{{ $item->course->title ?? '-' }}</td>
                                    <td>
                                        @if ($item->due_date->isPast())
                                            <span class="badge badge-danger">
                                                <i class="fas fa-clock mr-1"></i>{{ $item->due_date->format('d M Y, H:i') }}
                                            </span>
                                        @else
                                            <span class="badge badge-success">
                                                <i class="fas fa-clock mr-1"></i>{{ $item->due_date->format('d M Y, H:i') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $item->max_score }}</td>
                                    <td>
                                        <a href="{{ route('assignments.submissions', $item->id) }}"
                                            class="btn btn-xs btn-info">
                                            <i class="fas fa-users mr-1"></i>
                                            {{ $item->submissions_count }} siswa
                                        </a>
                                    </td>
                                    </td>
                                    <td>
                                        <a href="{{ route('assignments.show', $item->id) }}"
                                            class="btn btn-xs btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if (Auth::user()->hasPermission('assignments.edit'))
                                            <a href="{{ route('assignments.edit', $item->id) }}"
                                                class="btn btn-xs btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        @if (Auth::user()->hasPermission('assignments.delete'))
                                            <button class="btn btn-xs btn-danger btn-delete"
                                                data-url="{{ route('assignments.destroy', $item->id) }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        Belum ada tugas.
                                        @if (Auth::user()->hasPermission('assignments.create'))
                                            <a href="{{ route('assignments.create') }}">Tambah sekarang</a>.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
@endsection

@push('scripts')
    <script>
        ajaxDelete('.btn-delete', 'Hapus Tugas?');
    </script>
@endpush
