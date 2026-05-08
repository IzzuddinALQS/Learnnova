@extends('main')

@section('title', $thread->title)

@push('styles')
<style>
    /* ── Forum layout ─────────────────────────────────────────── */
    .forum-thread-body   { font-size: 1rem; line-height: 1.7; }
    .forum-post-wrap     { transition: background .2s; }
    .forum-post-wrap:hover { background: rgba(0,0,0,.015); }
    .forum-post-new      { animation: fadeInDown .35s ease; }
    @keyframes fadeInDown {
        from { opacity:0; transform:translateY(-8px); }
        to   { opacity:1; transform:translateY(0); }
    }
    .forum-avatar        { user-select: none; }
    .post-content        { word-break: break-word; }
    .forum-img           { transition: opacity .2s; }
    .forum-img:hover     { opacity: .85; }
    /* Garis nested */
    .forum-post-wrap[data-depth="1"] { border-left-color: #3490dc !important; }
    .forum-post-wrap[data-depth="2"] { border-left-color: #38a169 !important; }
    .forum-post-wrap[data-depth="3"] { border-left-color: #805ad5 !important; }
    .forum-post-wrap[data-depth="4"] { border-left-color: #dd6b20 !important; }
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-8">
                <h1 class="m-0" style="font-size:1.4rem">{{ $thread->title }}</h1>
            </div>
            <div class="col-sm-4">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Kelas</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('forum.index', $course->id) }}">Forum</a></li>
                    <li class="breadcrumb-item active">Thread</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        {{-- ── Thread utama ─────────────────────────────────────────────── --}}
        <div class="card card-outline card-primary mb-3">
            <div class="card-header py-2">
                <div class="d-flex align-items-start flex-wrap" style="gap:8px">

                    {{-- Judul & meta --}}
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center flex-wrap mb-1" style="gap:6px">
                            @if($thread->is_pinned)
                                <span class="badge badge-warning"><i class="fas fa-thumbtack mr-1"></i>Disematkan</span>
                            @endif
                            @if($thread->is_locked)
                                <span class="badge badge-secondary"><i class="fas fa-lock mr-1"></i>Terkunci</span>
                            @endif
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-user mr-1"></i><strong>{{ $thread->user->name ?? '-' }}</strong>
                            &nbsp;·&nbsp;
                            <i class="fas fa-clock mr-1"></i>{{ $thread->created_at->diffForHumans() }}
                            &nbsp;·&nbsp;
                            <i class="fas fa-eye mr-1"></i>{{ $thread->views }} views
                        </small>
                    </div>

                    {{-- Tombol moderasi --}}
                    <div class="d-flex align-items-center flex-wrap" style="gap:4px">
                        @if(auth()->user()->hasPermission('forum.moderate'))
                        <button type="button"
                                class="btn btn-sm {{ $thread->is_pinned ? 'btn-warning' : 'btn-outline-warning' }} btn-toggle-pin"
                                data-url="{{ route('forum.togglePin', [$course->id, $thread->id]) }}">
                            <i class="fas fa-thumbtack mr-1"></i>{{ $thread->is_pinned ? 'Lepas Pin' : 'Sematkan' }}
                        </button>
                        <button type="button"
                                class="btn btn-sm {{ $thread->is_locked ? 'btn-secondary' : 'btn-outline-secondary' }} btn-toggle-lock"
                                data-url="{{ route('forum.toggleLock', [$course->id, $thread->id]) }}">
                            <i class="fas {{ $thread->is_locked ? 'fa-unlock' : 'fa-lock' }} mr-1"></i>{{ $thread->is_locked ? 'Buka Kunci' : 'Kunci' }}
                        </button>
                        @endif
                        @if(auth()->id() === $thread->user_id || auth()->user()->hasPermission('forum.moderate'))
                        <button type="button"
                                class="btn btn-sm btn-outline-danger btn-delete-thread"
                                data-url="{{ route('forum.destroy', [$course->id, $thread->id]) }}">
                            <i class="fas fa-trash mr-1"></i>Hapus
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card-body forum-thread-body">
                <p class="mb-0" style="white-space:pre-wrap;">{{ $thread->content }}</p>
                @if($thread->image)
                <div class="mt-3">
                    <img src="{{ asset('storage/' . $thread->image) }}"
                         alt="Lampiran"
                         class="img-fluid rounded border forum-img"
                         style="max-height:400px;cursor:zoom-in;display:block">
                </div>
                @endif
            </div>
        </div>

        {{-- ── Daftar posts ─────────────────────────────────────────────── --}}
        <div class="card mb-3">
            <div class="card-header py-2">
                <h6 class="mb-0" id="replyCount">
                    <i class="fas fa-comments mr-2 text-primary"></i>
                    <span id="replyCountNum">{{ $thread->posts->count() }}</span> Balasan
                </h6>
            </div>

            <div class="card-body p-0" id="postsContainer">
                @forelse($thread->posts as $post)
                    @include('forum._post', ['post' => $post, 'thread' => $thread, 'course' => $course, 'depth' => 0])
                @empty
                <div id="emptyReplies" class="text-center text-muted py-4">
                    <i class="fas fa-comments fa-2x mb-2 d-block text-muted"></i>
                    Belum ada balasan. Jadilah yang pertama!
                </div>
                @endforelse
            </div>
        </div>

        {{-- ── Form reply ───────────────────────────────────────────────── --}}
        @if($thread->is_locked)
            <div class="alert alert-secondary">
                <i class="fas fa-lock mr-2"></i>Thread ini sudah dikunci. Tidak bisa menambahkan balasan.
            </div>
        @elseif(auth()->user()->hasPermission('forum.post'))
            <div class="card card-outline card-primary" id="replyCard">
                <div class="card-header py-2">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-reply mr-2 text-primary"></i>Tulis Balasan
                    </h6>
                </div>
                <div class="card-body">
                    <form id="replyForm"
                          action="{{ route('forum.storePost', [$course->id, $thread->id]) }}"
                          method="POST"
                          enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="parent_id" id="replyParentId" value="">

                        {{-- Replying to indicator --}}
                        <div id="replyingTo" class="alert alert-info py-2 px-3 mb-2 d-none d-flex align-items-center justify-content-between">
                            <small>
                                <i class="fas fa-reply mr-1"></i>Membalas <strong id="replyingToName"></strong>
                            </small>
                            <button type="button" id="cancelReply" class="btn btn-xs btn-outline-secondary">
                                <i class="fas fa-times"></i> Batal
                            </button>
                        </div>

                        {{-- Textarea --}}
                        <div class="form-group mb-2">
                            <textarea name="content"
                                      id="replyContent"
                                      class="form-control"
                                      rows="4"
                                      placeholder="Tulis balasan kamu..."></textarea>
                        </div>

                        {{-- Upload image --}}
                        <div class="form-group mb-3">
                            <div class="custom-file">
                                <input type="file"
                                       class="custom-file-input"
                                       id="replyImageInput"
                                       name="image"
                                       accept="image/jpeg,image/png,image/gif,image/webp">
                                <label class="custom-file-label" for="replyImageInput">
                                    <i class="fas fa-image mr-1"></i>Lampiran gambar (opsional, maks. 2MB)
                                </label>
                            </div>
                            <div id="replyImagePreviewWrap" class="mt-2 d-none">
                                <img id="replyImagePreview" src="" alt="Preview"
                                     class="img-fluid rounded border"
                                     style="max-height:180px;display:block">
                                <button type="button" id="removeReplyImage"
                                        class="btn btn-xs btn-outline-danger mt-1">
                                    <i class="fas fa-times mr-1"></i>Hapus gambar
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane mr-1"></i>Kirim Balasan
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <div class="mt-3 mb-4">
            <a href="{{ route('forum.index', $course->id) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i>Kembali ke Forum
            </a>
        </div>

    </div>
</section>

{{-- Lightbox --}}
<div class="modal fade" id="imgModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark border-0">
            <div class="modal-body p-2 text-center">
                <img id="imgModalSrc" src="" alt="" class="img-fluid rounded" style="max-height:85vh">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {

    const CSRF = $('meta[name="csrf-token"]').attr('content');

    // ── Reply count ───────────────────────────────────────────────────────
    function updateReplyCount(delta) {
        const $num = $('#replyCountNum');
        const next = Math.max(0, (parseInt($num.text()) || 0) + delta);
        $num.text(next);
        if (next === 0) {
            if (!$('#emptyReplies').length) {
                $('#postsContainer').html(`
                    <div id="emptyReplies" class="text-center text-muted py-4">
                        <i class="fas fa-comments fa-2x mb-2 d-block text-muted"></i>
                        Belum ada balasan. Jadilah yang pertama!
                    </div>`);
            }
        } else {
            $('#emptyReplies').remove();
        }
    }

    // ── Image preview ─────────────────────────────────────────────────────
    $('#replyImageInput').on('change', function () {
        const file = this.files[0];
        $(this).next('.custom-file-label').html(
            file ? `<i class="fas fa-image mr-1"></i>${file.name}` : '<i class="fas fa-image mr-1"></i>Lampiran gambar (opsional, maks. 2MB)'
        );
        if (file) {
            const reader = new FileReader();
            reader.onload = e => {
                $('#replyImagePreview').attr('src', e.target.result);
                $('#replyImagePreviewWrap').removeClass('d-none');
            };
            reader.readAsDataURL(file);
        }
    });

    $('#removeReplyImage').on('click', function () {
        $('#replyImageInput').val('');
        $('#replyImageInput').next('.custom-file-label').html('<i class="fas fa-image mr-1"></i>Lampiran gambar (opsional, maks. 2MB)');
        $('#replyImagePreviewWrap').addClass('d-none');
        $('#replyImagePreview').attr('src', '');
    });

    // ── Lightbox ──────────────────────────────────────────────────────────
    $(document).on('click', '.forum-img', function () {
        $('#imgModalSrc').attr('src', $(this).attr('src'));
        $('#imgModal').modal('show');
    });

    // ── Reply button ──────────────────────────────────────────────────────
    $(document).on('click', '.btn-reply', function () {
        const postId = $(this).data('post-id');
        const author = $(this).data('author');
        $('#replyParentId').val(postId);
        $('#replyingToName').text(author);
        $('#replyingTo').removeClass('d-none');
        $('#replyContent').focus();
        $('html, body').animate({ scrollTop: $('#replyCard').offset().top - 80 }, 350);
    });

    $('#cancelReply').on('click', function () {
        $('#replyParentId').val('');
        $('#replyingTo').addClass('d-none');
        $('#replyingToName').text('');
    });

    // ── Submit reply — inject ke DOM ──────────────────────────────────────
    $('#replyForm').on('submit', function (e) {
        e.preventDefault();

        const form     = $(this);
        const btn      = form.find('[type=submit]');
        const parentId = $('#replyParentId').val();

        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Mengirim...');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function (res) {
                // Reset form
                form[0].reset();
                btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i>Kirim Balasan');
                $('#replyParentId').val('');
                $('#replyingTo').addClass('d-none');
                $('#replyingToName').text('');
                $('#replyImagePreviewWrap').addClass('d-none');
                $('#replyImagePreview').attr('src', '');
                form.find('.custom-file-label').html('<i class="fas fa-image mr-1"></i>Lampiran gambar (opsional, maks. 2MB)');

                // Inject HTML
                const $newPost = $(res.html);
                $newPost.hide();

                if (parentId) {
                    // Nested: inject setelah parent post (dan semua nested-nya)
                    const $parent = $('#post-' + parentId);
                    if ($parent.length) {
                        // Cari elemen terakhir dalam grup parent (termasuk nested)
                        let $insertAfter = $parent;
                        $parent.nextAll('.forum-post-wrap').each(function () {
                            const d = parseInt($(this).data('depth')) || 0;
                            const parentDepth = parseInt($parent.data('depth')) || 0;
                            if (d > parentDepth) $insertAfter = $(this);
                            else return false;
                        });
                        $insertAfter.after($newPost);
                    } else {
                        $('#postsContainer').append($newPost);
                    }
                } else {
                    $('#postsContainer').append($newPost);
                }

                $newPost.slideDown(250);
                updateReplyCount(+1);
                $('html, body').animate({ scrollTop: $newPost.offset().top - 80 }, 350);

                Swal.fire({
                    icon: 'success', title: 'Terkirim!', text: res.message,
                    toast: true, position: 'top-end', timer: 2000, showConfirmButton: false,
                });
            },
            error: function (xhr) {
                btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i>Kirim Balasan');
                if (xhr.status === 422) {
                    $.each(xhr.responseJSON.errors, function (field, messages) {
                        const input = form.find(`[name="${field}"]`);
                        input.addClass('is-invalid');
                        input.after(`<span class="invalid-feedback d-block">${messages[0]}</span>`);
                    });
                    Swal.fire({ icon: 'error', title: 'Validasi Gagal', text: 'Periksa kembali isian form.', timer: 2000, showConfirmButton: false });
                } else {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Server error.', 'error');
                }
            }
        });
    });

    // ── Delete Post ───────────────────────────────────────────────────────
    $(document).on('click', '.btn-delete-post', function () {
        const url   = $(this).data('url');
        const $wrap = $(this).closest('.forum-post-wrap');

        Swal.fire({
            title: 'Hapus balasan ini?',
            text: 'Data yang dihapus tidak bisa dikembalikan.',
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#d33', cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash mr-1"></i> Ya, Hapus',
            cancelButtonText: 'Batal',
        }).then(result => {
            if (!result.isConfirmed) return;
            $.ajax({
                url, type: 'POST',
                data: { _method: 'DELETE', _token: CSRF },
                success: function (res) {
                    $wrap.slideUp(250, function () { $(this).remove(); updateReplyCount(-1); });
                    Swal.fire({ icon: 'success', title: 'Dihapus!', text: res.message,
                                toast: true, position: 'top-end', timer: 2000, showConfirmButton: false });
                },
                error: () => Swal.fire('Error', 'Gagal menghapus balasan.', 'error')
            });
        });
    });

    // ── Toggle Pin ────────────────────────────────────────────────────────
    $(document).on('click', '.btn-toggle-pin', function () {
        const btn = $(this);
        $.post(btn.data('url'), { _token: CSRF }, function (res) {
            const pinned = res.message.includes('dipasang');
            btn.toggleClass('btn-warning', pinned).toggleClass('btn-outline-warning', !pinned)
               .html(`<i class="fas fa-thumbtack mr-1"></i>${pinned ? 'Lepas Pin' : 'Sematkan'}`);
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message,
                        toast: true, position: 'top-end', timer: 2000, showConfirmButton: false });
        }).fail(() => Swal.fire('Error', 'Gagal mengubah status pin.', 'error'));
    });

    // ── Toggle Lock ───────────────────────────────────────────────────────
    $(document).on('click', '.btn-toggle-lock', function () {
        const btn = $(this);
        $.post(btn.data('url'), { _token: CSRF }, function (res) {
            const locked = res.message.includes('dikunci');
            btn.toggleClass('btn-secondary', locked).toggleClass('btn-outline-secondary', !locked)
               .html(`<i class="fas ${locked ? 'fa-unlock' : 'fa-lock'} mr-1"></i>${locked ? 'Buka Kunci' : 'Kunci'}`);
            if (locked) $('#replyCard').slideUp(300);
            else        $('#replyCard').slideDown(300);
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message,
                        toast: true, position: 'top-end', timer: 2000, showConfirmButton: false });
        }).fail(() => Swal.fire('Error', 'Gagal mengubah status kunci.', 'error'));
    });

    // ── Mark Solution ─────────────────────────────────────────────────────
    $(document).on('click', '.btn-mark-solution', function () {
        const btn = $(this), url = btn.data('url');
        Swal.fire({ title: 'Tandai sebagai Solusi?', icon: 'question',
                    showCancelButton: true, confirmButtonText: 'Ya, Tandai', cancelButtonText: 'Batal' })
            .then(result => {
                if (!result.isConfirmed) return;
                $.post(url, { _token: CSRF }, function (res) {
                    // Reset semua solusi
                    $('.forum-post-wrap').each(function () {
                        $(this).find('.badge-success').remove();
                        $(this).find('.btn-unmark-solution')
                               .attr('class', 'btn btn-xs btn-link text-success p-0 btn-mark-solution')
                               .attr('title', 'Tandai Solusi')
                               .html('<i class="fas fa-check-circle mr-1"></i>Tandai Solusi')
                               .data('url', $(this).find('.btn-mark-solution').data('url'));
                    });
                    // Tandai yang dipilih
                    const $wrap = btn.closest('.forum-post-wrap');
                    const $meta = $wrap.find('.d-flex.align-items-center.flex-wrap').first();
                    $meta.append('<span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i>Solusi Terbaik</span>');
                    btn.attr('class', 'btn btn-xs btn-link text-secondary p-0 btn-unmark-solution')
                       .html('<i class="fas fa-times mr-1"></i>Hapus Solusi')
                       .data('url', url.replace('/solution', '/unmark-solution'));
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message,
                                toast: true, position: 'top-end', timer: 2000, showConfirmButton: false });
                }).fail(() => Swal.fire('Error', 'Gagal menandai solusi.', 'error'));
            });
    });

    // ── Unmark Solution ───────────────────────────────────────────────────
    $(document).on('click', '.btn-unmark-solution', function () {
        const btn = $(this), url = btn.data('url');
        $.post(url, { _token: CSRF }, function (res) {
            btn.closest('.forum-post-wrap').find('.badge-success').remove();
            btn.attr('class', 'btn btn-xs btn-link text-success p-0 btn-mark-solution')
               .html('<i class="fas fa-check-circle mr-1"></i>Tandai Solusi')
               .data('url', url.replace('/unmark-solution', '/solution'));
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message,
                        toast: true, position: 'top-end', timer: 2000, showConfirmButton: false });
        }).fail(() => Swal.fire('Error', 'Gagal menghapus tanda solusi.', 'error'));
    });

    // ── Delete Thread ─────────────────────────────────────────────────────
    $(document).on('click', '.btn-delete-thread', function () {
        const url = $(this).data('url');
        Swal.fire({
            title: 'Hapus Thread?',
            text: 'Semua balasan juga akan dihapus. Tindakan ini tidak bisa dibatalkan.',
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#d33', cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash mr-1"></i> Ya, Hapus',
            cancelButtonText: 'Batal',
        }).then(result => {
            if (!result.isConfirmed) return;
            $.ajax({
                url, type: 'POST',
                data: { _method: 'DELETE', _token: CSRF },
                success: res => Swal.fire({ icon: 'success', title: 'Dihapus!', text: res.message, timer: 1500, showConfirmButton: false })
                    .then(() => window.location.href = '{{ route('forum.index', $course->id) }}'),
                error: () => Swal.fire('Error', 'Gagal menghapus thread.', 'error')
            });
        });
    });

});
</script>
@endpush
