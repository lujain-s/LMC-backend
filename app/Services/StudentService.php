<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Lesson;
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

}
