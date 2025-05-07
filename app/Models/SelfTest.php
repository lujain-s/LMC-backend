<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelfTest extends Model
{
    protected $table = 'selftests';

    use HasFactory;

    protected $fillable = [
        'LessonId',
        'Title'
    ];

    public function Lesson(){
        return $this->belongsTo(Lesson::class, 'LessonId');
    }
}
