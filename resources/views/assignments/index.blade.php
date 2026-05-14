@extends('main')
@section('title', 'Daftar Tugas')
@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Daftar Tugas</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Tugas</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<section>
<div class="container mt-4">

  <div class="row g-3">
    <div class="col-md-6">
      <div class="card p-3 shadow-sm h-80">
        <div class="mb-2">
          <span class="badge bg-warning text-dark">Bahasa Arab</span>
        </div>

        <!-- Judul -->
        <h5 class="fw-bold">
          <a href="{{ route('assignments.show', 1) }}" class="text-dark text-decoration-none">Tugas Gramatika</a>
        </h5>

        <!-- Deadline -->
        <p class="text-muted mb-2">Tenggat: 27 April 2026</p>
        <p class="text-muted small">
          Tugas ditulis tangan. Langsung dikerjakan di buku, difoto dan diunggah
        </p>
        <span class="badge bg-success mb-3">2 Hari Lagi</span>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card p-3 shadow-sm h-80">
        <div class="mb-2">
          <span class="badge bg-primary">Bahasa Inggris</span>
        </div>
        <h5 class="fw-bold">
          <a href="{{ route('assignments.show', 2) }}" class="text-dark text-decoration-none">Essay Grammar Struktur Kalimat</a>
        </h5>
        <p class="text-muted mb-2">Tenggat: 27 April 2026</p>
        <p class="text-muted small">
          Buat sebuah essay pendek tentang struktur kalimat dalam bahasa Inggris.
        </p>
        <span class="badge bg-success mb-3">4 Hari Lagi</span>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card p-3 shadow-sm h-80">
        <div class="mb-2">
          <span class="badge bg-primary">Matetika</span>
        </div>
        <h5 class="fw-bold">
          <a href="{{ route('assignments.show', 3) }}" class="text-dark text-decoration-none">Essay Grammar Struktur Kalimat</a>
        </h5>
        <p class="text-muted mb-2">Tenggat: 27 April 2026</p>
        <p class="text-muted small">
          Buat sebuah essay pendek tentang struktur kalimat dalam bahasa Inggris.
        </p>
        <span class="badge bg-success mb-3">4 Hari Lagi</span>
      </div>
    </div>

  </div>
</div>

</section>
@endsection