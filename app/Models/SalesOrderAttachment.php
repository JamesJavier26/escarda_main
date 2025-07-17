<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderAttachment extends Model
{
    protected $fillable = [
        'sales_order_id','label','metadata'
    ];
    protected $casts = [
        'metadata' => 'array',
    ];
    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

}
