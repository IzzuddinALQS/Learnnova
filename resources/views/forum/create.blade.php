@extends('main')

@section('title', 'Buat Thread Baru')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Buat Thread Baru</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Kelas</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('forum.index', $course->id) }}">Forum</a></li>
                    <li class="breadcrumb-item active">Buat Thread</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        <form id="threadForm"
              action="{{ route('forum.store', $course->id) }}"
              method="POST"
              enctype="multipart/form-data">
            @csrf

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-pen mr-2"></i>Thread Baru — {{ $course->title }}
                    </h3>
                </div>
                <div class="card-body">
                    {{-- Judul --}}
                    <div class="form-group">
                        <label for="title">Judul Thread <span class="text-danger">*</span></label>
                        <input type="text"
                               id="title"
                               name="title"
                               class="form-control"
                               placeholder="Tuliskan judul diskusi..."
                               value="{{ old('title') }}"
                               maxlength="255">
                    </div>

                    {{-- Isi --}}
                    <div class="form-group">
                        <label for="content">Isi Diskusi <span class="text-danger">*</span></label>
                        <textarea id="content"
                                  name="content"
                                  class="form-control"
                                  rows="8"
                                  placeholder="Jelaskan topik diskusi kamu...">{{ old('content') }}</textarea>
                    </div>

                    {{-- Upload Image --}}
                    <div class="form-group">
                        <label>
                            <i class="fas fa-image mr-1 text-primary"></i>
                            Lampiran Gambar
                            <small class="text-muted">(opsional, maks. 2MB — JPG/PNG/GIF/WebP)</small>
                        </label>
                        <div class="custom-file">
                            <input type="file"
                                   class="custom-file-input"
                                   id="imageInput"
                                   name="image"
                                   accept="image/jpeg,image/png,image/gif,image/webp">
                            <label class="custom-file-label" for="imageInput">Pilih gambar...</label>
                        </div>
                        {{-- Preview --}}
                        <div id="imagePreviewWrap" class="mt-2 d-none">
                            <img id="imagePreview"
                                 src=""
                                 alt="Preview"
                                 class="img-fluid rounded border"
                                 style="max-height:300px">
                            <button type="button" id="removeImage"
                                    class="btn btn-xs btn-outline-danger mt-1 d-block">
                                <i class="fas fa-times mr-1"></i>Hapus gambar
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Posting Thread
                    </button>
                    <a href="{{ route('forum.index', $course->id) }}" class="btn btn-secondary ml-2">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>

        </form>

    </div>
</section>
@endsection

@push('scripts')
<script>
    ajaxForm('#threadForm');

    // Custom file label + preview
    $('#imageInput').on('change', function () {
        const file  = this.files[0];
        const label = $(this).next('.custom-file-label');

        if (file) {
            label.text(file.name);
            const reader = new FileReader();
            reader.onload = e => {
                $('#imagePreview').attr('src', e.target.result);
                $('#imagePreviewWrap').removeClass('d-none');
            };
            reader.readAsDataURL(file);
        }
    });

    $('#removeImage').on('click', function () {
        $('#imageInput').val('');
        $('#imageInput').next('.custom-file-label').text('Pilih gambar...');
        $('#imagePreviewWrap').addClass('d-none');
        $('#imagePreview').attr('src', '');
    });
</script>
@endpush
