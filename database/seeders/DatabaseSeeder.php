<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed auth users (super_admin + viewer)
        $this->call(UserSeeder::class);

        // Clear existing Cabang data
        \App\Models\Cabang::truncate();

        // Seed the 6 Cabang (dealers) with sales stats from the report image
        $dealers = [
            [
                'nama' => 'Pekanbaru',
                'target_tantangan' => 100,
                'acv' => 64,
                'target_reguler' => 90,
                'lm' => 74,
                'target_reguler_2026' => 1000,
                'act_ytd_jan_2026' => 463,
                'target_perbulan_utk_2026' => 107,
                'stock_2024' => 0,
                'stock_2025' => 0,
                'stock_2026' => 70,
                'stock_breakdown' => ['PREMIUM' => 47, 'SPORT' => 2, 'MOPED' => 1, 'ATM' => 9, 'CLASSY' => 11],
            ],
            [
                'nama' => 'Sei Pagar',
                'target_tantangan' => 75,
                'acv' => 67,
                'target_reguler' => 70,
                'lm' => 61,
                'target_reguler_2026' => 785,
                'act_ytd_jan_2026' => 426,
                'target_perbulan_utk_2026' => 72,
                'stock_2024' => 0,
                'stock_2025' => 0,
                'stock_2026' => 52,
                'stock_breakdown' => ['PREMIUM' => 36, 'SPORT' => 1, 'MOPED' => 2, 'ATM' => 6, 'CLASSY' => 7],
            ],
            [
                'nama' => 'Air Molek',
                'target_tantangan' => 100,
                'acv' => 111,
                'target_reguler' => 103,
                'lm' => 73,
                'target_reguler_2026' => 1172,
                'act_ytd_jan_2026' => 657,
                'target_perbulan_utk_2026' => 103,
                'stock_2024' => 0,
                'stock_2025' => 0,
                'stock_2026' => 38,
                'stock_breakdown' => ['PREMIUM' => 23, 'SPORT' => 2, 'MOPED' => 2, 'ATM' => 8, 'CLASSY' => 3],
            ],
            [
                'nama' => 'Sorek',
                'target_tantangan' => 165,
                'acv' => 133,
                'target_reguler' => 167,
                'lm' => 143,
                'target_reguler_2026' => 1908,
                'act_ytd_jan_2026' => 989,
                'target_perbulan_utk_2026' => 184,
                'stock_2024' => 0,
                'stock_2025' => 0,
                'stock_2026' => 188,
                'stock_breakdown' => ['PREMIUM' => 143, 'SPORT' => 4, 'MOPED' => 5, 'AT STD' => 1, 'ATM' => 16, 'CLASSY' => 19],
            ],
            [
                'nama' => 'Kandis',
                'target_tantangan' => 115,
                'acv' => 90,
                'target_reguler' => 120,
                'lm' => 84,
                'target_reguler_2026' => 1375,
                'act_ytd_jan_2026' => 638,
                'target_perbulan_utk_2026' => 147,
                'stock_2024' => 1,
                'stock_2025' => 0,
                'stock_2026' => 64,
                'stock_breakdown' => ['PREMIUM' => 41, 'MOPED' => 3, 'ATM' => 8, 'CLASSY' => 13],
            ],
            [
                'nama' => 'Medan',
                'target_tantangan' => 125,
                'acv' => 98,
                'target_reguler' => 132,
                'lm' => 102,
                'target_reguler_2026' => 1505,
                'act_ytd_jan_2026' => 757,
                'target_perbulan_utk_2026' => 150,
                'stock_2024' => 0,
                'stock_2025' => 3,
                'stock_2026' => 58,
                'stock_breakdown' => ['PREMIUM' => 38, 'SPORT' => 3, 'MOPED' => 2, 'ATM' => 5, 'CLASSY' => 13],
            ],
        ];

        foreach ($dealers as $dealer) {
            $currentMonth = \Carbon\Carbon::now()->month;
            $remainingMonths = 12 - $currentMonth + 1;
            $diff = $dealer['target_reguler_2026'] - $dealer['act_ytd_jan_2026'];
            $dealer['target_perbulan_utk_2026'] = $remainingMonths > 0 ? (int)round($diff / $remainingMonths) : 0;

            \App\Models\Cabang::create($dealer);
        }
    }
}
