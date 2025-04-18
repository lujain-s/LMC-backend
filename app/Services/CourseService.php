<?php

namespace App\Services;

use App\Repositories\CourseRepository;

class CourseService
{
    protected $courseRepository;

    public function addLesson($courseId, array $data)
    {
        $data['Title'] = $data['Title'] ?? 'Lesson on ' . $data['date'];
        return $this->courseRepository->addLesson($courseId, $data);
    }

    public function calculateSuccessRate($courseId)
    {
        $course = $this->courseRepository->getWithStudentsAndMarks($courseId);

        $rates = [];
        foreach ($course->students as $student) {
            $marks = $student->pivot->marks ?? 0;
            $attended = $student->pivot->attended_lessons ?? 0;
            $total = $course->lessons->count();

            $rate = ($attended / $total) * 0.4 + ($marks / 100) * 0.6;
            $rates[] = [
                'student' => $student->name,
                'success_rate' => round($rate * 100, 2)
            ];
        }

        return $rates;
    }

    public function getRoadmap($language, $level)
    {
        return $this->courseRepository->getRoadmapByLanguageAndLevel($language, $level);
    }

}
