<?php

namespace App\Repositories;

use App\Models\Course;

class CourseRepository
{
    public function addLesson($courseId, array $data)
    {
        return Course::findOrFail($courseId)->lessons()->create($data);
    }

    public function getWithStudentsAndMarks($courseId)
    {
        return Course::with(['students' => function ($query) {
            $query->withPivot(['marks', 'attended_lessons']);
        }, 'lessons'])->findOrFail($courseId);
    }

    public function getRoadmapByLanguageAndLevel($language, $level)
    {
        /*return Roadmap::where('language', $language)
                    ->where('level', $level)
                    ->with('courses')
                    ->get();*/
    }
}
