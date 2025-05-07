<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleEnrollmentBackup extends Model
{
    use HasFactory;

    protected $table = 'schedule_enrollment_backups';

    protected $fillable = [
        'schedule_id',
        'original_start_enroll',
        'original_end_enroll'
    ];

    protected $dates = [
        'original_start_enroll',
        'original_end_enroll',
        'created_at',
        'updated_at'
    ];

    public function schedule()
    {
        return $this->belongsTo(CourseSchedule::class, 'schedule_id');
    }
}