<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonBackup extends Model
{
    protected $table = 'lesson_backups';

    use HasFactory;
    protected $fillable = [
        "CourseId",
        "Title",
        "Date","Start_Time" ,/*"isRecurring",*/"End_Time"
    ] ;

    public function course()
{
    return $this->belongsTo(Course::class, 'CourseId');
}


}