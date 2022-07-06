<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetails extends Model
{
    use HasFactory;
    protected $table = 'invoices_details';
    protected $fillable = [
        'invoice_id',
        'invoice_number',
        'product',
        'section_id',
        'status',
        'value_status',
        'note',
        'user',
        'payment_date',
    ];
}
