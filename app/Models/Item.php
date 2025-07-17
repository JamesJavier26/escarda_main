<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    // use SoftDeletes; 
    protected $table ='items';
    protected $fillable = [
        'category_id',
        'number',
        'barcode',
        'name',
        'description',
        'unit_id',
        'remarks',
        'is_taxable',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_taxable' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function category()
    {
        return $this->belongsTo(ItemCategory::class, 'category_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function user()
    {
        return $this->belongsTo( User::class,'created_by');
    }
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
