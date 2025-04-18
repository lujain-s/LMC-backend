<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Course;
use App\Repositories\StaffRepository;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Lesson;
use App\Models\User;

class StaffService
{
    private $staffRepository;

    public function __construct(StaffRepository $staffRepository)
    {
        $this->staffRepository = $staffRepository;
    }

    //Secretary--------------------------------------------------

    //Enrollment

    public function enrollStudent($data)
    {
        return DB::transaction(function () use ($data) {

            $this->staffRepository->updateUserRole($data['StudentId'], 5);

            return $this->staffRepository->createEnrollment($data);
        });
    }

    public function viewEnrolledStudentsInCourse($courseId)
    {
        return $this->staffRepository->getEnrolledStudentsInCourse($courseId);
    }

    //Add course
    public function createCourseWithSchedule($data)
    {
        return DB::transaction(function () use ($data) {

            $endDate = $this->staffRepository->calculateCourseEndDate(
                $data['Start_Date'],
                $data['CourseDays'],
                $data['Number_of_lessons']
            );

            $conflict = $this->staffRepository->checkCourseScheduleConflict(
                $data['RoomId'],
                $data['Start_Date'],
                $endDate,
                $data['CourseDays'],
                $data['Start_Time'],
                $data['End_Time']
            );


            if ($conflict) {
                return response()->json([
                    'Message' => 'The new course schedule conflicts with an existing course in the same room.'
                ], 400);
            }

            $course = $this->staffRepository->createCourse($data);

            $schedule = $this->staffRepository->createSchedule($course->id, [
                'RoomId' => $data['RoomId'],
                'Start_Enroll' => $data['Start_Enroll'],
                'End_Enroll' => $data['End_Enroll'],
                'Start_Date' => Carbon::parse($data['Start_Date'])->setTimeFromTimeString($data['Start_Time']),
                'End_Date' => $endDate,
                'Start_Time' => $data['Start_Time'],
                'End_Time' => $data['End_Time'],
                'CourseDays' => $data['CourseDays'],
            ]);

            $lessons = $this->generateLessons($course->id, $data['Start_Date'], $data['Start_Time'], $data['End_Time'], $data['Number_of_lessons'], $data['CourseDays']);

            Lesson::insert($lessons);

            return [
                'Course' => $course,
                'Schedule' => $schedule,
                'Lessons' => $lessons,
            ];
        });
    }

    private function generateLessons($courseId, $startDate, $startTime, $endTime, $lessonCount, $daysOfWeek)
    {
        $lessons = [];
        $date = Carbon::parse($startDate);
        $count = 0;

        while ($count < $lessonCount) {
            if (in_array($date->format('D'), $daysOfWeek)) {
                $lessons[] = [
                    'CourseId' => $courseId,
                    'Title' => "Lesson " . ($count + 1),
                    'Date' => $date->format('Y-m-d'),
                    'Start_Time' => $startTime,
                    'End_Time' => $endTime,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                $count++;
            }
            $date->addDay();
        }

        return $lessons;
    }

    //Edit course
    public function editCourse($data)
    {
        return DB::transaction(function () use ($data) {

            $endDate = $this->staffRepository->calculateCourseEndDate(
                $data['Start_Date'],
                $data['CourseDays'],
                $data['Number_of_lessons']
            );

            $conflict = $this->staffRepository->checkCourseScheduleConflict(
                $data['RoomId'],
                $data['Start_Date'],
                $endDate,
                $data['CourseDays'],
                $data['Start_Time'],
                $data['End_Time']
            );


            if ($conflict) {
                return response()->json([
                    'Message' => 'The updated course schedule conflicts with an existing course in the same room.'
                ], 400);
            }

            // Update the schedule
            $this->staffRepository->updateCourseSchedule($data['CourseId'], [
                'RoomId' => $data['RoomId'],
                'Start_Enroll' => $data['Start_Enroll'],
                'End_Enroll' => $data['End_Enroll'],
                'Start_Date' => Carbon::parse($data['Start_Date'])->setTimeFromTimeString($data['Start_Time']),
                'End_Date' => $endDate,
                'Start_Time' => $data['Start_Time'],
                'End_Time' => $data['End_Time'],
                'CourseDays' => $data['CourseDays'],
            ]);

            // Delete old lessons
            Lesson::where('CourseId', $data['CourseId'])->delete();

            // Generate new lessons
            $lessons = $this->generateLessons(
                $data['CourseId'],
                $data['Start_Date'],
                $data['Start_Time'],
                $data['End_Time'],
                $data['Number_of_lessons'],
                $data['CourseDays']
            );

            Lesson::insert($lessons);

            return [
                'UpdatedSchedule' => true,
                'Lessons' => $lessons,
            ];
        });
    }

    //Delete course
    public function deleteCourseWithLessons($course)
    {
        return DB::transaction(function () use ($course) {
            $this->staffRepository->deleteCourseAndLessons($course);
            return ['message' => 'Course and its lessons deleted successfully.'];
        });
    }

    //Teacher---------------------------------------------------------

    //Add flash card to lesson

    public function addFlashCard($data)
    {
        return DB::transaction(function () use ($data) {
            return $this->staffRepository->createFlashCard($data);
        });
    }

    //Review my schedule
    public function reviewSchedule($teacherId)
    {
        return $this->staffRepository->getCoursesSchedules($teacherId);
    }

    //Check attendance, enter bonus
    public function enterBonus($courseId, $studentId, $bonus)
    {
        $teacherId = auth()->user()->id;

        $course = Course::where('id', $courseId)
            ->where('TeacherId', $teacherId)
            ->first();

        if (!$course) {
            return ['error' => 'Course not found or not assigned to you'];
        }

        $attendance = Attendance::where('CourseId', $courseId)
            ->where('StudentId', $studentId)
            ->first();

        if (!$attendance) {
            return ['error' => 'Attendance record not found'];
        }

        $attendance->Bonus = $bonus;
        $attendance->save();

        return ['success' => 'Bonus updated successfully'];
    }

    public function markAttendance($courseId, $studentId)
    {
        $teacherId = auth()->user()->id;

        $course = Course::where('id', $courseId)->where('TeacherId', $teacherId)->first();

        if (!$course) {
            return ['error' => 'Course not found or not assigned to you'];
        }

        $isEnrolled = DB::table('enrollments')
        ->where('CourseId', $courseId)
        ->where('StudentId', $studentId)
        ->exists();

        if (!$isEnrolled) {
            return ['error' => 'Student is not enrolled in this course'];
        }

        Attendance::create([
            'CourseId' => $courseId,
            'StudentId' => $studentId,
            'Bonus' => 0,
        ]);

        return ['success' => 'Attendance record created'];
    }

}

