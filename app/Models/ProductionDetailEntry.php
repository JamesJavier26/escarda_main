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
        'salang_number',
        'time_per_salang',
        'weight_per_salang',
        'finished_weight_with_overflow',
        'finished_weight_without_overflow',
        'temperature',
        'beginning_weight',
        'finished_product_weight',
        'ending_weight',
        'raw_material_weight_salang',
        'quantity_made',
        'reject_quantity',
        'balance',
        'remarks',
        'correct_size',
        'correct_color',
        'correct_hardness',
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
