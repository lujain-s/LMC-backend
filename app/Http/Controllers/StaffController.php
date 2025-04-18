<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\StaffService;
use Carbon\Carbon;


class StaffController extends Controller
{
    protected $staffService;

    public function __construct(StaffService $staffService)
    {
        $this->staffService = $staffService;
    }

    //Logistic---------------------------------------------------
    public function addInvoice(Request $request) {

    }

    public function markCompletedTasks(Request $request) {

    }

    //Secretary--------------------------------------------------
    public function enrollStudent (Request $request) {
        $data = $request->validate([
            'StudentId' => 'required|exists:users,id',
            'CourseId' => 'required|exists:courses,id',
            'isPrivate' => 'required|boolean',
        ]);

        return response()->json(
            $this->staffService->enrollStudent($data)
        );
    }

    public function viewEnrolledStudentsInCourse($courseId) {
        return response()->json(
            $this->staffService->viewEnrolledStudentsInCourse($courseId)
        );
    }

    public function getAllEnrolledStudents()
    {
        return Enrollment::all()
            ->map(function ($enrollment) {
                $student = User::find($enrollment->StudentId);  // Fetch user details using StudentId
                return [
                    'EnrollmentId' => $enrollment->id,
                    'Student' => $student ? [
                        'id' => $student->id,
                        'name' => $student->name,
                        'email' => $student->email,
                    ] : null,
                ];
            });
    }

    public function addCourse(Request $request) {

        $data = $request->validate([
            'TeacherId' => 'required|exists:users,id',
            'LanguageId' => 'required|exists:languages,id',
            'RoomId' => 'required|exists:rooms,id',
            'Description' => 'required|string',
            'Level' => 'required|string',
            'Start_Enroll' => 'required|date|after_or_equal:now()|before_or_equal:End_Enroll',
            'End_Enroll' => 'required|date|after_or_equal:now()|after_or_equal:Start_Enroll',
            'Start_Date' => 'required|date|after_or_equal:now()|after:Start_Enroll|after:End_Enroll',
            'Start_Time' => 'required|date_format:H:i',
            'End_Time' => 'required|date_format:H:i|after:Start_Time',
            'Number_of_lessons' => 'required|integer|min:1',
            'CourseDays' => 'required|array|min:1',
            'CourseDays.*' => 'in:Sun,Mon,Tue,Wed,Thu,Fri,Sat',
        ]);

        $startDate = Carbon::parse($data['Start_Date']);
        $courseDays = $data['CourseDays'];
        $startDayOfWeek = $startDate->format('D');

        if (!in_array($startDayOfWeek, $courseDays)) {
            return response()->json([
                'error' => "The Start Date doesn't match the selected Course Days. Please adjust the Start Date to match one of the selected days."
            ], 400);
        }

        return response()->json(
            $this->staffService->createCourseWithSchedule($data)
        );
    }

    public function editCourse(Request $request) {
        $data = $request->validate([
            'CourseId' => 'required|exists:courses,id',
            'RoomId' => 'required|exists:rooms,id',
            'Start_Enroll' => 'required|date|after_or_equal:now()|before_or_equal:End_Enroll',
            'End_Enroll' => 'required|date|after_or_equal:now()|after_or_equal:Start_Enroll',
            'Start_Date' => 'required|date|after_or_equal:now()|after:Start_Enroll|after:End_Enroll',
            'Start_Time' => 'required|date_format:H:i',
            'End_Time' => 'required|date_format:H:i|after:Start_Time',
            'Number_of_lessons' => 'required|integer|min:1',
            'CourseDays' => 'required|array|min:1',
            'CourseDays.*' => 'in:Sun,Mon,Tue,Wed,Thu,Fri,Sat',
        ]);

        // Check if the Start_Date matches any of the CourseDays
        $startDate = Carbon::parse($data['Start_Date']);
        $courseDays = $data['CourseDays'];
        $startDayOfWeek = $startDate->format('D'); // Get the day of the week for Start_Date

        if (!in_array($startDayOfWeek, $courseDays)) {
            return response()->json([
                'error' => "The Start Date doesn't match the selected Course Days. Please adjust the Start Date to match one of the selected days."
            ], 400);
        }

        return response()->json(
            $this->staffService->editCourse($data)
        );
    }

    public function deleteCourse($courseId)
    {
        $course = Course::find($courseId);

        if (!$course) {
            return response()->json(['error' => 'Course not found'], 404);
        }

        return response()->json(
            $this->staffService->deleteCourseWithLessons($course)
        );
    }


    public function reviewRoomReservations (Request $request) {

    }

    public function addAnnouncement (Request $request) {

    }

    public function editAnnouncement (Request $request) {

    }

    public function deleteAnnouncement (Request $request) {

    }

    public function viewInvoices () {

    }

    //Teacher---------------------------------------------------
    public function sendAssignments() {

    }

    public function reviewMyCourses() {
        $teacherId = auth()->user()->id;

        $courses = Course::where('TeacherId', $teacherId)->with('CourseSchedule')->get();

        return response()->json([
            'My Courses' => $courses
        ]);
    }

    /*public function reviewSchedule() {

    }*/

    public function reviewStudentsNames($courseId) {
        $teacherId = auth()->user()->id;

        $course = Course::where('id', $courseId)->where('TeacherId', $teacherId)->first();

        if (!$course) {
            return response()->json(['message' => 'Course not found or not assigned to you'], 404);
        }

        $enrollments = $course->Enrollment()->with('User')->get();

        $studentNames = $enrollments->map(function ($enrollment) {
            return $enrollment->User->name ?? null;
        })->filter();

        return response()->json([
            'Students' => $studentNames->values()
        ]);
    }

    public function enterBonus(Request $request)
    {
        $validated = $request->validate([
            'CourseId' => 'required|exists:courses,id',
            'StudentId' => 'required|exists:users,id',
            'Bonus' => 'required|numeric|min:0',
        ]);

        $result = $this->staffService->enterBonus(
            $validated['CourseId'],
            $validated['StudentId'],
            $validated['Bonus']
        );

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], 404);
        }

        return response()->json(['message' => $result['success']]);
    }

    public function markAttendance(Request $request)
    {
        $validated = $request->validate([
            'CourseId' => 'required|exists:courses,id',
            'StudentId' => 'required|exists:users,id',
        ]);

        $result = $this->staffService->markAttendance(
            $validated['CourseId'],
            $validated['StudentId']
        );

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], 404);
        }

        return response()->json(['message' => $result['success']]);
    }

    public function addTest(Request $request) {

    }

    public function editTest(Request $request) {

    }

    public function deleteTest(Request $request) {

    }

    public function addSelfTest(Request $request) {

    }

    public function editSelfTest(Request $request) {

    }

    public function deleteSelfTest(Request $request) {

    }

    public function addFlashCard(Request $request) {
        $data = $request->validate([
            'LessonId' => 'required|exists:lessons,id',
            'Content' => 'required|string',
            'Translation' => 'required|string',
        ]);

        $flashcard = $this->staffService->addFlashCard($data);

        return response()->json([
            'message' => 'Flashcard added to lesson successfully.',
            'flashcard' => $flashcard,
        ]);
    }

    public function editFlashCard(Request $request) {
        $data = $request->validate([
            'FlashcardId' => 'required|exists:flash_cards,id',
            'Content' => 'required|string',
            'Translation' => 'required|string',
        ]);

        $flashcard = $this->staffService->editFlashCard($data);

        return response()->json([
            'message' => 'Flashcard updated successfully.',
            'Flashcard' => $flashcard,
        ]);
    }

    public function deleteFlashCard(Request $request) {
        $data = $request->validate([
            'FlashcardId' => 'required|exists:flash_cards,id',
        ]);

        $this->staffService->deleteFlashCard($data['FlashcardId']);

        return response()->json([
            'message' => 'Flashcard deleted successfully.',
        ]);
    }

    public function requestPrivateCourse() {

    }
}
