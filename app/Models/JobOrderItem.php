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
        'is_linear',
        'total_length'
    ];
    protected $casts = [
        'is_linear' => 'boolean'
    ];

    public function item(){
        return $this->belongsTo(Item::class,'item_id');
    }
    
    public function unit(){
        return $this->belongsTo(Unit::class,'unit_id');
    }
    
    public function mould()    { return $this->belongsTo(Mould::class); }
    public function material()    { return $this->belongsTo(Material::class); }
}
