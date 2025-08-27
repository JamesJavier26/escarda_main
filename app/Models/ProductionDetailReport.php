<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionDetailReport extends Model
{
    protected $fillable = [
        'jo_no',
        'job_order_id',
        'jo_item_id',
        'pressman_id',
        'press',
        'date_production',
        'production_type',
        'is_linear',
        'ply',
        'ply_types',
        'ply_type_others',
        'metal_insert',
        'metal_insert_types',
        'metal_insert_details',
        'salang_1',
        'salang_2',
        'salang_3',
        'pdr_status',
        'remarks',
    ];

    protected $casts = [
        'date_production' => 'datetime',
        'ply' => 'boolean',
        'metal_insert' => 'boolean',
        'salang_1' => 'array',
        'salang_2' => 'array',
        'salang_3' => 'array',
    ];

    public function jobOrder(){
        return $this->belongsTo(JobOrder::class);
    }
    public function jobOrderItem()
    {
        return $this->belongsTo(JobOrderItem::class, 'jo_item_id');
    }
    public function customer() {
        return $this->belongsTo(Customer::class);
    }
    public function item(){
        return $this->belongsTo(Item::class,'item_id');
    }
    public function material(){
        return $this->belongsTo(Material::class,'material_id');
    }
    // public function employee(){
    //     return $this->belongsTo(Employee::class,'pressman');
    // }
    public function user(){
        return $this->belongsTo(User::class,'pressman_id');
    }

    public function entries()
    {
        return $this->hasMany(ProductionDetailEntry::class, 'production_detail_report_id');
    }
}
