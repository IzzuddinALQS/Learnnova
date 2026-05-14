@extends('main')

@section('title', 'Jadwal')

@section('content')

<div class="content-wrapper">

    <!-- Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">

                <div class="col-sm-6">
                    <h1>Jadwal Kelas</h1>
                </div>

                <div class="col-sm-6 text-right">
                    <button class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Tambah Jadwal
                    </button>
                </div>

            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="content">
        <div class="container-fluid">

            <div class="card">

                <div class="card-header">
                    <h3 class="card-title">
                        Data Jadwal Pembelajaran
                    </h3>
                </div>

                <div class="card-body table-responsive p-0">

                    <table class="table table-hover text-nowrap">

                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Mata Pelajaran</th>
                                <th>Pengajar</th>
                                <th>Hari</th>
                                <th>Jam</th>
                                <th>Ruangan</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>

                            <tr>
                                <td>1</td>
                                <td>Pemrograman Web</td>
                                <td>Pak Budi</td>
                                <td>Senin</td>
                                <td>08:00 - 10:00</td>
                                <td>Lab Komputer</td>
                                <td>
                                    <span class="badge badge-success">
                                        Aktif
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <td>2</td>
                                <td>Basis Data</td>
                                <td>Bu Sinta</td>
                                <td>Selasa</td>
                                <td>10:00 - 12:00</td>
                                <td>Ruang A2</td>
                                <td>
                                    <span class="badge badge-warning">
                                        Pending
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <td>3</td>
                                <td>UI/UX Design</td>
                                <td>Pak Andi</td>
                                <td>Rabu</td>
                                <td>13:00 - 15:00</td>
                                <td>Lab Multimedia</td>
                                <td>
                                    <span class="badge badge-success">
                                        Aktif
                                    </span>
                                </td>
                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>
    </section>

</div>

@endsection