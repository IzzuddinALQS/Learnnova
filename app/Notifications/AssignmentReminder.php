<?php

namespace App\Notifications;

use App\Channels\WebPushChannel;
use App\Models\Assignment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AssignmentReminder extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Assignment  $assignment
     * @param  int  $hours                 The number of hours remaining (e.g., 24 or 3)
     */
    public function __construct(
        public Assignment $assignment,
        public int $hours
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @param  object  $notifiable
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', WebPushChannel::class];
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @param  object  $notifiable
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'assignment_id' => $this->assignment->id,
            'assignment_title' => $this->assignment->title,
            'course_title' => $this->assignment->course->title,
            'hours_left' => $this->hours,
            'message' => "Batas akhir mengumpulkan tugas \"{$this->assignment->title}\" pada kelas \"{$this->assignment->course->title}\" kurang dari {$this->hours} jam lagi!",
            'url' => route('assignments.show', $this->assignment->id),
        ];
    }

    /**
     * Get the Web Push representation of the notification.
     *
     * @param  object  $notifiable
     * @return array<string, mixed>
     */
    public function toWebPush(object $notifiable): array
    {
        return [
            'title' => 'Peringatan Batas Waktu ⏰',
            'body' => "Tugas \"{$this->assignment->title}\" kurang dari {$this->hours} jam lagi!",
            'url' => route('assignments.show', $this->assignment->id),
            'icon' => asset('dist/img/AdminLTELogo.png'),
        ];
    }
}
