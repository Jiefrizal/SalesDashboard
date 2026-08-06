<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\DailyReport;
use App\Models\MonthlyInsight;
use App\Services\DigitalMarketingService;
use Illuminate\Http\Request;

class DigitalMarketingController extends Controller
{
    protected $digitalMarketingService;

    public function __construct(DigitalMarketingService $digitalMarketingService)
    {
        $this->digitalMarketingService = $digitalMarketingService;
    }

    public function index(Request $request)
    {
        $tahun = (int) $request->input('tahun', now()->year);
        $bulan = (int) $request->input('bulan', now()->month);
        $cabangId = $request->filled('cabang_id') ? (int) $request->input('cabang_id') : null;

        $metrics = $this->digitalMarketingService->getDashboardMetrics($tahun, $bulan, $cabangId);
        $branches = Cabang::orderBy('nama')->get();

        // Chart Data Generation for Posting Trend (Days of the month)
        $dailyData = DailyReport::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->when($cabangId, fn($q) => $q->where('cabang_id', $cabangId))
            ->orderBy('tanggal')
            ->get();

        $chartLabels = [];
        $igPostsSeries = [];
        $fbPostsSeries = [];
        $tiktokPostsSeries = [];

        foreach ($dailyData->groupBy(fn($item) => $item->tanggal->format('d M')) as $dateLabel => $items) {
            $chartLabels[] = $dateLabel;
            $igPostsSeries[] = $items->sum('ig_feed') + $items->sum('ig_reels');
            $fbPostsSeries[] = $items->sum('fb_post') + $items->sum('fb_marketplace');
            $tiktokPostsSeries[] = $items->sum('tiktok_post') + $items->sum('tiktok_live');
        }

        // Views Trend Data from Monthly Insight
        $monthlyInsightsData = MonthlyInsight::with('cabang')
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->when($cabangId, fn($q) => $q->where('cabang_id', $cabangId))
            ->get();

        $platformDistribution = [
            'Instagram Views' => $monthlyInsightsData->sum('ig_views'),
            'Facebook Views' => $monthlyInsightsData->sum('fb_views'),
            'TikTok Views' => $monthlyInsightsData->sum('tiktok_views'),
        ];

        return view('digital_marketing.index', compact(
            'metrics',
            'branches',
            'tahun',
            'bulan',
            'cabangId',
            'chartLabels',
            'igPostsSeries',
            'fbPostsSeries',
            'tiktokPostsSeries',
            'platformDistribution'
        ));
    }
}
