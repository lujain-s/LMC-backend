<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LMCInfo extends Model
{
    protected $table = 'lmc_infos';

    use HasFactory;

    protected $casts = ['Descriptions' => 'array'];

    protected $fillable = [
        'Title',
        'Descriptions',
        'Photo'
    ];
}
