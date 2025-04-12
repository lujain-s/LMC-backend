<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [];

    public function CourseSchedule(){
        return $this->hasMany(CourseSchedule::class, 'CourseScheduleId');
    }
}
