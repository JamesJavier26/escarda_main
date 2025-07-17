<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Unit extends Model
{
    use LogsActivity;
    protected $table = 'units';
    protected $fillable = [
        'name', 'singular_name','short_name', 'type',
        'decimal_places', 'description', 'is_active',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() 
            ->logOnlyDirty() 
            ->useLogName('unit');
    }
    public function tapActivity(Activity $activity, string $eventName)
    {
        $activity->description = ucfirst($eventName) . " unit";
    }
}
