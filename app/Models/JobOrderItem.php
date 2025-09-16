<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobOrderItem extends Model
{
    protected $fillable = [
        'job_order_id',
        'sales_order_id',
        'item_id',
        'item_name',
        'item_description',
        'mould_id',
        'material_id',
        'hardness',
        'thickness',
        'thickness_unit',
        'quantity',
        'unit_id',
        'unit_price',
        'new_mould_no',
        'new_mould_name',
        'note',
        'is_linear',
        'total_length'
    ];

    protected $casts = [
        'is_linear' => 'boolean'
    ];

    // Relationships
    public function item()
    {
        return $this->belongsTo(Item::class,'item_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class,'unit_id');
    }

    public function pricingEstimateItem()
    {
        return $this->belongsTo(PricingEstimateItem::class, 'pricing_item_id');
    }

    public function mould()
    {
        return $this->belongsTo(Mould::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    // NEW: Job Order relationship
    public function jobOrder()
    {
        return $this->belongsTo(JobOrder::class, 'job_order_id');
    }

    // NEW: Customer relationship through JobOrder
    public function customer()
    {
        return $this->hasOneThrough(
            Customer::class,
            JobOrder::class,
            'id',          // JobOrder primary key
            'id',          // Customer primary key
            'job_order_id',// JobOrderItem's foreign key
            'customer_id'  // JobOrder's foreign key
        );
    }

    // Accessor
    protected $appends = ['final_unit_price'];

    public function getFinalUnitPriceAttribute()
    {
        logger()->debug('FinalUnitPrice Debug', [
            'unit_price' => $this->unit_price,
            'discount' => $this->discount,
            'calculated' => $this->unit_price - ($this->discount ?? 0),
        ]);

        return $this->unit_price - ($this->discount ?? 0);
    }
}
