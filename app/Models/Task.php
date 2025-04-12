<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [];

    public function UserTask(){
        return $this->hasMany(UserTask::class, 'UserTaskId');
    }

    public function Invoice(){
        return $this->hasMany(Invoice::class, 'InvoiceId');
    }
}
