<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlacementTest extends Model
{
    use HasFactory;

    protected $fillable = [];

    public function Language(){
        return $this->hasOne(Language::class, 'LanguageId');
    }

    public function User(){
        return $this->belongsTo(User::class, 'UserId');
    }
}
