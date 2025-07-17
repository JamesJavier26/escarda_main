<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemCategory extends Model
{
    protected $table = "item_categories";
    protected $fillable = ['name','code','description','is_active'];

    protected $casts = ['is_actie' => 'boolean'];

    public function items(){
        return $this->hasMany(Item::class);
    }
}
