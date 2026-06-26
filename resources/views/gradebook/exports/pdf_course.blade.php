<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Nilai — {{ $course->title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; }

        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #2196F3; padding-bottom: 15px; }
        .header h2 { font-size: 16px; color: #2196F3; margin-bottom: 5px; }
        .header p { font-size: 10px; color: #666; }

        .info-box { background: #f5f5f5; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .info-box table { width: 100%; }
        .info-box td { padding: 3px 8px; }
        .info-box td:first-child { font-weight: bold; width: 30%; }

        table.rekap { width: 100%; border-collapse: collapse; }
        table.rekap th { background: #1565C0; color: white; padding: 6px 8px; text-align: center; border: 1px solid #ddd; font-size: 10px; }
        table.rekap td { padding: 5px 8px; border: 1px solid #ddd; font-size: 10px; text-align: center; }
        table.rekap td:nth-child(2) { text-align: left; }
        table.rekap tr:nth-child(even) { background: #fafafa; }

        .badge { padding: 2px 5px; border-radius: 3px; font-size: 9px; }
        .badge-success { background: #4caf50; color: white; }
        .badge-warning { background: #ffc107; color: #333; }
        .badge-danger { background: #f44336; color: white; }

        .footer { margin-top: 20px; border-top: 1px solid #ddd; padding-top: 8px; font-size: 9px; color: #999; text-align: center; }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <h2>REKAP NILAI PELAJAR</h2>
        <p>{{ $course->title }} &bull; Dicetak pada {{ now()->format('d M Y, H:i') }} WIB</p>
    </div>

    {{-- Info Course --}}
    <div class="info-box">
        <table>
            <tr>
                <td>Kelas</td>
                <td>: {{ $course->title }}</td>
            </tr>
            <tr>
                <td>Bobot Penilaian</td>
                <td>: Tugas {{ $course->assignment_weight }}% | Kuis {{ $course->quiz_weight }}%</td>
            </tr>
            <tr>
                <td>Jumlah Pelajar</td>
                <td>: {{ $students->count() }} Pelajar</td>
            </tr>
        </table>
    </div>

    {{-- Tabel Rekap --}}
    <table class="rekap">
        <thead>
            <tr>
                <th>#</th>
                <th style="text-align:left;">Nama Pelajar</th>
                @foreach($course->assignments as $assignment)
                    <th>{{ Str::limit($assignment->title, 12) }}</th>
                @endforeach
                @foreach($course->quizzes as $quiz)
                    <th>{{ Str::limit($quiz->title, 12) }}</th>
                @endforeach
                <th>Rata-rata Tugas</th>
                <th>Rata-rata Kuis</th>
                <th>Nilai Akhir</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                @foreach($course->assignments as $assignment)
                    <th style="background:#1976D2; font-size:9px;">Tugas</th>
                @endforeach
                @foreach($course->quizzes as $quiz)
                    <th style="background:#0288D1; font-size:9px;">Kuis</th>
                @endforeach
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($gradebook as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="text-align:left; font-weight:bold;">{{ $item['student']->name }}</td>

                    @foreach($item['assignment_scores'] as $score)
                        <td>{{ $score ?? '-' }}</td>
                    @endforeach

                    @foreach($item['quiz_scores'] as $score)
                        <td>{{ $score ?? '-' }}</td>
                    @endforeach

                    <td>{{ $item['avg_assignment'] !== null ? $item['avg_assignment'] . '%' : '-' }}</td>
                    <td>{{ $item['avg_quiz'] !== null ? $item['avg_quiz'] . '%' : '-' }}</td>
                    <td>
                        @if($item['final_score'] !== null)
                            <span class="badge
                                @if($item['final_score'] >= 75) badge-success
                                @elseif($item['final_score'] >= 60) badge-warning
                                @else badge-danger
                                @endif">
                                {{ $item['final_score'] }}%
                            </span>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini digenerate otomatis oleh sistem E-Learning &bull; {{ now()->format('d M Y') }}
    </div>

</body>
</html>