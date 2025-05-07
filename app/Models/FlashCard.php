<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlashCard extends Model
{
    protected $table = 'flash_cards';

    use HasFactory;

    protected $fillable = [
        'LessonId',
        'CourseId',
        'Content',
        'Translation'
    ];

    public function Lesson(){
        return $this->belongsTo(Lesson::class, 'LessonId');
    }

    public function Course(){
        return $this->belongsTo(Course::class, 'CourseId');
    }
}
