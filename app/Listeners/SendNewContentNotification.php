<?php

namespace App\Listeners;

use App\Events\AssignmentCreated;
use App\Events\AnnouncementCreated;
use App\Events\QuizCreated;
use App\Models\Enrollment;
use App\Models\User;
use App\Notifications\NewContentNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class SendNewContentNotification
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $students = collect();

        if ($event instanceof AssignmentCreated) {
            $assignment = $event->assignment;
            $students = $this->getEnrolledStudents($assignment->course_id);
            
            if ($students->isNotEmpty()) {
                Notification::send($students, new NewContentNotification($assignment, 'assignment'));
                Log::info("Dispatched NewContentNotification (assignment) to " . $students->count() . " student(s).");
            }
        } elseif ($event instanceof AnnouncementCreated) {
            $announcement = $event->announcement;
            
            if ($announcement->course_id) {
                $students = $this->getEnrolledStudents($announcement->course_id);
            } else {
                $students = $this->getAllStudents();
            }

            if ($students->isNotEmpty()) {
                Notification::send($students, new NewContentNotification($announcement, 'announcement'));
                Log::info("Dispatched NewContentNotification (announcement) to " . $students->count() . " student(s).");
            }
        } elseif ($event instanceof QuizCreated) {
            $quiz = $event->quiz;
            $students = $this->getEnrolledStudents($quiz->course_id);

            if ($students->isNotEmpty()) {
                Notification::send($students, new NewContentNotification($quiz, 'quiz'));
                Log::info("Dispatched NewContentNotification (quiz) to " . $students->count() . " student(s).");
            }
        }
    }

    /**
     * Get active students enrolled in a specific course.
     */
    protected function getEnrolledStudents(int $courseId)
    {
        $studentIds = Enrollment::where('course_id', $courseId)
            ->where('status', 'active')
            ->pluck('student_id');

        return User::whereIn('id', $studentIds)
            ->where('is_active', true)
            ->get();
    }

    /**
     * Get all active students in the system.
     */
    protected function getAllStudents()
    {
        return User::whereHas('roles', function ($query) {
                $query->where('name', 'pelajar');
            })
            ->where('is_active', true)
            ->get();
    }
}
