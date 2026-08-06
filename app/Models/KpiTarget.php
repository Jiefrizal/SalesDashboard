<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KpiTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'kpi_id',
        'ig_feed_target',
        'ig_reels_target',
        'fb_post_target',
        'tiktok_post_target',
        'ig_followers_target',
    ];

    public function kpi(): BelongsTo
    {
        return $this->belongsTo(Kpi::class, 'kpi_id');
    }
}
