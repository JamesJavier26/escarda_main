<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    // protected $table = "currencies";
    protected $fillable = ['name','short_name', 'symbol','description', 'is_active'];
    protected $casts = [
        'is_active' => 'boolean',
    ];
}
