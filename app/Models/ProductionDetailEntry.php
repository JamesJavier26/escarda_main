<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductionDetailEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        // Relationships
        'production_detail_report_id',
        'pressman_id',

        // Core fields
        'production_date',
        'qty_to_be_produced',
        'unit_id',
        'daily_output',
        'reject_quantity',
        'day_no',

        // Machine & process fields
        'press_no',
        'temp_setting',
        'start_time',
        'process_duration',
        'insert_details',

        // Material tracking
        'beginning_weight_rawmat',
        'ending_weight_rawmat',
        'rawmat_size_salang',
        'rawmat_weight_salang',
        'overflow_weight_salang',
        'actual_finish_product_weight',

        // Computed values
        'remaining_balance',

        // Notes
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
    public function productionDetailReport()
    {
        return $this->belongsTo(ProductionDetailReport::class);
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
