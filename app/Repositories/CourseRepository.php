<?php

namespace App\Repositories;

use App\Models\Course;
use App\Models\CourseSchedule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CourseRepository
{
    public function addLesson($courseId, array $data)
    {
        return Course::findOrFail($courseId)->lessons()->create($data);
    }

    public function getWithStudentsAndMarks($courseId)
    {
        return Course::with(['students' => function ($query) {
            $query->withPivot(['marks', 'attended_lessons']);
        }, 'lessons'])->findOrFail($courseId);
    }

    public function getRoadmapByLanguageAndLevel($language, $level)
    {
        /*return Roadmap::where('language', $language)
                    ->where('level', $level)
                    ->with('courses')
                    ->get();*/
    }

    public function getSchedulesAffectedByHoliday(Carbon $startDate, Carbon $endDate)
    {
        return CourseSchedule::where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('Start_Date', [$startDate, $endDate])
                ->orWhereBetween('End_Date', [$startDate, $endDate])
                ->orWhere(function ($q) use ($startDate, $endDate) {
                    $q->where('Start_Date', '<=', $startDate)
                        ->where('End_Date', '>=', $endDate);
                });
        })->get();
    }

    public function updateScheduleEndDate($scheduleId, $endDate)
    {
        return CourseSchedule::where('id', $scheduleId)->update(['End_Date' => $endDate]);
    }

    public function updateScheduleEndDateByCourse($courseId, $endDate)
    {
        return CourseSchedule::where('CourseId', $courseId)->update(['End_Date' => $endDate]);
    }

    public function getEnrollmentPeriodsAffectedByHoliday(Carbon $startDate, Carbon $endDate)
    {
        return DB::table('course_schedules')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('Start_Enroll', [$startDate, $endDate])
                    ->orWhereBetween('End_Enroll', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('Start_Enroll', '<=', $startDate)
                        ->where('End_Enroll', '>=', $endDate);
                    });
            })
            ->get();
    }

    public function backupEnrollmentPeriods($schedules)
    {
        $backupData = $schedules->map(function ($schedule) {
            return [
                'schedule_id' => $schedule->id,
                'original_start_enroll' => $schedule->Start_Enroll,
                'original_end_enroll' => $schedule->End_Enroll,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        DB::table('schedule_enrollment_backups')->insert($backupData);
    }

    public function extendEnrollmentPeriod($scheduleId, $daysToAdd)
    {
        return DB::table('course_schedules')
            ->where('id', $scheduleId)
            ->update([
                'End_Enroll' => DB::raw("DATE_ADD(End_Enroll, INTERVAL $daysToAdd DAY)"),
                'updated_at' => now()
            ]);
    }

    public function backupEnrollmentPeriod($enrollment): void
    {
        DB::table('enrollment_backups')->insert([
            'schedule_id' => $enrollment->id,
            'original_start_enroll' => $enrollment->Start_Enroll,
            'original_end_enroll' => $enrollment->End_Enroll,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function updateEnrollmentPeriod($scheduleId, Carbon $startDate, Carbon $endDate): void
    {
        DB::table('course_schedules')
            ->where('id', $scheduleId)
            ->update([
                'Start_Enroll' => $startDate->toDateString(),
                'End_Enroll' => $endDate->toDateString(),
                'updated_at' => now()
            ]);
    }
}
