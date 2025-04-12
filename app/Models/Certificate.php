<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [] ;

    public function Enrollment(){
        return $this->belongsTo(Enrollment::class, 'EnrollmentId');
    }
}
