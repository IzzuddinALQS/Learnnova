<?php

namespace App\Exports;

use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\QuizAttempt;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class GradebookExport implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected ?User   $student;
    protected ?Course $course;
    protected $students;

    public function __construct(?User $student = null, ?Course $course = null, $students = null)
    {
        $this->student  = $student;
        $this->course   = $course;
        $this->students = $students;
    }

    public function collection(): Collection
    {
        // Export untuk pelajar (nilai sendiri)
        if ($this->student) {
            return $this->studentCollection();
        }

        // Export untuk pengajar (semua pelajar di course)
        return $this->courseCollection();
    }

    public function headings(): array
    {
        if ($this->student) {
            return [
                'Kelas',
                'Jenis',
                'Judul',
                'Nilai',
                'Nilai Maks',
                'Persentase',
                'Status',
            ];
        }

        return [
            'No',
            'Nama Pelajar',
            'Email',
            'Rata-rata Tugas (%)',
            'Rata-rata Kuis (%)',
            'Nilai Akhir (%)',
        ];
    }

    public function title(): string
    {
        if ($this->student) {
            return 'Nilai ' . $this->student->name;
        }

        return 'Rekap Nilai ' . ($this->course->title ?? '');
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF2196F3']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    // ── Data export pelajar ───────────────────────────────────────────
    private function studentCollection(): Collection
    {
        $rows    = collect();
        $courses = $this->student->enrolledCourses()->with(['assignments', 'quizzes.questions'])->get();

        foreach ($courses as $course) {
            // Tugas
            foreach ($course->assignments as $assignment) {
                $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
                    ->where('student_id', $this->student->id)
                    ->first();

                $score    = $submission?->score;
                $maxScore = $assignment->max_score;
                $persen   = ($score !== null && $maxScore > 0)
                    ? round(($score / $maxScore) * 100, 2) . '%'
                    : '-';

                $rows->push([
                    'kelas'   => $course->title,
                    'jenis'   => 'Tugas',
                    'judul'   => $assignment->title,
                    'nilai'   => $score ?? '-',
                    'max'     => $maxScore,
                    'persen'  => $persen,
                    'status'  => $submission?->status ?? 'Belum Dikumpulkan',
                ]);
            }

            // Kuis
            foreach ($course->quizzes as $quiz) {
                $attempt  = QuizAttempt::where('quiz_id', $quiz->id)
                    ->where('student_id', $this->student->id)
                    ->latest()
                    ->first();

                $maxScore = $quiz->questions->sum('points');
                $score    = $attempt?->score;
                $persen   = ($score !== null && $maxScore > 0)
                    ? round(($score / $maxScore) * 100, 2) . '%'
                    : '-';

                $rows->push([
                    'kelas'  => $course->title,
                    'jenis'  => 'Kuis',
                    'judul'  => $quiz->title,
                    'nilai'  => $score ?? '-',
                    'max'    => $maxScore,
                    'persen' => $persen,
                    'status' => $attempt
                        ? ($attempt->is_passed ? 'Lulus' : 'Tidak Lulus')
                        : 'Belum Dikerjakan',
                ]);
            }
        }

        return $rows;
    }

    // ── Data export pengajar ──────────────────────────────────────────
    private function courseCollection(): Collection
    {
        $rows             = collect();
        $assignmentWeight = $this->course->assignment_weight / 100;
        $quizWeight       = $this->course->quiz_weight / 100;
        $this->course->load(['assignments', 'quizzes.questions']);

        foreach ($this->students as $i => $student) {
            // Rata-rata tugas
            $totalAssignmentScore = 0;
            $totalAssignmentMax   = 0;

            foreach ($this->course->assignments as $assignment) {
                $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
                    ->where('student_id', $student->id)
                    ->first();

                if ($submission?->score !== null) {
                    $totalAssignmentScore += $submission->score;
                    $totalAssignmentMax   += $assignment->max_score;
                }
            }

            $avgAssignment = $totalAssignmentMax > 0
                ? round(($totalAssignmentScore / $totalAssignmentMax) * 100, 2)
                : '-';

            // Rata-rata kuis
            $totalQuizScore = 0;
            $totalQuizMax   = 0;

            foreach ($this->course->quizzes as $quiz) {
                $attempt  = QuizAttempt::where('quiz_id', $quiz->id)
                    ->where('student_id', $student->id)
                    ->latest()
                    ->first();

                $maxScore = $quiz->questions->sum('points');

                if ($attempt?->score !== null) {
                    $totalQuizScore += $attempt->score;
                    $totalQuizMax   += $maxScore;
                }
            }

            $avgQuiz = $totalQuizMax > 0
                ? round(($totalQuizScore / $totalQuizMax) * 100, 2)
                : '-';

            // Nilai akhir
            $finalScore = '-';
            if ($avgAssignment !== '-' && $avgQuiz !== '-') {
                $finalScore = round(($avgAssignment * $assignmentWeight) + ($avgQuiz * $quizWeight), 2);
            } elseif ($avgAssignment !== '-') {
                $finalScore = $avgAssignment;
            } elseif ($avgQuiz !== '-') {
                $finalScore = $avgQuiz;
            }

            $rows->push([
                'no'             => $i + 1,
                'nama'           => $student->name,
                'email'          => $student->email,
                'avg_assignment' => $avgAssignment !== '-' ? $avgAssignment . '%' : '-',
                'avg_quiz'       => $avgQuiz !== '-' ? $avgQuiz . '%' : '-',
                'final_score'    => $finalScore !== '-' ? $finalScore . '%' : '-',
            ]);
        }

        return $rows;
    }
}
