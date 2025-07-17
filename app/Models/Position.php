<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'code',
        'description',
        'salary_min',
        'salary_max',
        'is_managerial',
        'is_active'
    ];

    protected $casts = [
        'is_managerial' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
