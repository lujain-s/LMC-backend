<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = 'rooms';

    use HasFactory;

    protected $fillable = [
        'Capacity',
        'NumberOfRoom',
        'Status',
    ];

    public function CourseSchedule(){
        return $this->hasMany(CourseSchedule::class, 'RoomId');
    }
}
