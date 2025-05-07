<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceRecipient extends Model
{

    protected $table = 'invoice_recipients';
    use HasFactory;

    protected $fillable = [
        'InvoiceId',
        'UserId',
        'Status'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'InvoiceId');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'UserId');
    }

   
}
