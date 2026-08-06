<?php

namespace App\Services;

use App\Models\Cabang;
use App\Models\DailyReport;
use App\Models\Kpi;
use App\Models\MonthlyInsight;

class DigitalMarketingService
{
    public function getDashboardMetrics(?int $tahun = null, ?int $bulan = null, ?int $cabangId = null): array
    {
        $tahun = $tahun ?? now()->year;
        $bulan = $bulan ?? now()->month;

        $branchQuery = Cabang::query();
        if ($cabangId) {
            $branchQuery->where('id', $cabangId);
        }
        $branches = $branchQuery->get();
        $totalBranches = $branches->count();

        // Cabang yang belum menginput laporan hari ini
        $today = now()->format('Y-m-d');
        $submittedBranchIdsToday = DailyReport::whereDate('tanggal', $today)->pluck('cabang_id')->toArray();
        
        $missingBranchesToday = Cabang::whereNotIn('id', $submittedBranchIdsToday)->get();

        // Target KPI Bulanan
        $kpi = Kpi::with('target')->where('tahun', $tahun)->where('bulan', $bulan)->first();
        $target = $kpi?->target;

        // Agregasi Laporan Harian
        $dailyQuery = DailyReport::whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan);
        if ($cabangId) {
            $dailyQuery->where('cabang_id', $cabangId);
        }
        $dailyReports = $dailyQuery->get();

        $totalIgFeed = $dailyReports->sum('ig_feed');
        $totalIgReels = $dailyReports->sum('ig_reels');
        $totalIgStory = $dailyReports->sum('ig_story');
        $totalIgFollowersGained = $dailyReports->sum('ig_followers_gained');

        $totalFbPost = $dailyReports->sum('fb_post');
        $totalFbMarketplace = $dailyReports->sum('fb_marketplace');
        $totalFbFollowersGained = $dailyReports->sum('fb_followers_gained');

        $totalTiktokPost = $dailyReports->sum('tiktok_post');
        $totalTiktokLive = $dailyReports->sum('tiktok_live');
        $totalTiktokFollowersGained = $dailyReports->sum('tiktok_followers_gained');

        $avgGoogleRating = $dailyReports->avg('google_rating') ?: 0;
        $totalGoogleReviewGained = $dailyReports->sum('google_review_gained');

        $totalPosts = $totalIgFeed + $totalIgReels + $totalIgStory + $totalFbPost + $totalFbMarketplace + $totalTiktokPost;
        $totalFollowersGrowth = $totalIgFollowersGained + $totalFbFollowersGained + $totalTiktokFollowersGained;

        // Agregasi Monthly Insight Views
        $insightQuery = MonthlyInsight::where('tahun', $tahun)->where('bulan', $bulan);
        if ($cabangId) {
            $insightQuery->where('cabang_id', $cabangId);
        }
        $monthlyInsights = $insightQuery->get();

        $totalViews = $monthlyInsights->sum('ig_views') + $monthlyInsights->sum('fb_views') + $monthlyInsights->sum('tiktok_views');

        // Leaderboard Calculation
        $leaderboard = $this->calculateLeaderboard($tahun, $bulan);

        // Overall System Achievement
        $overallAchievement = $leaderboard->avg('achievement_pct') ?: 0;
        $statusBadge = self::getStatusBadge($overallAchievement);

        return [
            'total_branches' => $totalBranches,
            'missing_branches_today' => $missingBranchesToday,
            'missing_count_today' => $missingBranchesToday->count(),
            'total_posts' => $totalPosts,
            'followers_growth' => $totalFollowersGrowth,
            'total_views' => $totalViews,
            'avg_google_rating' => round($avgGoogleRating, 1),
            'google_reviews' => $totalGoogleReviewGained,
            'overall_achievement' => round($overallAchievement, 1),
            'status_badge' => $statusBadge,
            'leaderboard' => $leaderboard,
            'kpi_target' => $target,
        ];
    }

    public function calculateLeaderboard(int $tahun, int $bulan)
    {
        $branches = Cabang::all();
        $kpi = Kpi::with('target')->where('tahun', $tahun)->where('bulan', $bulan)->first();
        $target = $kpi?->target;

        $results = collect();

        foreach ($branches as $branch) {
            $reports = DailyReport::where('cabang_id', $branch->id)
                ->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->get();

            if (!$target || $reports->isEmpty()) {
                $achievementPct = 0;
            } else {
                $igFeedPct = ($target->ig_feed_target ?? 0) > 0 ? ($reports->sum('ig_feed') / $target->ig_feed_target) * 100 : 100;
                $igReelsPct = ($target->ig_reels_target ?? 0) > 0 ? ($reports->sum('ig_reels') / $target->ig_reels_target) * 100 : 100;
                $fbPostPct = ($target->fb_post_target ?? 0) > 0 ? ($reports->sum('fb_post') / $target->fb_post_target) * 100 : 100;
                $tiktokPostPct = ($target->tiktok_post_target ?? 0) > 0 ? ($reports->sum('tiktok_post') / $target->tiktok_post_target) * 100 : 100;
                $follPct = ($target->ig_followers_target ?? 0) > 0 ? ($reports->sum('ig_followers_gained') / $target->ig_followers_target) * 100 : 100;

                $achievementPct = round(($igFeedPct + $igReelsPct + $fbPostPct + $tiktokPostPct + $follPct) / 5, 1);
            }

            $badge = self::getStatusBadge($achievementPct);

            $results->push([
                'cabang_id' => $branch->id,
                'kode' => 'CBG-' . sprintf('%02d', $branch->id),
                'nama_cabang' => $branch->nama,
                'total_posts' => $reports->sum('ig_feed') + $reports->sum('ig_reels') + $reports->sum('fb_post') + $reports->sum('tiktok_post'),
                'total_followers_gained' => $reports->sum('ig_followers_gained') + $reports->sum('fb_followers_gained') + $reports->sum('tiktok_followers_gained'),
                'achievement_pct' => $achievementPct,
                'badge' => $badge,
            ]);
        }

        return $results->sortByDesc('achievement_pct')->values()->map(function ($item, $index) {
            $item['rank'] = $index + 1;
            return $item;
        });
    }

    public static function getStatusBadge(float $percentage): array
    {
        if ($percentage >= 100) {
            return [
                'label' => 'Excellent (>100%)',
                'color' => 'blue',
                'bg_class' => 'bg-blue-600 text-white',
                'badge_tailwind' => 'bg-blue-100 text-blue-800 border-blue-300',
                'hex' => '#2563eb',
            ];
        } elseif ($percentage >= 80) {
            return [
                'label' => 'Hijau (80%-100%)',
                'color' => 'emerald',
                'bg_class' => 'bg-emerald-600 text-white',
                'badge_tailwind' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                'hex' => '#059669',
            ];
        } elseif ($percentage >= 70) {
            return [
                'label' => 'Kuning (70%-80%)',
                'color' => 'amber',
                'bg_class' => 'bg-amber-500 text-white',
                'badge_tailwind' => 'bg-amber-100 text-amber-800 border-amber-300',
                'hex' => '#d97706',
            ];
        } else {
            return [
                'label' => 'Merah (<70%)',
                'color' => 'rose',
                'bg_class' => 'bg-rose-600 text-white',
                'badge_tailwind' => 'bg-rose-100 text-rose-800 border-rose-300',
                'hex' => '#e11d48',
            ];
        }
    }
}
