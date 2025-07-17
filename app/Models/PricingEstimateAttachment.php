<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Storage;

class PricingEstimateAttachment extends Model
{
    // protected $guarded = [];
    protected $table = "pricing_estimate_attachments";
    protected $fillable = [
        'pricing_estimate_id','label','metadata'
    ];
    protected $casts = [
        'metadata' => 'array',
    ];

     public function estimate()
    {
        return $this->belongsTo(PricingEstimate::class, 'pricing_estimate_id');
    }

    // Small helper so you can later get full URL
    public function getUrlAttribute(): string
    {
        return Storage::disk($this->metadata['disk'] ?? 'public')
                      ->url($this->metadata['path']);
    }
}
