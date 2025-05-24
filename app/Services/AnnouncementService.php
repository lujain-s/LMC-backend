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
            'Media' => 'sometimes|file|mimes:m4v,webm,flv,wmv,mov,mkv,avi,mp4,tif,tiff,heic,svg,bmp,webp,gif,png,jpeg,jpg'
        ]);

        if ($validator->fails()) {
            return ['data' => ['message' => 'Validator failed', 'errors' => $validator->errors()], 'status' => 403];
        }

        $MediaUrl = null;

        if ($request->hasFile('Media')) {
            $media = $request->file('Media');
            $new_name_media = time() . '_' . str_replace(' ', '_', $media->getClientOriginalName());
            $media->move(public_path('storage/announcementMedia'), $new_name_media);
            $MediaUrl = url('storage/announcementMedia/' . $new_name_media);

            if (!file_exists(public_path('storage/announcementMedia/' . $new_name_media))) {
                throw new Exception('Failed to upload image', 500);
            }
        }

        $announcement = $this->announcementRepo->createAnnouncement([
            'CreatorId' => Auth::id(),
            'Title' => $request->Title,
            'Content' => $request->Content,
            'Media' => $MediaUrl, // سيُمرر null إذا لم تكن هناك صورة
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
        'Media' => 'sometimes|file|mimes:m4v,webm,flv,wmv,mov,mkv,avi,mp4,tif,tiff,heic,svg,bmp,webp,gif,png,jpeg,jpg'
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

     if ($request->hasFile('Media')) {
        $media = $request->file('Media');
        $new_name_media = time() . '_' . str_replace(' ', '_', $media->getClientOriginalName());
        $media->move(public_path('storage/announcementMedia'), $new_name_media);
        $MediaUrl = url('storage/announcementMedia/' . $new_name_media);

        if (!file_exists(public_path('storage/announcementMedia/' . $new_name_media))) {
            throw new Exception('Failed to upload madia', 500);
        }

        $dataToUpdate['Media'] = $MediaUrl;
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
        if ($announcement->Media && file_exists(public_path('storage/announcementMedia/' . basename($announcement->Media)))) {
            unlink(public_path('storage/announcementMedia/' . basename($announcement->Media)));
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
