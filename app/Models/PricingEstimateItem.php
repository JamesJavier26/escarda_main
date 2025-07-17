<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingEstimateItem extends Model
{
    protected $table = "pricing_estimate_items";

    protected $fillable = [
        'pricing_estimate_id','item_id', 'item_name', 'item_description', 'pe_remarks', 'unit_id',
        'mould_id', 'discount_type', 'discount_amount', 'quantity',
        'unit_price', 'price_factor', 'factor_rate', 'per_quantity',
        'quot', 'notes', 'offer', 'is_offer','parent_item_id', 'sum_charge',
        'ref_item_id', 'is_active',
    ];

    protected $casts = [
        'price_factor' => 'array', 
        'quot'         => 'boolean',
        'is_offer'     => 'boolean',
        'is_active'    => 'boolean',
    ];
     public function estimate() { return $this->belongsTo(PricingEstimate::class,'pricing_estimate_id'); }
    // public function pricing_estimate(){
    //     return $this->belongsTo(PricingEstimate::class, 'pricing_estimate_id');
    // }
    public function item(){
        return $this->belongsTo(Item::class,'item_id');
    }

    public function unit(){
        return $this->belongsTo(Unit::class,'unit_id');
    }
    
    public function mould()    { return $this->belongsTo(Mould::class); }

    public function offers()
    {
        return $this->hasMany(PricingEstimateItem::class, 'parent_item_id');
    }

    public function getFinalUnitPriceAttribute()
    {
        $price = $this->unit_price;

        if ($this->discount_type === 'percentage') {
            $discount = ($price * $this->discount_amount) / 100;
        } elseif ($this->discount_type === 'fixed') {
            $discount = $this->discount_amount;
        } else {
            $discount = 0;
        }

        return max(0, $price - $discount); // prevent negative prices
    }


}
