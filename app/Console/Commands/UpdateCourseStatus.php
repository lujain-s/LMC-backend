<?php

namespace App\Console\Commands;

use App\Models\CourseSchedule;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateCourseStatus extends Command
{
    protected $signature = 'courses:update-course-status';

    protected $description = 'Automatically updates the status of courses based on their schedule dates';

    public function handle()
    {
        $now = Carbon::now()->toDateString();

        $schedules = CourseSchedule::with('Course')->get();

        foreach ($schedules as $schedule) {
            $course = $schedule->course;

            if(!$course) {
                continue;
            }

            if ($now < $schedule->Start_Date) {
                $course->Status = 'Unactive';
            } elseif ($now >= $schedule->Start_Date && $now <= $schedule->End_Date) {
                $course->Status = 'Active';
            } elseif ($now > $schedule->End_Date) {
                $course->Status = 'Done';
            }

            $course->save();
        }
        $this->info('Course status updated successfully');
    }
}
