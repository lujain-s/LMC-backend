<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $table = 'languages';

    use HasFactory;

    protected $fillable = [
        'Name',
        'Description'
    ];

    public function PlacementTest(){
        return $this->hasOne(PlacementTest::class, 'PlacementTestId');
    }

    public function Library(){
        return $this->belongsTo(Library::class, 'LibraryId');
    }
    public function Course(){
        return $this->hasMany(Course::class, 'CourseId');
    }
}
