<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductionDetailEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_detail_report_id',
        'pressman_id',
        'press',
        'production_date',
        'production_hour',
        'production_minute',
        'production_second',
        'temperature',
        'beginning_weight',
        'ending_weight',
        'raw_material_weight_salang',
        'finished_product_weight',
        'quantity_made',
        'reject_quantity',
        'balance',
        'remarks',
    ];

    // Relationships
    public function report()
    {
        return $this->belongsTo(ProductionDetailReport::class, 'production_detail_report_id');
    }

    public function pressman()
    {
        return $this->belongsTo(User::class, 'pressman_id');
    }
}
