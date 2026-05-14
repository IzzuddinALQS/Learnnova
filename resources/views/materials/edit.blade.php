@extends('main')

@section('title', 'Edit Materi — ' . $material->title)

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit Materi</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Kelas</a></li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('courses.materials.index', $course->id) }}">Materi</a>
                    </li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Materi — {{ $material->title }}
                </h3>
            </div>
            <div class="card-body">
                <form id="formMaterial"
                      action="{{ route('courses.materials.update', [$course->id, $material->id]) }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-8">
                            {{-- Title --}}
                            <div class="form-group">
                                <label for="materialTitle">Judul Materi <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="title"
                                       id="materialTitle"
                                       class="form-control"
                                       value="{{ $material->title }}"
                                       maxlength="255"
                                       required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            {{-- Order --}}
                            <div class="form-group">
                                <label for="materialOrder">Urutan <span class="text-danger">*</span></label>
                                <input type="number"
                                       name="order"
                                       id="materialOrder"
                                       class="form-control"
                                       min="0"
                                       value="{{ $material->order }}"
                                       required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            {{-- Module --}}
                            <div class="form-group">
                                <label for="materialModule">Bab <span class="text-danger">*</span></label>
                                <select name="module_id" id="materialModule" class="form-control select2" required>
                                    <option value="">-- Pilih Bab --</option>
                                    @foreach($modules as $module)
                                    <option value="{{ $module->id }}"
                                        {{ $material->module_id == $module->id ? 'selected' : '' }}>
                                        {{ $module->title }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            {{-- Type --}}
                            <div class="form-group">
                                <label for="materialType">Tipe Materi <span class="text-danger">*</span></label>
                                <select name="type" id="materialType" class="form-control select2" required>
                                    <option value="">-- Pilih Tipe --</option>
                                    <option value="video"  {{ $material->type === 'video'  ? 'selected' : '' }}>Video</option>
                                    <option value="pdf"    {{ $material->type === 'pdf'    ? 'selected' : '' }}>PDF</option>
                                    <option value="text"   {{ $material->type === 'text'   ? 'selected' : '' }}>Teks</option>
                                    <option value="link"   {{ $material->type === 'link'   ? 'selected' : '' }}>Link</option>
                                    <option value="audio"  {{ $material->type === 'audio'  ? 'selected' : '' }}>Audio</option>
                                    <option value="image"  {{ $material->type === 'image'  ? 'selected' : '' }}>Gambar</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Duration (video & audio only) --}}
                    <div class="form-group" id="fieldDuration" style="display:none">
                        <label for="materialDuration">Durasi (menit)</label>
                        <input type="number"
                               name="duration_minutes"
                               id="materialDuration"
                               class="form-control"
                               min="0"
                               value="{{ $material->duration_minutes }}">
                    </div>

                    {{-- File upload --}}
                    <div class="form-group" id="fieldFile" style="display:none">
                        <label for="materialFile" id="labelFile">Upload File</label>

                        {{-- Show existing file --}}
                        @if($material->file_path)
                        <div class="alert alert-info py-2 mb-2">
                            <i class="fas fa-paperclip mr-1"></i>
                            File saat ini:
                            <strong>{{ basename($material->file_path) }}</strong>
                            <small class="d-block text-muted">Upload file baru untuk mengganti.</small>
                        </div>
                        @endif

                        <div class="custom-file">
                            <input type="file"
                                   name="file_path"
                                   id="materialFile"
                                   class="custom-file-input"
                                   accept="">
                            <label class="custom-file-label" for="materialFile">Pilih file baru (opsional)...</label>
                        </div>
                        <small class="form-text text-muted" id="fileHint"></small>
                    </div>

                    {{-- Content --}}
                    <div class="form-group" id="fieldContent" style="display:none">
                        <label for="materialContent" id="labelContent">Konten</label>
                        <textarea name="content"
                                  id="materialContent"
                                  class="form-control"
                                  rows="5"
                                  placeholder="">{{ $material->content }}</textarea>
                        <small class="form-text text-muted" id="contentHint"></small>
                    </div>

                    {{-- is_preview --}}
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox"
                                   name="is_preview"
                                   id="materialIsPreview"
                                   class="custom-control-input"
                                   value="1"
                                   {{ $material->is_preview ? 'checked' : '' }}>
                            <label class="custom-control-label" for="materialIsPreview">
                                Materi Preview (dapat diakses tanpa enrollment)
                            </label>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save mr-1"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('courses.materials.index', $course->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
$(document).ready(function () {

    // Initialize Select2
    $('#materialModule, #materialType').select2({
        theme: 'bootstrap4',
        width: '100%',
    });

    // Dynamic field visibility
    function toggleMaterialFields(type) {
        $('#fieldDuration, #fieldFile, #fieldContent').hide();

        switch (type) {
            case 'video':
                $('#fieldDuration').show();
                $('#fieldFile').show();
                $('#labelFile').html('File Video <small class="text-muted">(opsional jika ada URL YouTube)</small>');
                $('#fileHint').text('Format: mp4, webm. Maks 100MB.');
                $('#fieldContent').show();
                $('#labelContent').text('URL YouTube (opsional)');
                $('#materialContent').attr('placeholder', 'https://www.youtube.com/watch?v=... atau https://youtu.be/...');
                $('#materialContent').attr('rows', 2);
                $('#contentHint').text('Tempel link YouTube biasa — akan otomatis dikonversi ke embed.');
                break;

            case 'audio':
                $('#fieldDuration').show();
                $('#fieldFile').show();
                $('#labelFile').html('File Audio');
                $('#fileHint').text('Format: mp3, wav. Maks 100MB.');
                break;

            case 'pdf':
                $('#fieldFile').show();
                $('#labelFile').html('File PDF');
                $('#fileHint').text('Format: pdf. Maks 100MB.');
                break;

            case 'image':
                $('#fieldFile').show();
                $('#labelFile').html('File Gambar');
                $('#fileHint').text('Format: jpg, jpeg, png, webp. Maks 100MB.');
                break;

            case 'text':
                $('#fieldContent').show();
                $('#labelContent').text('Konten Teks');
                $('#materialContent').attr('placeholder', 'Tulis konten materi di sini...');
                $('#materialContent').attr('rows', 10);
                $('#contentHint').text('Mendukung HTML dasar.');
                break;

            case 'link':
                $('#fieldContent').show();
                $('#labelContent').text('URL Link');
                $('#materialContent').attr('placeholder', 'https://...');
                $('#materialContent').attr('rows', 2);
                $('#contentHint').text('Masukkan URL lengkap yang akan dibuka di tab baru.');
                break;
        }

        const acceptMap = {
            video: 'video/mp4,video/webm',
            audio: 'audio/mpeg,audio/wav',
            pdf:   'application/pdf',
            image: 'image/jpeg,image/png,image/webp',
        };
        $('#materialFile').attr('accept', acceptMap[type] || '');
    }

    // Trigger on load for edit mode
    const currentType = $('#materialType').val();
    if (currentType) {
        toggleMaterialFields(currentType);
    }

    // Bind type change
    $('#materialType').on('change', function () {
        toggleMaterialFields($(this).val());
    });

    // Custom file label update
    $(document).on('change', '#materialFile', function () {
        const fileName = this.files[0] ? this.files[0].name : 'Pilih file baru (opsional)...';
        $(this).next('.custom-file-label').text(fileName);
    });

    // AJAX form submit
    ajaxForm('#formMaterial');

});
</script>
@endpush
