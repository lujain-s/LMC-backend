<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notes extends Model
{
    protected $table = 'notes';

    use HasFactory;

    protected $fillable = [
        'StudentId',
        'Content',
    ];

    public function User(){
        return $this->hasOne(User::class, 'StudentId');
    }
}
