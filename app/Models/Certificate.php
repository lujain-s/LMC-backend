<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $table = 'certificates';

    use HasFactory;

    protected $fillable = [
        'EnrollmentId',
        'Path'
    ];

    public function Enrollment(){
        return $this->belongsTo(Enrollment::class, 'EnrollmentId');
    }
}
