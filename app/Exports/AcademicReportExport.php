<?php

namespace App\Exports;

use App\Models\AssignmentSubmission;
use App\Models\QuizAttempt;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class AcademicReportExport implements WithMultipleSheets
{
    protected $courses;

    public function __construct($courses)
    {
        $this->courses = $courses;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Sheet 1 — Ringkasan
        $sheets[] = new AcademicReportSummarySheet($this->courses);

        // Sheet per Course
        foreach ($this->courses as $course) {
            $sheets[] = new AcademicReportCourseSheet($course);
        }

        return $sheets;
    }
}

// ── Sheet Ringkasan ───────────────────────────────────────────────
class AcademicReportSummarySheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected $courses;

    public function __construct($courses)
    {
        $this->courses = $courses;
    }

    public function title(): string
    {
        return 'Ringkasan';
    }

    public function headings(): array
    {
        return ['No', 'Nama Kelas', 'Jumlah Pelajar', 'Rata-rata Tugas (%)', 'Rata-rata Kuis (%)', 'Rata-rata Nilai Akhir (%)'];
    }

    public function collection(): Collection
    {
        $rows = collect();

        foreach ($this->courses as $i => $course) {
            $courseStudents = User::whereHas(
                'enrollments',
                fn($q) =>
                $q->where('course_id', $course->id)->where('status', 'active')
            )->get();

            $assignmentAvgs = [];
            $quizAvgs       = [];
            $finalScores    = [];

            foreach ($courseStudents as $student) {
                // Rata-rata tugas per pelajar
                $totalAssignmentScore = 0;
                $totalAssignmentMax   = 0;
                foreach ($course->assignments as $assignment) {
                    $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
                        ->where('student_id', $student->id)->first();
                    if ($submission?->score !== null) {
                        $totalAssignmentScore += $submission->score;
                        $totalAssignmentMax   += $assignment->max_score;
                    }
                }
                $avgAssignment = $totalAssignmentMax > 0
                    ? round(($totalAssignmentScore / $totalAssignmentMax) * 100, 2)
                    : null;
                if ($avgAssignment !== null) $assignmentAvgs[] = $avgAssignment;

                // Rata-rata kuis per pelajar
                $totalQuizScore = 0;
                $totalQuizMax   = 0;
                foreach ($course->quizzes as $quiz) {
                    $attempt  = QuizAttempt::where('quiz_id', $quiz->id)->where('student_id', $student->id)->latest()->first();
                    $maxScore = $quiz->questions->sum('points');
                    if ($attempt?->score !== null) {
                        $totalQuizScore += $attempt->score;
                        $totalQuizMax   += $maxScore;
                    }
                }
                $avgQuiz = $totalQuizMax > 0
                    ? round(($totalQuizScore / $totalQuizMax) * 100, 2)
                    : null;
                if ($avgQuiz !== null) $quizAvgs[] = $avgQuiz;

                // Nilai akhir
                $assignmentWeight = $course->assignment_weight / 100;
                $quizWeight       = $course->quiz_weight / 100;
                if ($avgAssignment !== null && $avgQuiz !== null) {
                    $finalScores[] = round(($avgAssignment * $assignmentWeight) + ($avgQuiz * $quizWeight), 2);
                } elseif ($avgAssignment !== null) {
                    $finalScores[] = $avgAssignment;
                } elseif ($avgQuiz !== null) {
                    $finalScores[] = $avgQuiz;
                }
            }

            $rows->push([
                'no'             => $i + 1,
                'course'         => $course->title,
                'total_students' => $courseStudents->count(),
                'avg_assignment' => count($assignmentAvgs) > 0 ? round(array_sum($assignmentAvgs) / count($assignmentAvgs), 2) . '%' : '-',
                'avg_quiz'       => count($quizAvgs) > 0 ? round(array_sum($quizAvgs) / count($quizAvgs), 2) . '%' : '-',
                'avg_final'      => count($finalScores) > 0 ? round(array_sum($finalScores) / count($finalScores), 2) . '%' : '-',
            ]);
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF1565C0']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }
}

// ── Sheet Per Course ──────────────────────────────────────────────
class AcademicReportCourseSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected $course;

    public function __construct($course)
    {
        $this->course = $course;
    }

    public function title(): string
    {
        return \Str::limit($this->course->title, 28);
    }

    public function headings(): array
    {
        $headings = ['No', 'Nama Pelajar', 'Email'];

        foreach ($this->course->assignments as $assignment) {
            $headings[] = \Str::limit($assignment->title, 15) . ' (Tugas)';
        }

        foreach ($this->course->quizzes as $quiz) {
            $headings[] = \Str::limit($quiz->title, 15) . ' (Kuis)';
        }

        $headings[] = 'Rata-rata Tugas (%)';
        $headings[] = 'Rata-rata Kuis (%)';
        $headings[] = 'Nilai Akhir (%)';

        return $headings;
    }

    public function collection(): Collection
    {
        $rows           = collect();
        $assignmentWeight = $this->course->assignment_weight / 100;
        $quizWeight       = $this->course->quiz_weight / 100;

        $students = User::whereHas(
            'enrollments',
            fn($q) =>
            $q->where('course_id', $this->course->id)->where('status', 'active')
        )->get();

        foreach ($students as $i => $student) {
            $row = [
                'no'    => $i + 1,
                'nama'  => $student->name,
                'email' => $student->email,
            ];

            // Nilai per tugas
            $totalAssignmentScore = 0;
            $totalAssignmentMax   = 0;
            foreach ($this->course->assignments as $assignment) {
                $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
                    ->where('student_id', $student->id)->first();
                $score = $submission?->score;
                $row[] = $score ?? '-';
                if ($score !== null) {
                    $totalAssignmentScore += $score;
                    $totalAssignmentMax   += $assignment->max_score;
                }
            }

            // Nilai per kuis
            $totalQuizScore = 0;
            $totalQuizMax   = 0;
            foreach ($this->course->quizzes as $quiz) {
                $attempt  = QuizAttempt::where('quiz_id', $quiz->id)->where('student_id', $student->id)->latest()->first();
                $maxScore = $quiz->questions->sum('points');
                $score    = $attempt?->score;
                $row[]    = $score ?? '-';
                if ($score !== null) {
                    $totalQuizScore += $score;
                    $totalQuizMax   += $maxScore;
                }
            }

            $avgAssignment = $totalAssignmentMax > 0
                ? round(($totalAssignmentScore / $totalAssignmentMax) * 100, 2)
                : null;

            $avgQuiz = $totalQuizMax > 0
                ? round(($totalQuizScore / $totalQuizMax) * 100, 2)
                : null;

            $finalScore = null;
            if ($avgAssignment !== null && $avgQuiz !== null) {
                $finalScore = round(($avgAssignment * $assignmentWeight) + ($avgQuiz * $quizWeight), 2);
            } elseif ($avgAssignment !== null) {
                $finalScore = $avgAssignment;
            } elseif ($avgQuiz !== null) {
                $finalScore = $avgQuiz;
            }

            $row[] = $avgAssignment !== null ? $avgAssignment . '%' : '-';
            $row[] = $avgQuiz !== null ? $avgQuiz . '%' : '-';
            $row[] = $finalScore !== null ? $finalScore . '%' : '-';

            $rows->push($row);
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF1976D2']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }
}
