<?php

namespace Database\Seeders;

use App\Models\Cabang;
use App\Models\DailyReport;
use App\Models\Kpi;
use App\Models\KpiTarget;
use App\Models\MonthlyInsight;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DigitalMarketingSeeder extends Seeder
{
    public function run(): void
    {
        $tahun = now()->year;
        $bulan = now()->month;

        // Ensure KPI and target exist
        $kpi = Kpi::firstOrCreate(
            ['tahun' => $tahun, 'bulan' => $bulan]
        );

        KpiTarget::firstOrCreate(
            ['kpi_id' => $kpi->id],
            [
                'ig_feed_target' => 15,
                'ig_reels_target' => 20,
                'fb_post_target' => 15,
                'tiktok_post_target' => 25,
                'ig_followers_target' => 200,
            ]
        );

        $cabangs = Cabang::all();
        if ($cabangs->isEmpty()) {
            return;
        }

        // Create sample daily reports for the current month up to yesterday
        $daysInMonth = min(now()->day - 1, 28);
        if ($daysInMonth < 1) $daysInMonth = 1;

        foreach ($cabangs as $index => $cabang) {
            // Seed daily reports
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $date = Carbon::create($tahun, $bulan, $d)->format('Y-m-d');
                
                DailyReport::updateOrCreate(
                    [
                        'cabang_id' => $cabang->id,
                        'tanggal' => $date,
                    ],
                    [
                        'ig_feed' => rand(0, 2),
                        'ig_reels' => rand(0, 2),
                        'ig_story' => rand(1, 4),
                        'ig_followers_gained' => rand(5, 25),
                        'fb_post' => rand(0, 2),
                        'fb_marketplace' => rand(0, 3),
                        'fb_followers_gained' => rand(2, 15),
                        'tiktok_post' => rand(0, 2),
                        'tiktok_live' => rand(0, 1),
                        'tiktok_followers_gained' => rand(5, 30),
                        'google_rating' => 4.5 + (rand(0, 5) / 10),
                        'google_review_gained' => rand(0, 5),
                    ]
                );
            }

            // Seed Monthly Insights
            MonthlyInsight::updateOrCreate(
                [
                    'cabang_id' => $cabang->id,
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                ],
                [
                    'ig_views' => rand(15000, 85000),
                    'fb_views' => rand(10000, 50000),
                    'tiktok_views' => rand(20000, 120000),
                ]
            );
        }
    }
}
