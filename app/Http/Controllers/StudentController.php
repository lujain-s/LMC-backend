<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\LMCInfo;
use App\Models\User;
use App\Services\StudentService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    protected $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    public function viewLMCInfo() {
        $info = LMCInfo::latest()->first();
        $teachers = User::where('role_id',3)->get();
        $languages = Language::all();

        return response()->json([
            'Title' => $info->Title,
            'Description' => $info->Descriptions ? json_decode($info->Descriptions) : [],
            'Photo' => $info->Photo,
            'Teachers' => $teachers,
            'Languages' => $languages,
        ]);
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

    public function viewTeacher($teacherId){
        $teacher = $this->studentService->getTeacher($teacherId);

        if (!$teacher) {
            return response()->json(['message' => 'Teacher not found.'], 404);
        }

        return response()->json([
            'message' => 'Teacher retrieved successfully.',
            'Teacher' => $teacher,
        ]);
    }

    public function viewAvailableCourses() {
        $courses = $this->studentService->getAvailableCourses();

        return response()->json([
            'message' => 'Available courses retrieved successfully.',
            'Available Courses' => $courses
        ]);
    }

    public function takePlacementTest() {

    }

    public function takeSelfTest() {

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

    public function viewAllFlashCards() {
        $studentId = auth()->user()->id;
        $flashCards = $this->studentService->getAllFlashCards($studentId);

        return response()->json([
            'message' => 'All flashcards retrieved successfully.',
            'FlashCards' => $flashCards
        ]);
    }

    public function viewFlashCard($flashcardId) {
        $studentId = auth()->user()->id;
        $flashCard = $this->studentService->getFlashCard($studentId, $flashcardId);

        if (!$flashCard) {
            return response()->json([
                'message' => 'Flashcard not found or not accessible.',
            ], 404);
        }

        return response()->json([
            'message' => 'Flashcard retrieved successfully.',
            'FlashCard' => $flashCard
        ]);
    }

    public function requestPrivateCourse() {

    }
    public function viewProgress() {
        $studentId = auth()->user()->id;

        $progress = $this->studentService->getProgress($studentId);

        return response()->json([
            'message' => 'Student progress retrieved successfully.',
            'Progress' => $progress
        ]);
    }

    public function viewRoadmap() {
        $guestId = auth()->user()->id;

        $roadmap = $this->studentService->getRoadmap($guestId);

        return response()->json([
            'Your Roadmap' => $roadmap
        ]);
    }
}
