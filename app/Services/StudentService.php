<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\FlashCard;
use App\Models\Lesson;
use App\Models\Notes;
use App\Models\User;
use App\Repositories\StudentRepository;
use Carbon\Carbon;

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

    //View available courses
    public function getAvailableCourses() {
        $currentDate = Carbon::today();

        $courses = Course::where('Status', 'Unactive')
            ->whereHas('CourseSchedule', function($query) use ($currentDate) {
                $query->where('Start_Enroll', '>=', $currentDate)
                      ->where('End_Enroll', '>=', $currentDate);
            })->get();

        return $courses;
    }

    public function getTeacher($teacherId)
    {
        return User::role('Teacher')
            ->where('id', $teacherId)
            ->select('id', 'name', 'email')
            ->first();
    }

    //View flash cards
    public function getAllFlashCards($studentId)
    {
        $courseIds = Enrollment::where('StudentId', $studentId)->pluck('CourseId');

        return FlashCard::whereIn('CourseId', $courseIds)->get();
    }

    public function getFlashCard($studentId, $flashCardId)
    {
        $flashCard = FlashCard::find($flashCardId);

        if (!$flashCard) {
            return null;
        }

        $isEnrolled = Enrollment::where('StudentId', $studentId)
            ->where('CourseId', $flashCard->CourseId)->exists();

        return $isEnrolled ? $flashCard : null;
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
    public function getProgress($studentId)
    {
        return $this->studentRepository->calculateProgress($studentId);
    }

}
