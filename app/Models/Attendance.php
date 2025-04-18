<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        "StudentId",
        "CourseId",
        "Bonus",
    ] ;

    public function User(){
        return $this->belongsTo(User::class, 'StudentId');
    }

    public function Course(){
        return $this->belongsTo(Course::class, 'CourseId');
    }
}
