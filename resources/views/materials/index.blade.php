@extends('main')

@section('title', 'Materi Kelas — ' . $course->title)

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Materi Kelas</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Kelas</a></li>
                    <li class="breadcrumb-item active">Materi</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        {{-- Header Card --}}
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-layer-group mr-2"></i>
                    {{ $course->title }}
                </h3>
                <div class="card-tools">
                    @if($isPengajar)
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalAddBab">
                        <i class="fas fa-plus mr-1"></i> Tambah Bab
                    </button>
                    <a href="{{ route('courses.materials.create', $course->id) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus mr-1"></i> Tambah Materi
                    </a>
                    @endif
                </div>
            </div>
            <div class="card-body p-0">

                @forelse($modules as $module)
                <div class="card card-outline card-secondary mb-0 border-left-0 border-right-0 border-top-0 rounded-0">
                    <div class="card-header bg-light py-2">
                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                            <div>
                                <h5 class="mb-0 font-weight-bold">
                                    <i class="fas fa-folder-open mr-2 text-primary"></i>
                                    {{ $module->title }}
                                </h5>
                                @if($module->description)
                                <small class="text-muted">{{ $module->description }}</small>
                                @endif
                            </div>
                            <div class="d-flex align-items-center" style="gap:6px">
                                {{-- Progress bar untuk pelajar enrolled --}}
                                @if($isEnrolled && !$isPengajar && isset($moduleProgress[$module->id]))
                                @php $prog = $moduleProgress[$module->id]; @endphp
                                <div class="mr-3" style="min-width:140px">
                                    <small class="text-muted">
                                        {{ $prog['completed'] }}/{{ $prog['total'] }} selesai
                                        ({{ $prog['percentage'] }}%)
                                    </small>
                                    <div class="progress progress-xs mt-1">
                                        <div class="progress-bar bg-success"
                                             style="width: {{ $prog['percentage'] }}%"></div>
                                    </div>
                                </div>
                                @endif

                                @if($isPengajar)
                                <button type="button"
                                        class="btn btn-warning btn-xs btn-edit-bab"
                                        data-id="{{ $module->id }}"
                                        data-title="{{ $module->title }}"
                                        data-description="{{ $module->description }}"
                                        data-order="{{ $module->order }}"
                                        data-url="{{ route('courses.modules.update', [$course->id, $module->id]) }}"
                                        data-toggle="modal" data-target="#modalEditBab">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button type="button"
                                        class="btn btn-danger btn-xs btn-delete-bab"
                                        data-url="{{ route('courses.modules.destroy', [$course->id, $module->id]) }}">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        @forelse($module->materials as $material)
                        {{-- Hide non-preview materials for non-enrolled students --}}
                        @if(!$isEnrolled && !$isPengajar && !$material->is_preview)
                            @continue
                        @endif
                        <div class="d-flex align-items-center px-3 py-2 border-bottom">
                            {{-- Type icon --}}
                            <div class="mr-3 text-center" style="min-width:32px">
                                @switch($material->type)
                                    @case('video')
                                        <i class="fas fa-play-circle fa-lg text-danger"></i>
                                        @break
                                    @case('pdf')
                                        <i class="fas fa-file-pdf fa-lg text-danger"></i>
                                        @break
                                    @case('text')
                                        <i class="fas fa-file-alt fa-lg text-info"></i>
                                        @break
                                    @case('link')
                                        <i class="fas fa-link fa-lg text-primary"></i>
                                        @break
                                    @case('audio')
                                        <i class="fas fa-headphones fa-lg text-warning"></i>
                                        @break
                                    @case('image')
                                        <i class="fas fa-image fa-lg text-success"></i>
                                        @break
                                    @default
                                        <i class="fas fa-file fa-lg text-secondary"></i>
                                @endswitch
                            </div>

                            {{-- Material info --}}
                            <div class="flex-grow-1">
                                <a href="{{ route('courses.materials.show', [$course->id, $material->id]) }}"
                                   class="font-weight-bold text-dark">
                                    {{ $material->title }}
                                </a>
                                <div class="d-flex align-items-center flex-wrap mt-1" style="gap:4px">
                                    <span class="badge badge-secondary">{{ strtoupper($material->type) }}</span>
                                    @if($material->is_preview)
                                    <span class="badge badge-info">
                                        <i class="fas fa-eye mr-1"></i>Preview
                                    </span>
                                    @endif
                                    @if($material->duration_minutes)
                                    <small class="text-muted">
                                        <i class="fas fa-clock mr-1"></i>{{ $material->duration_minutes }} menit
                                    </small>
                                    @endif
                                </div>
                            </div>

                            {{-- Progress indicator for enrolled students --}}
                            @if($isEnrolled && !$isPengajar)
                            <div class="mr-3">
                                @if(in_array($material->id, $completedMaterialIds))
                                <span class="badge badge-success">
                                    <i class="fas fa-check mr-1"></i>Selesai
                                </span>
                                @else
                                <span class="badge badge-light border">
                                    <i class="far fa-circle mr-1"></i>Belum
                                </span>
                                @endif
                            </div>
                            @endif

                            {{-- Action buttons for pengajar --}}
                            @if($isPengajar)
                            <div class="d-flex" style="gap:4px">
                                <a href="{{ route('courses.materials.edit', [$course->id, $material->id]) }}"
                                   class="btn btn-warning btn-xs">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button"
                                        class="btn btn-danger btn-xs btn-delete-material"
                                        data-url="{{ route('courses.materials.destroy', [$course->id, $material->id]) }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            @endif
                        </div>
                        @empty
                        <div class="px-3 py-2 text-muted">
                            <i class="fas fa-info-circle mr-1"></i>
                            Belum ada materi di bab ini.
                            @if($isPengajar)
                            <a href="{{ route('courses.materials.create', $course->id) }}">Tambah materi</a>
                            @endif
                        </div>
                        @endforelse
                    </div>
                </div>
                @empty
                <div class="p-4 text-center text-muted">
                    <i class="fas fa-layer-group fa-3x mb-3 d-block"></i>
                    Belum ada bab di kelas ini.
                    @if($isPengajar)
                    <br>
                    <button type="button" class="btn btn-primary btn-sm mt-2" data-toggle="modal" data-target="#modalAddBab">
                        <i class="fas fa-plus mr-1"></i> Tambah Bab Pertama
                    </button>
                    @endif
                </div>
                @endforelse

            </div>
        </div>

    </div>
</section>

{{-- Modal Tambah Bab --}}
@if($isPengajar)
<div class="modal fade" id="modalAddBab" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus mr-2"></i>Tambah Bab</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="formAddBab"
                      action="{{ route('courses.modules.store', $course->id) }}"
                      method="POST">
                    @csrf
                    @include('materials._module_form')
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit Bab --}}
<div class="modal fade" id="modalEditBab" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit mr-2"></i>Edit Bab</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="formEditBab"
                      action=""
                      method="POST">
                    @csrf
                    @method('PUT')
                    @include('materials._module_form')
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
$(document).ready(function () {

    // AJAX form untuk Tambah Bab
    ajaxForm('#formAddBab');

    // AJAX form untuk Edit Bab
    ajaxForm('#formEditBab');

    // AJAX delete bab
    ajaxDelete('.btn-delete-bab', 'Hapus Bab ini beserta semua materinya?');

    // AJAX delete materi
    ajaxDelete('.btn-delete-material', 'Hapus materi ini?');

    // Isi form Edit Bab saat modal dibuka
    $(document).on('click', '.btn-edit-bab', function () {
        const btn = $(this);
        $('#formEditBab').attr('action', btn.data('url'));
        $('#formEditBab [name="title"]').val(btn.data('title'));
        $('#formEditBab [name="description"]').val(btn.data('description'));
        $('#formEditBab [name="order"]').val(btn.data('order'));
        // Clear previous errors
        $('#formEditBab .is-invalid').removeClass('is-invalid');
        $('#formEditBab .invalid-feedback').remove();
    });

});
</script>
@endpush
