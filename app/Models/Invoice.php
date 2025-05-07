<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';

    use HasFactory;

    protected $fillable = [
        'TaskId',
        'Amount',
        'Status',
        'Image',
        'CreatorId',
    ];

    public function Task(){
        return $this->belongsTo(Task::class, 'TaskId');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'CreatorId');
    }

    public function recipients()
    {
        return $this->hasMany(InvoiceRecipient::class, 'InvoiceId');
    }
}
