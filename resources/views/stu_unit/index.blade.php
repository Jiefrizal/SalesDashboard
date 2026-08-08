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
    'PREMIUM'  => 0,
    'ATM'      => 0,
    'CLASSY'   => 0,
    'MOPED'    => 0,
    'SPORT'    => 0,
    'OFF ROAD' => 0,
    'AT STD'   => 0,
];

$officialSeriesList = [
    'NMAX SERIES'   => 'PREMIUM',
    'GEAR SERIES'   => 'ATM',
    'AEROX SERIES'  => 'PREMIUM',
    'FAZZIO SERIES' => 'CLASSY',
    'FILANO SERIES' => 'CLASSY',
    'VIXION SERIES' => 'SPORT',
    'WR SERIES'     => 'OFF ROAD',
    'MX SERIES'     => 'MOPED',
];

$allSeriesMap = [];
foreach ($officialSeriesList as $sName => $cat) {
    $allSeriesMap[$sName] = [
        'name'     => $sName,
        'category' => $cat,
        'total'    => 0,
        'cabangs'  => [],
    ];
}

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
        if (strtolower(trim($cat)) === 'pos') {
            continue;
        }
        $catSum = 0;
        if (is_array($sList)) {
            foreach ($sList as $sName => $count) {
                $sNameUpper = strtoupper(trim($sName));
                if (isset($allSeriesMap[$sNameUpper])) {
                    $count = (int)$count;
                    $allSeriesMap[$sNameUpper]['total'] += $count;
                    $grandSeriesTotals[$cat][$sNameUpper] = ($grandSeriesTotals[$cat][$sNameUpper] ?? 0) + $count;
                    $catSum += $count;
                    if ($count > 0) {
                        $allSeriesMap[$sNameUpper]['cabangs'][$cName] = ($allSeriesMap[$sNameUpper]['cabangs'][$cName] ?? 0) + $count;
                    }
                }
            }
        }
        if (isset($grandCategoryTotals[$cat])) {
            $grandCategoryTotals[$cat] += $catSum;
        } else {
            $grandCategoryTotals[$cat] = ($grandCategoryTotals[$cat] ?? 0) + $catSum;
        }
    }
}

uasort($allSeriesMap, fn($a, $b) => $b['total'] <=> $a['total']);
$totalStuAllSeries = array_sum(array_column($allSeriesMap, 'total'));

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
                    ['name' => 'PREMIUM',  'img' => asset('nmax.png')],
                    ['name' => 'ATM',      'img' => asset('gear ultima.png')],
                    ['name' => 'CLASSY',   'img' => asset('classy.png')],
                    ['name' => 'MOPED',    'img' => asset('moped.png')],
                    ['name' => 'SPORT',    'img' => asset('sport.png')],
                    ['name' => 'OFF ROAD', 'img' => asset('wr.png')],
                    ['name' => 'AT STD',   'img' => asset('atm.png')],
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

    <!-- PENCAPAIAN STU BERDASARKAN SERIES UNIT: STU ACHIEVEMENT RACE -->
    @php
        $categoryColors = [
            'PREMIUM'  => '#3b82f6', // Blue
            'ATM'      => '#f59e0b', // Amber/Yellow
            'CLASSY'   => '#ec4899', // Pink
            'MOPED'    => '#10b981', // Emerald/Green
            'SPORT'    => '#ef4444', // Red
            'OFF ROAD' => '#f97316', // Orange
            'AT STD'   => '#8b5cf6', // Purple
        ];

        $categoryImages = [
            'PREMIUM'  => asset('nmax.png'),
            'ATM'      => asset('gear ultima.png'),
            'CLASSY'   => asset('classy.png'),
            'MOPED'    => asset('moped.png'),
            'SPORT'    => asset('sport.png'),
            'OFF ROAD' => asset('wr.png'),
            'AT STD'   => asset('atm.png'),
        ];

        // $allSeriesMap is already pre-initialized and filtered at top of file for official 8 series models
        $maxSeriesTotal = !empty($allSeriesMap) ? reset($allSeriesMap)['total'] : 1;

        // Target benchmark for 100% finish line (scaled slightly above max total)
        $benchmarkTarget = (int)ceil($maxSeriesTotal * 1.12 / 100) * 100;
        if ($benchmarkTarget < $maxSeriesTotal) $benchmarkTarget = $maxSeriesTotal;

        $rankThemes = [
            1 => [
                'badge' => 'border-amber-400/80 bg-gradient-to-br from-amber-500/30 via-yellow-500/20 to-amber-600/40 text-amber-300 shadow-[0_0_18px_rgba(251,191,36,0.4)]',
                'name'  => 'text-transparent bg-clip-text bg-gradient-to-r from-amber-200 via-yellow-300 to-amber-400',
                'bar'   => 'bg-gradient-to-r from-amber-600 via-yellow-400 to-amber-300 shadow-[0_0_22px_rgba(245,158,11,0.6)] border-y border-amber-300/60',
                'pct'   => 'text-amber-400 drop-shadow-[0_0_10px_rgba(251,191,36,0.5)]',
            ],
            2 => [
                'badge' => 'border-cyan-400/80 bg-gradient-to-br from-cyan-500/30 via-blue-500/20 to-cyan-600/40 text-cyan-300 shadow-[0_0_18px_rgba(6,182,212,0.4)]',
                'name'  => 'text-transparent bg-clip-text bg-gradient-to-r from-cyan-200 via-blue-300 to-cyan-400',
                'bar'   => 'bg-gradient-to-r from-blue-600 via-cyan-400 to-cyan-300 shadow-[0_0_22px_rgba(6,182,212,0.6)] border-y border-cyan-300/60',
                'pct'   => 'text-cyan-400 drop-shadow-[0_0_10px_rgba(6,182,212,0.5)]',
            ],
            3 => [
                'badge' => 'border-emerald-400/80 bg-gradient-to-br from-emerald-500/30 via-teal-500/20 to-emerald-600/40 text-emerald-300 shadow-[0_0_18px_rgba(16,185,129,0.4)]',
                'name'  => 'text-transparent bg-clip-text bg-gradient-to-r from-emerald-200 via-teal-300 to-emerald-400',
                'bar'   => 'bg-gradient-to-r from-teal-600 via-emerald-400 to-teal-300 shadow-[0_0_22px_rgba(16,185,129,0.6)] border-y border-emerald-300/60',
                'pct'   => 'text-emerald-400 drop-shadow-[0_0_10px_rgba(16,185,129,0.5)]',
            ],
            4 => [
                'badge' => 'border-slate-300/80 bg-gradient-to-br from-slate-400/30 via-slate-500/20 to-slate-600/40 text-slate-200 shadow-[0_0_18px_rgba(226,232,240,0.4)]',
                'name'  => 'text-transparent bg-clip-text bg-gradient-to-r from-slate-100 via-white to-slate-300',
                'bar'   => 'bg-gradient-to-r from-slate-600 via-slate-300 to-white shadow-[0_0_22px_rgba(255,255,255,0.5)] border-y border-white/60',
                'pct'   => 'text-slate-200 drop-shadow-[0_0_10px_rgba(255,255,255,0.4)]',
            ],
            5 => [
                'badge' => 'border-purple-400/80 bg-gradient-to-br from-purple-500/30 via-fuchsia-500/20 to-purple-600/40 text-purple-300 shadow-[0_0_18px_rgba(168,85,247,0.4)]',
                'name'  => 'text-transparent bg-clip-text bg-gradient-to-r from-purple-200 via-fuchsia-300 to-purple-400',
                'bar'   => 'bg-gradient-to-r from-purple-600 via-fuchsia-400 to-purple-300 shadow-[0_0_22px_rgba(168,85,247,0.6)] border-y border-purple-300/60',
                'pct'   => 'text-purple-300 drop-shadow-[0_0_10px_rgba(168,85,247,0.5)]',
            ],
        ];
    @endphp

    <style>
        .race-track-bg {
            background: linear-gradient(180deg, #020617 0%, #0b1329 50%, #020617 100%);
            background-image: 
                radial-gradient(ellipse at 50% 0%, rgba(30, 58, 138, 0.4) 0%, transparent 70%),
                linear-gradient(to right, rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 100% 100%, 24px 24px, 24px 24px;
        }

        @keyframes race-chevron-anim {
            0% { background-position: 0 0; }
            100% { background-position: 36px 0; }
        }

        .race-chevrons {
            background-image: repeating-linear-gradient(
                -45deg,
                rgba(255, 255, 255, 0.2),
                rgba(255, 255, 255, 0.2) 8px,
                transparent 8px,
                transparent 16px
            );
            background-size: 36px 36px;
            animation: race-chevron-anim 1.5s linear infinite;
        }

        .finish-flag-line {
            background-image: repeating-linear-gradient(
                0deg,
                #000 0px, #000 10px,
                #fff 10px, #fff 20px
            );
        }
    </style>

    <div class="mt-6 race-track-bg border border-blue-800/60 rounded-2xl p-4 lg:p-6 shadow-2xl relative overflow-hidden">
        <!-- Floodlight Beam Decorations -->
        <div class="absolute -top-24 left-1/4 w-72 h-72 bg-blue-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -top-24 right-1/4 w-72 h-72 bg-indigo-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Header Row -->
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between border-b border-slate-800/80 pb-3.5 mb-4 gap-3">
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-r from-red-600 via-rose-600 to-amber-600 text-white p-2 lg:p-2.5 rounded-xl shadow-lg border border-red-500/40 flex items-center justify-center shrink-0">
                    <i class="bi bi-flag-fill text-xl lg:text-2xl text-yellow-300"></i>
                </div>
                <div>
                    <div class="flex items-center space-x-2.5">
                        <h2 class="text-xl lg:text-2xl font-black text-white italic tracking-wider uppercase drop-shadow-md font-mono">
                            STU ACHIEVEMENT <span class="text-red-500 italic">RACE</span> 🏁
                        </h2>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-black bg-red-500/20 text-red-400 border border-red-500/30 animate-pulse">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span> Live Update
                        </span>
                    </div>
                    <p class="text-[11px] sm:text-xs text-slate-400 font-medium tracking-wide mt-0.5">
                        Klasemen Pencapaian Penjualan STU Unit Berdasarkan Series Model (Balapan Target)
                    </p>
                </div>
            </div>

            <!-- Top Header Stats -->
            <div class="flex items-center space-x-3">
                <div class="bg-slate-950/90 border border-blue-900/80 px-3.5 py-1.5 rounded-xl shadow-inner text-right">
                    <span class="text-[9px] text-slate-400 font-extrabold uppercase tracking-wider block">Total STU Terjual</span>
                    <span class="text-sm lg:text-base font-black text-yellow-400">{{ number_format($totalStuAllSeries) }} UNIT</span>
                </div>
            </div>
        </div>

        <!-- Scale Distance Header Marks (0%, 50%, 100% FINISH) -->
        <div class="relative z-10 hidden md:flex items-center justify-between pl-[175px] pr-[145px] mb-2 text-[9px] font-extrabold text-slate-500 uppercase tracking-widest border-b border-slate-800/60 pb-1">
            <span>0%</span>
            <span>50%</span>
            <span class="text-red-400 font-black flex items-center space-x-1">
                <span>100%</span>
                <span class="bg-red-600 text-white px-1.5 py-0.5 rounded font-black text-[8.5px] tracking-wider ml-1">FINISH 🏁</span>
            </span>
        </div>

        <!-- Race Track Lanes Container -->
        <div class="relative z-10 space-y-2.5">
            @php $rank = 1; @endphp
            @foreach($allSeriesMap as $sName => $info)
                @php
                    $rankStr = sprintf('%02d', $rank);
                    $theme = $rankThemes[$rank] ?? [
                        'badge' => 'text-indigo-400 drop-shadow-[0_0_10px_rgba(99,102,241,0.6)]',
                        'name'  => 'text-transparent bg-clip-text bg-gradient-to-r from-indigo-200 via-blue-300 to-indigo-400',
                        'bar'   => 'bg-gradient-to-r from-indigo-600 via-blue-400 to-indigo-300 shadow-[0_0_22px_rgba(99,102,241,0.6)] border-y border-indigo-300/60',
                        'pct'   => 'text-indigo-300 drop-shadow-[0_0_10px_rgba(99,102,241,0.5)]',
                    ];

                    $cat = $info['category'];
                    $img = $categoryImages[$cat] ?? asset('nmax.png');
                    $sUpper = strtoupper($sName);
                    if (str_contains($sUpper, 'AEROX')) {
                        $img = asset('aerox.png');
                    } elseif (str_contains($sUpper, 'WR')) {
                        $img = asset('wr.png');
                    } elseif (str_contains($sUpper, 'FAZZIO')) {
                        $img = asset('fazzio.png');
                    } elseif (str_contains($sUpper, 'FILANO')) {
                        $img = asset('filano.png');
                    } elseif (str_contains($sUpper, 'GEAR')) {
                        $img = asset('gear ultima.png');
                    }
                    
                    // Percentage based on total STU across all series
                    $pct = $totalStuAllSeries > 0 ? round(($info['total'] / $totalStuAllSeries) * 100, 1) : 0;
                @endphp

                <div class="group relative flex flex-col md:flex-row md:items-center bg-slate-950/90 border border-slate-800/80 hover:border-blue-500/50 rounded-xl p-2.5 lg:p-3 transition duration-200 shadow-xl hover:shadow-cyan-950/30">
                    
                    <!-- Left Section: Rank Badge & Model Name -->
                    <div class="flex items-center space-x-2.5 md:w-[170px] shrink-0 mb-2 md:mb-0 z-10">
                        <!-- Rank Laurel Wreath Ring Badge -->
                        <div class="relative w-8 h-8 lg:w-9 lg:h-9 shrink-0 flex items-center justify-center">
                            <svg class="absolute inset-0 w-full h-full {{ $theme['badge'] }}" viewBox="0 0 100 100" fill="none">
                                <circle cx="50" cy="50" r="42" stroke="currentColor" stroke-width="3" stroke-dasharray="6 3" />
                                <path d="M22 50 C18 36 24 24 38 20 M78 50 C82 36 76 24 62 20" stroke="currentColor" stroke-width="3.5" stroke-linecap="round"/>
                                <path d="M22 50 C18 64 24 76 38 80 M78 50 C82 64 76 76 62 80" stroke="currentColor" stroke-width="3.5" stroke-linecap="round"/>
                            </svg>
                            <span class="text-xs lg:text-sm font-extrabold font-mono tracking-tighter text-white">{{ $rankStr }}</span>
                        </div>

                        <div class="min-w-0 flex-1">
                            <h3 class="text-xs sm:text-sm lg:text-base font-black tracking-wide uppercase italic truncate font-mono {{ $theme['name'] }}" title="{{ $sName }}">
                                {{ $sName }}
                            </h3>
                            <div class="flex items-center space-x-1 mt-0.5">
                                <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-slate-900/90 text-slate-300 border border-slate-800">
                                    {{ $cat }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Middle Section: Glowing Track Progress Bar with Chevron Motion & Bike Rider -->
                    <div class="flex-1 relative mx-0 md:mx-3 my-1.5 md:my-0 z-10">
                        <!-- Track Background Slot -->
                        <div class="w-full bg-slate-950/90 rounded-xl h-9 lg:h-10 border border-blue-900/50 relative overflow-hidden flex items-center shadow-[inset_0_2px_6px_rgba(0,0,0,0.8)]">
                            
                            <!-- Glowing Race Speed Progress Bar -->
                            <div class="h-full rounded-xl transition-all duration-700 relative flex items-center justify-end pr-2 {{ $theme['bar'] }}" style="width: {{ min(100, max(3, $pct)) }}%;">
                                <!-- Chevron Speed Arrows Overlay -->
                                <div class="absolute inset-0 race-chevrons opacity-30 rounded-xl"></div>

                                <!-- Chevron Indicator Inside Bar -->
                                <div class="hidden sm:flex items-center space-x-1 text-white/80 font-black text-xs lg:text-sm tracking-tighter opacity-80 mr-8 drop-shadow-md">
                                    <span>&rsaquo;</span><span>&rsaquo;</span><span>&rsaquo;</span><span>&rsaquo;</span><span>&rsaquo;</span>
                                </div>

                                <!-- Futuristic Bike Rider Asset at tip of Progress Bar -->
                                <div class="absolute -right-4 lg:-right-5 top-1/2 -translate-y-1/2 w-9 h-9 lg:w-11 lg:h-11 shrink-0 z-30 transition-all duration-300 transform group-hover:scale-115 group-hover:drop-shadow-[0_0_18px_rgba(56,189,248,1)]">
                                    <!-- Glowing Pulsing Aura Backdrop Behind Bike -->
                                    <div class="absolute inset-0.5 rounded-full bg-cyan-400/20 blur-sm animate-pulse"></div>
                                    <img src="{{ $img }}" alt="{{ $sName }}" class="w-full h-full object-contain relative z-10 filter drop-shadow-[0_0_10px_rgba(255,255,255,0.95)]">
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Right Section: Finish Line & Performance Stats Badge -->
                    <div class="flex items-center justify-between md:justify-end space-x-3 md:w-[145px] shrink-0 z-10 mt-1.5 md:mt-0 pt-1.5 md:pt-0 border-t md:border-t-0 border-slate-800/80">
                        
                        <!-- Finish Flag Strip -->
                        <div class="w-2.5 h-9 lg:h-10 rounded finish-flag-line border border-slate-700 opacity-90 hidden lg:block" title="Finish Line"></div>

                        <!-- Stats Box: % on top (from total STU), Actual / Total STU UNIT on bottom -->
                        <div class="text-right">
                            <div class="text-lg lg:text-xl font-black font-mono leading-none {{ $theme['pct'] }}">
                                {{ $pct }}%
                            </div>
                            <div class="text-[10px] sm:text-[11px] font-extrabold font-mono text-slate-300 mt-0.5 whitespace-nowrap tracking-wide">
                                {{ number_format($info['total'], 0, ',', '.') }} / {{ number_format($totalStuAllSeries, 0, ',', '.') }} UNIT
                            </div>
                        </div>

                    </div>

                </div>
                @php $rank++; @endphp
            @endforeach
        </div>

    </div>

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
                        ['name' => 'PREMIUM',  'desc' => 'Aerox, NMAX, XMAX Series',     'count' => array_sum($cSeriesData['PREMIUM'] ?? []),  'img' => asset('nmax.png')],
                        ['name' => 'ATM',      'desc' => 'Gear Series',                 'count' => array_sum($cSeriesData['ATM'] ?? []),      'img' => asset('gear ultima.png')],
                        ['name' => 'CLASSY',   'desc' => 'Filano, Fazzio Series',       'count' => array_sum($cSeriesData['CLASSY'] ?? []),   'img' => asset('classy.png')],
                        ['name' => 'MOPED',    'desc' => 'Jupiter, MX, Vega Series',    'count' => array_sum($cSeriesData['MOPED'] ?? []),    'img' => asset('moped.png')],
                        ['name' => 'SPORT',    'desc' => 'XSR, R15, Vixion Series',     'count' => array_sum($cSeriesData['SPORT'] ?? []),    'img' => asset('sport.png')],
                        ['name' => 'OFF ROAD', 'desc' => 'WR Series',                   'count' => array_sum($cSeriesData['OFF ROAD'] ?? []), 'img' => asset('wr.png')],
                        ['name' => 'AT STD',   'desc' => 'X-Ride, Mio Series',          'count' => array_sum($cSeriesData['AT STD'] ?? []),   'img' => asset('atm.png')],
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
            @if(auth()->user()->canEdit())
                <a href="{{ route('sync.spreadsheet') }}" onclick="this.classList.add('pointer-events-none', 'opacity-75'); this.querySelector('i').classList.add('animate-spin'); this.querySelector('span').innerText = 'Memproses Realtime...';" class="inline-flex items-center space-x-2 text-xs font-bold text-white bg-blue-700 hover:bg-blue-600 transition duration-200 px-4 py-2.5 rounded-xl shadow border border-blue-600">
                    <i class="bi bi-arrow-repeat"></i>
                    <span>Sinkronisasi Data Realtime</span>
                </a>
            @endif
        @endauth
    </div>

</div>

@endsection
