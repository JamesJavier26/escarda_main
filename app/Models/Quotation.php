<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    // protected $table = "quotations";
    protected $fillable = [
        'quote_no','version','parent_id', 'pricing_estimate_id', 'quote_date', 
        'currency_id', 'quote_status','revision_reason','rejected_pdf_path', 'is_final','quote_remarks'
    ];
    public function pricing_estimate()
    {
        return $this->belongsTo(PricingEstimate::class);
    }

    public function quotation_items()
    {
        return $this->hasMany(QuotationItem::class);
    }
    public function salesOrder()
    {
        return $this->hasOne(SalesOrder::class);
    }
}
