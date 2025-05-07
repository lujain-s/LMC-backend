<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $table = 'holidays';
    use HasFactory;
    protected $fillable = [
        "Name",
        "Description",
        "StartDate","EndDate" ,/*"isRecurring",*/"AffectsClasses"
    ] ;


 /*   public function affectedLessons()
{
    return Lesson::whereBetween('Date', [$this->StartDate, $this->EndDate])->get();
}*/

}
