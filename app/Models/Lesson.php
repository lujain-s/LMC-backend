<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [];

    public function Course(){
        return $this->belongsTo(Course::class, 'CourseId');
    }

    public function SelfTest(){
        return $this->hasMany(SelfTest::class, 'SelfTestId');
    }

    public function FlashCard(){
        return $this->hasMany(FlashCard::class, 'FlashCardId');
    }
}
