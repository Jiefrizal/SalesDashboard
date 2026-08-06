<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyInsight extends Model
{
    use HasFactory;

    protected $fillable = [
        'cabang_id',
        'tahun',
        'bulan',
        'ig_views',
        'fb_views',
        'tiktok_views',
    ];

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }
}
