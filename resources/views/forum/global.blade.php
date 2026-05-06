@extends('main')

@section('title', 'Forum Diskusi')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Forum Diskusi</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Forum</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-comments mr-2"></i>Semua Diskusi
                </h3>
                <div class="card-tools d-flex align-items-center" style="gap:8px">

                    {{-- Filter kelas --}}
                    <select id="courseFilter" class="form-control form-control-sm" style="min-width:160px">
                        <option value="">Semua Kelas</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" {{ $courseId == $c->id ? 'selected' : '' }}>
                                {{ Str::limit($c->title, 30) }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Filter urutan --}}
                    <select id="sortFilter" class="form-control form-control-sm" style="min-width:130px">
                        <option value="all"     {{ $filter === 'all'     ? 'selected' : '' }}>Terbaru</option>
                        <option value="popular" {{ $filter === 'popular' ? 'selected' : '' }}>Terpopuler</option>
                        <option value="recent"  {{ $filter === 'recent'  ? 'selected' : '' }}>Diperbarui</option>
                        <option value="pinned"  {{ $filter === 'pinned'  ? 'selected' : '' }}>Disematkan</option>
                    </select>

                </div>
            </div>

            <div class="card-body p-0">
                @forelse($threads as $thread)
                <div class="d-flex align-items-start p-3 border-bottom {{ $thread->is_pinned ? 'bg-light' : '' }}">

                    {{-- Avatar --}}
                    <div class="flex-shrink-0 mr-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white font-weight-bold"
                             style="width:42px;height:42px;font-size:1rem;
                                    background:{{ collect(['#3490dc','#38a169','#e53e3e','#805ad5','#dd6b20','#2b6cb0'])->get($thread->user_id % 6, '#6c757d') }}">
                            {{ strtoupper(substr($thread->user->name ?? 'U', 0, 1)) }}
                        </div>
                    </div>

                    {{-- Konten --}}
                    <div class="flex-grow-1 min-width-0">
                        {{-- Badges --}}
                        <div class="mb-1" style="line-height:1.8">
                            @if($thread->is_pinned)
                                <span class="badge badge-warning mr-1">
                                    <i class="fas fa-thumbtack mr-1"></i>Disematkan
                                </span>
                            @endif
                            @if($thread->is_locked)
                                <span class="badge badge-secondary mr-1">
                                    <i class="fas fa-lock mr-1"></i>Terkunci
                                </span>
                            @endif
                            {{-- Label kelas --}}
                            <a href="{{ route('forum.index', $thread->course_id) }}"
                               class="badge badge-primary mr-1"
                               title="Lihat forum kelas ini">
                                <i class="fas fa-chalkboard-teacher mr-1"></i>{{ Str::limit($thread->course->title ?? '-', 25) }}
                            </a>
                        </div>

                        {{-- Judul --}}
                        <a href="{{ route('forum.show', [$thread->course_id, $thread->id]) }}"
                           class="d-block font-weight-bold text-dark mb-1"
                           style="font-size:1rem;line-height:1.4">
                            {{ $thread->title }}
                        </a>

                        {{-- Meta --}}
                        <small class="text-muted">
                            <i class="fas fa-user mr-1"></i>{{ $thread->user->name ?? '-' }}
                            &nbsp;·&nbsp;
                            <i class="fas fa-clock mr-1"></i>{{ $thread->created_at->diffForHumans() }}
                        </small>
                    </div>

                    {{-- Statistik --}}
                    <div class="flex-shrink-0 text-right text-muted ml-3" style="min-width:90px;font-size:.85rem">
                        <div><i class="fas fa-reply mr-1"></i>{{ $thread->posts_count }}</div>
                        <div><i class="fas fa-eye mr-1"></i>{{ $thread->views }}</div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-5">
                    <i class="fas fa-comments fa-3x mb-3 d-block"></i>
                    Belum ada thread diskusi.
                </div>
                @endforelse
            </div>

            @if($threads->hasPages())
            <div class="card-footer">
                {{ $threads->appends(['filter' => $filter, 'course' => $courseId])->links() }}
            </div>
            @endif
        </div>

    </div>
</section>
@endsection

@push('scripts')
<script>
    function applyFilter() {
        const url    = new URL(window.location.href);
        const sort   = document.getElementById('sortFilter').value;
        const course = document.getElementById('courseFilter').value;

        url.searchParams.set('filter', sort);
        if (course) url.searchParams.set('course', course);
        else        url.searchParams.delete('course');

        window.location.href = url.toString();
    }

    document.getElementById('sortFilter').addEventListener('change', applyFilter);
    document.getElementById('courseFilter').addEventListener('change', applyFilter);
</script>
@endpush
