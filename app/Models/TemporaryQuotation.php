<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemporaryQuotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'sr_id',
        'customer_id',
        'attention',
        'terms',
        'pr_no',
        'delivery_date',
        'remarks',
        'vat',
        'pe_remarks',
        'sr_remarks',
        'currency_id'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function salesRep()
    {
        return $this->belongsTo(User::class, 'sr_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
    public function items()
    {
        return $this->hasMany(TemporaryQuotationItem::class);
    }
}
