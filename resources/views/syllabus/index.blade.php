@extends('main')

@section('title', 'Katalog Silabus')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Katalog Silabus</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Silabus</li>
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
                        Total: <strong>{{ $data_syllabi->count() }}</strong> silabus
                    </div>
                    @if (auth()->user()->hasPermission('syllabus.manage'))
                        <a href="{{ route('syllabus.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus mr-1"></i> Tambah Silabus
                        </a>
                    @endif
                </div>
            </div>

            <div class="row">
                @forelse($data_syllabi as $item)
                    @php
                        $imgUrl = $item->theme
                            ? (Str::startsWith($item->theme, ['http://', 'https://'])
                                ? $item->theme
                                : asset('img/' . $item->theme))
                            : asset('img/images.jpg');
                    @endphp

                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm">
                            <img src="{{ $imgUrl }}" class="card-img-top" style="height: 180px; object-fit: cover;"
                                alt="{{ $item->name }}">

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title font-weight-bold mb-1">{{ $item->name }}</h5>
                                <small class="text-muted mb-2">
                                    <i class="fas fa-clock mr-1"></i>{{ $item->duration_weeks }} minggu
                                    &nbsp;·&nbsp;
                                    <i class="fas fa-chalkboard mr-1"></i>{{ $item->courses_count }} kelas
                                    &nbsp;·&nbsp;
                                    <i class="fas fa-user mr-1"></i>{{ $item->instructor->name ?? '-' }}
                                    {{-- ← tambahkan --}}
                                </small>

                                @if ($item->description)
                                    <p class="card-text text-muted mb-3">
                                        {{ Str::limit($item->description, 100) }}
                                    </p>
                                @endif

                                <div class="mt-auto">
                                    <a href="{{ route('syllabus.show', $item->id) }}" class="btn btn-primary btn-block">
                                        <i class="fas fa-eye mr-1"></i> Lihat Detail
                                    </a>
                                    @if (auth()->user()->hasPermission('syllabus.manage'))
                                        <div class="btn-group btn-block mt-1">
                                            <a href="{{ route('syllabus.edit', $item->id) }}"
                                                class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </a>
                                            <button class="btn btn-danger btn-sm btn-delete"
                                                data-url="{{ route('syllabus.destroy', $item->id) }}">
                                                <i class="fas fa-trash mr-1"></i> Hapus
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
                            Belum ada data silabus.
                            @if (auth()->user()->hasPermission('syllabus.manage'))
                                <a href="{{ route('syllabus.create') }}">Tambah sekarang</a>.
                            @endif
                        </div>
                    </div>
                @endforelse
            </div>

        </div>
    </section>
@endsection

@push('scripts')
    <script>
        ajaxDelete('.btn-delete', 'Hapus Silabus?');
    </script>
@endpush
