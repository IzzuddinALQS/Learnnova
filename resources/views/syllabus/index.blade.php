@extends('main')
<<<<<<< HEAD
@section('title', 'Catalog Syllabus')
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Katalog Syllabus</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Syllabus</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section>
        <div class="container mt-4">
            <!-- Tombol Tambah Data (Opsional, untuk memudahkan navigasi nantinya) -->
            <div class="mb-4">
                <a href="{{ route('syllabus.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Tambah Syllabus Baru
                </a>
            </div>

            <div class="row g-4">
                @forelse ($data_syllabi as $item)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            {{-- Logika Gambar: Jika kolom image berisi URL lengkap atau nama file di public/img --}}
                            <img src="{{ Str::startsWith($item->theme, ['http://', 'https://']) ? $item->theme : asset('img/' . $item->theme) }}"
                                class="card-img-top" style="height: 180px; object-fit: cover;" alt="{{ $item->name }}">

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title font-weight-bold">{{ $item->name }}</h5>
                                <small class="card-text text-muted">
                                    {{ Str::limit($item->instructor, 255) }}
                                </small>

                                <div class="mt-auto">
                                    <a href="{{ route('syllabus.show', $item->id) }}"
                                        class="btn btn-primary btn-block shadow-sm">
                                        <i class="fas fa-eye mr-1"></i> Lihat Syllabus
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    {{-- Tampilan jika database masih kosong --}}
                    <div class="col-12 text-center">
                        <div class="alert alert-info">
                            Belum ada data syllabus. Silakan tambah data terlebih dahulu.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
=======
@section('title', 'Syllabus Course')
@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Silabus Kelas</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Kelas</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<section>

<div class="container mt-4">
  <div class="row g-4">

    <div class="col-md-4 mb-4">
      <div class="card h-100">
        <img src="/img/images.jpg"
             class="card-img-top"
             style="height: 180px; object-fit: cover;">
        <div class="card-body d-flex flex-column">
          <h5 class="card-title">Bahasa Arab Muhadatsah (Percakapan)</h5>
          <p class="card-text">
            Mempelajari dasar percakapan Bahasa Arab (muhadatsah), mulai dari salam, perkenalan, kosakata sehari-hari..
          </p>

          <div class="mt-auto">
            <a href="#" class="btn btn-primary w-100">Lihat Kelas</a>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4 mb-4">
      <div class="card h-100">
        <img src="/img/belajar-bahasa-arab.jpg"
             class="card-img-top"
             style="height: 180px; object-fit: cover;">
        <div class="card-body d-flex flex-column">
          <h5 class="card-title">Belajar Dasar Bahasa Arab Qur’ani</h5>
          <p class="card-text">
            Mempelajari dasar-dasar Bahasa Arab Qur’ani, mulai dari pengenalan huruf hijaiyah, kosakata dalam Al-Qur’an...
          </p>
          <div class="mt-auto">
            <a href="#" class="btn btn-primary w-100">Lihat Kelas</a>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4 mb-4">
      <div class="card h-100">
        <img src="/img/bahasa.jpg"
             class="card-img-top"
             style="height: 180px; object-fit: cover;">

        <div class="card-body d-flex flex-column">
          <h5 class="card-title">Belajar Bahasa Inggris Beginner</h5>
          <p class="card-text">
            Mempelajari dasar-dasar Bahasa Inggris untuk pemula, mulai dari kosakata dasar, tata bahasa sederhana, hingga kemampuan..
          </p>

          <div class="mt-auto">
            <a href="#" class="btn btn-primary w-100">Lihat Kelas</a>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4 mb-4">
      <div class="card h-100">
        <img src="/img/Matematika.jpg"
             class="card-img-top"
             style="height: 180px; object-fit: cover;">

        <div class="card-body d-flex flex-column">
          <h5 class="card-title">Matematika Diskrit</h5>
          <p class="card-text">
            Mempelajari konsep dasar matematika diskrit seperti logika, himpunan, relasi, fungsi, kombinatorika, dan graf..
          </p>

          <div class="mt-auto">
            <a href="#" class="btn btn-primary w-100">Lihat Kelas</a>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4 mb-4">
      <div class="card h-100">
        <img src="/img/Ipa.png"
             class="card-img-top"
             style="height: 180px; object-fit: cover;">

        <div class="card-body d-flex flex-column">
          <h5 class="card-title">Ilmu Pengetahuan Alam</h5>
          <p class="card-text">
            Mempelajari konsep dasar Ilmu Pengetahuan Alam yang mencakup fisika, kimia, dan biologi, mulai dari pengenalan fenomena alam, materi, energi
          </p>
          <div class="mt-auto">
            <a href="#" class="btn btn-primary w-100">Lihat Kelas</a>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4 mb-4">
      <div class="card h-100">
        <img src="/img/bahasa.jpg"
             class="card-img-top"
             style="height: 180px; object-fit: cover;">

        <div class="card-body d-flex flex-column">
          <h5 class="card-title">Belajar Bahasa Inggris Beginner</h5>
          <p class="card-text">
            Mempelajari dasar-dasar Bahasa Inggris untuk pemula, mulai dari kosakata dasar, tata bahasa sederhana, hingga kemampuan..
          </p>

          <div class="mt-auto">
            <a href="#" class="btn btn-primary w-100">Lihat Kelas</a>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

</section>
@endsection
>>>>>>> 5d8d10b622e562f93524fce1b6e38614ab17d922
