<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CourseService;

class CourseController extends Controller
{
    protected $courseService;
    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }
    public function addLesson(Request $request, $courseId)
    {
        $data = $request->validate([
            'CourseId' => 'required|integer',
            'FlashCardId' => 'nullable|integer',
            'Title' => 'nullable|string',
        ]);

        return response()->json(
            $this->courseService->addLesson($courseId, $data)
        );
    }

    public function calculateSuccessRate($courseId)
    {
        return response()->json(
            $this->courseService->calculateSuccessRate($courseId)
        );
    }

    public function viewRoadmap($language, $level)
    {
        return response()->json(
            $this->courseService->getRoadmap($language, $level)
        );
    }
}
