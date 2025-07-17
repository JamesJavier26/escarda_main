<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = ['name', 'short_name','is_active'];

    protected $cast =[
        'is_active' => 'boolean'
    ];

}
