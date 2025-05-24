<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class enrollment_day extends Model
{
    protected $table = 'enrollment_days';

    use HasFactory;

    protected $fillable =[
        "CourseId",
        "Enroll_Date",
    ];

}
