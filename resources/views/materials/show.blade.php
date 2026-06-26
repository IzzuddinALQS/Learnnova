@extends('main')

@section('title', $material->title)

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-8">
                <h1 class="m-0" id="pageTitle" style="font-size:1.3rem">{{ $material->title }}</h1>
            </div>
            <div class="col-sm-4">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Kelas</a></li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('courses.materials.index', $course->id) }}">Materi</a>
                    </li>
                    <li class="breadcrumb-item active" id="breadcrumbMaterial">{{ Str::limit($material->title, 30) }}</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-between">

    <a href="{{ route('courses.materials.index', $course->id) }}"
       class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Daftar Materi
    </a>

    <div>

        <button type="button"
                class="btn btnBookmark {{ $isBookmarked ? 'btn-warning' : 'btn-outline-warning' }}"
                data-url="{{ route('courses.materials.bookmark', [$course->id, $material->id]) }}">

            <i class="fas fa-bookmark"></i>

            <span class="bookmarkText">
                {{ $isBookmarked ? 'Bookmarked' : 'Bookmark' }}
            </span>

        </button>

        <button type="button"
                id="btnMarkComplete"
                data-url="{{ route('courses.materials.progress', [$course->id, $material->id]) }}"
                data-material-id="{{ $material->id }}"
                data-completed="{{ ($progress?->is_completed ?? false) ? '1' : '0' }}"
                class="btn {{ ($progress?->is_completed ?? false) ? 'btn-success' : 'btn-outline-success' }}">

            <i class="fas {{ ($progress?->is_completed ?? false) ? 'fa-check-circle' : 'fa-circle' }} mr-1"></i>

            <span id="completeText">
                {{ ($progress?->is_completed ?? false) ? 'Selesai' : 'Tandai Selesai' }}
            </span>

        </button>

        <a href="{{ route('bookmarks.index') }}" class="btn btn-outline-primary ml-2">
            <i class="fas fa-bookmark"></i> Daftar Bookmark
        </a>

    </div>

</div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">

            {{-- Main content --}}
            <div class="col-lg-9">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                            <div>
                                <h5 class="card-title mb-0" id="materialTitle">{{ $material->title }}</h5>
                                <div class="mt-1" style="gap:4px; display:flex; flex-wrap:wrap">
                                    <span class="badge badge-secondary" id="materialTypeBadge">
                                        @switch($material->type)
                                            @case('video') <i class="fas fa-play-circle mr-1"></i>Video @break
                                            @case('pdf')   <i class="fas fa-file-pdf mr-1"></i>PDF @break
                                            @case('text')  <i class="fas fa-file-alt mr-1"></i>Teks @break
                                            @case('link')  <i class="fas fa-link mr-1"></i>Link @break
                                            @case('audio') <i class="fas fa-headphones mr-1"></i>Audio @break
                                            @case('image') <i class="fas fa-image mr-1"></i>Gambar @break
                                        @endswitch
                                    </span>
                                    <span class="badge badge-info" id="materialPreviewBadge"
                                        style="{{ $material->is_preview ? '' : 'display:none' }}">
                                        <i class="fas fa-eye mr-1"></i>Preview
                                    </span>
                                    <span class="badge badge-light border" id="materialDurationBadge"
                                        style="{{ $material->duration_minutes ? '' : 'display:none' }}">
                                        <i class="fas fa-clock mr-1"></i>
                                        <span>{{ $material->duration_minutes }} menit</span>
                                    </span>
                                </div>
                            </div>
    
                            {{-- Pengajar actions --}}
                            @if($isPengajar)
                            <div class="d-flex" style="gap:6px">
                                <a id="btnEditMaterial"
                                   href="{{ route('courses.materials.edit', [$course->id, $material->id]) }}"
                                   class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                                <button type="button" id="btnDeleteMaterial"
                                        class="btn btn-danger btn-sm btn-delete-material"
                                        data-url="{{ route('courses.materials.destroy', [$course->id, $material->id]) }}">
                                    <i class="fas fa-trash mr-1"></i>Hapus
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Loading overlay --}}
                    <div id="contentLoader" style="display:none; position:relative;">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <p class="text-muted mt-3 mb-0">Memuat materi...</p>
                        </div>
                    </div>

                    <div class="card-body" id="materialContent">
                        {{-- ── Content Viewer ─────────────────────────────────── --}}

                        @if($material->type === 'video')
                            @if($material->file_path)
                            {{-- HTML5 Video Player --}}
                            <div class="embed-responsive embed-responsive-16by9 mb-3">
                                <video id="mediaPlayer"
                                       class="embed-responsive-item"
                                       controls
                                       controlsList="nodownload"
                                       data-material-id="{{ $material->id }}"
                                       data-last-position="{{ $progress->last_position ?? 0 }}">
                                    <source src="{{ asset('storage/' . $material->file_path) }}"
                                            type="video/mp4">
                                    Browser Anda tidak mendukung tag video.
                                </video>
                            </div>
                            @elseif($material->content)
                            {{-- YouTube Embed — auto-convert semua format URL ke embed --}}
                            @php
                                $ytUrl = $material->content;
                                // Tangkap video ID dari berbagai format YouTube:
                                // - https://www.youtube.com/watch?v=ID
                                // - https://youtu.be/ID
                                // - https://www.youtube.com/embed/ID  (sudah benar)
                                // - https://www.youtube.com/shorts/ID
                                $videoId = null;
                                if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube\.com\/shorts\/)([a-zA-Z0-9_-]{11})/', $ytUrl, $m)) {
                                    $videoId = $m[1];
                                }
                                $embedUrl = $videoId
                                    ? 'https://www.youtube.com/embed/' . $videoId . '?rel=0'
                                    : $ytUrl; // fallback: pakai apa adanya
                            @endphp
                            <div class="embed-responsive embed-responsive-16by9 mb-3">
                                <iframe class="embed-responsive-item"
                                        src="{{ $embedUrl }}"
                                        allowfullscreen
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        referrerpolicy="strict-origin-when-cross-origin">
                                </iframe>
                            </div>
                            @if(!$videoId)
                                <div class="alert alert-warning py-2">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    URL video tidak dikenali. Pastikan menggunakan link YouTube yang valid.
                                </div>
                            @endif
                            @endif

                        @elseif($material->type === 'pdf')
                            @php
                                $pdfUrl        = asset('storage/' . $material->file_path);
                                $pdfAbsUrl     = url('storage/' . $material->file_path);
                                $googleViewUrl = 'https://docs.google.com/viewer?url=' . urlencode($pdfAbsUrl) . '&embedded=true';
                            @endphp
                            <div class="mb-3">
                                {{-- Tab switcher: Native vs Google Docs Viewer --}}
                                <div class="d-flex align-items-center mb-2" style="gap:6px;">
                                    <button class="btn btn-sm btn-primary active" id="btnViewNative"
                                        onclick="switchPdfViewer('native')">
                                        <i class="fas fa-file-pdf mr-1"></i>Tampilan Langsung
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" id="btnViewGoogle"
                                        onclick="switchPdfViewer('google')">
                                        <i class="fab fa-google mr-1"></i>Google Viewer
                                    </button>
                                    <small class="text-muted ml-2">
                                        Jika PDF tidak tampil, coba Google Viewer
                                    </small>
                                </div>

                                {{-- Native iframe --}}
                                <div id="pdfViewerNative">
                                    <iframe src="{{ $pdfUrl }}"
                                            width="100%" height="650px"
                                            style="border:1px solid #dee2e6; border-radius:4px;">
                                    </iframe>
                                </div>

                                {{-- Google Docs Viewer (lazy — hanya load saat diklik) --}}
                                <div id="pdfViewerGoogle" style="display:none;">
                                    <iframe src=""
                                            data-src="{{ $googleViewUrl }}"
                                            width="100%" height="650px"
                                            style="border:1px solid #dee2e6; border-radius:4px;"
                                            id="googleViewerFrame">
                                    </iframe>
                                </div>

                                <div class="mt-2 d-flex" style="gap:8px;">
                                    <a href="{{ $pdfUrl }}" target="_blank"
                                       class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-external-link-alt mr-1"></i>Buka di Tab Baru
                                    </a>
                                    <a href="{{ $pdfUrl }}" download
                                       class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-download mr-1"></i>Download
                                    </a>
                                </div>
                            </div>

                        @elseif($material->type === 'text')
                            <div class="material-text-content p-3 border rounded bg-white">
                                {!! $material->content !!}
                            </div>

                        @elseif($material->type === 'link')
                            <div class="text-center py-4">
                                <i class="fas fa-link fa-3x text-primary mb-3 d-block"></i>
                                <p class="text-muted mb-3">Klik tombol di bawah untuk membuka link materi.</p>
                                <a href="{{ $material->content }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="btn btn-primary btn-lg">
                                    <i class="fas fa-external-link-alt mr-2"></i>Buka Link
                                </a>
                                <div class="mt-2">
                                    <small class="text-muted">{{ $material->content }}</small>
                                </div>
                            </div>

                        @elseif($material->type === 'audio')
                            <div class="text-center py-3">
                                <i class="fas fa-headphones fa-3x text-warning mb-3 d-block"></i>
                                <audio id="mediaPlayer"
                                       controls
                                       class="w-100"
                                       data-material-id="{{ $material->id }}"
                                       data-last-position="{{ $progress->last_position ?? 0 }}">
                                    <source src="{{ asset('storage/' . $material->file_path) }}"
                                            type="audio/mpeg">
                                    Browser Anda tidak mendukung tag audio.
                                </audio>
                            </div>

                        @elseif($material->type === 'image')
                            <div class="text-center">
                                <img src="{{ asset('storage/' . $material->file_path) }}"
                                     alt="{{ $material->title }}"
                                     class="img-fluid rounded border"
                                     style="max-height:600px">
                            </div>
                        @endif

                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            {{-- Prev --}}
                            @if($prevMaterial)
                                <a id="btnPrev"
                                   href="{{ route('courses.materials.show', [$course->id, $prevMaterial->id]) }}"
                                   data-url="{{ route('courses.materials.show', [$course->id, $prevMaterial->id]) }}"
                                   class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-chevron-left mr-1"></i> Sebelumnya
                                </a>
                            @else
                                <a id="btnPrev"
                                   href="{{ route('courses.materials.index', $course->id) }}"
                                   class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left mr-1"></i> Daftar Materi
                                </a>
                            @endif

                            {{-- Next --}}
                            @if($nextMaterial)
                                <a id="btnNext"
                                   href="{{ route('courses.materials.show', [$course->id, $nextMaterial->id]) }}"
                                   data-url="{{ route('courses.materials.show', [$course->id, $nextMaterial->id]) }}"
                                   class="btn btn-primary btn-sm">
                                    Selanjutnya <i class="fas fa-chevron-right ml-1"></i>
                                </a>
                            @else
                                <a id="btnNext"
                                   href="{{ route('courses.materials.index', $course->id) }}"
                                   class="btn btn-success btn-sm">
                                    <i class="fas fa-flag-checkered mr-1"></i> Selesai
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar: navigasi materi --}}
            <div class="col-lg-3">

                {{--
@if($isEnrolled && !$isPengajar)
<div class="card card-outline card-warning mb-3">
    <div class="card-body py-2 text-center">
        <button type="button" class="btn btn-sm btn-block btnBookmark"
                data-url="{{ route('courses.materials.bookmark', [$course->id, $material->id]) }}"
                data-material-id="{{ $material->id }}"
                data-bookmarked="{{ $isBookmarked ? '1' : '0' }}">
            @if($isBookmarked)
                <i class="fas fa-bookmark mr-1"></i>Hapus Bookmark
            @else
                <i class="far fa-bookmark mr-1"></i>Bookmark
            @endif
        </button>
    </div>
</div>
@endif
--}}

                {{-- Daftar Materi per Bab --}}
                <div class="card">
                    <div class="card-header py-2">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-list mr-2"></i>Daftar Materi
                        </h6>
                    </div>
                    <div class="card-body p-0" style="max-height: 520px; overflow-y: auto;">
                        @foreach($modules as $module)
                            <div class="px-3 pt-2 pb-1 bg-light border-bottom">
                                <small class="text-uppercase font-weight-bold text-muted"
                                    style="font-size: 0.7rem; letter-spacing: 0.05em;">
                                    <i class="fas fa-folder mr-1"></i>{{ $module->title }}
                                </small>
                            </div>
                            @foreach($module->materials as $mat)
                                @php
                                    $isActive     = $mat->id === $material->id;
                                    $isDone       = in_array($mat->id, $completedIds);
                                    $isAccessible = $isPengajar || $isEnrolled || $mat->is_preview;
                                    $typeIcon     = ['video'=>'fa-play-circle','pdf'=>'fa-file-pdf','text'=>'fa-file-alt','link'=>'fa-link','audio'=>'fa-headphones','image'=>'fa-image'][$mat->type] ?? 'fa-file';
                                @endphp
                                @if($isAccessible)
                                <a href="{{ route('courses.materials.show', [$course->id, $mat->id]) }}"
                                    data-url="{{ route('courses.materials.show', [$course->id, $mat->id]) }}"
                                    data-id="{{ $mat->id }}"
                                    class="sidebar-mat-item d-flex align-items-center px-3 py-2 border-bottom text-decoration-none
                                        {{ $isActive ? 'bg-primary text-white' : 'text-dark' }}"
                                    style="transition: background 0.15s;">
                                    <div class="mr-2" style="min-width:18px; text-align:center;">
                                        @if($isDone)
                                            <i class="fas fa-check-circle text-{{ $isActive ? 'white' : 'success' }}" style="font-size:0.85rem;"></i>
                                        @elseif($isActive)
                                            <i class="fas fa-play-circle text-white" style="font-size:0.85rem;"></i>
                                        @else
                                            <i class="far fa-circle text-muted" style="font-size:0.85rem;"></i>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1" style="font-size:0.85rem; line-height:1.3;">
                                        <span class="{{ $isActive ? 'font-weight-bold' : '' }}">{{ $mat->title }}</span>
                                        @if($mat->duration_minutes)
                                            <br><small class="{{ $isActive ? 'text-white-50' : 'text-muted' }}">
                                                <i class="fas fa-clock mr-1"></i>{{ $mat->duration_minutes }} mnt
                                            </small>
                                        @endif
                                    </div>
                                    <small class="{{ $isActive ? 'text-white-50' : 'text-muted' }} ml-1">
                                        <i class="fas {{ $typeIcon }}"></i>
                                    </small>
                                </a>
                                @else
                                <div class="d-flex align-items-center px-3 py-2 border-bottom text-muted"
                                    style="cursor:not-allowed; opacity:0.55;">
                                    <div class="mr-2" style="min-width:18px; text-align:center;">
                                        <i class="fas fa-lock" style="font-size:0.8rem;"></i>
                                    </div>
                                    <small>{{ $mat->title }}</small>
                                </div>
                                @endif
                            @endforeach
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
$(document).ready(function () {

    const CSRF    = $('meta[name="csrf-token"]').attr('content');
    const courseId = {{ $course->id }};

    // ── AJAX Material Navigation ──────────────────────────────────────────

    /**
     * Render konten materi berdasarkan type
     */
    function renderContent(mat, progress) {
        const lastPos = progress.last_position || 0;
        let html = '';

        if (mat.type === 'video') {
            if (mat.file_path) {
                html = `
                <div class="embed-responsive embed-responsive-16by9 mb-3">
                    <video id="mediaPlayer" class="embed-responsive-item" controls controlsList="nodownload"
                           data-material-id="${mat.id}" data-last-position="${lastPos}">
                        <source src="${mat.file_path}" type="video/mp4">
                    </video>
                </div>`;
            } else if (mat.content) {
                // Auto-convert YouTube URL ke embed
                let embedUrl = mat.content;
                const ytMatch = mat.content.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube\.com\/shorts\/)([a-zA-Z0-9_-]{11})/);
                if (ytMatch) embedUrl = 'https://www.youtube.com/embed/' + ytMatch[1] + '?rel=0';
                html = `
                <div class="embed-responsive embed-responsive-16by9 mb-3">
                    <iframe class="embed-responsive-item" src="${embedUrl}" allowfullscreen
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            referrerpolicy="strict-origin-when-cross-origin"></iframe>
                </div>`;
            }
        } else if (mat.type === 'pdf') {
            const googleUrl = 'https://docs.google.com/viewer?url=' + encodeURIComponent(mat.file_path) + '&embedded=true';
            html = `
            <div class="mb-3">
                <div class="d-flex align-items-center mb-2" style="gap:6px;">
                    <button class="btn btn-sm btn-primary active" id="btnViewNative" onclick="switchPdfViewer('native')">
                        <i class="fas fa-file-pdf mr-1"></i>Tampilan Langsung
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" id="btnViewGoogle" onclick="switchPdfViewer('google')">
                        <i class="fab fa-google mr-1"></i>Google Viewer
                    </button>
                    <small class="text-muted ml-2">Jika PDF tidak tampil, coba Google Viewer</small>
                </div>
                <div id="pdfViewerNative">
                    <iframe src="${mat.file_path}" width="100%" height="650px"
                            style="border:1px solid #dee2e6; border-radius:4px;"></iframe>
                </div>
                <div id="pdfViewerGoogle" style="display:none;">
                    <iframe src="" data-src="${googleUrl}" id="googleViewerFrame"
                            width="100%" height="650px"
                            style="border:1px solid #dee2e6; border-radius:4px;"></iframe>
                </div>
                <div class="mt-2 d-flex" style="gap:8px;">
                    <a href="${mat.file_path}" target="_blank" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-external-link-alt mr-1"></i>Buka di Tab Baru
                    </a>
                    <a href="${mat.file_path}" download class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-download mr-1"></i>Download
                    </a>
                </div>
            </div>`;
        } else if (mat.type === 'text') {
            html = `<div class="material-text-content p-3 border rounded bg-white">${mat.content || ''}</div>`;
        } else if (mat.type === 'link') {
            html = `
            <div class="text-center py-4">
                <i class="fas fa-link fa-3x text-primary mb-3 d-block"></i>
                <p class="text-muted mb-3">Klik tombol di bawah untuk membuka link materi.</p>
                <a href="${mat.content}" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-lg">
                    <i class="fas fa-external-link-alt mr-2"></i>Buka Link
                </a>
                <div class="mt-2"><small class="text-muted">${mat.content}</small></div>
            </div>`;
        } else if (mat.type === 'audio') {
            html = `
            <div class="text-center py-3">
                <i class="fas fa-headphones fa-3x text-warning mb-3 d-block"></i>
                <audio id="mediaPlayer" controls class="w-100"
                       data-material-id="${mat.id}" data-last-position="${lastPos}">
                    <source src="${mat.file_path}" type="audio/mpeg">
                </audio>
            </div>`;
        } else if (mat.type === 'image') {
            html = `
            <div class="text-center">
                <img src="${mat.file_path}" alt="${mat.title}" class="img-fluid rounded border" style="max-height:600px">
            </div>`;
        }
        return html;
    }

    /**
     * Badge icon per type
     */
    function typeLabel(type) {
        const map = {
            video: '<i class="fas fa-play-circle mr-1"></i>Video',
            pdf:   '<i class="fas fa-file-pdf mr-1"></i>PDF',
            text:  '<i class="fas fa-file-alt mr-1"></i>Teks',
            link:  '<i class="fas fa-link mr-1"></i>Link',
            audio: '<i class="fas fa-headphones mr-1"></i>Audio',
            image: '<i class="fas fa-image mr-1"></i>Gambar',
        };
        return map[type] || type;
    }

    /**
     * Load materi via AJAX dan swap konten tanpa full reload
     */
    function loadMaterial(url) {
        // Animasi fade-out
        $('#materialContent').css({ opacity: 0, transition: 'opacity 0.2s' });
        $('#contentLoader').show();

        $.ajax({
            url: url,
            type: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (res) {
                const mat  = res.material;
                const prog = res.progress;

                // Update judul halaman & breadcrumb
                document.title = mat.title + ' — E-Learning';
                $('#pageTitle').text(mat.title);
                $('#breadcrumbMaterial').text(mat.title.length > 30 ? mat.title.substring(0, 30) + '...' : mat.title);

                // Update card header
                $('#materialTitle').text(mat.title);
                $('#materialTypeBadge').html(typeLabel(mat.type));
                $('#materialPreviewBadge').toggle(mat.is_preview);
                $('#materialDurationBadge').toggle(!!mat.duration_minutes)
                    .find('span').text(mat.duration_minutes + ' menit');

                // Update tombol edit/hapus
                if (mat.edit_url) {
                    $('#btnEditMaterial').attr('href', mat.edit_url).show();
                    $('#btnDeleteMaterial').attr('data-url', mat.delete_url).show();
                } else {
                    $('#btnEditMaterial, #btnDeleteMaterial').hide();
                }

                // Swap konten
                $('#materialContent').html(renderContent(mat, prog));

                // Update tombol prev/next
                if (res.prev_url) {
                    $('#btnPrev').attr('href', res.prev_url).attr('data-url', res.prev_url)
                        .html('<i class="fas fa-chevron-left mr-1"></i> Sebelumnya').show();
                } else {
                    $('#btnPrev').attr('href', '{{ route('courses.materials.index', $course->id) }}')
                        .removeAttr('data-url')
                        .html('<i class="fas fa-arrow-left mr-1"></i> Daftar Materi');
                }
                if (res.next_url) {
                    $('#btnNext').attr('href', res.next_url).attr('data-url', res.next_url)
                        .html('Selanjutnya <i class="fas fa-chevron-right ml-1"></i>')
                        .removeClass('btn-success').addClass('btn-primary').show();
                } else {
                    $('#btnNext').attr('href', '{{ route('courses.materials.index', $course->id) }}')
                        .removeAttr('data-url')
                        .html('<i class="fas fa-flag-checkered mr-1"></i> Selesai')
                        .removeClass('btn-primary').addClass('btn-success');
                }

                // Update tombol mark complete
                const $btnMC = $('#btnMarkComplete');
                if ($btnMC.length) {
                    $btnMC.attr('data-material-id', mat.id).attr('data-completed', prog.is_completed ? '1' : '0');
                    if (prog.is_completed) {
                        $btnMC.removeClass('btn-outline-success').addClass('btn-success')
                              .html('<i class="fas fa-check-circle mr-1"></i> Selesai');
                    } else {
                        $btnMC.removeClass('btn-success').addClass('btn-outline-success')
                              .html('<i class="far fa-circle mr-1"></i> Tandai Selesai');
                    }
                }

                // Update bookmark
                const $btnBM = $('.btnBookmark');
                if ($btnBM.length) {
                    $btnBM.attr('data-url', res.bookmark_url)
                          .attr('data-material-id', mat.id)
                          .attr('data-bookmarked', res.is_bookmarked ? '1' : '0');
                    if (res.is_bookmarked) {
                        $btnBM.removeClass('btn-outline-warning').addClass('btn-warning')
                              .html('<i class="fas fa-bookmark mr-1"></i>Hapus Bookmark');
                    } else {
                        $btnBM.removeClass('btn-warning').addClass('btn-outline-warning')
                              .html('<i class="far fa-bookmark mr-1"></i>Tambah Bookmark');
                    }
                }

                // Update highlight sidebar — pakai .attr() bukan .data()
                $('.sidebar-mat-item').removeClass('bg-primary text-white').addClass('text-dark');
                $(`.sidebar-mat-item[data-id="${mat.id}"]`)
                    .removeClass('text-dark').addClass('bg-primary text-white');

                // Update URL tanpa reload
                history.pushState({ url: mat.url }, mat.title, mat.url);

                // Init media player
                initMediaPlayer(mat.id);

                // Fade-in
                $('#contentLoader').hide();
                $('#materialContent').css({ opacity: 1 });

                // Scroll ke atas konten
                $('html, body').animate({ scrollTop: $('#materialContent').offset().top - 80 }, 300);
            },
            error: function () {
                $('#contentLoader').hide();
                $('#materialContent').css({ opacity: 1 });
                Swal.fire('Error', 'Gagal memuat materi.', 'error');
            }
        });
    }

    /**
     * Init media player setelah swap
     */
    function initMediaPlayer(materialId) {
        const $player = $('#mediaPlayer');
        if (!$player.length) return;

        const lastPos = parseFloat($player.data('last-position')) || 0;
        if (lastPos > 0) {
            $player[0].addEventListener('loadedmetadata', function () {
                this.currentTime = lastPos;
            }, { once: true });
        }

        let saveTimer = null;
        $player[0].addEventListener('timeupdate', function () {
            const pos = Math.floor(this.currentTime);
            clearTimeout(saveTimer);
            saveTimer = setTimeout(() => saveLastPosition(materialId, pos), 3000);
        });
    }

    // ── Intercept navigasi materi (sidebar + prev/next) ───────────────────
    $(document).on('click', 'a[data-url]', function (e) {
        e.preventDefault();
        // Pakai .attr() bukan .data() agar selalu baca dari DOM (bukan jQuery cache)
        loadMaterial($(this).attr('data-url'));
    });

    // Handle browser back/forward
    window.addEventListener('popstate', function (e) {
        if (e.state && e.state.url) loadMaterial(e.state.url);
    });

    // ── Init media player saat pertama load ───────────────────────────────
    initMediaPlayer({{ $material->id }});

    // ── PDF viewer switcher ───────────────────────────────────────────────
    window.switchPdfViewer = function (mode) {
        if (mode === 'google') {
            $('#pdfViewerNative').hide();
            $('#pdfViewerGoogle').show();
            $('#btnViewNative').removeClass('btn-primary active').addClass('btn-outline-secondary');
            $('#btnViewGoogle').removeClass('btn-outline-secondary').addClass('btn-primary active');
            // Lazy load Google Viewer iframe
            const $frame = $('#googleViewerFrame');
            if (!$frame.attr('src')) {
                $frame.attr('src', $frame.data('src'));
            }
        } else {
            $('#pdfViewerGoogle').hide();
            $('#pdfViewerNative').show();
            $('#btnViewGoogle').removeClass('btn-primary active').addClass('btn-outline-secondary');
            $('#btnViewNative').removeClass('btn-outline-secondary').addClass('btn-primary active');
        }
    };

    // ── Save last position ────────────────────────────────────────────────
    function saveLastPosition(materialId, position) {
        $.ajax({
            url: '/courses/' + courseId + '/materials/' + materialId + '/progress',
            type: 'POST',
            data: { _token: CSRF, last_position: position, is_completed: 0 },
        });
    }

    // ── Mark complete ─────────────────────────────────────────────────────
    $(document).on('click', '#btnMarkComplete', function () {
        const btn         = $(this);
        const materialId  = btn.data('material-id');
        const isCompleted = btn.data('completed') == '1';

        btn.prop('disabled', true);
        $.ajax({
            url: '/courses/' + courseId + '/materials/' + materialId + '/progress',
            type: 'POST',
            data: { _token: CSRF, is_completed: isCompleted ? 0 : 1 },
            success: function (res) {
                btn.prop('disabled', false);
                
                let icon = $(`.sidebar-mat-item[data-id="${materialId}"]`).find('div.mr-2 i');
                let isActive = $(`.sidebar-mat-item[data-id="${materialId}"]`).hasClass('bg-primary');

                if (res.is_completed) {
                    btn.data('completed', '1').removeClass('btn-outline-success').addClass('btn-success')
                       .html('<i class="fas fa-check-circle mr-1"></i> Selesai');
                       
                    icon.removeClass('far fa-circle fas fa-play-circle text-muted text-white text-success')
                        .addClass('fas fa-check-circle ' + (isActive ? 'text-white' : 'text-success'));
                } else {
                    btn.data('completed', '0').removeClass('btn-success').addClass('btn-outline-success')
                       .html('<i class="far fa-circle mr-1"></i> Tandai Selesai');
                       
                    icon.removeClass('fas fa-check-circle text-success text-white')
                        .addClass(isActive ? 'fas fa-play-circle text-white' : 'far fa-circle text-muted');
                }
                Swal.fire({ icon:'success', title:'Berhasil!', text: res.message,
                    toast:true, position:'top-end', timer:2000, showConfirmButton:false });
            },
            error: function () {
                btn.prop('disabled', false);
                Swal.fire('Error', 'Gagal memperbarui progress.', 'error');
            }
        });
    });

$(document).on('click', '.btnBookmark', function(e) {
    e.preventDefault();
    
    const btn = $(this).closest('.btnBookmark'); 
    const url = btn.data('url');
    
    // Mengambil CSRF token dari meta tag bawaan Laravel di head HTML
    const CSRF = $('meta[name="csrf-token"]').attr('content'); 

    // Cek di console log apakah URL dan CSRF terbaca dengan benar
    console.log('URL:', url);
    console.log('CSRF Token:', CSRF);

    if (!url) {
        console.error('Atribut data-url tidak terbaca!');
        return;
    }

    $.ajax({
        url: url,
        type: 'POST',
        method: 'POST', 
        headers: {
            'X-CSRF-TOKEN': CSRF
        },
        success: function (res) {
            console.log('Respon server:', res);

            const statusBookmark = res.is_bookmarked !== undefined ? res.is_bookmarked : res.isBookmarked;

            if (statusBookmark) {
                btn.removeClass('btn-outline-warning')
                   .addClass('btn-warning');
                
                // Mengubah teks spesifik di dalam tombol yang sedang diklik
                btn.find('.bookmarkText').text('Bookmarked');
            } else {
                btn.removeClass('btn-warning')
                   .addClass('btn-outline-warning');
                
                // Mengubah teks spesifik di dalam tombol yang sedang diklik
                btn.find('.bookmarkText').text('Bookmark');
            }
        },
        error: function(xhr){
            console.error('Terjadi error pada AJAX:', xhr.status, xhr.responseText);
        }
    });
});




    // ── Delete material ───────────────────────────────────────────────────
    ajaxDelete('.btn-delete-material', 'Hapus materi ini?');

});
</script>
@endpush
