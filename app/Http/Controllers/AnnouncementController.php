<?php

namespace App\Http\Controllers;

use App\Services\AnnouncementService;
use Illuminate\Http\Request;


class AnnouncementController extends Controller
{
    protected $announcementService;

    public function __construct(AnnouncementService $announcementService)
    {
        $this->announcementService = $announcementService;
    }

    public function addAnnouncement(Request $request)
    {
        $result = $this->announcementService->createAnnouncement($request);
        return response()->json($result['data'], $result['status']);
    }

    public function updateAnnouncement(Request $request, $id)
    {
        $result = $this->announcementService->updateAnnouncement($request, $id);
        return response()->json($result['data'], $result['status']);
    }

    public function deleteAnnouncement($id)
    {
        $result = $this->announcementService->deleteAnnouncement($id);
        return response()->json($result['data'], $result['status']);
    }


    public function getAnnouncement($id)
    {
     $result = $this->announcementService->getAnnouncementById($id);
     return response()->json($result['data'], $result['status']);
    }

    public function getAllAnnouncements()
    {
     $result = $this->announcementService->getAllAnnouncements();
     return response()->json($result['data'], $result['status']);
    }
}
