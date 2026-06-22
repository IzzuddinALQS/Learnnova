<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Akademik</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; }

        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1565C0; padding-bottom: 15px; }
        .header h2 { font-size: 16px; color: #1565C0; margin-bottom: 5px; }
        .header p { font-size: 10px; color: #666; }

        .summary-box { margin-bottom: 20px; }
        .summary-box table { width: 100%; border-collapse: collapse; }
        .summary-box td { padding: 8px 12px; border: 1px solid #ddd; text-align: center; }
        .summary-box th { padding: 8px 12px; background: #1565C0; color: white; border: 1px solid #1565C0; text-align: center; }
        .summary-box .value { font-size: 20px; font-weight: bold; color: #1565C0; }

        .section-title { background: #1565C0; color: white; padding: 7px 12px; font-weight: bold; font-size: 12px; margin-top: 20px; margin-bottom: 8px; border-radius: 3px; }

        table.rekap { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        table.rekap th { background: #1976D2; color: white; padding: 5px 6px; text-align: center; border: 1px solid #ddd; font-size: 9px; }
        table.rekap td { padding: 4px 6px; border: 1px solid #ddd; font-size: 9px; text-align: center; }
        table.rekap td.nama { text-align: left; font-weight: bold; }
        table.rekap tr:nth-child(even) { background: #f5f5f5; }

        .badge { padding: 2px 5px; border-radius: 3px; font-size: 9px; }
        .badge-success { background: #4caf50; color: white; }
        .badge-warning { background: #ffc107; color: #333; }
        .badge-danger { background: #f44336; color: white; }

        .course-info { font-size: 9px; color: #666; margin-bottom: 5px; }

        .page-break { page-break-after: always; }

        .footer { margin-top: 20px; border-top: 1px solid #ddd; padding-top: 8px; font-size: 9px; color: #999; text-align: center; }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <h2>LAPORAN AKADEMIK</h2>
        <p>Dicetak pada {{ now()->format('d M Y, H:i') }} WIB</p>
    </div>

    {{-- Statistik Ringkasan --}}
    <div class="summary-box">
        <table>
            <thead>
                <tr>
                    <th>Total Pelajar</th>
                    <th>Total Kelas</th>
                    <th>Total Tugas</th>
                    <th>Total Kuis</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><div class="value">{{ $summary['total_students'] }}</div></td>
                    <td><div class="value">{{ $summary['total_courses'] }}</div></td>
                    <td><div class="value">{{ $summary['total_assignments'] }}</div></td>
                    <td><div class="value">{{ $summary['total_quizzes'] }}</div></td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Ringkasan Per Course --}}
    <div class="section-title">Ringkasan Per Kelas</div>
    <table class="rekap">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th>Nama Kelas</th>
                <th width="12%">Pelajar</th>
                <th width="15%">Rata-rata Tugas</th>
                <th width="15%">Rata-rata Kuis</th>
                <th width="15%">Nilai Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="nama">{{ $item['course']->title }}</td>
                    <td>{{ $item['total_students'] }} pelajar</td>
                    <td>{{ $item['avg_assignment'] !== null ? $item['avg_assignment'] . '%' : '-' }}</td>
                    <td>{{ $item['avg_quiz'] !== null ? $item['avg_quiz'] . '%' : '-' }}</td>
                    <td>
                        @if($item['avg_final'] !== null)
                            <span class="badge
                                @if($item['avg_final'] >= 75) badge-success
                                @elseif($item['avg_final'] >= 60) badge-warning
                                @else badge-danger
                                @endif">
                                {{ $item['avg_final'] }}%
                            </span>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Detail Per Course --}}
    @foreach($report as $item)
        <div class="page-break"></div>

        <div class="section-title">
            {{ $item['course']->title }}
        </div>
        <div class="course-info">
            Bobot Tugas: {{ $item['course']->assignment_weight }}% &nbsp;|&nbsp;
            Bobot Kuis: {{ $item['course']->quiz_weight }}% &nbsp;|&nbsp;
            Jumlah Pelajar: {{ $item['total_students'] }}
        </div>

        <table class="rekap">
            <thead>
                <tr>
                    <th width="4%">#</th>
                    <th style="text-align:left;">Nama Pelajar</th>
                    @foreach($item['course']->assignments as $assignment)
                        <th>{{ Str::limit($assignment->title, 10) }}</th>
                    @endforeach
                    @foreach($item['course']->quizzes as $quiz)
                        <th>{{ Str::limit($quiz->title, 10) }}</th>
                    @endforeach
                    <th>Rata-rata Tugas</th>
                    <th>Rata-rata Kuis</th>
                    <th>Nilai Akhir</th>
                </tr>
            </thead>
            <tbody>
                @forelse($item['gradebook'] as $i => $row)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="nama">{{ $row['student']->name }}</td>

                        @foreach($row['assignment_scores'] as $score)
                            <td>{{ $score ?? '-' }}</td>
                        @endforeach

                        @foreach($row['quiz_scores'] as $score)
                            <td>{{ $score ?? '-' }}</td>
                        @endforeach

                        <td>{{ $row['avg_assignment'] !== null ? $row['avg_assignment'] . '%' : '-' }}</td>
                        <td>{{ $row['avg_quiz'] !== null ? $row['avg_quiz'] . '%' : '-' }}</td>
                        <td>
                            @if($row['final_score'] !== null)
                                <span class="badge
                                    @if($row['final_score'] >= 75) badge-success
                                    @elseif($row['final_score'] >= 60) badge-warning
                                    @else badge-danger
                                    @endif">
                                    {{ $row['final_score'] }}%
                                </span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="20" style="text-align:center; color:#999; padding:8px;">
                            Belum ada pelajar terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endforeach

    <div class="footer">
        Dokumen ini digenerate otomatis oleh sistem E-Learning &bull; {{ now()->format('d M Y') }}
    </div>

</body>
</html>