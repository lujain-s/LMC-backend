<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Library extends Model
{
    protected $table = 'libraries';

    use HasFactory;

    protected $fillable = [
        'LanguageId'
    ];

    public function language(){
        return $this->belongsTo(Language::class, 'LanguageId');
    }

    public function items(){
        return $this->hasMany(Item::class, 'LibraryId');
    }
}
