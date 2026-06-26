@extends('main')

@section('title', 'Pengumuman')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Pengumuman</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Pengumuman</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold">Daftar Pengumuman</h3>
                @if(Auth::user()->hasPermission('announcements.manage'))
                    <a href="{{ route('announcements.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i> Tambah Pengumuman
                    </a>
                @endif
            </div>

            <div class="card-body p-0">
                @if($announcements->isEmpty())
                    <div class="p-4 text-center text-muted">
                        Belum ada pengumuman yang tersedia.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Kelas</th>
                                    <th>Status</th>
                                    <th>Ditayangkan</th>
                                    <th>Dibuat oleh</th>
                                    <th class="text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($announcements as $announcement)
                                    <tr>
                                        <td>
                                            <a href="{{ route('announcements.show', $announcement) }}">
                                                {{ $announcement->title }}
                                            </a>
                                        </td>
                                        <td>{{ $announcement->course->title ?? 'Umum' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $announcement->is_published ? 'success' : 'secondary' }}">
                                                {{ $announcement->is_published ? 'Published' : 'Draft' }}
                                            </span>
                                        </td>
                                        <td>{{ $announcement->published_at ? $announcement->published_at->format('d M Y H:i') : '-' }}</td>
                                        <td>{{ $announcement->creator->name ?? 'N/A' }}</td>
                                        <td class="text-right">
                                            <a href="{{ route('announcements.show', $announcement) }}" class="btn btn-sm btn-outline-primary mr-1">Lihat</a>
                                            @if(Auth::user()->hasPermission('announcements.manage'))
                                                <a href="{{ route('announcements.edit', $announcement) }}" class="btn btn-sm btn-warning mr-1">Edit</a>
                                                <form action="{{ route('announcements.destroy', $announcement) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Hapus pengumuman ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
