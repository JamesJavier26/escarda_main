<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
   protected $fillable = [
        'name',
        'address',
        'telno',
        'faxno',
        'email',
        'mobile_no',
        'tin_no',
        'contact_person',
        'website',
        'status',
        'terms_of_payment',
        'business_type',
        'business_style',
        'customer_category',
        'remarks',
        'tax_exempt'
    ];

    protected $casts = [
        'tax_exempt' => 'boolean',
    ];
}
