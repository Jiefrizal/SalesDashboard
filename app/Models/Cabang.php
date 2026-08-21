<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    protected $fillable = [
        'nama',
        'spreadsheet_url',
        'stock_url',
        'lm_url',
        'target_tantangan',
        'target_classy_program',
        'acv',
        'target_reguler',
        'lm',
        'target_reguler_2026',
        'act_ytd_jan_2026',
        'target_perbulan_utk_2026',
        'stock_2024',
        'stock_2025',
        'stock_2026',
        'stock_breakdown',
        'stu_breakdown',
        'daily_performance',
        'leasing_breakdown',
        'monthly_sales',
    ];

    protected $casts = [
        'stock_breakdown' => 'array',
        'stu_breakdown' => 'array',
        'daily_performance' => 'array',
        'leasing_breakdown' => 'array',
        'monthly_sales' => 'array',
    ];

    public function getMonthlySalesData(): array
    {
        $sales = is_array($this->monthly_sales) ? $this->monthly_sales : [];
        $result = [];
        for ($i = 0; $i < 12; $i++) {
            $result[$i] = (int)($sales[$i] ?? 0);
        }
        return $result;
    }
}