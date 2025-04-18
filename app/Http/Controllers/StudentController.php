<?php

namespace App\Http\Controllers;

use App\Services\StudentService;

class StudentController extends Controller
{
    protected $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    public function viewLMCInfo () {

    }

    public function viewEnrolledCourses() {
        $studentId = auth()->user()->id;

        $courses = $this->studentService->getEnrolledCourses($studentId);

        return response()->json([
            'message' => 'Enrolled courses retrieved successfully.',
            'Courses' => $courses,
        ]);
    }

    public function viewMyLessons($courseId) {
        $studentId = auth()->user()->id;

        $lessons = $this->studentService->getMyLessons($studentId, $courseId);

        if (isset($lessons['error'])) {
            return response()->json(['message' => $lessons['error']], 403);
        }

        return response()->json([
            'message' => 'Lessons retrieved successfully.',
            'My Lessons' => $lessons,
        ]);
    }

    public function viewTeachers() {
        $teachers = $this->studentService->getAllTeachers();

        return response()->json([
            'message' => 'Teachers retrieved successfully.',
            'Teachers' => $teachers,
        ]);
    }

    public function takePlacementTest() {

    }

    public function takeSelfTest() {

    }

    public function viewProgress () {

    }

    public function addNote() {

    }

    public function requestPrivateCourse() {

    }

    public function calculateAttendance() {

    }
}
