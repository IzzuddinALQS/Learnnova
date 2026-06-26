<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Nilai — {{ $student->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }

        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #2196F3; padding-bottom: 15px; }
        .header h2 { font-size: 18px; color: #2196F3; margin-bottom: 5px; }
        .header p { font-size: 11px; color: #666; }

        .info-box { background: #f5f5f5; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .info-box table { width: 100%; }
        .info-box td { padding: 3px 8px; }
        .info-box td:first-child { font-weight: bold; width: 30%; }

        .course-title { background: #2196F3; color: white; padding: 8px 12px; font-weight: bold; font-size: 13px; margin-top: 20px; margin-bottom: 10px; border-radius: 4px; }

        table.nilai { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.nilai th { background: #e3f2fd; color: #1565C0; padding: 6px 8px; text-align: left; border: 1px solid #ddd; font-size: 11px; }
        table.nilai td { padding: 5px 8px; border: 1px solid #ddd; font-size: 11px; }
        table.nilai tr:nth-child(even) { background: #fafafa; }
        table.nilai tfoot td { background: #e3f2fd; font-weight: bold; }

        .final-score { margin-top: 8px; padding: 8px 12px; border-radius: 4px; font-weight: bold; }
        .final-score.baik { background: #e8f5e9; color: #2e7d32; border-left: 4px solid #4caf50; }
        .final-score.cukup { background: #fff8e1; color: #f57f17; border-left: 4px solid #ffc107; }
        .final-score.kurang { background: #ffebee; color: #c62828; border-left: 4px solid #f44336; }
        .final-score.none { background: #f5f5f5; color: #999; border-left: 4px solid #ddd; }

        .footer { margin-top: 30px; border-top: 1px solid #ddd; padding-top: 10px; font-size: 10px; color: #999; text-align: center; }

        .badge { padding: 2px 6px; border-radius: 3px; font-size: 10px; }
        .badge-success { background: #4caf50; color: white; }
        .badge-warning { background: #ffc107; color: #333; }
        .badge-danger { background: #f44336; color: white; }
        .badge-info { background: #2196F3; color: white; }
        .badge-secondary { background: #9e9e9e; color: white; }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <h2>LAPORAN NILAI PELAJAR</h2>
        <p>Dicetak pada {{ now()->format('d M Y, H:i') }} WIB</p>
    </div>

    {{-- Info Pelajar --}}
    <div class="info-box">
        <table>
            <tr>
                <td>Nama</td>
                <td>: {{ $student->name }}</td>
            </tr>
            <tr>
                <td>Email</td>
                <td>: {{ $student->email }}</td>
            </tr>
            <tr>
                <td>Total Kelas</td>
                <td>: {{ count($gradebook) }} Kelas</td>
            </tr>
        </table>
    </div>

    {{-- Per Course --}}
    @foreach($gradebook as $item)
        <div class="course-title">
            {{ $item['course']->title }}
            &nbsp;|&nbsp; Bobot Tugas: {{ $item['assignment_weight'] }}%
            &nbsp;|&nbsp; Bobot Kuis: {{ $item['quiz_weight'] }}%
        </div>

        {{-- Tugas --}}
        @if(count($item['assignments']) > 0)
            <p style="font-weight:bold; margin-bottom:5px;">Tugas</p>
            <table class="nilai">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Judul Tugas</th>
                        <th>Nilai</th>
                        <th>Maks</th>
                        <th>%</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($item['assignments'] as $i => $assignment)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $assignment['title'] }}</td>
                            <td>{{ $assignment['score'] ?? '-' }}</td>
                            <td>{{ $assignment['max'] }}</td>
                            <td>
                                @if($assignment['score'] !== null && $assignment['max'] > 0)
                                    {{ round(($assignment['score'] / $assignment['max']) * 100, 1) }}%
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @switch($assignment['status'])
                                    @case('graded') <span class="badge badge-success">Dinilai</span> @break
                                    @case('submitted') <span class="badge badge-info">Dikumpulkan</span> @break
                                    @case('returned') <span class="badge badge-warning">Dikembalikan</span> @break
                                    @default <span class="badge badge-secondary">Belum</span>
                                @endswitch
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4">Rata-rata Tugas</td>
                        <td colspan="2">{{ $item['avg_assignment'] !== null ? $item['avg_assignment'] . '%' : '-' }}</td>
                    </tr>
                </tfoot>
            </table>
        @endif

        {{-- Kuis --}}
        @if(count($item['quizzes']) > 0)
            <p style="font-weight:bold; margin: 10px 0 5px;">Kuis</p>
            <table class="nilai">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Judul Kuis</th>
                        <th>Nilai</th>
                        <th>Maks</th>
                        <th>%</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($item['quizzes'] as $i => $quiz)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $quiz['title'] }}</td>
                            <td>{{ $quiz['score'] ?? '-' }}</td>
                            <td>{{ $quiz['max'] }}</td>
                            <td>
                                @if($quiz['score'] !== null && $quiz['max'] > 0)
                                    {{ round(($quiz['score'] / $quiz['max']) * 100, 1) }}%
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($quiz['is_passed'] === null)
                                    <span class="badge badge-secondary">Belum</span>
                                @elseif($quiz['is_passed'])
                                    <span class="badge badge-success">Lulus</span>
                                @else
                                    <span class="badge badge-danger">Tidak Lulus</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4">Rata-rata Kuis</td>
                        <td colspan="2">{{ $item['avg_quiz'] !== null ? $item['avg_quiz'] . '%' : '-' }}</td>
                    </tr>
                </tfoot>
            </table>
        @endif

        {{-- Nilai Akhir --}}
        @php
            $fs    = $item['final_score'];
            $class = $fs === null ? 'none' : ($fs >= 75 ? 'baik' : ($fs >= 60 ? 'cukup' : 'kurang'));
        @endphp
        <div class="final-score {{ $class }}">
            Nilai Akhir:
            @if($fs !== null)
                {{ $fs }}%
                @if($fs >= 75) — Baik
                @elseif($fs >= 60) — Cukup
                @else — Perlu Peningkatan
                @endif
            @else
                Belum ada nilai
            @endif
        </div>

    @endforeach

    <div class="footer">
        Dokumen ini digenerate otomatis oleh sistem E-Learning &bull; {{ now()->format('d M Y') }}
    </div>

</body>
</html>