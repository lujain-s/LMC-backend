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
        'holiday_id',
        'original_start_enroll',
        'original_end_enroll'
    ];

    public function schedule()
    {
        return $this->belongsTo(CourseSchedule::class, 'schedule_id');
    }

    public function holiday()
    {
        return $this->belongsTo(Holiday::class, 'holiday_id');
    }
}
