<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'courses';

    use HasFactory;

    protected $fillable = [
        "TeacherId",
        "LanguageId",
        "Description",
        "Status",
        "Level",
    ];

    public function User(){
        return $this->belongsTo(User::class, 'TeacherId');
    }

    public function Language(){
        return $this->belongsTo(Language::class, 'LanguageId');
    }

    public function Test(){
        return $this->hasOne(Test::class, 'TestId');
    }

    public function Lesson(){
        return $this->hasMany(Lesson::class, 'CourseId');
    }

    public function FlashCard(){
        return $this->hasMany(FlashCard::class, 'FlashCardId');
    }

    public function StudentProgress(){
        return $this->hasMany(StudentProgress::class, 'StudentProgressId');
    }

    public function CourseSchedule(){
        return $this->hasMany(CourseSchedule::class, 'CourseId');
    }

    public function Enrollment(){
        return $this->hasMany(Enrollment::class, 'CourseId');
    }
}
