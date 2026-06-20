@extends('main')

@section('title', 'Opsi Jawaban')

@section('content')

<div class="card">

    <div class="card-header">

    <div class="d-flex justify-content-between align-items-center">

        <h3 class="card-title mb-0">
            <i class="fas fa-list"></i>
            Opsi Jawaban
        </h3>

        <div>

            <a href="{{ route('quizzes.questions.index', $question->quiz_id) }}"
               class="btn btn-secondary btn-sm">

                <i class="fas fa-arrow-left"></i>
                Kembali

            </a>

        </div>

    </div>

</div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="alert alert-info">

            <strong>Pertanyaan :</strong><br>

            {{ $question->question }}

        </div>

        @if($options->count() > 0 && !$options->where('is_correct', true)->count())

            <div class="alert alert-warning">

                <i class="fas fa-exclamation-triangle"></i>
                Belum ada jawaban benar yang dipilih untuk soal ini.

            </div>

        @endif

        <div class="card card-primary mb-4">

            <div class="card-header">
                <h5 class="mb-0">Tambah Opsi Jawaban</h5>
            </div>

            <div class="card-body">

                <form method="POST"
                      action="{{ route('questions.options.store', $question->id) }}">

                    @csrf

                    <div class="row">

                        <div class="col-md-8">

                            <div class="form-group">

                                <label>Opsi Jawaban</label>

                                <input type="text"
                                       name="option_text"
                                       class="form-control"
                                       placeholder="Masukkan opsi jawaban"
                                       required>

                            </div>

                        </div>

                        <div class="col-md-2">

                            <div class="form-group">

                                <label>Jawaban Benar</label>

                                <div class="form-check mt-2">

                                    <input type="checkbox"
                                           name="is_correct"
                                           value="1"
                                           class="form-check-input"
                                           id="is_correct">

                                    <label class="form-check-label"
                                           for="is_correct">

                                        Benar

                                    </label>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-2">

                            <div class="form-group">

                                <label>&nbsp;</label>

                                <button type="submit"
                                        class="btn btn-primary btn-block">

                                    <i class="fas fa-plus"></i>
                                    Tambah

                                </button>

                            </div>

                        </div>

                    </div>

                </form>

            </div>

        </div>

        <div class="card">

            <div class="card-header">

                <strong>
                    Total Opsi :
                    {{ $options->count() }}
                </strong>

            </div>

            <div class="card-body p-0">

                <table class="table table-hover table-bordered mb-0">

                    <thead class="thead-light">

                        <tr>

                            <th width="8%">Kode</th>
                            <th>Opsi Jawaban</th>
                            <th width="20%">Status</th>
                            <th width="20%">Aksi</th>

                        </tr>

                    </thead>

                    <tbody>

                    @forelse($options as $option)

                        <tr class="{{ $option->is_correct ? 'table-success' : '' }}">

                            <td>

                                <strong>
                                    {{ chr(64 + $loop->iteration) }}
                                </strong>

                            </td>

                            <td>

                                {{ $option->option_text }}

                            </td>

                            <td>

                                @if($option->is_correct)

                                    <span class="badge badge-success">

                                        <i class="fas fa-check"></i>
                                        Jawaban Benar

                                    </span>

                                @else

                                    <span class="badge badge-secondary">

                                        Salah

                                    </span>

                                @endif

                            </td>

                            <td>

                                <button
                                    type="button"
                                    class="btn btn-warning btn-sm"
                                    data-toggle="modal"
                                    data-target="#editOption{{ $option->id }}">

                                    <i class="fas fa-edit"></i>

                                </button>

                                <form action="{{ route('questions.options.destroy', [$question->id, $option->id]) }}"
                                      method="POST"
                                      style="display:inline">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Hapus opsi jawaban ini?')">

                                        <i class="fas fa-trash"></i>

                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="4"
                                class="text-center p-4">

                                <i class="fas fa-info-circle"></i>
                                Belum ada opsi jawaban

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@foreach($options as $option)

<div class="modal fade"
     id="editOption{{ $option->id }}"
     tabindex="-1">

    <div class="modal-dialog">

        <div class="modal-content">

            <form action="{{ route('questions.options.update', [$question->id, $option->id]) }}"
                  method="POST">

                @csrf
                @method('PUT')

                <div class="modal-header">

                    <h5 class="modal-title">

                        Edit Opsi Jawaban

                    </h5>

                    <button type="button"
                            class="close"
                            data-dismiss="modal">

                        <span>&times;</span>

                    </button>

                </div>

                <div class="modal-body">

                    <div class="form-group">

                        <label>Opsi Jawaban</label>

                        <input type="text"
                               name="option_text"
                               class="form-control"
                               value="{{ $option->option_text }}"
                               required>

                    </div>

                    <div class="form-check">

                        <input type="checkbox"
                               name="is_correct"
                               value="1"
                               class="form-check-input"
                               id="correct{{ $option->id }}"
                               {{ $option->is_correct ? 'checked' : '' }}>

                        <label class="form-check-label"
                               for="correct{{ $option->id }}">

                            Jadikan Jawaban Benar

                        </label>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">

                        Batal

                    </button>

                    <button type="submit"
                            class="btn btn-primary">

                        Simpan

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endforeach

@endsection