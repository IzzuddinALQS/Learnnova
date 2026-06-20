<?php

namespace App\Console\Commands;

use App\Models\Assignment;
use App\Models\Enrollment;
use App\Models\User;
use App\Notifications\AssignmentReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendAssignmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-assignment-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send deadline reminders to students who have not submitted assignments approaching due dates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for approaching assignment deadlines...');

        // Fetch assignments that are due in the future
        $assignments = Assignment::with('course')
            ->where('due_date', '>', now())
            ->get();

        $remindersSent = 0;

        foreach ($assignments as $assignment) {
            $dueAt = $assignment->due_date;
            $hoursRemaining = now()->diffInHours($dueAt, false);

            // Determine if the assignment is within a reminder window
            if ($hoursRemaining <= 0) {
                continue;
            }

            if ($hoursRemaining <= 3) {
                $hoursVal = 3;
            } elseif ($hoursRemaining <= 24) {
                $hoursVal = 24;
            } else {
                // Not in the reminder window (> 24 hours left)
                continue;
            }

            // Find all active students enrolled in the assignment's course
            $studentIds = Enrollment::where('course_id', $assignment->course_id)
                ->where('status', 'active')
                ->pluck('student_id');

            $students = User::whereIn('id', $studentIds)
                ->where('is_active', true)
                ->get();

            foreach ($students as $student) {
                // Check if the student has already submitted (status 'submitted', 'graded', or 'returned')
                $hasSubmitted = \DB::table('assignment_submissions')
                    ->where('assignment_id', $assignment->id)
                    ->where('student_id', $student->id)
                    ->whereIn('status', ['submitted', 'graded', 'returned'])
                    ->exists();

                if ($hasSubmitted) {
                    continue;
                }

                // Check if we have already sent a reminder for this assignment and specific hour threshold
                $alreadySent = \DB::table('notifications')
                    ->where('notifiable_type', User::class)
                    ->where('notifiable_id', $student->id)
                    ->where('type', AssignmentReminder::class)
                    ->where('data->assignment_id', $assignment->id)
                    ->where('data->hours_left', $hoursVal)
                    ->exists();

                if ($alreadySent) {
                    continue;
                }

                // Send the reminder
                try {
                    $student->notify(new AssignmentReminder($assignment, $hoursVal));
                    $remindersSent++;
                    $this->info("Reminder ({$hoursVal}h) sent to student #{$student->id} ({$student->name}) for assignment #{$assignment->id}");
                } catch (\Exception $e) {
                    Log::error("Failed to notify student #{$student->id} of assignment #{$assignment->id}: " . $e->getMessage());
                }
            }
        }

        $this->info("Completed checking deadlines. Total reminders sent: {$remindersSent}");
        Log::info("Command app:send-assignment-reminders completed. Sent {$remindersSent} reminders.");

        return Command::SUCCESS;
    }
}
