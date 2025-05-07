<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendances';

    use HasFactory;

    protected $fillable = [
        "StudentId",
        "LessonId",
        "Bonus",
    ] ;

    public function User(){
        return $this->belongsTo(User::class, 'StudentId');
    }

    public function Lesson(){
        return $this->belongsTo(Lesson::class, 'LessonId');
    }
}
