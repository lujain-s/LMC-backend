<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleEnrollmentWorkingDay extends Model
{
    protected $fillable = [
        'schedule_id',
        'holiday_id',
        'working_days',
    ];

    protected $casts = [
        'working_days' => 'array',
    ];

    public function schedule()
    {
        return $this->belongsTo(CourseSchedule::class);
    }

    public function holiday()
    {
        return $this->belongsTo(Holiday::class);
    }
}
