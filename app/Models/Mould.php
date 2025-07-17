<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mould extends Model
{
    protected $fillable =['no','name','is_active'];

    protected $casts = [
        'is_active' => 'boolean'
    ];
}
