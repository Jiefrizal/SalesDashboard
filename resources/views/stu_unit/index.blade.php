@extends('layouts.app')

@section('content')

@php
$totalTargetTantangan = 0;
$totalTargetMin = 0;
$totalAcv = 0;
$totalTargetReguler = 0;
$totalLm = 0;
$totalTargetReguler2026 = 0;
$totalActYtdJan2026 = 0;

$grandSeriesTotals = [];
$grandCategoryTotals = [
    'PREMIUM' => 0,
    'ATM'     => 0,
    'CLASSY'  => 0,
    'MOPED'   => 0,
    'SPORT'   => 0,
    'AT STD'  => 0,
];

$exactData = [];

foreach($cabangs as $cabang) {
    $totalTargetTantangan += $cabang->target_tantangan;
    $totalAcv += $cabang->acv;
    $totalTargetReguler += $cabang->target_reguler;
    $totalLm += $cabang->lm;
    $totalTargetReguler2026 += $cabang->target_reguler_2026;
    $totalActYtdJan2026 += $cabang->act_ytd_jan_2026;

    if ($cabang->target_tantangan > 0 && $cabang->target_reguler > 0) {
        $minTarget = min($cabang->target_tantangan, $cabang->target_reguler);
    } else {
        $minTarget = max($cabang->target_tantangan, $cabang->target_reguler);
    }
    $totalTargetMin += $minTarget;

    $cName = $cabang->nama;
    $cBreakdown = $cabang->stu_breakdown ?: [];
    $exactData[$cName] = $cBreakdown;

    foreach ($cBreakdown as $cat => $sList) {
        $catSum = 0;
        if (is_array($sList)) {
            foreach ($sList as $sName => $count) {
                $count = (int)$count;
                $grandSeriesTotals[$cat][$sName] = ($grandSeriesTotals[$cat][$sName] ?? 0) + $count;
                $catSum += $count;
            }
        }
        if (isset($grandCategoryTotals[$cat])) {
            $grandCategoryTotals[$cat] += $catSum;
        } else {
            $grandCategoryTotals[$cat] = ($grandCategoryTotals[$cat] ?? 0) + $catSum;
        }
    }
}

$overallAcvPercent = $totalTargetMin > 0 ? ($totalAcv / $totalTargetMin) * 100 : 0;
$overallRegulerAcvPercent = $totalTargetReguler > 0 ? ($totalAcv / $totalTargetReguler) * 100 : 0;
$overallGrowthPercent = $totalLm > 0 ? ($totalAcv / $totalLm) * 100 : 0;
$overallYtdPercent = $totalTargetReguler2026 > 0 ? ($totalActYtdJan2026 / $totalTargetReguler2026) * 100 : 0;
@endphp

<!-- Outer Glassmorphic Container -->
<div class="bg-gradient-to-br from-blue-950 via-slate-900 to-blue-900 text-white rounded-2xl lg:rounded-3xl p-4 lg:p-6 shadow-2xl border border-blue-900 overflow-hidden relative">

    <!-- Header Section -->
    <header class="bg-gradient-to-r from-blue-900 via-blue-950 to-blue-900 rounded-2xl p-4 lg:p-6 border border-blue-800 shadow-2xl relative overflow-hidden flex flex-col md:flex-row md:items-center md:justify-between">
        <!-- background glow -->
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-blue-500 rounded-full blur-3xl opacity-20"></div>

        <!-- Left: Yamaha Logo -->
        <div class="flex items-center justify-center md:justify-start z-10">
            <img src="{{ asset('yamaha_logo.png') }}" alt="YAMAHA" class="h-10 lg:h-16 w-auto object-contain drop-shadow-lg">
        </div>

        <!-- Center: Title -->
        <div class="text-center my-3 md:my-0 z-10 flex-1">
            <h1 class="text-xl lg:text-3xl font-black text-white tracking-wider drop-shadow-lg flex items-center justify-center gap-3">
                <i class="bi bi-graph-up text-yellow-400"></i>
                <span>STU UNIT PER DEALER</span>
            </h1>
            <p class="text-yellow-400 font-bold tracking-widest text-[11px] lg:text-xs mt-1 uppercase">
                Monitoring Penjualan STU Unit Terjual & Capaian Target Per Cabang
            </p>
        </div>

        <!-- Right: Actions -->
        <div class="z-10 flex flex-col items-center md:items-end space-y-2">
            <div class="bg-gradient-to-r from-yellow-400 via-amber-400 to-yellow-500 text-blue-950 font-black px-3.5 py-1.5 rounded-xl text-xs shadow-xl border border-yellow-300 uppercase tracking-tight flex items-center space-x-1.5 transform hover:scale-105 transition">
                <i class="bi bi-pie-chart-fill text-sm"></i>
                <span>STU Performance</span>
            </div>
        </div>
    </header>

    <!-- KPI Summary Grid (4 Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mt-6">
        
        <!-- Card 1: Total STU Unit Terjual -->
        <div class="bg-slate-900/80 border border-blue-900/60 rounded-2xl p-4 shadow-xl backdrop-blur-md relative overflow-hidden flex flex-col justify-between hover:border-yellow-500/50 transition duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Total STU Unit (ACV)</p>
                    <p class="text-2xl font-black text-yellow-400 mt-1"><span class="counter-animate" data-target="{{ $totalAcv }}">{{ number_format($totalAcv) }}</span> <span class="text-xs text-slate-400 font-normal">unit</span></p>
                </div>
                <div class="bg-yellow-500/10 text-yellow-400 p-3 rounded-xl border border-yellow-500/20">
                    <i class="bi bi-graph-up-arrow text-xl"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <span class="text-slate-400 font-medium">vs Target:</span>
                <span class="counter-animate font-black px-2.5 py-0.5 rounded-full {{ $overallAcvPercent >= 100 ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-400 border border-amber-500/30' }}" data-target="{{ round($overallAcvPercent, 1) }}" data-decimals="1" data-suffix="% Target">
                    {{ round($overallAcvPercent, 1) }}% Target
                </span>
            </div>
        </div>

        <!-- Card 2: STU vs Target Reguler -->
        <div class="bg-slate-900/80 border border-blue-900/60 rounded-2xl p-4 shadow-xl backdrop-blur-md relative overflow-hidden flex flex-col justify-between hover:border-purple-500/50 transition duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Target STU Reguler</p>
                    <p class="text-2xl font-black text-purple-300 mt-1"><span class="counter-animate" data-target="{{ $totalTargetReguler }}">{{ number_format($totalTargetReguler) }}</span> <span class="text-xs text-slate-400 font-normal">unit</span></p>
                </div>
                <div class="bg-purple-500/10 text-purple-400 p-3 rounded-xl border border-purple-500/20">
                    <i class="bi bi-flag-fill text-xl"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <span class="text-slate-400 font-medium">Pencapaian Reguler:</span>
                <span class="counter-animate font-black text-purple-300 px-2 py-0.5 rounded-full bg-purple-500/20 border border-purple-500/30" data-target="{{ round($overallRegulerAcvPercent, 1) }}" data-decimals="1" data-suffix="%">
                    {{ round($overallRegulerAcvPercent, 1) }}%
                </span>
            </div>
        </div>

        <!-- Card 3: Growth STU vs LM -->
        <div class="bg-slate-900/80 border border-blue-900/60 rounded-2xl p-4 shadow-xl backdrop-blur-md relative overflow-hidden flex flex-col justify-between hover:border-emerald-500/50 transition duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Growth STU vs LM</p>
                    <p class="text-2xl font-black text-emerald-400 mt-1"><span class="counter-animate" data-target="{{ $totalAcv }}">{{ number_format($totalAcv) }}</span> <span class="text-xs text-slate-400 font-normal">vs <span class="counter-animate" data-target="{{ $totalLm }}">{{ number_format($totalLm) }}</span> LM</span></p>
                </div>
                <div class="bg-emerald-500/10 text-emerald-400 p-3 rounded-xl border border-emerald-500/20">
                    <i class="bi bi-arrow-up-right-circle-fill text-xl"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <span class="text-slate-400 font-medium">Growth Rasio:</span>
                <span class="counter-animate font-black px-2.5 py-0.5 rounded-full {{ $overallGrowthPercent >= 100 ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-400 border border-rose-500/30' }}" data-target="{{ round($overallGrowthPercent, 1) }}" data-decimals="1" data-suffix="%">
                    {{ round($overallGrowthPercent, 1) }}%
                </span>
            </div>
        </div>

        <!-- Card 4: Actual YTD 2026 -->
        <div class="bg-slate-900/80 border border-blue-900/60 rounded-2xl p-4 shadow-xl backdrop-blur-md relative overflow-hidden flex flex-col justify-between hover:border-blue-500/50 transition duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Actual YTD 2026 STU</p>
                    <p class="text-2xl font-black text-blue-400 mt-1"><span class="counter-animate" data-target="{{ $totalActYtdJan2026 }}">{{ number_format($totalActYtdJan2026) }}</span> <span class="text-xs text-slate-400 font-normal">/ <span class="counter-animate" data-target="{{ $totalTargetReguler2026 }}">{{ number_format($totalTargetReguler2026) }}</span> unit</span></p>
                </div>
                <div class="bg-blue-500/10 text-blue-400 p-3 rounded-xl border border-blue-500/20">
                    <i class="bi bi-bar-chart-line-fill text-xl"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <span class="text-slate-400 font-medium">Progress YTD 2026:</span>
                <span class="counter-animate font-black text-blue-400 bg-blue-500/20 border border-blue-500/30 px-2 py-0.5 rounded-full" data-target="{{ round($overallYtdPercent, 1) }}" data-decimals="1" data-suffix="%">
                    {{ round($overallYtdPercent, 1) }}%
                </span>
            </div>
        </div>

    </div>

    <!-- Informasi STU Seluruh Dealer (Header di Atas, Detail Kategori di Bawah 1 Baris Horizontal) -->
    <div class="mt-7 bg-[#0b132b]/95 border-2 border-yellow-500/50 rounded-3xl p-6 lg:p-7 shadow-2xl backdrop-blur-md relative overflow-hidden">
        <div class="absolute -right-20 -bottom-20 w-72 h-72 bg-yellow-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Top Header Row (Fully Responsive) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-blue-900/80 pb-4 mb-4 gap-3">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="bg-yellow-500/20 text-yellow-400 p-2 sm:p-2.5 rounded-xl border border-yellow-500/40 flex items-center justify-center shrink-0 shadow-lg">
                    <i class="bi bi-trophy-fill text-lg sm:text-xl lg:text-2xl leading-none"></i>
                </div>
                <div>
                    <h2 class="text-sm sm:text-base lg:text-lg font-black text-white uppercase tracking-wider leading-snug">REKAPITULASI STU SELURUH DEALER</h2>
                    <p class="text-[11px] sm:text-xs text-yellow-400 font-bold leading-snug mt-0.5">Total Penjualan STU Gabungan Seluruh Cabang & Rincian Kategori Unit</p>
                </div>
            </div>

            <!-- Total STU Badge -->
            <div class="bg-slate-950/95 border border-yellow-500/40 px-4 sm:px-5 py-2 rounded-2xl flex items-center justify-between sm:justify-start space-x-3 shadow-xl shrink-0 w-full sm:w-auto">
                <span class="text-xs text-slate-400 font-extrabold uppercase tracking-wider">Total STU Terjual:</span>
                <span class="text-lg sm:text-xl lg:text-2xl font-black text-yellow-400 drop-shadow-md">{{ number_format($totalAcv) }} UNIT</span>
            </div>
        </div>

        <!-- Bottom: Category Breakdown Cards (Dynamic Grid) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3.5 mt-4 pt-0.5">
            @php
                $allCategories = [
                    ['name' => 'PREMIUM', 'img' => asset('nmax.png')],
                    ['name' => 'ATM',     'img' => asset('gear ultima.png')],
                    ['name' => 'CLASSY',  'img' => asset('classy.png')],
                    ['name' => 'MOPED',   'img' => asset('moped.png')],
                    ['name' => 'SPORT',   'img' => asset('sport.png')],
                    ['name' => 'AT STD',  'img' => asset('atm.png')],
                ];
            @endphp

            @foreach($allCategories as $c)
                @php
                    $catName = $c['name'];
                    $catCount = $grandCategoryTotals[$catName] ?? 0;
                    if ($catCount <= 0) {
                        continue;
                    }
                    $pctAll = $totalAcv > 0 ? round(($catCount / $totalAcv) * 100) : 0;
                    $seriesMap = $grandSeriesTotals[$catName] ?? [];
                @endphp
                <div class="flex flex-col justify-between p-3.5 sm:p-4 rounded-2xl bg-slate-950/80 border border-slate-800 hover:border-yellow-400/60 shadow-lg h-full transition duration-200">
                    <div>
                        <!-- Header: Icon, Name & % Badge -->
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-2.5 mb-3">
                            <div class="flex items-center space-x-2 min-w-0">
                                <img src="{{ $c['img'] }}" alt="{{ $catName }}" class="h-8 w-8 sm:h-9 sm:w-9 object-contain shrink-0">
                                <span class="text-white font-black text-xs sm:text-sm uppercase tracking-wide">{{ $catName }}</span>
                            </div>
                            <span class="bg-slate-900/90 border border-slate-700/80 text-slate-100 text-[11px] font-black px-2 py-0.5 rounded-lg shrink-0 shadow">
                                {{ $pctAll }}%
                            </span>
                        </div>

                        <!-- Vertical Sub-list of Model Series -->
                        <div class="space-y-1.5 mb-3">
                            @foreach($seriesMap as $sName => $sCount)
                                @if(($sCount ?? 0) <= 0)
                                    @continue
                                @endif
                                <div class="flex items-center justify-between text-xs py-1 px-2 rounded-lg bg-slate-900/60 border border-slate-800/60">
                                    <span class="text-yellow-400 font-bold flex items-center space-x-1.5 min-w-0">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-400 shrink-0"></span>
                                        <span class="truncate">{{ $sName }}</span>
                                    </span>
                                    <span class="text-white font-extrabold text-[11px] shrink-0 ml-1.5">
                                        {{ number_format($sCount) }} Unit
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Total STU Category Badge -->
                    <div class="pt-2.5 border-t border-slate-800/80 flex items-center justify-between">
                        <span class="text-[10.5px] text-slate-400 font-extrabold uppercase tracking-wider">Total {{ $catName }}:</span>
                        <p class="text-white font-black text-sm sm:text-base leading-none"><span class="text-yellow-400 font-black text-base sm:text-lg">{{ number_format($catCount) }}</span> <span class="text-xs text-slate-400 font-normal">Unit</span></p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- PENCAPAIAN STU BERDASARKAN SERIES UNIT (REKAPITULASI SELURUH DEALER) -->
    @php
        $allSeriesMap = [];
        $categoryColors = [
            'PREMIUM' => '#3b82f6', // Blue
            'ATM'     => '#f59e0b', // Amber/Yellow
            'CLASSY'  => '#ec4899', // Pink
            'MOPED'   => '#10b981', // Emerald/Green
            'SPORT'   => '#ef4444', // Red
            'AT STD'  => '#8b5cf6', // Purple
        ];

        foreach ($cabangs as $cabang) {
            $cBreakdown = $cabang->stu_breakdown ?: [];
            foreach ($cBreakdown as $cat => $sList) {
                if (is_array($sList)) {
                    foreach ($sList as $sName => $count) {
                        $count = (int)$count;
                        if ($count > 0) {
                            if (!isset($allSeriesMap[$sName])) {
                                $allSeriesMap[$sName] = [
                                    'name'     => $sName,
                                    'category' => $cat,
                                    'total'    => 0,
                                    'cabangs'  => [],
                                ];
                            }
                            $allSeriesMap[$sName]['total'] += $count;
                            $allSeriesMap[$sName]['cabangs'][$cabang->nama] = ($allSeriesMap[$sName]['cabangs'][$cabang->nama] ?? 0) + $count;
                        }
                    }
                }
            }
        }

        // Sort by total count descending
        uasort($allSeriesMap, fn($a, $b) => $b['total'] <=> $a['total']);

        $totalStuAllSeries = array_sum(array_column($allSeriesMap, 'total'));

        $seriesLabels = [];
        $seriesTotals = [];
        $seriesPercentages = [];
        $seriesBarColors = [];
        $seriesCategories = [];
        $seriesCabangBreakdown = [];

        foreach ($allSeriesMap as $sName => $info) {
            $seriesLabels[] = $sName;
            $seriesTotals[] = $info['total'];
            $pct = $totalStuAllSeries > 0 ? round(($info['total'] / $totalStuAllSeries) * 100, 1) : 0;
            $seriesPercentages[] = $pct;
            $seriesCategories[] = $info['category'];
            $seriesBarColors[] = $categoryColors[$info['category']] ?? '#38bdf8';
            $seriesCabangBreakdown[] = $info['cabangs'];
        }

        $topSeries = !empty($allSeriesMap) ? reset($allSeriesMap) : null;
        $topSeriesPct = ($topSeries && $totalStuAllSeries > 0) ? round(($topSeries['total'] / $totalStuAllSeries) * 100, 1) : 0;
        $avgStuPerSeries = count($seriesLabels) > 0 ? round($totalStuAllSeries / count($seriesLabels), 1) : 0;
    @endphp

    <div class="mt-7 bg-slate-900/90 border border-blue-900/70 rounded-2xl lg:rounded-3xl p-4 lg:p-6 shadow-2xl backdrop-blur-xl relative overflow-hidden">
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-blue-900 via-indigo-950 to-blue-900 text-white font-bold py-3 px-4 rounded-xl mb-4 tracking-wider text-xs lg:text-sm uppercase flex flex-col sm:flex-row justify-between items-center shadow-inner gap-2 sm:gap-0">
            <div class="flex items-center space-x-2.5">
                <div class="bg-yellow-500/20 p-1.5 rounded-lg border border-yellow-500/30">
                    <i class="bi bi-bar-chart-line-fill text-yellow-400 text-base lg:text-lg leading-none"></i>
                </div>
                <div>
                    <span class="text-white font-black">Grafik Pencapaian STU Berdasarkan Series Unit</span>
                    <span class="text-slate-300 font-normal block text-[10px] normal-case">Rekapitulasi Penjualan Seluruh Dealer Cabang</span>
                </div>
            </div>
            <div class="flex items-center space-x-2 text-[11px] lg:text-xs text-yellow-400 normal-case font-extrabold bg-slate-950/60 px-3 py-1 rounded-lg border border-yellow-500/30">
                <span class="w-2 h-2 rounded-full bg-yellow-400 animate-pulse"></span>
                <span>Total {{ count($seriesLabels) }} Series Unit Terjual ({{ number_format($totalStuAllSeries) }} Unit)</span>
            </div>
        </div>

        <!-- Quick Summary Mini Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
            <div class="bg-slate-950/70 border border-slate-800/80 rounded-xl p-3 flex flex-col justify-between">
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Total STU Terjual</span>
                <div class="flex items-baseline space-x-1 mt-1">
                    <span class="text-lg font-black text-yellow-400">{{ number_format($totalStuAllSeries) }}</span>
                    <span class="text-xs text-slate-400 font-semibold">Unit (100%)</span>
                </div>
            </div>
            <div class="bg-slate-950/70 border border-slate-800/80 rounded-xl p-3 flex flex-col justify-between">
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Series Terlaris</span>
                <div class="flex items-baseline space-x-1 mt-1 truncate">
                    <span class="text-lg font-black text-blue-400 truncate">{{ $topSeries['name'] ?? '-' }}</span>
                    <span class="text-xs text-yellow-400 font-extrabold shrink-0">({{ number_format($topSeries['total'] ?? 0) }} / {{ $topSeriesPct }}%)</span>
                </div>
            </div>
            <div class="bg-slate-950/70 border border-slate-800/80 rounded-xl p-3 flex flex-col justify-between">
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Jumlah Series Active</span>
                <div class="flex items-baseline space-x-1 mt-1">
                    <span class="text-lg font-black text-emerald-400">{{ count($seriesLabels) }}</span>
                    <span class="text-xs text-slate-400 font-semibold">Model Series</span>
                </div>
            </div>
            <div class="bg-slate-950/70 border border-slate-800/80 rounded-xl p-3 flex flex-col justify-between">
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Rata-rata / Series</span>
                <div class="flex items-baseline space-x-1 mt-1">
                    <span class="text-lg font-black text-purple-400">{{ number_format($avgStuPerSeries, 1) }}</span>
                    <span class="text-xs text-slate-400 font-semibold">Unit</span>
                </div>
            </div>
        </div>

        <!-- Category Legend & Percentage Badges -->
        <div class="flex flex-wrap items-center justify-center gap-2 mb-4">
            @foreach($categoryColors as $catName => $catColor)
                @php
                    $catTotal = $grandCategoryTotals[$catName] ?? 0;
                    $catPct = $totalStuAllSeries > 0 ? round(($catTotal / $totalStuAllSeries) * 100, 1) : 0;
                @endphp
                @if($catTotal > 0)
                    <div class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-xl bg-slate-950/80 border border-slate-800/80 text-xs font-bold shadow-md hover:border-slate-700 transition">
                        <span class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $catColor }}; box-shadow: 0 0 8px {{ $catColor }}b0;"></span>
                        <span class="text-slate-300 font-semibold">{{ $catName }}:</span>
                        <span class="text-white font-extrabold">{{ number_format($catTotal) }} Unit</span>
                        <span class="text-yellow-400 font-black bg-yellow-400/10 px-1.5 py-0.5 rounded text-[10.5px] border border-yellow-400/20">
                            {{ $catPct }}%
                        </span>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Chart Container -->
        <div class="rounded-xl border border-blue-900/80 bg-slate-950/70 p-3 sm:p-5 relative overflow-x-auto shadow-inner">
            <div class="min-w-[700px] sm:min-w-full h-[360px] md:h-[420px] relative">
                <canvas id="stuSeriesChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('stuSeriesChart');
            if (!ctx) return;

            const labels = @json($seriesLabels);
            const dataTotals = @json($seriesTotals);
            const dataPercentages = @json($seriesPercentages);
            const barColors = @json($seriesBarColors);
            const categories = @json($seriesCategories);
            const cabangBreakdowns = @json($seriesCabangBreakdown);
            const grandTotalStu = {{ $totalStuAllSeries }};

            // Custom Plugin: Draw X UNIT (% share) badge on top of each bar
            const topBarLabelPlugin = {
                id: 'topBarLabel',
                afterDatasetsDraw(chart) {
                    const { ctx } = chart;
                    const meta = chart.getDatasetMeta(0);
                    if (!meta.hidden && meta.data) {
                        meta.data.forEach((bar, index) => {
                            const val = dataTotals[index];
                            const pct = dataPercentages[index];
                            if (val !== undefined && val !== null) {
                                const color = barColors[index] || '#38bdf8';
                                ctx.save();
                                
                                const textUnit = val + ' UNIT';
                                const textPct = '(' + pct + '%)';
                                
                                // Measure font width
                                ctx.font = 'bold 11px "Plus Jakarta Sans", sans-serif';
                                const textUnitWidth = ctx.measureText(textUnit).width;
                                
                                ctx.font = '800 11px "Plus Jakarta Sans", sans-serif';
                                const textPctWidth = ctx.measureText(textPct).width;

                                const spacing = 4;
                                const paddingX = 8;
                                const rectW = textUnitWidth + spacing + textPctWidth + (paddingX * 2);
                                const rectH = 22;

                                let rectX = bar.x - (rectW / 2);
                                let rectY = bar.y - 28;

                                // Clip prevention
                                if (rectY < 4) rectY = 4;

                                // Draw pill background with dark slate fill and category stroke
                                ctx.fillStyle = 'rgba(15, 23, 42, 0.92)';
                                ctx.strokeStyle = color;
                                ctx.lineWidth = 1.5;
                                ctx.beginPath();
                                if (ctx.roundRect) {
                                    ctx.roundRect(rectX, rectY, rectW, rectH, 6);
                                } else {
                                    ctx.rect(rectX, rectY, rectW, rectH);
                                }
                                ctx.fill();
                                ctx.stroke();

                                // Text drawing
                                ctx.textAlign = 'left';
                                ctx.textBaseline = 'middle';
                                const startX = rectX + paddingX;
                                const centerY = rectY + (rectH / 2);

                                // Draw Unit text (White)
                                ctx.fillStyle = '#ffffff';
                                ctx.font = 'bold 11px "Plus Jakarta Sans", sans-serif';
                                ctx.fillText(textUnit, startX, centerY);

                                // Draw % text (Yellow)
                                ctx.fillStyle = '#fbbf24';
                                ctx.font = '800 11px "Plus Jakarta Sans", sans-serif';
                                ctx.fillText(textPct, startX + textUnitWidth + spacing, centerY);

                                ctx.restore();
                            }
                        });
                    }
                }
            };

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Penjualan STU Unit',
                        data: dataTotals,
                        backgroundColor: barColors,
                        borderColor: barColors,
                        borderWidth: 1.5,
                        borderRadius: { topLeft: 8, topRight: 8, bottomLeft: 0, bottomRight: 0 },
                        borderSkipped: false,
                        barPercentage: 0.55,
                        categoryPercentage: 0.7,
                    }]
                },
                plugins: [topBarLabelPlugin],
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false,
                            },
                            ticks: {
                                color: '#f8fafc',
                                font: {
                                    size: 11,
                                    weight: 'bold',
                                    family: "'Plus Jakarta Sans', sans-serif"
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grace: '15%',
                            grid: {
                                color: 'rgba(51, 65, 85, 0.25)',
                                drawBorder: false,
                            },
                            ticks: {
                                color: '#94a3b8',
                                font: {
                                    size: 11,
                                    family: "'Plus Jakarta Sans', sans-serif"
                                },
                                stepSize: 5
                            },
                            title: {
                                display: true,
                                text: 'JUMLAH STU TERJUAL (UNIT)',
                                color: '#64748b',
                                font: {
                                    size: 10,
                                    weight: 'bold'
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.96)',
                            titleColor: '#fbbf24',
                            bodyColor: '#f8fafc',
                            borderColor: '#3b82f6',
                            borderWidth: 1.5,
                            padding: 12,
                            boxPadding: 4,
                            callbacks: {
                                title: function(context) {
                                    const idx = context[0].dataIndex;
                                    return '🏍️ ' + labels[idx] + ' (' + categories[idx] + ')';
                                },
                                label: function(context) {
                                    const idx = context.dataIndex;
                                    const val = context.parsed.y;
                                    const pct = dataPercentages[idx];
                                    return [
                                        ' Total Terjual: ' + val + ' Unit',
                                        ' Kontribusi STU: ' + pct + '% dari Total STU (' + grandTotalStu + ' Unit)'
                                    ];
                                },
                                afterBody: function(context) {
                                    const idx = context[0].dataIndex;
                                    const cbMap = cabangBreakdowns[idx] || {};
                                    const val = dataTotals[idx];
                                    const lines = ['', ' 📊 Rincian Per Dealer:'];
                                    for (const [cName, cVal] of Object.entries(cbMap)) {
                                        const dealerPct = val > 0 ? ((cVal / val) * 100).toFixed(1) : '0';
                                        lines.push('  • ' + cName + ': ' + cVal + ' Unit (' + dealerPct + '%)');
                                    }
                                    return lines;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>

    <!-- Dealer STU Cards Grid -->
    <div class="mt-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 border-b border-blue-900/60 pb-3 gap-2">
            <div>
                <h2 class="text-lg font-extrabold text-white uppercase tracking-wider flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-teal-400 inline-block animate-pulse"></span>
                    <span>Monitoring Penjualan STU Unit Per Dealer</span>
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Detail perbandingan STU unit terjual (ACV) vs target STU serta rincian unit per kategori untuk masing-masing dealer.</p>
            </div>
        </div>

        <!-- Grid of Dealer Cards (2 Horizontal Rows of 3 Cards: Pekanbaru, Sei Pagar, Air Molek on Row 1; Sorek, Kandis, Medan on Row 2) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @php $hasCards = false; @endphp
            @forelse($cabangs as $cabang)
                @php
                    $targetStu = $cabang->target_tantangan > 0 ? $cabang->target_tantangan : ($cabang->target_reguler > 0 ? $cabang->target_reguler : 100);
                    $stuAcv    = $cabang->acv;

                    if ($stuAcv <= 0) {
                        continue;
                    }
                    $hasCards  = true;
                    $diffStu   = $stuAcv - $targetStu;
                    
                    $ratioFormatted = $targetStu > 0 ? number_format($stuAcv / $targetStu, 2, ',', '.') : '0,00';
                    $progressPct    = $targetStu > 0 ? min(100, round(($stuAcv / $targetStu) * 100)) : 0;

                    $cName = $cabang->nama;
                    $cSeriesData = $exactData[$cName] ?? [];

                    $units = [
                        ['name' => 'PREMIUM', 'desc' => 'Aerox, NMAX, XMAX Series',     'count' => array_sum($cSeriesData['PREMIUM'] ?? []), 'img' => asset('nmax.png')],
                        ['name' => 'ATM',     'desc' => 'Gear Series',                 'count' => array_sum($cSeriesData['ATM'] ?? []),     'img' => asset('gear ultima.png')],
                        ['name' => 'CLASSY',  'desc' => 'Filano, Fazzio Series',       'count' => array_sum($cSeriesData['CLASSY'] ?? []),  'img' => asset('classy.png')],
                        ['name' => 'MOPED',   'desc' => 'Jupiter, MX, Vega Series',    'count' => array_sum($cSeriesData['MOPED'] ?? []),   'img' => asset('moped.png')],
                        ['name' => 'SPORT',   'desc' => 'WR, XSR, R15, Vixion Series', 'count' => array_sum($cSeriesData['SPORT'] ?? []),   'img' => asset('sport.png')],
                        ['name' => 'AT STD',  'desc' => 'X-Ride, Mio Series',          'count' => array_sum($cSeriesData['AT STD'] ?? []),  'img' => asset('atm.png')],
                    ];
                @endphp

                <!-- Dealer Card -->
                <div class="bg-[#0b132b]/90 border border-blue-900/70 rounded-3xl p-5 shadow-2xl backdrop-blur-md relative overflow-hidden flex flex-col justify-between hover:border-teal-500/60 transition duration-300">
                    
                    <div>
                        <!-- Header: Name & Ratio Badge -->
                        <div class="flex items-center justify-between border-b border-blue-950 pb-3 mb-4">
                            <div class="flex items-center space-x-2.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-teal-400 shrink-0"></span>
                                <h3 class="text-base lg:text-lg font-black text-white uppercase tracking-wide">{{ $cabang->nama }}</h3>
                            </div>
                            @php
                                $acvPct = $targetStu > 0 ? round(($stuAcv / $targetStu) * 100) : 0;
                            @endphp
                            <div class="counter-animate bg-blue-950/90 border border-blue-800 text-xs font-black px-3 py-1 rounded-xl shadow-inner {{ $acvPct >= 100 ? 'text-emerald-400 border-emerald-500/40' : ($acvPct < 80 ? 'text-rose-400 border-rose-500/40' : 'text-amber-400 border-amber-500/40') }}" data-target="{{ $acvPct }}" data-prefix="ACV: " data-suffix="%">
                                ACV: {{ $acvPct }}%
                            </div>
                        </div>

                        <!-- Progress Section: STU VS TARGET -->
                        <div>
                            <div class="flex items-center justify-between text-xs font-extrabold uppercase tracking-wider mb-1.5">
                                <span class="text-slate-400">STU ACV VS TARGET</span>
                                <span class="text-white font-black"><span class="counter-animate" data-target="{{ $stuAcv }}">{{ $stuAcv }}</span> / <span class="counter-animate" data-target="{{ $targetStu }}">{{ $targetStu }}</span> UNIT</span>
                            </div>
                            <!-- Progress bar -->
                            <div class="w-full h-2 bg-slate-900/90 rounded-full overflow-hidden border border-slate-800">
                                <div class="bg-gradient-to-r from-teal-400 via-emerald-400 to-teal-300 h-full rounded-full transition-all duration-500" style="width: {{ $progressPct }}%"></div>
                            </div>
                        </div>

                        <!-- 3-Column Metrics Box -->
                        <div class="bg-slate-950/60 rounded-2xl p-3 border border-slate-800/80 text-center grid grid-cols-3 gap-2 mt-4 shadow-inner">
                            <div>
                                <p class="text-[9.5px] text-slate-400 font-extrabold uppercase tracking-wider">TARGET STU</p>
                                <p class="counter-animate text-white font-black text-base mt-0.5" data-target="{{ $targetStu }}">{{ $targetStu }}</p>
                            </div>
                            <div class="border-x border-slate-800/80 px-1">
                                <p class="text-[9.5px] text-slate-400 font-extrabold uppercase tracking-wider">STU ACV</p>
                                <p class="counter-animate text-yellow-400 font-black text-base mt-0.5" data-target="{{ $stuAcv }}">{{ $stuAcv }}</p>
                            </div>
                            <div>
                                <p class="text-[9.5px] text-slate-400 font-extrabold uppercase tracking-wider">-/+ STU</p>
                                <p class="counter-animate font-black text-base mt-0.5 {{ $diffStu >= 0 ? 'text-emerald-400' : 'text-rose-400' }}" data-target="{{ abs($diffStu) }}" data-prefix="{{ $diffStu > 0 ? '+' : ($diffStu < 0 ? '-' : '') }}">
                                    {{ $diffStu > 0 ? '+'.$diffStu : $diffStu }}
                                </p>
                            </div>
                        </div>

                        <!-- UNIT DETAIL Section (STU Terjual Per Kategori) -->
                        <div class="mt-5">
                            <h4 class="text-[11px] text-slate-400 font-extrabold uppercase tracking-wider mb-3">UNIT DETAIL (STU TERJUAL)</h4>
                            
                            <div class="space-y-2">
                                @foreach($units as $u)
                                    @if(($u['count'] ?? 0) <= 0)
                                        @continue
                                    @endif
                                    @php
                                        $unitPct = $stuAcv > 0 ? round(($u['count'] / $stuAcv) * 100) : 0;
                                    @endphp
                                    <div class="bg-slate-950/50 rounded-xl p-2.5 border border-slate-800/60 flex items-center justify-between transition hover:border-blue-500/50">
                                        <div class="flex items-center space-x-3 min-w-0 flex-1">
                                            <img src="{{ $u['img'] }}" alt="{{ $u['name'] }}" class="h-8 w-8 object-contain shrink-0">
                                            <div class="min-w-0 flex-1">
                                                <p class="text-white font-extrabold text-xs uppercase leading-tight">{{ $u['name'] }}</p>
                                                <p class="text-xs text-yellow-400 font-semibold leading-snug break-words mt-0.5">{{ $u['desc'] }}</p>
                                                <p class="text-slate-300 text-[10.5px] font-bold mt-0.5">{{ $u['count'] }} Unit</p>
                                            </div>
                                        </div>
                                        <span class="bg-slate-900 border border-slate-800 text-slate-200 text-xs font-black px-2.5 py-1 rounded-lg shrink-0 ml-2">
                                            {{ $unitPct }}%
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>

                </div>
            @empty
                <div class="col-span-full p-8 text-center text-slate-500 italic bg-slate-900/60 rounded-2xl border border-slate-800">
                    Belum ada data dealer cabang.
                </div>
            @endforelse

            @if(!$hasCards && count($cabangs) > 0)
                <div class="col-span-full p-8 text-center text-slate-500 italic bg-slate-900/60 rounded-2xl border border-slate-800">
                    Tidak ada dealer cabang dengan nilai STU unit > 0.
                </div>
            @endif
        </div>
    </div>

    <!-- Back & Action Navigation -->
    <div class="mt-6 flex justify-between items-center">
        <a href="{{ url('/') }}" class="inline-flex items-center space-x-2 text-xs font-bold text-gray-300 hover:text-white transition duration-200 bg-slate-900/60 border border-blue-900 hover:border-blue-700 px-4 py-2.5 rounded-xl shadow">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali ke Dashboard</span>
        </a>

        @auth
            @if(auth()->user()->isSuperAdmin())
                <a href="{{ route('sync.spreadsheet') }}" class="inline-flex items-center space-x-2 text-xs font-bold text-white bg-blue-700 hover:bg-blue-600 transition duration-200 px-4 py-2.5 rounded-xl shadow border border-blue-600">
                    <i class="bi bi-arrow-repeat"></i>
                    <span>Sinkronisasi Data Realtime</span>
                </a>
            @endif
        @endauth
    </div>

</div>

@endsection
