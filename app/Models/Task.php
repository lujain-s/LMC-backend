<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'CreatorId',
        'Description',
        'Status',
        'Deadline',
        'Completed_at'
    ];

    public function UserTask(){
        return $this->hasMany(UserTask::class, 'UserTaskId');
    }

    public function userTasks()
    {
        return $this->hasMany(UserTask::class, 'TaskId');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'usertasks', 'TaskId', 'UserId')
                ->using(UserTask::class)
                ->withPivot('Completed', 'created_at', 'updated_at');
    }

    public function creator()
     {
      return $this->belongsTo(User::class, 'CreatorId');
     }

    public function Invoice(){
        return $this->hasMany(Invoice::class, 'InvoiceId');
    }
}
