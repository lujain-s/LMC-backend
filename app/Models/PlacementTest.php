<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlacementTest extends Model
{
    protected $table = 'placement_tests';

    use HasFactory;

    protected $fillable = [
        'GuestId',
        'LanguageId',
        'Level',
        'AudioScore',
        'ReadingScore',
        'SpeakingScore',
        'TotalScore',
        'Status'
    ];

    public function Language(){
        return $this->hasOne(Language::class, 'LanguageId');
    }

    public function User(){
        return $this->belongsTo(User::class, 'UserId');
    }
}
