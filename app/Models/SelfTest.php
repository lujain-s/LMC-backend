<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelfTest extends Model
{
    use HasFactory;

    protected $fillable = [];

    public function Lesson(){
        return $this->belongsTo(Lesson::class, 'LessonId');
    }
}
