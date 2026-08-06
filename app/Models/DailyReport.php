<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'cabang_id',
        'tanggal',
        'ig_feed',
        'ig_reels',
        'ig_story',
        'ig_followers_gained',
        'fb_post',
        'fb_marketplace',
        'fb_followers_gained',
        'tiktok_post',
        'tiktok_live',
        'tiktok_followers_gained',
        'google_rating',
        'google_review_gained',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }
}
