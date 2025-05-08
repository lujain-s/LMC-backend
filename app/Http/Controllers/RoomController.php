<?php

namespace App\Http\Controllers;

use App\Services\RoomService;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    protected $roomService;

    public function __construct(RoomService $roomService){
            $this->roomService = $roomService;
    }

    public function addRoom(Request $request){
        $room = $this->roomService->addRoom($request->all());

        return response()->json([
            'message' => 'Room added successfully',
            'data' => $room,
        ], 201);
    }

    public function updateRoom(Request $request, $id)
    {
    $response = $this->roomService->updateRoom($id, $request->all());

    if (!$response['status']) {
        return response()->json([
            'message' => $response['message'],
            'errors' => $response['errors'] ?? null,
        ], 400); // Bad Request
    }

    return response()->json([
        'message' => $response['message'],
        'data' => $response['data'],
    ]);
    }

    public function checkAvailability(Request $request) {

    }

    public function reserveRoom(Request $request) {

    }

    public function viewReservedRooms() {

    }

    public function viewAvailableRooms(Request $request) {
        $validated = $request->validate([
            'Start_Date' => 'required|date',
            'Start_Time' => 'required|date_format:H:i',
            'NumberOfLessons' => 'required|integer|min:1',
            'CourseDays' => 'required|array|min:1'
        ]);

        $availableRooms = $this->roomService->getAvailableRooms(
            $validated['Start_Date'],
            $validated['Start_Time'],
            $validated['NumberOfLessons'],
            $validated['CourseDays']
        );

        return response()->json([
            'AvailableRooms' => $availableRooms
        ]);
    }
}
