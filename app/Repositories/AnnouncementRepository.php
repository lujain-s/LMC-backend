<?php

namespace App\Repositories;

use App\Models\Announcement;

class AnnouncementRepository
{
    public function createAnnouncement(array $data)
    {
        return Announcement::create($data);
    }

    /*public function findAnnouncementById($id)
    {
        return Announcement::find($id);
    }*/

    public function updateAnnouncement(Announcement $announcement, array $data)
    {
        $announcement->update($data);
        return $announcement;
    }

    public function deleteAnnouncement(Announcement $announcement)
    {
        return $announcement->delete();
    }

    /*public function getAllAnnouncements()
    {
     return Announcement::orderBy('created_at', 'desc')->get();
    }*/

    public function findAnnouncementById($id)
    {
     return Announcement::with('creator')->find($id);
    }

    public function getAllAnnouncements()
    {
     return Announcement::with('creator')->get();
    }
}
