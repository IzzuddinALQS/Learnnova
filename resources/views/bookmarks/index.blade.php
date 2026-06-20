@extends('main')

@section('title', 'Bookmark Saya')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-8">
                <h1 class="m-0" style="font-size:1.3rem">Bookmark Saya</h1>
            </div>
            <div class="col-sm-4 text-right">
                <a href="{{ route('materi.hub') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Materi
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Daftar materi yang Anda bookmark</h3>
                    </div>
                    <div class="card-body">
                        @if($bookmarks->isEmpty())
                            <div class="alert alert-info">
                                Anda belum memiliki bookmark.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Materi</th>
                                            <th>Kelas / Modul</th>
                                            <th>Jenis</th>
                                            <th>Waktu Bookmark</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bookmarks as $bookmark)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $bookmark->material->title ?? 'Materi tidak ditemukan' }}</td>
                                                <td>
                                                    @if($bookmark->material && $bookmark->material->module)
                                                        {{ $bookmark->material->module->course->title ?? 'Kelas' }}
                                                        <br>
                                                        <small>{{ $bookmark->material->module->title }}</small>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ ucfirst($bookmark->material->type ?? '-') }}</td>
                                                <td>{{ optional($bookmark->created_at)->format('d M Y H:i') }}</td>
                                                <td>
                                                    @if($bookmark->material && $bookmark->material->module)
                                                        <a href="{{ route('courses.materials.show', [$bookmark->material->module->course->id, $bookmark->material->id]) }}" class="btn btn-sm btn-primary">
                                                            Buka Materi
                                                        </a>
                                                    @else
                                                        <span class="text-muted">Tidak tersedia</span>
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
        </div>
    </div>
</section>
@endsection
