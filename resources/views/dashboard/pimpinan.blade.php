@extends('main')

@section('title', 'Dashboard Pimpinan')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold text-dark">Dashboard Analisis Akademik</h1>
                <p class="text-muted mb-0">Overview performa belajar pelajar dan statistik kelas keseluruhan.</p>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- 1. Stats Overview Cards -->
        <div class="row">
            <!-- Total Kelas -->
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box shadow-sm" style="border-radius: 12px;">
                    <span class="info-box-icon bg-indigo elevation-1" style="border-radius: 10px; color: white;"><i class="fas fa-school"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-muted">Total Kelas</span>
                        <span class="info-box-number h4 font-weight-bold mb-0">{{ $totalKelas }}</span>
                    </div>
                </div>
            </div>

            <!-- Rata-rata Keseluruhan -->
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box shadow-sm" style="border-radius: 12px;">
                    <span class="info-box-icon bg-info elevation-1" style="border-radius: 10px; color: white;"><i class="fas fa-chart-line"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-muted">Rata-rata Nilai</span>
                        <span class="info-box-number h4 font-weight-bold mb-0">{{ number_format($rataKeseluruhan, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Nilai Tertinggi -->
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box shadow-sm" style="border-radius: 12px;">
                    <span class="info-box-icon bg-success elevation-1" style="border-radius: 10px; color: white;"><i class="fas fa-trophy"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-muted">Nilai Tertinggi</span>
                        <span class="info-box-number h4 font-weight-bold mb-0">
                            {{ count($nilaiTertinggi) > 0 ? number_format($nilaiTertinggi[0]->score, 0) : '0' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Nilai Terendah -->
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box shadow-sm" style="border-radius: 12px;">
                    <span class="info-box-icon bg-danger elevation-1" style="border-radius: 10px; color: white;"><i class="fas fa-arrow-down"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-muted">Nilai Terendah</span>
                        <span class="info-box-number h4 font-weight-bold mb-0">
                            {{ count($nilaiTerendah) > 0 ? number_format($nilaiTerendah[0]->score, 0) : '0' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Chart Section -->
        <div class="row mt-3">
            <div class="col-md-8">
                <div class="card card-outline card-primary shadow-sm" style="border-radius: 15px;">
                    <div class="card-header bg-white border-0 pt-3 px-4">
                        <h5 class="card-title font-weight-bold mb-0"><i class="fas fa-chart-bar text-primary mr-2"></i> Perbandingan Rata-rata Nilai Per Kelas</h5>
                    </div>
                    <div class="card-body px-4 pb-4">
                        @if($rataRata->isEmpty())
                            <div class="text-center py-5">
                                <i class="far fa-chart-bar fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">Belum ada data nilai quiz yang tersedia.</p>
                            </div>
                        @else
                            <div style="position: relative; height: 320px;">
                                <canvas id="kelasAverageChart"></canvas>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Ringkasan Kelas Teraktif / Rata-rata Tabel -->
            <div class="col-md-4">
                <div class="card card-outline card-primary shadow-sm h-100" style="border-radius: 15px;">
                    <div class="card-header bg-white border-0 pt-3 px-4">
                        <h5 class="card-title font-weight-bold mb-0"><i class="fas fa-list-ol text-primary mr-2"></i> Peringkat Rata-rata Kelas</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr class="text-muted" style="font-size: 0.85rem;">
                                        <th class="border-0 px-4">Kelas</th>
                                        <th class="border-0 text-right px-4">Rata-rata</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rataRata as $kelas)
                                        <tr>
                                            <td class="px-4 font-weight-bold" style="font-size: 0.9rem; color: #374151;">
                                                {{ $kelas->title }}
                                            </td>
                                            <td class="text-right px-4">
                                                <span class="badge badge-pill badge-primary px-3 py-1 font-weight-bold" style="font-size: 0.85rem;">
                                                    {{ number_format($kelas->rata_rata, 1) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center py-4 text-muted">Belum ada kelas beraktifitas.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Top and Bottom Score Tables -->
        <div class="row mt-4">
            <!-- Top 10 Scores -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0" style="border-radius: 15px;">
                    <div class="card-header bg-white border-0 pt-3 px-4">
                        <h5 class="card-title font-weight-bold mb-0 text-success"><i class="fas fa-arrow-circle-up mr-2"></i> Top 10 Nilai Tertinggi (Keseluruhan)</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead>
                                    <tr class="text-muted" style="font-size: 0.85rem;">
                                        <th class="border-0 px-4">Nama Pelajar</th>
                                        <th class="border-0 px-3">Kelas</th>
                                        <th class="border-0 text-center px-4">Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($nilaiTertinggi as $item)
                                        <tr>
                                            <td class="px-4 font-weight-bold" style="font-size: 0.9rem; color: #374151;">{{ $item->student_name }}</td>
                                            <td class="px-3 text-muted" style="font-size: 0.85rem;">{{ $item->course_title }}</td>
                                            <td class="text-center px-4 font-weight-bold text-success">{{ number_format($item->score, 0) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">Belum ada nilai terinput.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom 10 Scores -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0" style="border-radius: 15px;">
                    <div class="card-header bg-white border-0 pt-3 px-4">
                        <h5 class="card-title font-weight-bold mb-0 text-danger"><i class="fas fa-arrow-circle-down mr-2"></i> Bottom 10 Nilai Terendah (Keseluruhan)</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead>
                                    <tr class="text-muted" style="font-size: 0.85rem;">
                                        <th class="border-0 px-4">Nama Pelajar</th>
                                        <th class="border-0 px-3">Kelas</th>
                                        <th class="border-0 text-center px-4">Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($nilaiTerendah as $item)
                                        <tr>
                                            <td class="px-4 font-weight-bold" style="font-size: 0.9rem; color: #374151;">{{ $item->student_name }}</td>
                                            <td class="px-3 text-muted" style="font-size: 0.85rem;">{{ $item->course_title }}</td>
                                            <td class="text-center px-4 font-weight-bold text-danger">{{ number_format($item->score, 0) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">Belum ada nilai terinput.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Class Breakdown Table Accordion style / Cards -->
        <div class="row mt-4 mb-5">
            <div class="col-12">
                <div class="card shadow-sm border-0" style="border-radius: 15px;">
                    <div class="card-header bg-white border-0 pt-3 px-4">
                        <h5 class="card-title font-weight-bold mb-0 text-dark"><i class="fas fa-list mr-2"></i> Detail Peringkat Nilai Per Kelas</h5>
                    </div>
                    <div class="card-body px-4 pb-4">
                        @if($nilaiPerKelas->isEmpty())
                            <div class="text-center py-4 text-muted">Belum ada detail per kelas.</div>
                        @else
                            <div class="row">
                                @foreach($nilaiPerKelas as $courseId => $attempts)
                                    <div class="col-md-6 mb-4">
                                        <div class="card border shadow-none" style="border-radius: 12px; overflow: hidden;">
                                            <div class="card-header bg-light py-2 px-3 d-flex align-items-center">

    <h6 class="font-weight-bold mb-0 text-primary flex-grow-1">
        {{ $attempts->first()->course_title }}
    </h6>

    <div id="length-{{ $courseId }}" class="ml-auto"></div>

</div>
</div>
                                            <div class="card-body p-0">
                                                <table id="table-{{ $courseId }}" class="table table-sm table-hover kelasTable">
                                                    <thead>
                                                        <tr class="text-muted" style="font-size: 0.8rem;">
                                                            <th class="px-3">#</th>
                                                            <th>Nama Pelajar</th>
                                                            <th class="text-center px-3">Nilai</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($attempts as $index => $item)
                                                            <tr>
                                                                <td class="px-3 text-muted">{{ $index + 1 }}</td>
                                                                <td class="font-weight-bold" style="font-size: 0.85rem;">{{ $item->student_name }}</td>
                                                                <td class="text-center px-3 font-weight-bold">{{ number_format($item->score, 0) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
.kelasTable_wrapper .dataTables_length{
    margin:10px;
}

.kelasTable_wrapper .dataTables_filter{
    display:none;
}

.kelasTable_wrapper .dataTables_info{
    display:none;
}

.kelasTable_wrapper .dataTables_paginate{
    margin-top:10px;
}

.kelas-scroll{
    overflow-y:auto;
}

.dataTables_scrollHeadInner,
.dataTables_scrollHeadInner table{
    width:100% !important;
}

.dataTables_length{
    margin:0;
}

.dataTables_length label{
    margin-bottom:0;
    font-weight:600;
    font-size:14px;
}

.dataTables_paginate{
    margin-top:10px;
}

</style>
@endpush


@push('scripts')
<script>
$(function () {
    @if(!$rataRata->isEmpty())
        let labels = {!! json_encode($rataRata->pluck('title')) !!};
        let data = {!! json_encode($rataRata->pluck('rata_rata')) !!};

        let ctx = document.getElementById('kelasAverageChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Rata-rata Nilai',
                    data: data,
                    backgroundColor: 'rgba(79, 70, 229, 0.75)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 1.5,
                    borderRadius: 8,
                    barPercentage: 0.5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            max: 100,
                            fontColor: '#4b5563',
                            fontSize: 11
                        },
                        gridLines: {
                            color: '#e5e7eb',
                            zeroLineColor: '#e5e7eb'
                        }
                    }],
                    xAxes: [{
                        ticks: {
                            fontColor: '#4b5563',
                            fontSize: 11
                        },
                        gridLines: {
                            display: false
                        }
                    }]
                }
            }
        });
    @endif
});

$('.kelasTable').each(function () {

    let table = $(this).DataTable({

        pageLength: 10,

        lengthMenu: [
            [10,25,50,100,-1],
            [10,25,50,100,"All"]
        ],

        searching:false,
        ordering:false,
        info:false,

        paging:true,

        scrollY:'350px',

        scrollCollapse:true,

        language:{
            lengthMenu:"Show _MENU_"
        }

    });

    // ===========================
    // PINDAHKAN SHOW KE HEADER
    // ===========================

    let id = $(this).attr('id').replace('table-','');

    $('#length-' + id).append(
        $(table.table().container()).find('.dataTables_length')
    );

    // ===========================
    // SCROLL JIKA >10
    // ===========================

    function updateScroll(){

        let len = table.page.len();

        let wrapper = $(table.table().container())
            .find('.dataTables_scrollBody');

        if(len == 10){

            wrapper.css({
                "max-height":"",
                "overflow-y":"hidden"
            });

        }else{

            wrapper.css({
                "max-height":"350px",
                "overflow-y":"auto"
            });

        }

    }

    table.on('length.dt', updateScroll);

    updateScroll();

});

</script>
@endpush
