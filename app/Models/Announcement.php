<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $table = 'announcements';

    use HasFactory;

    protected $fillable = [
        'CreatorId',
        'Title',
        'Content',
        'Photo'
    ];

    public function User(){
        return $this->belongsTo(User::class, 'UserId');
    }

    public function creator()
    {
     return $this->belongsTo(User::class, 'CreatorId');
    }
}
