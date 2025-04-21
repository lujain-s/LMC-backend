<?php

namespace App\Repositories;

use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Notes;
use App\Models\StudentProgress;
use Carbon\Carbon;

class StudentRepository
{
    //Student progress
    public function getEnrollmentsForStudent($studentId)
    {
        return Enrollment::where('StudentId', $studentId)
            ->with('course.Lesson')->get();
    }

    public function getUpcomingLessons($lessons)
    {
        return $lessons->where('Date', '>=', Carbon::today()->toDateString())
            ->sortBy('Date')->values();
    }

    public function getAttendanceCount($studentId, $lessons)
    {
        return Attendance::where('StudentId', $studentId)
            ->whereIn('LessonId', $lessons->pluck('id'))->count();
    }

    public function getStudentProgress($studentId, $courseId)
    {
        return StudentProgress::where('StudentId', $studentId)
            ->where('CourseId', $courseId)->first();
    }

    public function calculateProgress($studentId)
    {
        $enrollments = $this->getEnrollmentsForStudent($studentId);

        $result = [];

        foreach ($enrollments as $enrollment) {
            $course = $enrollment->course;
            $lessons = $course->Lesson;

            $studentProgress = $this->getStudentProgress($studentId, $course->id);

            $attendancePercentage = 0;
            $score = 0;

            if ($studentProgress) {
                $attendancePercentage = $studentProgress->Percentage;
                $score = $studentProgress->Score;
            }

            $totalLessons = $lessons->count();
            $attendedLessons = $this->getAttendanceCount($studentId, $lessons);

            $upcomingLessons = $this->getUpcomingLessons($lessons);

            $result[] = [
                'CourseId' => $course->id,
                'Total Lessons' => $totalLessons,
                'Attended Lessons' => $attendedLessons,
                'Attendance Percentage' => $attendancePercentage . '%',
                'Score' => $score,
                'Upcoming Lessons' => $upcomingLessons,
            ];
        }
        return $result;
    }

    //Note
    public function createNote($data) {
        return Notes::create($data);
    }

    public function updateNote($note, $content) {
        $note->Content = $content;
        $note->save();
        return $note;
    }

    public function deleteNote($note) {
        $note->delete();
        return true;
    }
}
