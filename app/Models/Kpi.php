<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Kpi extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun',
        'bulan',
    ];

    public function target(): HasOne
    {
        return $this->hasOne(KpiTarget::class, 'kpi_id');
    }
}
