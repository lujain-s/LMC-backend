<?php

namespace App\Repositories;

use App\Models\Room;
use Carbon\Carbon;

class RoomRepository
{
    public function createRoom(array $data): Room
    {
        return Room::create($data);
    }

    public function updateRoom(int $id, array $data): ?Room
    {
        $room = Room::find($id);
        if ($room) {
            $room->update($data);
        }
        return $room;
    }

    public function findRoomById($id)
    {
     return Room::find($id);
    }

    public function calculateCourseEndDate($startDate, $daysOfWeek, $numberOfLessons)
    {
        $date = Carbon::parse($startDate);
        $count = 0;

        while ($count < $numberOfLessons) {
            if (in_array($date->format('D'), $daysOfWeek)) {
                $count++;
            }
            $date->addDay();
        }

        return $date->subDay(); // go back to last valid day
    }
}
