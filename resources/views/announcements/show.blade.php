@extends('main')

@section('title', 'Detail Pengumuman')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Detail Pengumuman</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('announcements.index') }}">Pengumuman</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="card-title font-weight-bold">{{ $announcement->title }}</h3>
                    <div class="text-muted small mt-1">
                        {{ $announcement->course->title ?? 'Pengumuman Umum' }} ·
                        {{ $announcement->published_at ? $announcement->published_at->format('d M Y H:i') : 'Belum dipublikasikan' }}
                    </div>
                </div>
                @if(Auth::user()->hasPermission('announcements.manage'))
                    <div class="btn-group">
                        <a href="{{ route('announcements.edit', $announcement) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('announcements.destroy', $announcement) }}" method="POST" onsubmit="return confirm('Hapus pengumuman ini?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </div>
                @endif
            </div>

            <div class="card-body">
                <div class="mb-4">
                    <strong>Oleh:</strong> {{ $announcement->creator->name ?? 'N/A' }}<br>
                    <strong>Status:</strong>
                    <span class="badge badge-{{ $announcement->is_published ? 'success' : 'secondary' }}">
                        {{ $announcement->is_published ? 'Published' : 'Draft' }}
                    </span>
                </div>

                <div class="announcement-content">
                    {!! nl2br(e($announcement->content)) !!}
                </div>
            </div>

            <div class="card-footer">
                <a href="{{ route('announcements.index') }}" class="btn btn-secondary">Kembali ke daftar</a>
            </div>
        </div>
    </div>
</section>
@endsection
