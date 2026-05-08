@extends('main')

@section('title', 'Forum Diskusi - ' . $course->title)

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
                    <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Kelas</a></li>
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
                    <i class="fas fa-comments mr-2"></i>
                    Forum Diskusi — {{ $course->title }}
                </h3>
                <div class="card-tools d-flex align-items-center">
                    {{-- Filter --}}
                    <div class="mr-2">
                        <select id="filterSelect" class="form-control form-control-sm">
                            <option value="all"    {{ $filter === 'all'    ? 'selected' : '' }}>Semua</option>
                            <option value="recent" {{ $filter === 'recent' ? 'selected' : '' }}>Terbaru</option>
                            <option value="popular"{{ $filter === 'popular'? 'selected' : '' }}>Terpopuler</option>
                            <option value="pinned" {{ $filter === 'pinned' ? 'selected' : '' }}>Disematkan</option>
                        </select>
                    </div>
                    {{-- Tombol buat thread --}}
                    @if(auth()->user()->hasPermission('forum.post'))
                    <a href="{{ route('forum.create', $course->id) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> Buat Thread
                    </a>
                    @endif
                </div>
            </div>

            <div class="card-body p-0">
                @forelse($threads as $thread)
                <div class="d-flex align-items-start p-3 border-bottom {{ $thread->is_pinned ? 'bg-light' : '' }}">
                    {{-- Avatar --}}
                    <div class="mr-3 text-center" style="min-width:42px">
                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white"
                             style="width:42px;height:42px;font-size:1.1rem">
                            {{ strtoupper(substr($thread->user->name ?? 'U', 0, 1)) }}
                        </div>
                    </div>

                    {{-- Konten thread --}}
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center flex-wrap mb-1">
                            @if($thread->is_pinned)
                                <span class="badge badge-warning mr-1"><i class="fas fa-thumbtack mr-1"></i>Disematkan</span>
                            @endif
                            @if($thread->is_locked)
                                <span class="badge badge-secondary mr-1"><i class="fas fa-lock mr-1"></i>Terkunci</span>
                            @endif
                            <a href="{{ route('forum.show', [$course->id, $thread->id]) }}"
                               class="font-weight-bold text-dark h6 mb-0">
                                {{ $thread->title }}
                            </a>
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-user mr-1"></i>{{ $thread->user->name ?? '-' }}
                            &nbsp;·&nbsp;
                            <i class="fas fa-clock mr-1"></i>{{ $thread->created_at->diffForHumans() }}
                        </small>
                    </div>

                    {{-- Statistik --}}
                    <div class="text-right text-muted ml-3" style="min-width:100px">
                        <div><i class="fas fa-reply mr-1"></i>{{ $thread->posts_count }} balasan</div>
                        <div><i class="fas fa-eye mr-1"></i>{{ $thread->views }} views</div>
                    </div>
                </div>
                @empty
                <div class="p-4 text-center text-muted">
                    <i class="fas fa-comments fa-3x mb-3 d-block"></i>
                    Belum ada thread diskusi.
                    @if(auth()->user()->hasPermission('forum.post'))
                        <a href="{{ route('forum.create', $course->id) }}">Mulai diskusi pertama!</a>
                    @endif
                </div>
                @endforelse
            </div>

            @if($threads->hasPages())
            <div class="card-footer">
                {{ $threads->appends(['filter' => $filter])->links() }}
            </div>
            @endif
        </div>

    </div>
</section>
@endsection

@push('scripts')
<script>
    document.getElementById('filterSelect').addEventListener('change', function () {
        const url = new URL(window.location.href);
        url.searchParams.set('filter', this.value);
        window.location.href = url.toString();
    });
</script>
@endpush
