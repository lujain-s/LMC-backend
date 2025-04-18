<?php

namespace App\Http\Controllers;

use App\Services\StudentService;
use Illuminate\Http\Request;


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

    public function addNote(Request $request)
    {
        $data = $request->validate([
            'Content' => 'required|string',
        ]);

        $data['StudentId'] = auth()->user()->id;

        $note = $this->studentService->addNote($data);

        return response()->json([
            'message' => 'Note added successfully.',
            'Note' => $note,
        ]);
    }

    public function editNote(Request $request, $noteId)
    {
        $data = $request->validate([
            'Content' => 'required|string',
        ]);

        $studentId = auth()->user()->id;

        $result = $this->studentService->editNote($studentId, $noteId, $data['Content']);

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], 403);
        }

        return response()->json([
            'message' => 'Note updated successfully.',
            'Note' => $result,
        ]);
    }

    public function deleteNote($noteId)
    {
        $studentId = auth()->user()->id;

        $result = $this->studentService->deleteNote($studentId, $noteId);

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], 403);
        }

        return response()->json(['message' => 'Note deleted successfully.']);
    }

    public function viewMyNotes() {
        $studentId = auth()->user()->id;

        $notes = $this->studentService->getMyNotes($studentId);

        if ($notes->isEmpty()) {
            return response()->json([
                'message' => 'You do not have any notes.'
            ]);
        }

        return response()->json([
            'message' => 'Notes retrieved successfully.',
            'Notes' => $notes,
        ]);
    }

    public function requestPrivateCourse() {

    }

    public function calculateAttendance() {

    }
}
