<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [] ;

    public function User(){
        return $this->belongsTo(User::class, 'UserId');
    }

    public function Course(){
        return $this->belongsTo(Course::class, 'CourseId');
    }
}
