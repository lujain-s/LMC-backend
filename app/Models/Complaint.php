<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        "TeacherId",
        "Subject",
        "Status",
    ];

    public function User(){
        return $this->belongsTo(User::class, 'TeacherId');
    }

    public function Task(){
        return $this->belongsTo(Task::class, 'TaskId');
    }
}
