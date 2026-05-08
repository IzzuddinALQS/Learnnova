@extends('main')
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