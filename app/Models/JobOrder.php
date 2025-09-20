<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobOrder extends Model
{
    protected $fillable = [
        'jo_no', 'sales_order_id', 'user_id','duedate','remarks', 'instructions', 'delivery_details',
        'jo_status' ,'approval_status', 'approval_date','approved_by', 'is_delivered',
    ];
    protected $casts = [
        'duedate' => 'datetime',
        'approval_date' => 'datetime',
        'is_delivered' => 'boolean',
    ];
    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    // Access customer through salesOrder
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id', 'salesOrder');
    }

        // Access Quotation through SalesOrder
    public function quotation()
    {
        return $this->salesOrder ? $this->salesOrder->quotation() : null;
    }

    // Access Pricing Estimate through Quotation
    public function pricingEstimate()
    {
        $quotation = $this->quotation();
        return $quotation ? $quotation->pricing_estimate() : null;
    }
    

    public function jo_items()
    {
        return $this->hasMany(JobOrderItem::class);
    }

    public function productionDetailReports()
    {
        return $this->hasMany(ProductionDetailReport::class);
    }
    public function isDraft()
    {
        return $this->approval_status === 'draft';
    }

    public function isPending()
    {
        return $this->approval_status === 'pending';
    }
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
