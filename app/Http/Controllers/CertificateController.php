<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\CertificateSigner;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\MaterialProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\ActivityLog;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->hasPermission('certificates.manage')) {
            $courses = Course::whereHas('enrollments')
                ->orderBy('title')
                ->get();

            $selectedCourseId = $request->integer('course_id') ?: $courses->first()?->id;
            $selectedCourse = $selectedCourseId
                ? Course::with(['certificateSigner', 'modules.materials', 'enrollments.student'])->find($selectedCourseId)
                : null;

            $rows = collect();

            if ($selectedCourse) {
                $certificates = Certificate::where('course_id', $selectedCourse->id)
                    ->get()
                    ->keyBy('student_id');

                $rows = $selectedCourse->enrollments
                    ->sortBy(fn($enrollment) => $enrollment->student?->name)
                    ->map(function ($enrollment) use ($selectedCourse, $certificates) {
                        $progress = $this->getCompletionSummary($selectedCourse, $enrollment->student_id);

                        return [
                            'enrollment' => $enrollment,
                            'student' => $enrollment->student,
                            'certificate' => $certificates->get($enrollment->student_id),
                            'total_materials' => $progress['total_materials'],
                            'completed_materials' => $progress['completed_materials'],
                            'progress_percent' => $progress['progress_percent'],
                            'is_eligible' => $progress['is_eligible'],
                        ];
                    })
                    ->values();
            }

            return view('certificates.index', compact('courses', 'selectedCourse', 'selectedCourseId', 'rows'));
        }

        $certificates = Certificate::with(['course', 'student'])
            ->where('student_id', $user->id)
            ->latest('issued_at')
            ->get();

        return view('certificates.my', compact('certificates'));
    }

    public function issue(Request $request)
    {
        if (!Auth::user()->hasPermission('certificates.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'student_id' => 'required|exists:users,id',
        ]);

        $course = Course::with('modules.materials')->findOrFail($validated['course_id']);
        $signer = CertificateSigner::where('course_id', $course->id)
            ->where('is_active', true)
            ->first();

        if (!$signer) {
            return response()->json([
                'message' => 'Atur penandatangan dan upload TTD terlebih dahulu sebelum menerbitkan sertifikat.',
            ], 422);
        }

        $enrollment = Enrollment::where('course_id', $course->id)
            ->where('student_id', $validated['student_id'])
            ->firstOrFail();

        $progress = $this->getCompletionSummary($course, $enrollment->student_id);

        if (!$progress['is_eligible']) {
            return response()->json([
                'message' => 'Sertifikat belum bisa diterbitkan karena progress materi belum 100%.',
            ], 422);
        }

        if ($enrollment->status !== 'completed') {
            $enrollment->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }

        $certificate = Certificate::firstOrCreate(
            [
                'course_id' => $course->id,
                'student_id' => $enrollment->student_id,
            ],
            [
                'certificate_no' => $this->generateCertificateNo($course->id),
                'certificate_signer_id' => $signer->id,
                'signer_name' => $signer->name,
                'signer_position' => $signer->position,
                'signature_path' => $signer->signature_path,
                'issued_at' => now(),
            ]
        );

        $studentName = $enrollment->student->name ?? 'Pelajar';
        ActivityLog::log(
            "Menerbitkan sertifikat dengan nomor {$certificate->certificate_no} untuk pelajar {$studentName} pada kelas {$course->title}",
            $certificate,
            [
                'certificate_no' => $certificate->certificate_no,
                'student_id' => $certificate->student_id,
                'student_name' => $studentName,
                'course_id' => $course->id,
                'course_title' => $course->title,
            ],
            'certificate'
        );

        return response()->json([
            'message' => 'Sertifikat berhasil diterbitkan.',
            'print_url' => route('certificates.print', $certificate->id),
        ]);
    }

    public function saveSigner(Request $request)
    {
        if (!Auth::user()->hasPermission('certificates.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'signature' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $signer = CertificateSigner::where('course_id', $validated['course_id'])->first();

        if (!$signer && !$request->hasFile('signature')) {
            return response()->json([
                'message' => 'Upload gambar TTD wajib diisi untuk penandatangan baru.',
            ], 422);
        }

        $signaturePath = $signer?->signature_path;

        if ($request->hasFile('signature')) {
            if ($signaturePath && Storage::disk('public')->exists($signaturePath)) {
                Storage::disk('public')->delete($signaturePath);
            }

            $signaturePath = $request->file('signature')->store('certificates/signatures', 'public');
        }

        $signer = CertificateSigner::updateOrCreate(
            ['course_id' => $validated['course_id']],
            [
                'name' => $validated['name'],
                'position' => $validated['position'],
                'signature_path' => $signaturePath,
                'is_active' => true,
                'created_by' => Auth::id(),
            ]
        );

        $course = Course::findOrFail($validated['course_id']);
        ActivityLog::log(
            "Mengatur penandatangan sertifikat kelas {$course->title} menjadi {$signer->name} ({$signer->position})",
            $signer,
            [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'signer_name' => $signer->name,
                'signer_position' => $signer->position,
            ],
            'certificate'
        );

        return response()->json([
            'message' => 'Penandatangan sertifikat berhasil disimpan.',
        ]);
    }

    public function print(Certificate $certificate)
    {
        $user = Auth::user();

        if ($certificate->student_id !== $user->id && !$user->hasPermission('certificates.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $certificate->load(['course.instructors', 'course.certificateSigner', 'student', 'signer']);

        return view('certificates.print', compact('certificate'));
    }

    private function getCompletionSummary(Course $course, int $studentId): array
    {
        $materialIds = $course->modules
            ->flatMap(fn($module) => $module->materials)
            ->pluck('id')
            ->values();

        $totalMaterials = $materialIds->count();
        $completedMaterials = $totalMaterials > 0
            ? MaterialProgress::where('student_id', $studentId)
                ->whereIn('material_id', $materialIds)
                ->where('is_completed', true)
                ->count()
            : 0;

        $progressPercent = $totalMaterials > 0
            ? round(($completedMaterials / $totalMaterials) * 100)
            : 0;

        return [
            'total_materials' => $totalMaterials,
            'completed_materials' => $completedMaterials,
            'progress_percent' => $progressPercent,
            'is_eligible' => $totalMaterials > 0 && $completedMaterials === $totalMaterials,
        ];
    }

    private function generateCertificateNo(int $courseId): string
    {
        do {
            $number = 'CERT-' . now()->format('Ymd') . '-' . str_pad((string) $courseId, 4, '0', STR_PAD_LEFT) . '-' . Str::upper(Str::random(6));
        } while (Certificate::where('certificate_no', $number)->exists());

        return $number;
    }
}
