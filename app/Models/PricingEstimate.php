<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingEstimate extends Model
{
    protected $fillable = [
        'pe_no', 'sr_id', 'customer_id', 'attention', 'terms', 'pr_no',
        'delivery_date', 'remarks', 'pe_status','revision_reason', 'vat', 'created_by',
        'pe_remarks', 'sr_remarks', 'currency_id',
    ];
    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function salesRep() {
        return $this->belongsTo(User::class, 'sr_id');
    }

    public function pe_items() {
        return $this->hasMany(PricingEstimateItem::class, 'pricing_estimate_id');
    }
    public function attachments()
    {
        return $this->hasMany(PricingEstimateAttachment::class);
    }

    public function currency(){
        return $this->belongsTo(Currency::class);
    }
    
    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }
    public function sales_orders()
    {
        return $this->hasMany(SalesOrder::class);
    }
    public function getapprovedQuotationAttribute()
    {
        return $this->quotations()
            ->where('quote_status', 'approved')
            ->where('is_final', true)
            ->latest()
            ->first();
    }
    public function getApprovedQuoteNumberAttribute()
    {
        return $this->quotations()
            ->where('quote_status', 'approved')
            ->where('is_final', true)
            ->value('quote_no');
    }
    public function getQuoteNumberAttribute()
    {
        return $this->quotations()
            // ->where('quote_status', 'approved')
            // ->where('is_final', true)
            ->latest()
            ->value('quote_no');
    }
    public function getLatestQuoteAttribute()
    {
        return $this->quotations()
            ->latest()
            ->first();
    }

}
