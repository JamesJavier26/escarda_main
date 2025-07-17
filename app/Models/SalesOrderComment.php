<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderComment extends Model
{
    protected $fillable = ['sales_order_id', 'comment', 'user_id'];
    public function salesOrder(){
        return $this->belongsTo(SalesOrder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
