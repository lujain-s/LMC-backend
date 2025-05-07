<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'items';

    use HasFactory;

    protected $fillable = [
        'LibraryId',
        'File',
        'Description'
    ];

    public function Library(){
        return $this->belongsTo(Library::class, 'LibraryId');
    }
}
