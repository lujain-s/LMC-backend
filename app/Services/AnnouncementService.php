<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Repositories\AnnouncementRepository;
use Exception;


class AnnouncementService
{
    protected $announcementRepo;

    public function __construct(AnnouncementRepository $announcementRepo)
    {
        $this->announcementRepo = $announcementRepo;
    }


    public function createAnnouncement(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Title' => 'required|string|max:255',
            'Content' => 'required|string',
            'Photo' => 'sometimes|file|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            return ['data' => ['message' => 'Validator failed'], 'status' => 403];
        }

        $imageUrl = null;

        if ($request->hasFile('Photo')) {
            $image = $request->file('Photo');
            $new_name = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('storage/Announcements_photos'), $new_name);
            $imageUrl = url('storage/Announcements_photos/' . $new_name);

            if (!file_exists(public_path('storage/Announcements_photos/' . $new_name))) {
                throw new Exception('Failed to upload image', 500);
            }
        }

        $announcement = $this->announcementRepo->createAnnouncement([
            'CreatorId' => Auth::id(),
            'Title' => $request->Title,
            'Content' => $request->Content,
            'Photo' => $imageUrl, // سيُمرر null إذا لم تكن هناك صورة
        ]);

        return ['data' => ['message' => 'Announcement created successfully.', 'announcement' => $announcement], 'status' => 201];
    }

    public function updateAnnouncement(Request $request, $id)
    {
     $announcement = $this->announcementRepo->findAnnouncementById($id);

     if (!$announcement) {
        return ['data' => ['message' => 'Announcement not found'], 'status' => 403];
     }

     if ($announcement->CreatorId !== Auth::id()) {
        return ['data' => ['message' => 'Unauthorized'], 'status' => 403];
     }

     $validator = Validator::make($request->all(), [
        'Title' => 'sometimes|string|max:255',
        'Content' => 'sometimes|string',
        'Photo' => 'sometimes|file|mimes:jpeg,png,jpg,gif,svg|max:2048'
     ]);

     if ($validator->fails()) {
        return ['data' => ['message' => 'Validator failed'], 'status' => 403];
     }

     $dataToUpdate = [];

     if ($request->has('Title')) {
        $dataToUpdate['Title'] = $request->Title;
     }

     if ($request->has('Content')) {
        $dataToUpdate['Content'] = $request->Content;
     }

     if ($request->hasFile('Photo')) {
        $image = $request->file('Photo');
        $new_name = time() . '_' . $image->getClientOriginalName();
        $image->move(public_path('storage/Announcements_photos'), $new_name);
        $imageUrl = url('storage/Announcements_photos/' . $new_name);

        if (!file_exists(public_path('storage/Announcements_photos/' . $new_name))) {
            throw new \Exception('Failed to upload image', 500);
        }

        $dataToUpdate['Photo'] = $imageUrl;
     }

     if (empty($dataToUpdate)) {
        return ['data' => ['message' => 'No data provided for update'], 'status' => 400];
     }

     $updated = $this->announcementRepo->updateAnnouncement($announcement, $dataToUpdate);

     return ['data' => ['message' => 'Announcement updated successfully.', 'announcement' => $updated], 'status' => 200];
    }


    public function deleteAnnouncement($id)
    {
        $announcement = $this->announcementRepo->findAnnouncementById($id);

        if (!$announcement) {
            return ['data' => ['message' => 'Announcement not found or deleted before'], 'status' => 403];
        }

        if ($announcement->CreatorId !== Auth::id()) {
            return ['data' => ['message' => 'Unauthorized or you are not the creator'], 'status' => 403];
        }

        // Optional: Delete image file if exists
        if ($announcement->Photo && file_exists(public_path('storage/Announcements_photos/' . basename($announcement->Photo)))) {
            unlink(public_path('storage/Announcements_photos/' . basename($announcement->Photo)));
        }

        $this->announcementRepo->deleteAnnouncement($announcement);

        return ['data' => ['message' => 'Announcement deleted successfully.'], 'status' => 200];
    }

    public function getAnnouncementById($id)
    {
        $announcement = $this->announcementRepo->findAnnouncementById($id);

        if (!$announcement) {
            return ['data' => ['message' => 'Announcement not found'], 'status' => 404];
        }

        return ['data' => ['announcement' => $announcement], 'status' => 200];
    }

    public function getAllAnnouncements()
    {
        $announcements = $this->announcementRepo->getAllAnnouncements();
        return ['data' => ['announcements' => $announcements], 'status' => 200];
    }

}
