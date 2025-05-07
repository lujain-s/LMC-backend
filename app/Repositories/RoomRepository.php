<?php

namespace App\Repositories;

use App\Models\Room;

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

}
