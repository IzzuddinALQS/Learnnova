@extends('main')

@section('title', 'Detail Tugas — ' . $assignment->title)

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Detail Tugas</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('assignments.index') }}">Tugas</a></li>
                        <li class="breadcrumb-item active">{{ Str::limit($assignment->title, 30) }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">

                {{-- Kolom Utama --}}
                <div class="col-lg-8 col-md-12 mb-4">

                    {{-- Detail Tugas --}}
                    <div class="card card-primary card-outline mb-4">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold">{{ $assignment->title }}</h3>
                            <div class="card-tools">
                                @if ($assignment->due_date->isPast())
                                    <span class="badge badge-danger px-3 py-2">
                                        <i class="fas fa-clock mr-1"></i>Deadline Lewat
                                    </span>
                                @else
                                    <span class="badge badge-success px-3 py-2">
                                        <i class="fas fa-clock mr-1"></i>Aktif
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="font-weight-bold text-muted text-uppercase mb-2">Deskripsi / Instruksi</h6>
                            <p style="line-height: 1.8;">{{ $assignment->description }}</p>

                            @if ($assignment->file)
                                <div class="mt-3">
                                    <h6 class="font-weight-bold text-muted text-uppercase mb-2">File Lampiran Pengajar</h6>
                                    <a href="{{ asset('storage/' . $assignment->file) }}" target="_blank"
                                        class="btn btn-info btn-sm">
                                        <i class="fas fa-paperclip mr-1"></i> Unduh File
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Form Submission --}}
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold">
                                <i class="fas fa-upload mr-2"></i>
                                {{ $submission ? 'Jawaban Saya' : 'Kumpulkan Tugas' }}
                            </h3>
                        </div>

                        @php
                            $isLocked =
                                $assignment->due_date->isPast() ||
                                in_array($submission?->status, ['graded', 'returned']);
                        @endphp

                        <form id="submissionForm"
                            action="{{ route('assignments.submit', $assignment->id) }}"
                            method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="card-body">

                                @if ($isLocked)
                                    <div class="alert alert-warning">
                                        <i class="fas fa-lock mr-1"></i>
                                        @if ($assignment->due_date->isPast())
                                            Deadline telah lewat. Tugas tidak bisa diubah.
                                        @else
                                            Tugas sudah dinilai. Tidak bisa diubah.
                                        @endif
                                    </div>
                                @endif

                                {{-- Jawaban Teks --}}
                                <div class="form-group">
                                    <label>Jawaban Teks</label>
                                    <textarea name="text_answer" rows="6" class="form-control"
                                        placeholder="Tulis jawaban Anda di sini..."
                                        {{ $isLocked ? 'disabled' : '' }}>{{ old('text_answer', $submission?->text_answer ?? '') }}</textarea>
                                </div>

                                {{-- Upload File --}}
                                <div class="form-group">
                                    <label>Upload File Jawaban</label>
                                    <div class="custom-file">
                                        <input type="file" name="file_path" id="submissionFile"
                                            class="custom-file-input"
                                            {{ $isLocked ? 'disabled' : '' }}>
                                        <label class="custom-file-label" for="submissionFile">Pilih file...</label>
                                    </div>
                                    <small class="text-muted">Format: PDF, DOC, DOCX, PPT, PPTX, PNG, JPG. Maks 2MB.</small>

                                    @if ($submission?->file_path)
                                        <div class="mt-2">
                                            <span class="text-muted">File saat ini: </span>
                                            <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank">
                                                <i class="fas fa-paperclip mr-1"></i>Lihat File
                                            </a>
                                        </div>
                                    @endif
                                </div>

                                {{-- Catatan --}}
                                <div class="form-group">
                                    <label>Catatan <small class="text-muted">(opsional)</small></label>
                                    <textarea name="note" rows="3" class="form-control"
                                        placeholder="Tambahkan catatan untuk pengajar..."
                                        {{ $isLocked ? 'disabled' : '' }}>{{ old('note', $submission?->note ?? '') }}</textarea>
                                </div>

                                {{-- Feedback Pengajar --}}
                                @if ($submission?->feedback)
                                    <div class="form-group">
                                        <label class="font-weight-bold text-success">Feedback Pengajar</label>
                                        <div class="p-3 bg-light rounded border">
                                            {{ $submission->feedback }}
                                        </div>
                                    </div>
                                @endif

                                {{-- Nilai --}}
                                @if ($submission?->score !== null)
                                    <div class="form-group">
                                        <label class="font-weight-bold text-success">Nilai</label>
                                        <div class="p-3 bg-light rounded border">
                                            <strong>{{ $submission->score }}</strong> / {{ $assignment->max_score }}
                                        </div>
                                    </div>
                                @endif

                            </div>

                            @if (!$isLocked)
                                <div class="card-footer">
                                    <button type="submit" name="action" value="draft" class="btn btn-warning">
                                        <i class="fas fa-save mr-1"></i> Simpan Draft
                                    </button>
                                    <button type="submit" name="action" value="submit" class="btn btn-primary ml-2">
                                        <i class="fas fa-paper-plane mr-1"></i> Kumpulkan
                                    </button>
                                    <a href="{{ route('assignments.index') }}" class="btn btn-secondary ml-2">
                                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                                    </a>
                                </div>
                            @else
                                <div class="card-footer">
                                    <a href="{{ route('assignments.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                                    </a>
                                </div>
                            @endif
                        </form>
                    </div>

                </div>

                {{-- Kolom Info --}}
                <div class="col-lg-4 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i>Informasi</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted pl-3" style="width: 40%;">Kelas</td>
                                    <td class="font-weight-bold">{{ $assignment->course->title ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted pl-3">Deadline</td>
                                    <td class="font-weight-bold">{{ $assignment->due_date->format('d M Y, H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted pl-3">Nilai Maks</td>
                                    <td class="font-weight-bold">{{ $assignment->max_score }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted pl-3">Status</td>
                                    <td>
                                        @switch($submission?->status ?? 'belum')
                                            @case('belum')
                                                <span class="badge badge-secondary">Belum Dikumpulkan</span>
                                            @break

                                            @case('draft')
                                                <span class="badge badge-warning">Draft</span>
                                            @break

                                            @case('submitted')
                                                <span class="badge badge-primary">Dikumpulkan</span>
                                            @break

                                            @case('graded')
                                                <span class="badge badge-success">Sudah Dinilai</span>
                                            @break

                                            @case('returned')
                                                <span class="badge badge-info">Dikembalikan</span>
                                            @break
                                        @endswitch
                                    </td>
                                </tr>
                                @if ($submission?->submitted_at)
                                    <tr>
                                        <td class="text-muted pl-3">Dikumpulkan</td>
                                        <td>{{ $submission->submitted_at->format('d M Y, H:i') }}</td>
                                    </tr>
                                @endif
                                @if ($submission?->graded_at)
                                    <tr>
                                        <td class="text-muted pl-3">Dinilai</td>
                                        <td>{{ $submission->graded_at->format('d M Y, H:i') }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $('#submissionFile').on('change', function() {
            const file = this.files[0];
            $('.custom-file-label').text(file ? file.name : 'Pilih file...');
        });

        ajaxForm('#submissionForm', {
            onBeforeSubmit: function(formData) {
                const action = document.activeElement.value;
                formData.append('action', action);
            }
        });
    </script>
@endpush