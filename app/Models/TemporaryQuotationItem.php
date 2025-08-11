<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemporaryQuotationItem extends Model
{
    protected $fillable = [
        'temporary_quotation_id',
        'item_id',
        'item_name',
        'item_description',
        'remarks',
        'unit_id',
        'quantity',
        'unit_price',
        // Add other columns you want to handle (discounts, notes, etc.)
    ];

    public function temporaryQuotation()
    {
        return $this->belongsTo(TemporaryQuotation::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    protected $appends = ['final_unit_price'];

    public function getFinalUnitPriceAttribute()
    {
        // Add your pricing calculation logic here if needed
        return $this->unit_price;
    }
}
