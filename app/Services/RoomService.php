<?php

namespace App\Services;

use App\Repositories\RoomRepository;
use Illuminate\Support\Facades\Validator;

class RoomService
{
    protected $roomRepository;

    public function __construct(RoomRepository $roomRepository)
    {
        $this->roomRepository = $roomRepository;
    }

    public function addRoom(array $data)
    {
        $validator = Validator::make($data, [
            'Capacity' => 'required|integer|min:1',
            'NumberOfRoom' => 'required|string|unique:rooms,NumberOfRoom',
        ]);

        if ($validator->fails()) {
            return [
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ];
        }

        // لا نُرسل 'Status' حتى لا نكسر القيمة الافتراضية
        unset($data['Status']);

        return $this->roomRepository->createRoom($data);
    }

    public function updateRoom(int $id, array $data)
    {
        // تحقق من وجود الغرفة أولًا
        $room = $this->roomRepository->findRoomById($id);
        if (!$room) {
            return [
                'status' => false,
                'message' => "Room with ID $id not found",
            ];
        }

        // تحقق من صحة البيانات المدخلة
        $validator = Validator::make($data, [
            'Capacity' => 'sometimes|integer|min:1',
            'NumberOfRoom' => 'sometimes|string|unique:rooms,NumberOfRoom,' . $id,
            'Status' => 'sometimes|in:Available,NotAvailable',
        ]);

        if ($validator->fails()) {
            return [
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ];
        }

        // تنفيذ التحديث
        $updatedRoom = $this->roomRepository->updateRoom($id, $data);

        return [
            'status' => true,
            'message' => 'Room updated successfully',
            'data' => $updatedRoom,
        ];
    }

}
