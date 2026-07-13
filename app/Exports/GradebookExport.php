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
    protected ?int    $courseId;

    public function __construct(?User $student = null, ?Course $course = null, $students = null, ?int $courseId = null)
    {
        $this->student  = $student;
        $this->course   = $course;
        $this->students = $students;
        $this->courseId = $courseId;
    }

    public function collection(): Collection
    {
        if ($this->student) {
            return $this->studentCollection();
        }
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

        $headings = ['No', 'Nama Pelajar', 'Email'];

        if ($this->course) {
            $this->course->load(['assignments', 'quizzes.questions']);
            foreach ($this->course->assignments as $assignment) {
                $headings[] = \Str::limit($assignment->title, 15) . ' (Tugas)';
            }
            foreach ($this->course->quizzes as $quiz) {
                $headings[] = \Str::limit($quiz->title, 15) . ' (Kuis)';
            }
        }

        $headings[] = 'Rata-rata Tugas (%)';
        $headings[] = 'Rata-rata Kuis (%)';
        $headings[] = 'Nilai Akhir (%)';

        return $headings;
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

        // Filter berdasarkan course_id jika ada
        if ($this->courseId) {
            $courses = $courses->where('id', $this->courseId);
        }

        foreach ($courses as $course) {
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
                    'kelas'  => $course->title,
                    'jenis'  => 'Tugas',
                    'judul'  => $assignment->title,
                    'nilai'  => $score ?? '-',
                    'max'    => $maxScore,
                    'persen' => $persen,
                    'status' => $submission?->status ?? 'Belum Dikumpulkan',
                ]);
            }

            foreach ($course->quizzes as $quiz) {
                $attempt = QuizAttempt::where('quiz_id', $quiz->id)
                    ->where('student_id', $this->student->id)
                    ->orderBy('score', 'desc')
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
                    ->where('student_id', $student->id)
                    ->first();
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
                $attempt = QuizAttempt::where('quiz_id', $quiz->id)
                    ->where('student_id', $student->id)
                    ->orderBy('score', 'desc')
                    ->first();
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
}
