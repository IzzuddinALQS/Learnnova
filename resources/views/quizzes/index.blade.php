@extends('main')

@section('title', 'Daftar Quiz')

@section('content')

<div class="card-header">

    <h3 class="card-title">
        <i class="fas fa-question-circle mr-2"></i>
        Daftar Quiz
    </h3>

    <div class="card-tools">

        @if(
            auth()->user()->hasRole('super_admin') ||
            auth()->user()->hasRole('pengajar') ||
            auth()->user()->hasRole('akademik')
        )
            <a href="{{ route('quizzes.create') }}"
               class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i>
                Tambah Quiz
            </a>
        @endif

    </div>

</div>

<div class="card-body">

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Statistik --}}
    <div class="row mb-3">

        <div class="col-md-3">

            <div class="small-box bg-info">

                <div class="inner">
                    <h3>{{ $totalQuiz }}</h3>
                    <p>Total Quiz</p>
                </div>

                <div class="icon">
                    <i class="fas fa-question-circle"></i>
                </div>

            </div>

        </div>

    </div>

    <table class="table table-bordered table-striped">

        <thead>
            <tr>
                <th width="5%">#</th>
                <th>Kelas</th>
                <th>Judul Quiz</th>
                <th>Jumlah Soal</th>
                <th>Deadline</th>
                <th>Durasi</th>
                <th width="30%">Aksi</th>
            </tr>
        </thead>

        <tbody>

            @forelse($quizzes as $quiz)

                <tr>

                    <td>{{ $loop->iteration }}</td>

                    <td>
                        {{ $quiz->course->title ?? '-' }}
                    </td>

                    <td>
                        {{ $quiz->title }}
                    </td>

                    <td>
                        <span class="badge badge-info">
                            {{ $quiz->questions_count }} Soal
                        </span>
                    </td>

                    <td>
                        {{ $quiz->deadline
                            ? \Carbon\Carbon::parse($quiz->deadline)->format('d M Y H:i')
                            : '-' }}
                    </td>

                    <td>
                        {{ $quiz->duration_minutes ?? '-' }} menit
                    </td>

                    <td>

                        {{-- Detail Quiz --}}
                        <a href="{{ route('quizzes.show', $quiz->id) }}"
                           class="btn btn-secondary btn-sm">
                            <i class="fas fa-eye"></i>
                        </a>

                        {{-- Kelola Soal --}}
                        @if(
                            auth()->user()->hasRole('super_admin') ||
                            auth()->user()->hasRole('pengajar') ||
                            auth()->user()->hasRole('akademik')
                        )
                            <a href="{{ route('quizzes.questions.index', $quiz->id) }}"
                               class="btn btn-info btn-sm">
                                <i class="fas fa-list"></i>
                                Soal
                            </a>
                        @endif

                        @if(auth()->user()->hasPermission('quizzes.take'))
                            <a href="{{ route('quizzes.attempts.take', $quiz->id) }}"
                               class="btn btn-success btn-sm">
                                <i class="fas fa-play"></i>
                                Ikuti
                            </a>
                        @endif

                        @if(auth()->user()->hasPermission('quizzes.grade'))
                            <a href="{{ route('quizzes.attempts.index', $quiz->id) }}"
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-clipboard-list"></i>
                                Attempt
                            </a>
                        @endif

                        @if(
                            auth()->user()->hasRole('super_admin') ||
                            auth()->user()->hasRole('pengajar') ||
                            auth()->user()->hasRole('akademik')
                        )

                            {{-- Edit --}}
                            <a href="{{ route('quizzes.edit', $quiz->id) }}"
                               class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>

                            {{-- Hapus --}}
                            <form action="{{ route('quizzes.destroy', $quiz->id) }}"
                                  method="POST"
                                  style="display:inline-block">

                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Hapus quiz ini?')">

                                    <i class="fas fa-trash"></i>

                                </button>

                            </form>

                        @endif

                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="7" class="text-center">
                        Belum ada quiz
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

</div>

@endsection