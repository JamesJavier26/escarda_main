<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    protected $fillable = [
        'so_no',
        'date_ordered',
        'customer_id',
        'pr_no',
        'attention',
        'terms',
        'po_no',
        'sr_id',
        'jo_no',
        'is_vat',
        'approval_status',
        'approval_date',
        'approved_by',
        'remarks',
        'contact_person',
        'contact_no',
        'delivery_date',
        'quotation_id',
        'currency_id',
        'is_notified',
        'tax_status',
        'so_status'
    ];

    protected $casts = [
    'approval_date' => 'datetime',
    ];

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function salesRep() {
        return $this->belongsTo(User::class, 'sr_id');
    }
    public function currency() {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function comments(){
        return $this->hasMany(SalesOrderComment::class);
    }

    public function jobOrder()
    {
        return $this->hasOne(JobOrder::class);
    }
    
    public function items()
    {
            return $this->hasMany(JobOrderItem::class, 'sales_order_id');
    }
    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
    public function attachments()
    {
        return $this->hasMany(SalesOrderAttachment::class);
    }
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public static function generateSONumber()
    {
        $last = self::latest('id')->first();

        if (!$last) {
            return 'SO-0001';
        }

        $lastNumber = (int) substr($last->so_no, 3);

        $newNumber = $lastNumber + 1;

        return 'SO-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
    public function pricing_estimate()
    {
        return $this->belongsTo(PricingEstimate::class);
    }

}
