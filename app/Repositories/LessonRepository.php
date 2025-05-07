<?php

// LessonRepository.php
namespace App\Repositories;

use App\Models\Lesson;
use Illuminate\Support\Facades\DB;


class LessonRepository
{
    public function backupLessons($lessons)
    {
        $backupData = $lessons->map(function ($lesson) {
            return [
                'CourseId' => $lesson->CourseId,
                'Title' => $lesson->Title,
                'Date' => $lesson->Date,
                'Start_Time' => $lesson->Start_Time,
                'End_Time' => $lesson->End_Time,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        DB::table('lesson_backups')->insert($backupData);
    }

    public function deleteLessonsByCourse($courseId)
    {
        return Lesson::where('CourseId', $courseId)->delete();
    }

    public function insertLessons(array $lessons)
    {
        return Lesson::insert($lessons);
    }

    public function getCourseIdsWithBackupsInDateRange($startDate, $endDate)
    {
        return DB::table('lesson_backups')
            ->whereBetween('Date', [$startDate, $endDate])
            ->pluck('CourseId')
            ->unique();
    }

    public function getBackupsByCourse($courseId)
    {
        return DB::table('lesson_backups')
            ->where('CourseId', $courseId)
            ->get();
    }

    public function deleteBackupsByCourse($courseId)
    {
        return DB::table('lesson_backups')
            ->where('CourseId', $courseId)
            ->delete();
    }


    public function restoreLessons($courseId)
    {
        // Retrieve the lessons backup for the specified course
        $backedUpLessons = DB::table('lesson_backups')
            ->where('CourseId', $courseId)
            ->get();

        // Restore lessons by re-inserting them into the lessons table
        foreach ($backedUpLessons as $lesson) {
            DB::table('lessons')->insert([
                'CourseId' => $lesson->CourseId,
                'Title' => $lesson->Title,
                'Date' => $lesson->Date,
                'Start_Time' => $lesson->Start_Time,
                'End_Time' => $lesson->End_Time,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return $backedUpLessons;
    }

}
