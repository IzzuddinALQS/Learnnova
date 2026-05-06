@extends('main')

@section('title', 'Daftar Kelas')

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

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pilih Kelas untuk Akses Forum</h3>
            </div>
            <div class="card-body">
                @forelse($courses as $course)
                <div class="d-flex align-items-center p-3 border-bottom">
                    <div class="flex-grow-1">
                        <h6 class="mb-0">{{ $course->title }}</h6>
                        <small class="text-muted">
                            <i class="fas fa-chalkboard-teacher mr-1"></i>
                            {{ $course->instructor->name ?? '-' }}
                        </small>
                    </div>
                    <div>
                        @if(auth()->user()->hasPermission('forum.view'))
                        <a href="{{ route('forum.index', $course->id) }}"
                           class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-comments mr-1"></i>Forum
                        </a>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="fas fa-chalkboard fa-3x mb-3 d-block"></i>
                    Belum ada kelas tersedia.
                </div>
                @endforelse
            </div>
            @if($courses->hasPages())
            <div class="card-footer">
                {{ $courses->links() }}
            </div>
            @endif
        </div>

    </div>
</section>
@endsection
