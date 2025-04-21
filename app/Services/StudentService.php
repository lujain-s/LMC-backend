<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Notes;
use App\Models\User;
use App\Repositories\StudentRepository;

class StudentService
{
    private $studentRepository;

    public function __construct(StudentRepository $studentRepository)
    {
        $this->studentRepository = $studentRepository;
    }

    //View my courses
    public function getEnrolledCourses($studentId)
    {
        return Enrollment::where('StudentId', $studentId)
        ->with('course.CourseSchedule')->get()->pluck('course');
    }

    //View my lessons for a course
    public function getMyLessons($studentId, $courseId)
    {
        $isEnrolled = Enrollment::where('StudentId', $studentId)->where('CourseId', $courseId)->exists();

        if (!$isEnrolled) {
            return ['error' => 'You are not enrolled in this course.'];
        }

        return Lesson::where('CourseId', $courseId)->get();
    }

    //View teachers
    public function getAllTeachers() {
        return User::role('Teacher')->select('id', 'name', 'email')->get();
    }

    public function getTeacher($teacherId)
    {
        return User::role('Teacher')
            ->where('id', $teacherId)
            ->select('id', 'name', 'email')
            ->first();
    }

    //Note
    public function addNote($data) {
        return $this->studentRepository->createNote($data);
    }

    public function editNote($studentId, $noteId, $content) {
        $note = Notes::find($noteId);

        if (!$note || $note->StudentId !== $studentId) {
            return ['error' => 'Note not found.'];
        }

        return $this->studentRepository->updateNote($note, $content);
    }

    public function deleteNote($studentId, $noteId) {
        $note = Notes::find($noteId);

        if (!$note || $note->StudentId !== $studentId) {
            return ['error' => 'Note not found or unauthorized.'];
        }

        return $this->studentRepository->deleteNote($note);
    }

    public function getMyNotes($studentId) {
        return Notes::where('StudentId', $studentId)->latest()->get();
    }

    //View progress
    public function calculateAttendance($studentId)
    {
        $enrollments = Enrollment::where('StudentId', $studentId)->with('course.Lesson')->get();

        $data = [];

        foreach ($enrollments as $enrollment) {
            $course = $enrollment->course;
            $lessons = $course->Lesson;

            $totalLessons = $lessons->count();
            $attended = Attendance::where('StudentId', $studentId)
                ->whereIn('LessonId', $lessons->pluck('id'))
                ->count();

            $percentage = $totalLessons > 0
                ? round(($attended / $totalLessons) * 100, 2)
                : 0;

            $data[] = [
                'CourseId' => $course->id,
                'Total Lessons' => $totalLessons,
                'Attended' => $attended,
                'Attendance %' => $percentage . '%',
            ];
        }

        return $data;
    }

    public function getProgress($studentId)
    {
        $enrollments = Enrollment::where('StudentId', $studentId)->with('course.Lesson')->get();

        $result = [];

        foreach ($enrollments as $enrollment) {
            $course = $enrollment->course;
            $lessons = $course->Lesson;

            $totalLessons = $lessons->count();
            $attendedLessons = Attendance::where('StudentId', $studentId)
                ->whereIn('LessonId', $lessons->pluck('id'))
                ->count();

            $attendancePercentage = $totalLessons > 0
                ? round(($attendedLessons / $totalLessons) * 100, 2)
                : 0;

            $upcomingLessons = $lessons->where('Date', '>=', now()->toDateString())
                                        ->sortBy('Date')
                                        ->values();

            $result[] = [
                'CourseId' => $course->id,
                'Total Lessons' => $totalLessons,
                'Attended Lessons' => $attendedLessons,
                'Attendance Percentage' => $attendancePercentage . '%',
                'Upcoming Lessons' => $upcomingLessons,
            ];
        }

        return $result;
    }

}
