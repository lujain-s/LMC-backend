<?php

namespace App\Repositories;

use App\Models\Complaint;
use App\Models\User;
use Spatie\Permission\Models\Role;

class ComplaintRepository
{
    public function createComplaint(array $data)
    {
     return Complaint::create([
        'TeacherId' => $data['TeacherId'],
        'Subject' => $data['Subject'],
        'Status' => $data['Status']
     ]);
    }

    public function findComplaintWithUser($id)
    {
        return Complaint::with('User')->find($id);
    }

    public function updateSubject($complaint, $subject)
    {
        $complaint->update([
            'Subject' => $subject,
            'updated_at' => now()
        ]);

        return $complaint;
    }

    public function findByIdAndTeacher($id, $teacherId)
    {
        return Complaint::where('id', $id)
                        ->where('TeacherId', $teacherId)
                        ->first();
    }

    public function delete($complaint)
    {
        $complaint->delete();
    }

    public function getAllWithUser()
    {
        return Complaint::with('User')->get();
    }

    public function findTeacherById($id)
    {
        $teacherRoleId = Role::where('name', 'Teacher')->value('id');

        return User::where('id', $id)
                   ->where('role_id', $teacherRoleId)
                   ->first();
    }

    public function getByTeacherId($teacherId)
    {
        return Complaint::where('TeacherId', $teacherId)
                        ->orderBy('created_at', 'desc')
                        ->get();
    }

    public function getComplaintsByStatus(string $status)
    {
        return Complaint::with('User')
                        ->where('Status', $status)
                        ->orderBy('created_at', 'desc')
                        ->get();
    }

    public function getComplaintsByStatusForTeacher($teacherId, string $status)
    {
     return Complaint::where('TeacherId', $teacherId)
                    ->where('Status', $status)
                    ->orderBy('created_at', 'desc')
                    ->get();
    }
}
