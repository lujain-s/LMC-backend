<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'CourseId',
        'TeacherId',
        'Title',
        'Duration',
        'Mark',
    ];

    public function User(){
        return $this->belongsTo(User::class, 'TeacherId');
    }

    public function Course(){
        return $this->hasOne(Course::class, 'CourseId');
    }
}
