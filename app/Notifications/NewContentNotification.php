<?php

namespace App\Notifications;

use App\Channels\WebPushChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewContentNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param  mixed  $content      The created model instance (Assignment, Announcement, or Quiz)
     * @param  string $contentType  The type of content ('assignment', 'announcement', or 'quiz')
     */
    public function __construct(
        public mixed $content,
        public string $contentType
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
        $data = [
            'content_id' => $this->content->id,
            'content_type' => $this->contentType,
            'title' => $this->content->title,
        ];

        switch ($this->contentType) {
            case 'assignment':
                $courseTitle = $this->content->course->title;
                $data['course_title'] = $courseTitle;
                $data['message'] = "Tugas Baru: \"{$this->content->title}\" telah ditambahkan di kelas \"{$courseTitle}\".";
                $data['url'] = route('assignments.show', $this->content->id);
                break;

            case 'announcement':
                $courseTitle = $this->content->course?->title;
                $data['course_title'] = $courseTitle;
                $data['message'] = $courseTitle
                    ? "Pengumuman Baru: \"{$this->content->title}\" di kelas \"{$courseTitle}\"."
                    : "Pengumuman Baru: \"{$this->content->title}\".";
                $data['url'] = route('announcements.show', $this->content->id);
                break;

            case 'quiz':
                $courseTitle = $this->content->course->title;
                $data['course_title'] = $courseTitle;
                $data['message'] = "Kuis Baru: \"{$this->content->title}\" telah ditambahkan di kelas \"{$courseTitle}\".";
                $data['url'] = route('quizzes.show', $this->content->id);
                break;

            default:
                $data['message'] = "Konten Baru: \"{$this->content->title}\".";
                $data['url'] = '/';
                break;
        }

        return $data;
    }

    /**
     * Get the Web Push representation of the notification.
     *
     * @param  object  $notifiable
     * @return array<string, mixed>
     */
    public function toWebPush(object $notifiable): array
    {
        $dbData = $this->toDatabase($notifiable);
        
        $titleMap = [
            'assignment' => 'Tugas Baru 📝',
            'announcement' => 'Pengumuman Baru 📢',
            'quiz' => 'Kuis Baru ⚡',
        ];

        return [
            'title' => $titleMap[$this->contentType] ?? 'Konten Baru',
            'body' => $dbData['message'],
            'url' => $dbData['url'],
            'icon' => asset('dist/img/AdminLTELogo.png'),
        ];
    }
}
