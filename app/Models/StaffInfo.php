<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'UserId',
        'Photo',
        'Description'
    ];

    public function User()
    {
        return $this->belongsTo(User::class, 'UserId');
    }
}
