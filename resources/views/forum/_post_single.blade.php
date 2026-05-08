{{--
    Partial untuk render SATU post baru via AJAX inject.
    Variables: $post, $thread, $course
--}}
<div class="forum-post-wrap forum-post-new" id="post-{{ $post->id }}"
     data-depth="{{ $post->parent_id ? 1 : 0 }}"
     style="{{ $post->parent_id ? 'margin-left:24px; border-left:3px solid #dee2e6; padding-left:12px;' : '' }}">

    <div class="media py-3">

        {{-- Avatar --}}
        <div class="forum-avatar mr-3 flex-shrink-0">
            <div class="rounded-circle d-flex align-items-center justify-content-center text-white font-weight-bold"
                 style="width:38px;height:38px;font-size:.9rem;
                        background:{{ collect(['#3490dc','#38a169','#e53e3e','#805ad5','#dd6b20','#2b6cb0'])->get($post->user_id % 6, '#6c757d') }}">
                {{ strtoupper(substr($post->user->name ?? 'U', 0, 1)) }}
            </div>
        </div>

        {{-- Konten --}}
        <div class="media-body">
            <div class="d-flex align-items-center flex-wrap mb-1" style="gap:6px">
                <strong class="text-dark">{{ $post->user->name ?? 'Unknown' }}</strong>
                <small class="text-muted">
                    <i class="fas fa-clock mr-1"></i>baru saja
                </small>
                @if($post->parent_id)
                    <small class="text-muted">
                        <i class="fas fa-level-up-alt fa-rotate-90 mr-1"></i>membalas
                    </small>
                @endif
            </div>

            <div class="post-content mb-2" style="white-space:pre-wrap;line-height:1.6;">{{ $post->content }}</div>

            @if($post->image)
            <div class="mb-2">
                <img src="{{ asset('storage/' . $post->image) }}"
                     alt="Lampiran"
                     class="img-fluid rounded border forum-img"
                     style="max-height:280px;cursor:zoom-in;display:block">
            </div>
            @endif

            <div class="d-flex align-items-center flex-wrap" style="gap:6px">
                @if(!$thread->is_locked && auth()->user()->hasPermission('forum.post'))
                <button type="button"
                        class="btn btn-xs btn-link text-primary p-0 btn-reply"
                        data-post-id="{{ $post->id }}"
                        data-author="{{ $post->user->name ?? '' }}">
                    <i class="fas fa-reply mr-1"></i>Balas
                </button>
                @endif

                @if(auth()->user()->hasPermission('forum.moderate'))
                <button type="button"
                        class="btn btn-xs btn-link text-success p-0 btn-mark-solution"
                        data-url="{{ route('forum.markSolution', [$course->id, $thread->id, $post->id]) }}">
                    <i class="fas fa-check-circle mr-1"></i>Tandai Solusi
                </button>
                @endif

                @if(auth()->id() === $post->user_id || auth()->user()->hasPermission('forum.moderate'))
                <button type="button"
                        class="btn btn-xs btn-link text-danger p-0 btn-delete-post"
                        data-url="{{ route('forum.destroyPost', [$course->id, $thread->id, $post->id]) }}">
                    <i class="fas fa-trash mr-1"></i>Hapus
                </button>
                @endif
            </div>
        </div>
    </div>
</div>
