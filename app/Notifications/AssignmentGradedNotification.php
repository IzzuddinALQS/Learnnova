<?php

namespace App\Notifications;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AssignmentGradedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Assignment $assignment,
        public AssignmentSubmission $submission
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'assignment_id'   => $this->assignment->id,
            'assignment_title' => $this->assignment->title,
            'course_title'    => $this->assignment->course->title,
            'score'           => $this->submission->score,
            'max_score'       => $this->assignment->max_score,
            'status'          => $this->submission->status,
            'message'         => "Tugas \"{$this->assignment->title}\" pada kelas \"{$this->assignment->course->title}\" telah dinilai. Nilai Anda: {$this->submission->score}/{$this->assignment->max_score}",
        ];
    }
}
