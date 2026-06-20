@extends('main')

@section('title', 'Kelola Soal Quiz')

@section('content')

<div class="card">

    <div class="card-header">

    <h3 class="card-title">
        <i class="fas fa-question-circle mr-2"></i>
        Soal Quiz : {{ $quiz->title }}
    </h3>

    <div class="card-tools">

        @if(
    auth()->user()->hasRole('super_admin') ||
    auth()->user()->hasRole('pengajar') ||
    auth()->user()->hasRole('akademik')
)

<a href="{{ route('quizzes.questions.create', $quiz->id) }}"
   class="btn btn-primary btn-sm">
    <i class="fas fa-plus"></i>
    Tambah Soal
</a>

@endif

        <a href="{{ route('quizzes.index') }}"
           class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i>
            Kembali
        </a>

    </div>

</div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
<div class="row mb-3">

    <div class="col-md-3">

        <div class="small-box bg-info">

            <div class="inner">
                <h3>{{ $questions->count() }}</h3>
                <p>Total Soal</p>
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
        <th>Pertanyaan</th>
        <th width="15%">Tipe</th>
        <th width="10%">Poin</th>
        <th width="30%">Aksi</th>
    </tr>
</thead>

<tbody>

    @forelse($questions as $question)

        <tr>

            <td>{{ $loop->iteration }}</td>

            <td>{{ $question->question }}</td>

            <td>
                @switch($question->type)
                    @case('multiple_choice')
                        Pilihan Ganda
                        @break

                    @case('true_false')
                        Benar / Salah
                        @break

                    @case('short_answer')
                        Jawaban Singkat
                        @break

                    @case('essay')
                        Essay
                        @break

                    @default
                        {{ $question->type }}
                @endswitch
            </td>

            <td>{{ $question->points }}</td>

            <td>

    @if(
    auth()->user()->hasRole('super_admin') ||
    auth()->user()->hasRole('pengajar') ||
    auth()->user()->hasRole('akademik')
)

    @if($question->type == 'multiple_choice')

    <a href="{{ route('questions.options.index', $question->id) }}"
       class="btn btn-info btn-sm">

        <i class="fas fa-list"></i>

        Opsi

        @if($question->options_count == 0)
            <span class="badge badge-danger">
                0
            </span>
        @else
            <span class="badge badge-light">
                {{ $question->options_count }}
            </span>
        @endif

    </a>

    @endif

@endif

                {{-- Tombol Edit & Hapus --}}
                @if(
                    auth()->user()->hasRole('super_admin') ||
                    auth()->user()->hasRole('pengajar') ||
                    auth()->user()->hasRole('akademik')
                )

                    <a href="{{ route('quizzes.questions.edit', [$quiz->id, $question->id]) }}"
                       class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i>
                    </a>

                    <form action="{{ route('quizzes.questions.destroy', [$quiz->id, $question->id]) }}"
                          method="POST"
                          style="display:inline-block">

                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Hapus soal ini?')">
                            <i class="fas fa-trash"></i>
                        </button>

                    </form>

                @else

                    <span class="badge badge-secondary">
                        Read Only
                    </span>

                @endif

            </td>

        </tr>

    @empty

        <tr>
            <td colspan="5" class="text-center">
                Belum ada soal untuk quiz ini
            </td>
        </tr>

    @endforelse

</tbody>

        </table>

    </div>

</div>

@endsection