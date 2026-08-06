@extends('layouts.app')

@section('content')
@php
    $isValidUrl = $isValidUrl ?? false;
@endphp

<style>
    @keyframes spin-horizontal {
        0% {
            transform: rotateY(0deg);
        }
        100% {
            transform: rotateY(360deg);
        }
    }
    .animate-spin-horizontal {
        animation: spin-horizontal 6s linear infinite;
        perspective: 1000px;
        transform-style: preserve-3d;
    }

    /* ========== HIGHLIGHT & ANALISIS ANIMATIONS ========== */

    /* Rotating rainbow border glow */
    @keyframes highlight-border-spin {
        0%   { border-color: rgba(96,165,250,0.8); box-shadow: 0 0 16px 3px rgba(96,165,250,0.35), 0 0 40px 8px rgba(99,102,241,0.15); }
        25%  { border-color: rgba(167,139,250,0.8); box-shadow: 0 0 16px 3px rgba(167,139,250,0.35), 0 0 40px 8px rgba(99,102,241,0.15); }
        50%  { border-color: rgba(244,114,182,0.6); box-shadow: 0 0 16px 3px rgba(244,114,182,0.30), 0 0 40px 8px rgba(99,102,241,0.15); }
        75%  { border-color: rgba(52,211,153,0.7); box-shadow: 0 0 16px 3px rgba(52,211,153,0.30), 0 0 40px 8px rgba(99,102,241,0.15); }
        100% { border-color: rgba(96,165,250,0.8); box-shadow: 0 0 16px 3px rgba(96,165,250,0.35), 0 0 40px 8px rgba(99,102,241,0.15); }
    }
    .highlight-card-animated {
        border: 1.5px solid rgba(96,165,250,0.8);
        animation: highlight-border-spin 4s linear infinite;
        will-change: border-color, box-shadow;
        transform: translateZ(0);
    }

    /* Floating glow orbs */
    @keyframes float-orb {
        0%, 100% { transform: translate3d(0, 0, 0) scale(1); opacity: 0.20; }
        50%       { transform: translate3d(-8px, 8px, 0) scale(1.15); opacity: 0.35; }
    }
    @keyframes float-orb2 {
        0%, 100% { transform: translate3d(0, 0, 0) scale(1); opacity: 0.15; }
        50%       { transform: translate3d(8px, -6px, 0) scale(1.1); opacity: 0.28; }
    }
    .highlight-glow-orb  { animation: float-orb  5s ease-in-out infinite; will-change: transform, opacity; }
    .highlight-glow-orb2 { animation: float-orb2 6s ease-in-out infinite; will-change: transform, opacity; }

    /* Shimmer text effect for title */
    @keyframes shimmer-text {
        0%   { background-position: -200% center; }
        100% { background-position: 200% center; }
    }
    .highlight-shimmer-text {
        background: linear-gradient(90deg, #93c5fd 0%, #ffffff 40%, #c4b5fd 60%, #93c5fd 100%);
        background-size: 200% auto;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: shimmer-text 3s linear infinite;
        will-change: background-position;
    }

    /* Icon soft pulse */
    @keyframes icon-pulse {
        0%, 100% { transform: scale3d(1, 1, 1);    box-shadow: 0 0 0 0 rgba(234,179,8,0.4); }
        50%       { transform: scale3d(1.08, 1.08, 1); box-shadow: 0 0 0 6px rgba(234,179,8,0); }
    }
    .highlight-icon-pulse { animation: icon-pulse 2.5s ease-in-out infinite; will-change: transform, box-shadow; }

    /* Alert blink border for stock warning */
    @keyframes alert-blink {
        0%, 100% { border-color: rgba(244,63,94,0.35); }
        50%       { border-color: rgba(244,63,94,0.90); box-shadow: 0 0 10px 2px rgba(244,63,94,0.25); }
    }
    .highlight-alert-blink { animation: alert-blink 1.4s ease-in-out infinite; will-change: border-color, box-shadow; }

    /* Staggered slide-in for rows */
    @keyframes slide-in-up {
        from { opacity: 0; transform: translate3d(0, 18px, 0); }
        to   { opacity: 1; transform: translate3d(0, 0, 0); }
    }
    .highlight-item-1 { animation: slide-in-up 0.5s ease-out 0.1s both; }
    .highlight-item-2 { animation: slide-in-up 0.5s ease-out 0.3s both; }
    .highlight-item-3 { animation: slide-in-up 0.5s ease-out 0.5s both; }

    /* ===================================================== */
</style>

@php
if (!function_exists('getPercentColorClass')) {
    function getPercentColorClass($val) {
        if ($val >= 100) return 'text-emerald-600 font-extrabold';
        if ($val < 80) return 'text-rose-600 font-extrabold';
        return 'text-slate-800 font-semibold';
    }
}

// Calculate totals dynamically from database
$totalTargetTantangan = 0;
$totalTargetMin = 0;
$totalAcv = 0;
$totalTargetReguler = 0;
$totalLm = 0;
$totalTargetReguler2026 = 0;
$totalActYtdJan2026 = 0;
$totalTargetPerbulanUtk2026 = 0;
$totalStock2024 = 0;
$totalStock2025 = 0;
$totalStock2026 = 0;
$totalStockTotal = 0;

$dealersStock2024Map = [];
$dealersStock2025Map = [];
$dealersStock2026Map = [];

$dealersOnTarget = 0;
$dealersBelowTarget = 0;
$dealersOnTargetNames = [];
$oldStockAlerts = [];

foreach($cabangs as $cabang) {
    $totalTargetTantangan += $cabang->target_tantangan;
    $totalAcv += $cabang->acv;
    $totalTargetReguler += $cabang->target_reguler;
    $totalLm += $cabang->lm;
    $totalTargetReguler2026 += $cabang->target_reguler_2026;
    $totalActYtdJan2026 += $cabang->act_ytd_jan_2026;
    $totalTargetPerbulanUtk2026 += $cabang->target_perbulan_utk_2026;
    $totalStock2024 += $cabang->stock_2024;
    $totalStock2025 += $cabang->stock_2025;
    $totalStock2026 += $cabang->stock_2026;

    if ($cabang->stock_2024 > 0) {
        $dealersStock2024Map[] = $cabang->nama . ' (' . $cabang->stock_2024 . ' Unit)';
    }
    if ($cabang->stock_2025 > 0) {
        $dealersStock2025Map[] = $cabang->nama . ' (' . $cabang->stock_2025 . ' Unit)';
    }
    if ($cabang->stock_2026 > 0) {
        $dealersStock2026Map[] = $cabang->nama . ': ' . $cabang->stock_2026 . ' Unit';
    }

    // Minimum target between target reguler & target tantangan per branch
    if ($cabang->target_tantangan > 0 && $cabang->target_reguler > 0) {
        $minTarget = min($cabang->target_tantangan, $cabang->target_reguler);
    } else {
        $minTarget = max($cabang->target_tantangan, $cabang->target_reguler);
    }
    $totalTargetMin += $minTarget;

    $acvPercent = $minTarget > 0 ? ($cabang->acv / $minTarget) * 100 : 0;
    if ($acvPercent >= 100) {
        $dealersOnTarget++;
        $dealersOnTargetNames[] = $cabang->nama . ' (' . round($acvPercent) . '%)';
    } else {
        $dealersBelowTarget++;
    }

    if ($cabang->stock_2024 > 0) {
        $oldStockAlerts[] = $cabang->nama . ' (Stock 2024: ' . $cabang->stock_2024 . ' unit)';
    }
    if ($cabang->stock_2025 > 0) {
        $oldStockAlerts[] = $cabang->nama . ' (Stock 2025: ' . $cabang->stock_2025 . ' unit)';
    }
}

$totalStockTotal = $totalStock2024 + $totalStock2025 + $totalStock2026;

// Overall percentages
$overallAcvPercent = $totalTargetMin > 0 ? ($totalAcv / $totalTargetMin) * 100 : 0;
$overallRegulerAcvPercent = $totalTargetReguler > 0 ? ($totalAcv / $totalTargetReguler) * 100 : 0;
$overallGrowthPercent = $totalLm > 0 ? ($totalAcv / $totalLm) * 100 : 0;
$overallYtdPercent = $totalTargetReguler2026 > 0 ? ($totalActYtdJan2026 / $totalTargetReguler2026) * 100 : 0;

$colorPalette = [
    'Pekanbaru' => '#9ca3af', // Gray
    'Sei Pagar' => '#f59e0b', // Amber/Yellow
    'Air Molek' => '#3b82f6', // Blue
    'Sorek' => '#22c55e',     // Green
    'Kandis' => '#1e3a8a',    // Dark Navy Blue
    'Medan' => '#b45309',     // Brown/Bronze
];
@endphp

<!-- Outer Glassmorphic / Premium Border Card wrapper -->
<div class="bg-gradient-to-br from-blue-950 via-slate-900 to-blue-900 text-white rounded-2xl lg:rounded-3xl p-3 lg:p-6 shadow-2xl border border-blue-900 overflow-hidden">

    <!-- Header Section -->
    <header class="bg-gradient-to-r from-blue-900 via-blue-950 to-blue-900 rounded-xl lg:rounded-2xl p-4 lg:p-6 border border-blue-800 shadow-2xl relative overflow-hidden flex flex-col md:flex-row md:items-center md:justify-between">
        <!-- background glow -->
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-blue-500 rounded-full blur-3xl opacity-20"></div>
        
        <!-- Left: Yamaha Logo -->
        <div class="flex items-center justify-center md:justify-start z-10">
            <img src="{{ asset('yamaha_logo.png') }}" alt="YAMAHA" class="h-10 lg:h-16 w-auto object-contain">
        </div>

        <!-- Center: Title -->
        <div class="text-center my-3 md:my-0 z-10 flex-1">
            <h1 class="text-xl lg:text-3xl font-extrabold text-white tracking-wider drop-shadow-md">
                REPORT SALES
            </h1>
            <p class="text-yellow-400 font-semibold tracking-widest text-[10px] lg:text-sm mt-0.5 uppercase">
                {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}
            </p>
        </div>

        <!-- Right: GASPOLL Badge -->
        <div class="z-10 flex flex-col items-center md:items-end">
            <div class="bg-gradient-to-r from-yellow-400 to-amber-500 text-blue-950 font-extrabold px-2.5 py-1.5 lg:px-4 lg:py-2 rounded-lg lg:rounded-xl italic text-[10px] lg:text-xs shadow-lg transform hover:scale-105 transition duration-300 border border-yellow-300 uppercase tracking-tight flex items-center space-x-1">
                <span>Gebrak Bersama</span>
                <span class="text-red-700 font-extrabold">Full Gasspoll!</span>
            </div>
        </div>
    </header>

    <!-- Spreadsheet Sync Status & Notification Alerts -->
    <div class="mt-6">
        @if(session('success'))
            <div class="mb-4 bg-emerald-500/20 border border-emerald-500 text-emerald-100 rounded-xl p-3.5 flex items-center space-x-3 text-sm">
                <i class="bi bi-check-circle-fill text-lg text-emerald-400 animate-bounce"></i>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-rose-500/20 border border-rose-500 text-rose-100 rounded-xl p-3.5 flex items-center space-x-3 text-sm">
                <i class="bi bi-exclamation-triangle-fill text-lg text-rose-400 animate-pulse"></i>
                <span class="font-semibold">{{ session('error') }}</span>
            </div>
        @endif

        @if(!$isValidUrl)
            <div class="bg-blue-500/10 border border-blue-800 text-blue-200 rounded-xl p-4 text-xs flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0 shadow-lg">
                <div class="flex items-start space-x-3">
                    <i class="bi bi-info-circle-fill text-lg text-blue-400 mt-0.5"></i>
                    <div>
                        <span class="font-extrabold text-blue-300 block text-sm">Hubungkan Spreadsheet Cabang Anda</span>
                        <span class="text-gray-300 mt-1 block">Silakan atur URL Google Spreadsheet khusus untuk masing-masing cabang agar sinkronisasi realtime aktif.</span>
                    </div>
                </div>
                @auth
                    @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('cabang.index') }}" class="bg-blue-800 hover:bg-blue-700 text-white font-extrabold px-4 py-2 rounded-xl transition duration-200 text-center whitespace-nowrap flex items-center justify-center space-x-2 border border-blue-700 hover:border-blue-600 shadow-md">
                            <span>Atur URL Cabang</span>
                            <i class="bi bi-gear-fill"></i>
                        </a>
                    @endif
                @endauth
            </div>
        @else
            <div class="bg-slate-900/50 border border-blue-900 rounded-xl p-3.5 flex flex-col sm:flex-row sm:items-center sm:justify-between text-sm text-gray-300 shadow-lg">
                <div class="flex items-center space-x-3">
                    <span class="flex h-2.5 w-2.5 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    @auth
                        @if(auth()->user()->isSuperAdmin())
                            <span class="font-bold text-slate-200">Koneksi Spreadsheet Cabang Terhubung (Klik "Sinkronisasi Sekarang" untuk memperbarui data)</span>
                        @else
                            <span class="font-bold text-slate-200">Koneksi Spreadsheet Cabang Terhubung (Data Realtime Sales & Stok Cabang)</span>
                        @endif
                    @endauth
                </div>
                @auth
                    @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('sync.spreadsheet') }}" class="mt-2 sm:mt-0 bg-blue-800 hover:bg-blue-700 text-white font-extrabold px-4 py-2.5 rounded-xl transition duration-200 inline-flex items-center space-x-2 border border-blue-700 hover:border-blue-600 shadow-md transform hover:scale-105 active:scale-95 duration-150">
                            <i class="bi bi-arrow-repeat text-sm"></i>
                            <span>Sinkronisasi Sekarang</span>
                        </a>
                    @endif
                @endauth
            </div>
        @endif
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-4 lg:gap-6 mt-4 lg:mt-6">
        
        <!-- Left Column: Sidebar Cards (1/4 Width) -->
        <div class="xl:col-span-1 flex flex-col space-y-4 lg:space-y-6">
            
            <!-- Ringkasan Kinerja Card -->
            <div class="bg-slate-900/80 border border-blue-900/60 rounded-2xl lg:rounded-3xl p-4 lg:p-5 shadow-2xl backdrop-blur-md relative overflow-hidden flex flex-col justify-start hover:border-blue-700/80 transition duration-300">
                <!-- Background glow decoration -->
                <div class="absolute -right-16 -top-16 w-32 h-32 bg-blue-500/10 rounded-full blur-2xl"></div>

                <!-- Frame Header -->
                <div class="flex items-center justify-between border-b border-blue-950 pb-2.5 mb-3.5">
                    <h3 class="text-sm lg:text-base font-extrabold text-white tracking-wider uppercase flex items-center space-x-2">
                        <span class="inline-block w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                        <span>Ringkasan Kinerja</span>
                    </h3>
                </div>
                
                <div class="space-y-3">
                    <!-- STU vs Target -->
                    <div class="bg-slate-950/40 rounded-xl p-3 flex items-center justify-between shadow-inner border border-blue-950/80 hover:border-blue-700/50 hover:bg-slate-950/60 transition duration-300">
                        <div class="flex items-center space-x-3 min-w-0 flex-1">
                            <div class="bg-blue-500/10 text-blue-400 p-2.5 rounded-lg shrink-0 border border-blue-500/20">
                                <i class="bi bi-record-circle-fill text-lg"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs lg:text-sm text-slate-300 font-bold uppercase tracking-wider leading-tight">ACV VS TARGET</p>
                                <p class="text-sm lg:text-base text-slate-100 mt-0.5 font-extrabold leading-tight"><span class="counter-animate" data-target="{{ $totalAcv }}">{{ number_format($totalAcv) }}</span> <span class="text-slate-400 font-medium">/ <span class="counter-animate" data-target="{{ $totalTargetMin }}">{{ number_format($totalTargetMin) }}</span></span></p>
                            </div>
                        </div>
                        <div class="text-right shrink-0 pl-2">
                            <span class="counter-animate text-xl lg:text-3xl font-black tracking-tight text-blue-400 leading-none" data-target="{{ round($overallAcvPercent) }}" data-suffix="%">{{ round($overallAcvPercent) }}%</span>
                        </div>
                    </div>

                    <!-- Growth (VS LM) -->
                    <div class="bg-slate-950/40 rounded-xl p-3 flex items-center justify-between shadow-inner border border-blue-950/80 hover:border-emerald-700/50 hover:bg-slate-950/60 transition duration-300">
                        <div class="flex items-center space-x-3 min-w-0 flex-1">
                            <div class="bg-emerald-500/10 text-emerald-400 p-2.5 rounded-lg shrink-0 border border-emerald-500/20">
                                <i class="bi bi-graph-up-arrow text-lg"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs lg:text-sm text-slate-300 font-bold uppercase tracking-wider leading-tight">VS Last Month (LM)</p>
                                <p class="text-sm lg:text-base text-slate-100 mt-0.5 font-extrabold leading-tight"><span class="counter-animate" data-target="{{ $totalAcv }}">{{ number_format($totalAcv) }}</span> <span class="text-slate-400 font-medium">/ <span class="counter-animate" data-target="{{ $totalLm }}">{{ number_format($totalLm) }}</span></span></p>
                            </div>
                        </div>
                        <div class="text-right shrink-0 pl-2">
                            <span class="counter-animate text-xl lg:text-3xl font-black tracking-tight text-emerald-400 leading-none" data-target="{{ round($overallGrowthPercent) }}" data-suffix="%">{{ round($overallGrowthPercent) }}%</span>
                        </div>
                    </div>

                    <!-- YTD Achievement -->
                    <div class="bg-slate-950/40 rounded-xl p-3 flex items-center justify-between shadow-inner border border-blue-950/80 hover:border-purple-700/50 hover:bg-slate-955/50 transition duration-300">
                        <div class="flex items-center space-x-3 min-w-0 flex-1">
                            <div class="bg-purple-500/10 text-purple-400 p-2.5 rounded-lg shrink-0 border border-purple-500/20">
                                <i class="bi bi-calendar2-check-fill text-lg"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs lg:text-sm text-slate-300 font-bold uppercase tracking-wider leading-tight">ACT YTD vs Target 2026</p>
                                <p class="text-sm lg:text-base text-slate-100 mt-0.5 font-extrabold leading-tight"><span class="counter-animate" data-target="{{ $totalActYtdJan2026 }}">{{ number_format($totalActYtdJan2026) }}</span> <span class="text-slate-400 font-medium">/ <span class="counter-animate" data-target="{{ $totalTargetReguler2026 }}">{{ number_format($totalTargetReguler2026) }}</span></span></p>
                            </div>
                        </div>
                        <div class="text-right shrink-0 pl-2">
                            <span class="counter-animate text-xl lg:text-3xl font-black tracking-tight text-purple-400 leading-none" data-target="{{ round($overallYtdPercent) }}" data-suffix="%">{{ round($overallYtdPercent) }}%</span>
                        </div>
                    </div>

                    <!-- DEALER ON TARGET -->
                    <div class="bg-slate-950/40 rounded-xl p-3 flex items-center justify-between shadow-inner border border-blue-950/80 hover:border-yellow-700/50 hover:bg-slate-955/50 transition duration-300">
                        <div class="flex items-center space-x-3 min-w-0 flex-1">
                            <div class="bg-yellow-500/10 text-yellow-450 p-2.5 rounded-lg shrink-0 border border-yellow-500/20">
                                <i class="bi bi-trophy-fill text-lg text-yellow-400"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs lg:text-sm text-slate-300 font-bold uppercase tracking-wider leading-tight">Dealer On Target</p>
                                <p class="text-xs text-slate-400 mt-0.5 font-semibold leading-tight">Capai Target</p>
                            </div>
                        </div>
                        <div class="text-right shrink-0 pl-2">
                            <span class="counter-animate text-xl lg:text-3xl font-black tracking-tight text-yellow-400 leading-none" data-target="{{ $dealersOnTarget }}">{{ $dealersOnTarget }}</span>
                        </div>
                    </div>

                    <!-- DEALER DI BAWAH TARGET -->
                    <div class="bg-slate-950/40 rounded-xl p-3 flex items-center justify-between shadow-inner border border-blue-950/80 hover:border-rose-700/50 hover:bg-slate-955/50 transition duration-300">
                        <div class="flex items-center space-x-3 min-w-0 flex-1">
                            <div class="bg-rose-500/10 text-rose-455 p-2.5 rounded-lg shrink-0 border border-rose-500/20">
                                <i class="bi bi-graph-down-arrow text-lg text-rose-400"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs lg:text-sm text-slate-300 font-bold uppercase tracking-wider leading-tight">Dealer Below Target</p>
                                <p class="text-xs text-slate-400 mt-0.5 font-semibold leading-tight">Belum Capai Target</p>
                            </div>
                        </div>
                        <div class="text-right shrink-0 pl-2">
                            <span class="counter-animate text-xl lg:text-3xl font-black tracking-tight text-rose-400 leading-none" data-target="{{ $dealersBelowTarget }}">{{ $dealersBelowTarget }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Highlights & Alerts Card (Highlight & Analisis) -->
            <div id="highlight-card" class="bg-slate-900/80 rounded-2xl lg:rounded-3xl p-4 lg:p-5 shadow-2xl backdrop-blur-md relative overflow-hidden flex flex-col justify-start highlight-card-animated">
                <!-- Background glow decoration -->
                <div class="absolute -right-16 -top-16 w-40 h-40 bg-blue-500/20 rounded-full blur-2xl highlight-glow-orb"></div>
                <div class="absolute -left-10 -bottom-10 w-32 h-32 bg-indigo-500/15 rounded-full blur-2xl highlight-glow-orb2"></div>

                <!-- Frame Header -->
                <div class="flex items-center justify-between border-b border-blue-950 pb-2.5 mb-3.5">
                    <h3 class="text-sm lg:text-base font-extrabold text-white tracking-wider uppercase flex items-center space-x-2">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                        </span>
                        <span class="highlight-shimmer-text">Highlight & Analisis</span>
                    </h3>
                </div>
                
                <div class="space-y-2.5">
                    <!-- Achieved Target -->
                    <div class="bg-slate-950/30 rounded-xl p-2.5 lg:p-3 flex items-start space-x-2.5 shadow-inner border border-yellow-500/20 hover:border-yellow-400/60 hover:bg-yellow-500/5 transition duration-300 highlight-item-1">
                        <div class="bg-yellow-500/15 text-yellow-400 p-2 rounded-lg shrink-0 border border-yellow-500/30 highlight-icon-pulse">
                            <i class="bi bi-trophy-fill text-base"></i>
                        </div>
                        <div class="min-w-0 flex-1 text-left">
                            <p class="text-[9.5px] lg:text-xs text-slate-400 font-semibold uppercase tracking-wider leading-tight">Achieved Target</p>
                            <p class="text-xs text-yellow-400 font-bold leading-tight mt-0.5">{{ $dealersOnTarget }} Dealer Capai Target</p>
                            <p class="text-[9.5px] lg:text-[10.5px] text-slate-400 mt-1 leading-normal font-semibold">{{ !empty($dealersOnTargetNames) ? implode(', ', $dealersOnTargetNames) : 'Belum ada dealer' }}</p>
                        </div>
                    </div>

                    <!-- Older Stock Alert -->
                    @if(!empty($oldStockAlerts))
                        <div class="bg-slate-950/30 rounded-xl p-2.5 lg:p-3 flex items-start space-x-2.5 shadow-inner border border-rose-500/30 hover:border-rose-400/60 hover:bg-rose-500/5 transition duration-300 highlight-item-2 highlight-alert-blink">
                            <div class="bg-rose-500/10 text-rose-400 p-2 rounded-lg shrink-0 border border-rose-500/20">
                                <i class="bi bi-exclamation-triangle-fill text-base"></i>
                            </div>
                            <div class="min-w-0 flex-1 text-left">
                                <p class="text-[9.5px] lg:text-xs text-slate-400 font-semibold uppercase tracking-wider leading-tight">Stock Alert</p>
                                <p class="text-xs text-rose-400 font-bold leading-tight mt-0.5">Prioritaskan Penjualan</p>
                                <p class="text-[9.5px] lg:text-[10.5px] text-slate-400 mt-1 leading-normal font-semibold">{{ implode(', ', $oldStockAlerts) }}</p>
                            </div>
                        </div>
                    @else
                        <div class="bg-slate-950/30 rounded-xl p-2.5 lg:p-3 flex items-start space-x-2.5 shadow-inner border border-emerald-500/20 hover:border-emerald-400/60 hover:bg-emerald-500/5 transition duration-300 highlight-item-2">
                            <div class="bg-emerald-500/10 text-emerald-400 p-2 rounded-lg shrink-0 border border-emerald-500/20">
                                <i class="bi bi-shield-check text-base"></i>
                            </div>
                            <div class="min-w-0 flex-1 text-left">
                                <p class="text-[9.5px] lg:text-xs text-slate-400 font-semibold uppercase tracking-wider leading-tight">Stock Condition</p>
                                <p class="text-xs text-emerald-400 font-bold leading-tight mt-0.5">Optimal</p>
                                <p class="text-[9.5px] lg:text-[10.5px] text-slate-400 mt-1 leading-normal font-semibold">Seluruh cabang bersih dari stock lama (2024/2025).</p>
                            </div>
                        </div>
                    @endif

                    <!-- YTD Progress -->
                    <div class="bg-slate-950/30 rounded-xl p-2.5 lg:p-3 flex items-start space-x-2.5 shadow-inner border border-blue-500/20 hover:border-blue-400/60 hover:bg-blue-500/5 transition duration-300 highlight-item-3">
                        <div class="bg-blue-500/10 text-blue-400 p-2 rounded-lg shrink-0 border border-blue-500/20">
                            <i class="bi bi-bar-chart-fill text-base"></i>
                        </div>
                        <div class="min-w-0 flex-1 text-left">
                            <p class="text-[9.5px] lg:text-xs text-slate-400 font-semibold uppercase tracking-wider leading-tight">YTD 2026 Progress</p>
                            <p class="text-xs text-blue-400 font-bold leading-tight mt-0.5">Pencapaian: <span id="highlight-ytd-percent">{{ round($overallYtdPercent) }}</span>%</p>
                            <p class="text-[9.5px] lg:text-[10.5px] text-slate-400 mt-1 leading-normal font-semibold">Total actual YTD <span id="highlight-ytd-act">{{ $totalActYtdJan2026 }}</span> unit dari target 2026 <span id="highlight-ytd-target">{{ $totalTargetReguler2026 }}</span> unit.</p>
                        </div>
                    </div>

                    <!-- Catatan — editable by super_admin, read-only for viewer -->
                    <div class="bg-slate-950/30 rounded-xl shadow-inner border border-indigo-500/20 hover:border-indigo-400/50 transition duration-300" style="animation: slide-in-up 0.5s ease-out 0.7s both; animation-fill-mode: both;">

                        @auth
                            @if(auth()->user()->isSuperAdmin())
                                {{-- Super Admin: icon header + editable textarea --}}
                                <div class="flex items-start space-x-2.5 px-2.5 pt-2.5 pb-2">
                                    <div class="bg-indigo-500/10 text-indigo-400 p-2 rounded-lg shrink-0 border border-indigo-500/20">
                                        <i class="bi bi-pencil-square text-base"></i>
                                    </div>
                                    <div class="min-w-0 flex-1 text-left">
                                        <p class="text-[9.5px] lg:text-xs text-slate-400 font-semibold uppercase tracking-wider leading-tight">Catatan</p>
                                        <p class="text-xs text-indigo-400 font-bold leading-tight mt-0.5">Admin Note</p>
                                    </div>
                                </div>

                                @if(session('notes_success'))
                                    <div class="mx-2.5 mb-1.5 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 rounded-lg px-2.5 py-1.5 text-[9.5px] font-semibold flex items-center space-x-1.5">
                                        <i class="bi bi-check-circle-fill"></i>
                                        <span>{{ session('notes_success') }}</span>
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('dashboard.notes.save') }}" id="catatan-form">
                                    @csrf
                                    <textarea
                                        name="content"
                                        id="catatan-textarea"
                                        rows="4"
                                        maxlength="2000"
                                        placeholder="Tulis catatan penting di sini…"
                                        class="w-full bg-transparent text-slate-300 text-[10.5px] lg:text-xs leading-relaxed resize-none px-2.5 py-1.5 focus:outline-none placeholder-slate-600 font-medium border-t border-indigo-900/30"
                                    >{{ old('content', $dashboardNote->content) }}</textarea>
                                    <div class="flex items-center justify-between px-2.5 pb-2.5 pt-1 border-t border-indigo-900/30">
                                        <span id="catatan-char" class="text-[9px] text-slate-600">0 / 2000</span>
                                        <button type="submit"
                                            class="inline-flex items-center space-x-1.5 bg-indigo-600 hover:bg-indigo-500 active:scale-95 text-white text-[9.5px] font-extrabold uppercase tracking-wider px-3 py-1.5 rounded-lg transition duration-150 shadow border border-indigo-500">
                                            <i class="bi bi-floppy-fill"></i>
                                            <span>Simpan</span>
                                        </button>
                                    </div>
                                </form>

                            @else
                                {{-- Viewer: same icon layout, text only --}}
                                <div class="flex items-start space-x-2.5 p-2.5">
                                    <div class="bg-indigo-500/10 text-indigo-400 p-2 rounded-lg shrink-0 border border-indigo-500/20">
                                        <i class="bi bi-journal-text text-base"></i>
                                    </div>
                                    <div class="min-w-0 flex-1 text-left">
                                        <p class="text-[9.5px] lg:text-xs text-slate-400 font-semibold uppercase tracking-wider leading-tight">Catatan</p>
                                        @if($dashboardNote->content)
                                            <p class="text-[10.5px] text-slate-300 leading-relaxed whitespace-pre-wrap font-medium mt-1">{{ $dashboardNote->content }}</p>
                                        @else
                                            <p class="text-[10px] text-slate-600 italic mt-0.5">Belum ada catatan.</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endauth
                    </div>


                </div>
            </div>

            <!-- Target overall Gauge card (Pencapaian Target) -->
            <div class="bg-slate-900/80 border border-blue-900/60 rounded-2xl lg:rounded-3xl p-4 lg:p-5 shadow-2xl backdrop-blur-md relative overflow-hidden flex flex-col items-center justify-center text-center hover:border-blue-700/80 transition duration-300">
                <!-- Background glow decoration -->
                <div class="absolute -right-16 -top-16 w-32 h-32 bg-blue-500/10 rounded-full blur-2xl"></div>

                <!-- Frame Header -->
                <div class="flex items-center justify-between border-b border-blue-950 pb-2.5 mb-3.5 w-full">
                    <h3 class="text-sm lg:text-base font-extrabold text-white tracking-wider uppercase flex items-center space-x-2">
                        <span class="inline-block w-2 h-2 rounded-full bg-yellow-400 animate-pulse"></span>
                        <span>Pencapaian Target</span>
                    </h3>
                </div>
                
                <div class="relative w-28 h-28 lg:w-36 lg:h-36 flex items-center justify-center my-2">
                    <!-- SVG Circular Progress Ring -->
                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                        <!-- BG circle -->
                        <circle cx="50" cy="50" r="40" stroke="#1E293B" stroke-width="10" fill="transparent" />
                        <!-- Progress indicator -->
                        <circle cx="50" cy="50" r="40" stroke="#EAB308" stroke-width="10" fill="transparent"
                                stroke-dasharray="251.2"
                                stroke-dashoffset="{{ 251.2 - (251.2 * min(100, $overallAcvPercent)) / 100 }}"
                                stroke-linecap="round"
                                class="transition-all duration-1000 ease-out" />
                    </svg>
                    <div class="absolute flex flex-col items-center justify-center">
                        <span class="text-2xl lg:text-4xl font-extrabold text-white leading-none">{{ round($overallAcvPercent) }}%</span>
                    </div>
                </div>
                
                <div class="mt-2 text-center">
                    <p class="text-lg lg:text-2xl font-extrabold text-yellow-400">{{ $totalAcv }} / {{ $totalTargetMin }}</p>
                    <p class="text-xs text-slate-500 font-semibold uppercase mt-0.5">ACV vs Target</p>
                </div>
            </div>

            <!-- Status Input Laporan Harian (Tanggal Hari Ini) - Susunan Vertikal -->
            @php
                $todayDay = (int)date('j');
                $reportingDay = min(31, max(1, $todayDay));
                $reportingIdx = $reportingDay - 1;

                $sudahInputCabangs = [];
                $belumInputCabangs = [];

                foreach ($cabangs as $cabang) {
                    $rawPoints = $cabang->daily_performance ?: [];
                    if (isset($rawPoints[$reportingIdx]) && $rawPoints[$reportingIdx] !== null) {
                        $sudahInputCabangs[] = $cabang;
                    } else {
                        $belumInputCabangs[] = $cabang;
                    }
                }
            @endphp

            <!-- Box 1: Sudah Input Laporan Hari Ini -->
            <div class="bg-emerald-955/50 border border-emerald-500/40 rounded-2xl lg:rounded-3xl p-4 shadow-xl backdrop-blur-md relative overflow-hidden flex flex-col justify-start hover:border-emerald-400/60 transition duration-300">
                <div class="flex items-center justify-between border-b border-emerald-500/30 pb-2.5 mb-3">
                    <div class="flex items-center space-x-2 min-w-0">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
                        <h4 class="text-xs lg:text-sm font-extrabold text-emerald-300 uppercase tracking-wider leading-tight">
                            Sudah Input Laporan Hari Ini
                        </h4>
                    </div>
                    <span class="text-[10px] lg:text-xs font-black bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 px-2 py-0.5 rounded-md shrink-0">
                        {{ count($sudahInputCabangs) }} Cabang
                    </span>
                </div>

                <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wide mb-2">Tanggal Laporan: {{ $reportingDay }} {{ \Carbon\Carbon::now()->locale('id')->isoFormat('MMMM Y') }}</p>
                
                @if(count($sudahInputCabangs) > 0)
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($sudahInputCabangs as $c)
                            @php
                                $color = $colorPalette[$c->nama] ?? '#22c55e';
                                $valToday = $c->daily_performance[$reportingIdx] ?? $c->acv;
                            @endphp
                            <div class="inline-flex items-center space-x-1.5 bg-slate-950/80 border border-emerald-500/30 px-2.5 py-1 rounded-lg text-xs font-bold text-white shadow">
                                <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $color }}; box-shadow: 0 0 6px {{ $color }}b0;"></span>
                                <span>{{ $c->nama }}</span>
                                <span class="text-emerald-400 font-extrabold">({{ $valToday }} Unit)</span>
                                <i class="bi bi-check-circle-fill text-emerald-400 text-xs"></i>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-400 italic">Belum ada cabang yang menginputkan laporan hari ini.</p>
                @endif
            </div>

            <!-- Box 2: Belum Input Laporan Hari Ini -->
            <div class="bg-rose-955/50 border border-rose-500/40 rounded-2xl lg:rounded-3xl p-4 shadow-xl backdrop-blur-md relative overflow-hidden flex flex-col justify-start hover:border-rose-400/60 transition duration-300">
                <div class="flex items-center justify-between border-b border-rose-500/30 pb-2.5 mb-3">
                    <div class="flex items-center space-x-2 min-w-0">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-400 animate-pulse shrink-0"></span>
                        <h4 class="text-xs lg:text-sm font-extrabold text-rose-300 uppercase tracking-wider leading-tight">
                            Belum Input Laporan Hari Ini
                        </h4>
                    </div>
                    <span class="text-[10px] lg:text-xs font-black bg-rose-500/20 text-rose-300 border border-rose-500/40 px-2 py-0.5 rounded-md shrink-0">
                        {{ count($belumInputCabangs) }} Cabang
                    </span>
                </div>

                <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wide mb-2">Tanggal Laporan: {{ $reportingDay }} {{ \Carbon\Carbon::now()->locale('id')->isoFormat('MMMM Y') }}</p>

                @if(count($belumInputCabangs) > 0)
                    <div class="flex flex-col space-y-1.5">
                        @foreach($belumInputCabangs as $c)
                            @php
                                $color = $colorPalette[$c->nama] ?? '#f43f5e';
                                $rawP = $c->daily_performance ?: [];
                                $lastDayNum = 0;
                                $lastVal = 0;
                                for ($i = count($rawP) - 1; $i >= 0; $i--) {
                                    if (isset($rawP[$i]) && $rawP[$i] !== null) {
                                        $lastDayNum = $i + 1;
                                        $lastVal = $rawP[$i];
                                        break;
                                    }
                                }
                            @endphp
                            <div class="flex items-center justify-between bg-slate-950/80 border border-rose-500/30 px-2.5 py-1.5 rounded-lg text-xs font-bold text-white shadow">
                                <div class="flex items-center space-x-1.5 min-w-0">
                                    <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $color }}; box-shadow: 0 0 6px {{ $color }}b0;"></span>
                                    <span class="truncate">{{ $c->nama }}</span>
                                </div>
                                <div class="flex items-center space-x-1 shrink-0 ml-2">
                                    @if($lastDayNum > 0)
                                        <span class="text-slate-400 font-medium text-[10.5px]">Tgl {{ $lastDayNum }}: <strong class="text-yellow-400 font-black">{{ $lastVal }}</strong></span>
                                    @else
                                        <span class="text-rose-400 text-[10.5px] font-bold">Belum Input</span>
                                    @endif
                                    <i class="bi bi-clock-history text-rose-400 text-xs"></i>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-emerald-500/10 border border-emerald-500/30 rounded-xl p-2.5 text-center text-emerald-300 text-xs font-bold flex items-center justify-center space-x-1.5">
                        <i class="bi bi-check-all text-base"></i>
                        <span>Semua cabang sudah menginputkan laporan hari ini!</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Columns: Table and Sub-cards (3/4 Width) -->
        <div class="xl:col-span-3 flex flex-col space-y-4 lg:space-y-6">
            
            <!-- Table Card -->
            <div class="bg-slate-900/80 border border-blue-900/60 rounded-2xl lg:rounded-3xl p-3 lg:p-5 shadow-2xl backdrop-blur-md overflow-hidden">
                <div class="bg-gradient-to-r from-blue-900 to-indigo-950 text-white font-bold text-center py-2 px-3 rounded-lg mb-3.5 tracking-wider text-sm lg:text-base uppercase flex flex-col sm:flex-row justify-between items-center shadow-inner">
                    <span>Daftar Performa Dealer (STU & Stock)</span>
                    <span class="text-xs lg:text-sm text-blue-300 normal-case italic mt-1 sm:mt-0">Persentase: >=100% (Hijau), <80% (Merah)</span>
                </div>
                
                <div class="overflow-x-auto rounded-xl border border-blue-800">
                    <table class="w-full text-sm lg:text-base text-center border-collapse whitespace-nowrap">
                        <thead class="bg-blue-950 text-white border-b-2 border-blue-800 text-sm lg:text-base font-extrabold uppercase tracking-wide whitespace-normal">
                            <tr>
                                <th class="sticky left-0 z-20 bg-blue-950 p-2.5 border border-blue-800 text-center align-middle w-10">No</th>
                                <th class="sticky left-10 z-20 bg-blue-950 p-2.5 border border-blue-800 text-left align-middle w-28">CABANG</th>
                                <th class="p-2.5 border border-blue-800 bg-slate-900/60">TARGET TANTANGAN</th>
                                <th class="p-2.5 border border-blue-800 bg-yellow-950/40">ACV</th>
                                <th class="p-2.5 border border-blue-800">% ACV</th>
                                <th class="p-2.5 border border-blue-800">+/-</th>
                                <th class="p-2.5 border border-blue-800 bg-purple-950/60">TARGET REGULER</th>
                                <th class="p-2.5 border border-blue-800 bg-purple-950/60">% ACV</th>
                                <th class="p-2.5 border border-blue-800 bg-purple-950/60">+/-</th>
                                <th class="p-2.5 border border-blue-800 bg-yellow-950/40">LM</th>
                                <th class="p-2.5 border border-blue-800">VS LM %</th>
                                <th class="p-2.5 border border-blue-800">VS LM UNIT</th>
                                <th class="p-2.5 border border-blue-800">KET</th>
                                <th class="p-2.5 border border-blue-800 bg-teal-950/40">TARGET REGULER 2026</th>
                                <th class="p-2.5 border border-blue-800 bg-teal-950/40">ACT YTD JAN 2026</th>
                                <th class="p-2.5 border border-blue-800 bg-teal-950/40">+/-</th>
                                <th class="p-2.5 border border-blue-800 bg-teal-950/40">% ACV</th>
                                <th class="p-2.5 border border-blue-800 bg-blue-900/30">TARGET PERBULAN UTK 2026</th>
                                <th class="p-2.5 border border-blue-800 bg-amber-950/50">STOCK 2024</th>
                                <th class="p-2.5 border border-blue-800 bg-amber-950/50">STOCK 2025</th>
                                <th class="p-2.5 border border-blue-800 bg-amber-950/50">STOCK 2026</th>
                                <th class="p-2.5 border border-slate-800 bg-slate-800">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody class="bg-slate-950/20 text-slate-100 font-semibold backdrop-blur-md">
                            @foreach($cabangs as $index => $cabang)
                            @php
                                $acvPercent = $cabang->target_tantangan > 0 ? ($cabang->acv / $cabang->target_tantangan) * 100 : 0;
                                $acvDiff = $cabang->acv - $cabang->target_tantangan;
                                
                                $regulerAcvPercent = $cabang->target_reguler > 0 ? ($cabang->acv / $cabang->target_reguler) * 100 : 0;
                                $regulerDiff = $cabang->acv - $cabang->target_reguler;
                                
                                $vsLmPercent = $cabang->lm > 0 ? ($cabang->acv / $cabang->lm) * 100 : 0;
                                $vsLmUnit = $cabang->acv - $cabang->lm;
                                $ket = $vsLmUnit >= 0 ? 'NAIK' : 'TURUN';
                                
                                $ytdDiff = $cabang->act_ytd_jan_2026 - $cabang->target_reguler_2026;
                                $ytdPercent = $cabang->target_reguler_2026 > 0 ? ($cabang->act_ytd_jan_2026 / $cabang->target_reguler_2026) * 100 : 0;
                                
                                $totalStock = $cabang->stock_2024 + $cabang->stock_2025 + $cabang->stock_2026;
                            @endphp
                            <tr class="branch-row group hover:bg-blue-900/30 border-b border-blue-900/30 transition duration-150 text-center" data-id="{{ $cabang->id }}">
                                <td class="sticky left-0 z-10 bg-slate-950/90 group-hover:bg-slate-900/95 py-2.5 px-3 border border-blue-900/40 text-center font-bold w-10 text-slate-400">{{ $index + 1 }}</td>
                                <td class="sticky left-10 z-10 bg-slate-950/90 group-hover:bg-slate-900/95 py-2.5 px-3 border border-blue-900/40 text-left font-extrabold w-28 truncate text-white">{{ $cabang->nama }}</td>
                                
                                <!-- TARGET TANTANGAN -->
                                <td class="py-2.5 px-3 border border-blue-900/30 text-slate-300 font-bold text-center">
                                    {{ $cabang->target_tantangan }}
                                </td>
                                <td class="acv-cell py-2.5 px-3 border border-blue-900/30 bg-yellow-500/10 text-yellow-300 font-extrabold" data-val="{{ $cabang->acv }}">{{ $cabang->acv }}</td>
                                <td class="acv-percent-cell py-2 px-2 lg:py-3.5 lg:px-4 border border-blue-900/30">
                                    @if($acvPercent >= 100)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-extrabold">{{ round($acvPercent) }}%</span>
                                    @elseif($acvPercent < 80)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-rose-500/20 text-rose-300 border border-rose-500/30 text-xs font-extrabold">{{ round($acvPercent) }}%</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-slate-800 text-slate-300 text-xs font-bold">{{ round($acvPercent) }}%</span>
                                    @endif
                                </td>
                                <td class="acv-diff-cell py-2 px-2 lg:py-3.5 lg:px-4 border border-blue-900/30 font-extrabold {{ $acvDiff < 0 ? 'text-rose-400' : 'text-emerald-400' }}">
                                    {{ $acvDiff > 0 ? '+' : '' }}{{ $acvDiff }}
                                </td>
                                
                                <!-- TARGET REGULER -->
                                <td class="py-2 px-2 lg:py-3.5 lg:px-4 border border-blue-900/30 bg-purple-500/5 text-purple-300 font-bold text-center">
                                    {{ $cabang->target_reguler }}
                                </td>
                                <td class="reguler-percent-cell py-2 px-2 lg:py-3.5 lg:px-4 border border-blue-900/30">
                                    @if($regulerAcvPercent >= 100)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-extrabold">{{ round($regulerAcvPercent) }}%</span>
                                    @elseif($regulerAcvPercent < 80)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-rose-500/20 text-rose-300 border border-rose-500/30 text-xs font-extrabold">{{ round($regulerAcvPercent) }}%</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-slate-800 text-slate-300 text-xs font-bold">{{ round($regulerAcvPercent) }}%</span>
                                    @endif
                                </td>
                                <td class="reguler-diff-cell py-2 px-2 lg:py-3.5 lg:px-4 border border-blue-900/30 font-extrabold {{ $regulerDiff < 0 ? 'text-rose-400' : 'text-emerald-400' }}">
                                    {{ $regulerDiff > 0 ? '+' : '' }}{{ $regulerDiff }}
                                </td>
                                
                                <!-- LM -->
                                <td class="lm-cell py-2 px-2 lg:py-3.5 lg:px-4 border border-blue-900/30 bg-yellow-500/10 text-yellow-300 font-extrabold" data-val="{{ $cabang->lm }}">{{ $cabang->lm }}</td>
                                <td class="lm-percent-cell py-2 px-2 lg:py-3.5 lg:px-4 border border-blue-900/30">
                                    @if($vsLmPercent >= 100)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-extrabold">{{ round($vsLmPercent) }}%</span>
                                    @elseif($vsLmPercent < 80)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-rose-500/20 text-rose-300 border border-rose-500/30 text-xs font-extrabold">{{ round($vsLmPercent) }}%</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-slate-800 text-slate-300 text-xs font-bold">{{ round($vsLmPercent) }}%</span>
                                    @endif
                                </td>
                                <td class="lm-diff-cell py-2 px-2 lg:py-3.5 lg:px-4 border border-blue-900/30 font-extrabold {{ $vsLmUnit < 0 ? 'text-rose-400' : 'text-emerald-400' }}">
                                    {{ $vsLmUnit > 0 ? '+' : '' }}{{ $vsLmUnit }}
                                </td>
                                <td class="lm-ket-cell py-2 px-2 lg:py-3.5 lg:px-4 border border-blue-900/30">
                                    @if($vsLmUnit >= 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 uppercase tracking-wider">
                                            <i class="bi bi-caret-up-fill mr-0.5 text-emerald-400"></i> NAIK
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-extrabold bg-rose-500/20 text-rose-300 border border-rose-500/30 uppercase tracking-wider">
                                            <i class="bi bi-caret-down-fill mr-0.5 text-rose-400"></i> TURUN
                                        </span>
                                    @endif
                                </td>
                                
                                <!-- YTD -->
                                <td class="py-2 px-2 lg:py-3.5 lg:px-4 border border-blue-900/30 bg-teal-500/5 text-teal-300 font-bold text-center">
                                    {{ $cabang->target_reguler_2026 }}
                                </td>
                                 <td class="ytd-act-cell py-2 px-2 border border-blue-900/30 bg-teal-500/5 text-center text-teal-300 font-bold">
                                    @auth
                                        @if(auth()->user()->isSuperAdmin())
                                            <input 
                                                type="number" 
                                                value="{{ $cabang->act_ytd_jan_2026 }}" 
                                                class="update-ytd-input w-24 text-center bg-teal-950/40 border border-teal-900/50 rounded px-2 py-1 text-teal-300 text-sm lg:text-base font-bold focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-transparent [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                                data-id="{{ $cabang->id }}"
                                                data-target-reguler="{{ $cabang->target_reguler_2026 }}"
                                            >
                                        @else
                                            <span>{{ $cabang->act_ytd_jan_2026 }}</span>
                                        @endif
                                    @else
                                        <span>{{ $cabang->act_ytd_jan_2026 }}</span>
                                    @endauth
                                 </td>
                                 <td class="ytd-diff-cell py-2 px-2 lg:py-3.5 lg:px-4 border border-blue-900/30 bg-teal-500/5 font-extrabold {{ $ytdDiff < 0 ? 'text-rose-400' : 'text-emerald-400' }}">
                                    {{ $ytdDiff > 0 ? '+' : '' }}{{ $ytdDiff }}
                                </td>
                                <td class="ytd-percent-cell py-2 px-2 lg:py-3.5 lg:px-4 border border-blue-900/30 bg-teal-500/5">
                                    @if($ytdPercent >= 100)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-extrabold">{{ round($ytdPercent) }}%</span>
                                    @elseif($ytdPercent < 80)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-rose-500/20 text-rose-300 border border-rose-500/30 text-xs font-extrabold">{{ round($ytdPercent) }}%</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-slate-800 text-slate-300 text-xs font-bold">{{ round($ytdPercent) }}%</span>
                                    @endif
                                </td>
                                
                                <!-- TARGET PERBULAN -->
                                <td class="ytd-target-perbulan-cell py-2 px-2 lg:py-3.5 lg:px-4 border border-blue-900/30 bg-blue-500/5 text-blue-300 font-extrabold">{{ $cabang->target_perbulan_utk_2026 }}</td>
                                
                                <!-- STOCK -->
                                <td class="py-2 px-2 lg:py-3.5 lg:px-4 border border-blue-900/30 bg-amber-500/10 text-amber-300">{{ $cabang->stock_2024 }}</td>
                                <td class="py-2 px-2 lg:py-3.5 lg:px-4 border border-blue-900/30 bg-amber-500/10 text-amber-300">{{ $cabang->stock_2025 }}</td>
                                <td class="py-2 px-2 lg:py-3.5 lg:px-4 border border-blue-900/30 bg-amber-500/10 text-amber-300">{{ $cabang->stock_2026 }}</td>
                                <td class="py-2 px-2 lg:py-3.5 lg:px-4 border border-blue-900/40 bg-slate-800 text-white font-extrabold">{{ $totalStock }}</td>
                            </tr>
                            @endforeach

                            <!-- TOTAL ROW -->
                            <tr class="total-row bg-blue-950/80 text-white font-extrabold border-t border-b border-blue-900 shadow-inner">
                                <td colspan="2" class="sticky left-0 z-10 bg-blue-950 py-2.5 px-3 lg:py-4 lg:px-4 border border-blue-900 text-left uppercase tracking-wider text-base">TOTAL</td>
                                
                                <!-- TARGET TANTANGAN TOTAL -->
                                <td class="total-tantangan-cell py-2.5 px-3 lg:py-4 lg:px-4 border border-blue-900">{{ $totalTargetTantangan }}</td>
                                <td class="total-acv-cell py-2.5 px-3 lg:py-4 lg:px-4 border border-blue-900 bg-yellow-500/20 text-yellow-300">{{ $totalAcv }}</td>
                                <td class="total-acv-percent-cell py-2.5 px-3 lg:py-4 lg:px-4 border border-blue-900">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-500/20 text-blue-300 border border-blue-500/30 text-xs font-extrabold">{{ round($overallAcvPercent) }}%</span>
                                </td>
                                <td class="total-acv-diff-cell py-2.5 px-3 lg:py-4 lg:px-4 border border-blue-900 {{ ($totalAcv - $totalTargetTantangan) < 0 ? 'text-rose-400' : 'text-emerald-400' }}">
                                    {{ ($totalAcv - $totalTargetTantangan) > 0 ? '+' : '' }}{{ $totalAcv - $totalTargetTantangan }}
                                </td>
                                
                                <!-- TARGET REGULER TOTAL -->
                                <td class="total-reguler-cell py-2.5 px-3 lg:py-4 lg:px-4 border border-blue-900 bg-purple-900/30 text-purple-300">{{ $totalTargetReguler }}</td>
                                <td class="total-reguler-percent-cell py-2.5 px-3 lg:py-4 lg:px-4 border border-blue-900 bg-purple-900/30">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-500/20 text-blue-300 border border-blue-500/30 text-xs font-extrabold">{{ round($overallRegulerAcvPercent) }}%</span>
                                </td>
                                <td class="total-reguler-diff-cell py-2.5 px-3 lg:py-4 lg:px-4 border-blue-900 bg-purple-900/30 {{ ($totalAcv - $totalTargetReguler) < 0 ? 'text-rose-400' : 'text-emerald-400' }}">
                                    {{ ($totalAcv - $totalTargetReguler) > 0 ? '+' : '' }}{{ $totalAcv - $totalTargetReguler }}
                                </td>
                                
                                <!-- LM TOTAL -->
                                <td class="total-lm-cell py-2.5 px-3 lg:py-4 lg:px-4 border border-blue-900 bg-yellow-500/20 text-yellow-300">{{ $totalLm }}</td>
                                <td class="total-growth-percent-cell py-2.5 px-3 lg:py-4 lg:px-4 border border-blue-900">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-500/20 text-blue-300 border border-blue-500/30 text-xs font-extrabold">{{ round($overallGrowthPercent) }}%</span>
                                </td>
                                <td class="total-growth-diff-cell py-2.5 px-3 lg:py-4 lg:px-4 border border-blue-900 {{ ($totalAcv - $totalLm) < 0 ? 'text-rose-400' : 'text-emerald-400' }}">
                                    {{ ($totalAcv - $totalLm) > 0 ? '+' : '' }}{{ $totalAcv - $totalLm }}
                                </td>
                                <td class="total-growth-ket-cell py-2.5 px-3 lg:py-4 lg:px-4 border border-blue-900">
                                    @if(($totalAcv - $totalLm) >= 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">NAIK</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-extrabold bg-rose-500/20 text-rose-300 border border-rose-500/30">TURUN</span>
                                    @endif
                                </td>
                                
                                <!-- YTD TOTAL -->
                                <td class="total-ytd-target-cell py-2.5 px-3 lg:py-4 lg:px-4 border border-blue-900 bg-teal-900/20">{{ $totalTargetReguler2026 }}</td>
                                <td class="total-ytd-act-cell py-2.5 px-3 lg:py-4 lg:px-4 border border-blue-900 bg-teal-900/20">{{ $totalActYtdJan2026 }}</td>
                                <td class="total-ytd-diff-cell py-2.5 px-3 lg:py-4 lg:px-4 border border-blue-900 bg-teal-900/20 {{ ($totalActYtdJan2026 - $totalTargetReguler2026) < 0 ? 'text-rose-400' : 'text-emerald-400' }}">
                                    {{ ($totalActYtdJan2026 - $totalTargetReguler2026) > 0 ? '+' : '' }}{{ $totalActYtdJan2026 - $totalTargetReguler2026 }}
                                </td>
                                <td class="total-ytd-percent-cell py-2.5 px-3 lg:py-4 lg:px-4 border border-blue-900 bg-teal-900/20">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-500/20 text-blue-300 border border-blue-500/30 text-xs font-extrabold">{{ round($overallYtdPercent) }}%</span>
                                </td>
                                
                                <!-- TARGET BULANAN TOTAL -->
                                <td class="total-monthly-target-cell py-2.5 px-3 lg:py-4 lg:px-4 border border-blue-900 bg-blue-900/40 text-blue-300">{{ $totalTargetPerbulanUtk2026 }}</td>
                                
                                <!-- STOCK TOTAL -->
                                <td class="total-stock-24-cell py-2.5 px-3 lg:py-4 lg:px-4 border border-blue-900 bg-amber-900/30 text-amber-300">{{ $totalStock2024 }}</td>
                                <td class="total-stock-25-cell py-2.5 px-3 lg:py-4 lg:px-4 border border-blue-900 bg-amber-900/30 text-amber-300">{{ $totalStock2025 }}</td>
                                <td class="total-stock-26-cell py-2.5 px-3 lg:py-4 lg:px-4 border border-blue-900 bg-amber-900/30 text-amber-300">{{ $totalStock2026 }}</td>
                                <td class="total-stock-total-cell py-2.5 px-3 lg:py-4 lg:px-4 border border-slate-700 bg-slate-800 text-white">{{ $totalStockTotal }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            @php
                $colorPalette = [
                    'Pekanbaru' => '#9ca3af', // Gray
                    'Sei Pagar' => '#f59e0b', // Amber/Yellow
                    'Air Molek' => '#3b82f6', // Blue
                    'Sorek' => '#22c55e',     // Green
                    'Kandis' => '#1e3a8a',    // Dark Navy Blue
                    'Medan' => '#b45309',     // Brown/Bronze
                ];

                $fallbackColors = [
                    '#ec4899', '#8b5cf6', '#06b6d4', '#10b981', '#f97316', '#e11d48', '#84cc16'
                ];

                $chartDatasets = [];
                $colorIndex = 0;

                foreach($cabangs as $cabang) {
                    $color = $colorPalette[$cabang->nama] ?? ($fallbackColors[$colorIndex % count($fallbackColors)]);
                    $colorIndex++;

                    $rawPoints = $cabang->daily_performance ?: [];

                    // Find the last index where THIS specific cabang has non-null data inputted in spreadsheet
                    $cabangLastIdx = -1;
                    for ($i = count($rawPoints) - 1; $i >= 0; $i--) {
                        if (isset($rawPoints[$i]) && $rawPoints[$i] !== null) {
                            $cabangLastIdx = $i;
                            break;
                        }
                    }

                    $processedData = [];
                    $lastValue = 0;

                    for ($day = 1; $day <= 31; $day++) {
                        $idx = $day - 1;
                        if ($cabangLastIdx !== -1 && $idx <= $cabangLastIdx) {
                            if (isset($rawPoints[$idx]) && $rawPoints[$idx] !== null) {
                                $lastValue = (int)$rawPoints[$idx];
                            }
                            $processedData[] = $lastValue;
                        } else {
                            $processedData[] = null;
                        }
                    }

                    $chartDatasets[] = [
                        'label' => $cabang->nama,
                        'data' => $processedData,
                        'borderColor' => $color,
                        'backgroundColor' => $color,
                        'borderWidth' => 3,
                        'tension' => 0.35,
                        'pointRadius' => 4,
                        'pointHoverRadius' => 7,
                        'pointBackgroundColor' => '#0f172a',
                        'pointBorderColor' => $color,
                        'pointBorderWidth' => 2,
                        'spanGaps' => false,
                    ];
                }
            @endphp

            <!-- DAILY PERFORMANCE SECTION -->
            <div class="bg-slate-900/80 border border-blue-900/60 rounded-2xl lg:rounded-3xl p-3 lg:p-5 shadow-2xl backdrop-blur-md overflow-hidden">
                <!-- Card Header -->
                <div class="bg-gradient-to-r from-blue-900 to-indigo-950 text-white font-bold text-center py-2 px-3 rounded-lg mb-3.5 tracking-wider text-xs lg:text-sm uppercase flex flex-col sm:flex-row justify-between items-center shadow-inner gap-1.5 sm:gap-0">
                    <div class="flex items-center space-x-2">
                        <i class="bi bi-graph-up-arrow text-blue-400 text-sm lg:text-base"></i>
                        <span>Daily Performance (Kumulatif Sales Tanggal 1 - 31)</span>
                    </div>
                    <div class="flex items-center space-x-1.5 text-[10px] lg:text-xs text-emerald-300 normal-case font-semibold">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Update Realtime Spreadsheet</span>
                    </div>
                </div>

                <!-- Combined Flex Container: Pinned Left Column (Always On Top) + Scrollable Right Chart Canvas -->
                <div class="flex flex-row rounded-xl border border-blue-800 bg-slate-950/40 overflow-hidden relative">
                    
                    <!-- Fixed Left Column: Dealer Legend List (Always on Top / Pinned on Left on Mobile & Desktop) -->
                    <div class="w-40 sm:w-56 shrink-0 bg-blue-950/95 border-r border-blue-800/80 p-2.5 sm:p-4 flex flex-col justify-center space-y-2.5 z-20 shadow-xl select-none">
                        <div class="text-xs sm:text-sm font-black uppercase text-slate-300 tracking-wider mb-1 border-b border-blue-800/80 pb-2 flex items-center justify-between">
                            <span class="flex items-center space-x-1.5">
                                <i class="bi bi-buildings-fill text-blue-400 text-xs sm:text-sm"></i>
                                <span>CABANG</span>
                            </span>
                            <span class="text-yellow-400 font-extrabold">STU</span>
                        </div>
                        @foreach($cabangs as $index => $cabang)
                            @php
                                $color = $colorPalette[$cabang->nama] ?? ($fallbackColors[$index % count($fallbackColors)]);
                                $lastStu = (int)$cabang->acv;
                            @endphp
                            <div id="legend-item-{{ $index }}" 
                                 class="flex items-center justify-between space-x-2 text-xs sm:text-sm font-bold text-white cursor-pointer hover:bg-blue-900/60 transition duration-150 py-1.5 px-2 rounded-lg border border-transparent hover:border-blue-700/50"
                                 onclick="toggleDataset({{ $index }})"
                                 title="Klik untuk tampilkan/sembunyikan {{ $cabang->nama }}">
                                <div class="flex items-center space-x-2 min-w-0">
                                    <span class="w-3 h-3 rounded-full shrink-0 shadow-md" style="background-color: {{ $color }}; box-shadow: 0 0 8px {{ $color }}b0;"></span>
                                    <span class="truncate text-slate-100 font-bold text-xs sm:text-sm tracking-wide">{{ $cabang->nama }}</span>
                                </div>
                                <span class="min-w-[42px] sm:min-w-[50px] inline-flex items-center justify-end px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-md text-xs sm:text-sm font-black bg-slate-900/90 border border-slate-700 text-yellow-400 shrink-0 shadow-inner tracking-tight text-right">{{ $lastStu }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Scrollable Right Column: Chart Canvas -->
                    <div class="flex-1 overflow-x-auto p-2 lg:p-4 min-w-0">
                        <div class="min-w-[600px] sm:min-w-full h-[320px] md:h-[400px] relative">
                            <canvas id="dailyPerformanceChart"></canvas>
                        </div>
                    </div>

                </div>

            </div>

            <script>
                window.dailyChart = null;

                window.toggleDataset = function(index) {
                    if (!window.dailyChart) return;
                    const isVisible = window.dailyChart.isDatasetVisible(index);
                    window.dailyChart.setDatasetVisibility(index, !isVisible);
                    window.dailyChart.update();

                    const el = document.getElementById('legend-item-' + index);
                    if (el) {
                        if (isVisible) {
                            el.classList.add('opacity-40', 'line-through');
                        } else {
                            el.classList.remove('opacity-40', 'line-through');
                        }
                    }
                };

                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('dailyPerformanceChart');
                    if (!ctx) return;

                    const labels = Array.from({length: 31}, (_, i) => (i + 1).toString());
                    const datasets = @json($chartDatasets);

                    // Custom Plugin: Draw STU total label badge at the end tip of each line
                    const endLineLabelPlugin = {
                        id: 'endLineLabel',
                        afterDatasetsDraw(chart) {
                            const { ctx, chartArea } = chart;
                            chart.data.datasets.forEach((dataset, datasetIndex) => {
                                const meta = chart.getDatasetMeta(datasetIndex);
                                if (!meta.hidden && meta.data && meta.data.length > 0) {
                                    let lastPointIndex = -1;
                                    for (let i = meta.data.length - 1; i >= 0; i--) {
                                        if (dataset.data[i] !== null && dataset.data[i] !== undefined) {
                                            lastPointIndex = i;
                                            break;
                                        }
                                    }

                                    if (lastPointIndex !== -1 && meta.data[lastPointIndex]) {
                                        const point = meta.data[lastPointIndex];
                                        const val = dataset.data[lastPointIndex];
                                        const color = dataset.borderColor || '#38bdf8';

                                        ctx.save();
                                        ctx.font = 'bold 12.5px "Plus Jakarta Sans", sans-serif';
                                        
                                        const text = val + ' UNIT';
                                        const textMetrics = ctx.measureText(text);
                                        const textWidth = textMetrics.width;
                                        const paddingX = 6;
                                        const rectW = textWidth + (paddingX * 2);
                                        const rectH = 20;

                                        let posX = point.x + 8;
                                        let rectX = posX;
                                        let rectY = point.y - 10;

                                        // Overflow boundary protection
                                        if (posX + rectW > chartArea.right) {
                                            rectX = point.x - rectW - 8;
                                        }

                                        // Draw pill badge background
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

                                        // Draw STU text
                                        ctx.fillStyle = '#ffffff';
                                        ctx.textAlign = 'left';
                                        ctx.textBaseline = 'middle';
                                        ctx.fillText(text, rectX + paddingX, point.y);
                                        ctx.restore();
                                    }
                                }
                            });
                        }
                    };

                    window.dailyChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: datasets
                        },
                        plugins: [endLineLabelPlugin],
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            layout: {
                                padding: {
                                    right: 56
                                }
                            },
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                            plugins: {
                                legend: {
                                    display: false // Using interactive left column legend
                                },
                                tooltip: {
                                    backgroundColor: '#0f172a',
                                    borderColor: '#1e293b',
                                    borderWidth: 1,
                                    titleColor: '#38bdf8',
                                    bodyColor: '#f8fafc',
                                    titleFont: { family: "'Plus Jakarta Sans', sans-serif", size: 14, weight: 'bold' },
                                    bodyFont: { family: "'Plus Jakarta Sans', sans-serif", size: 13 },
                                    padding: 12,
                                    boxPadding: 6,
                                    usePointStyle: true,
                                    callbacks: {
                                        title: function(tooltipItems) {
                                            return 'Tanggal ' + tooltipItems[0].label;
                                        },
                                        label: function(context) {
                                            let label = context.dataset.label || '';
                                            if (label) label += ': ';
                                            if (context.parsed.y !== null) {
                                                label += context.parsed.y + ' Unit (Kumulatif)';
                                            }
                                            return label;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        color: 'rgba(51, 65, 85, 0.3)',
                                        drawBorder: false
                                    },
                                    ticks: {
                                        color: '#cbd5e1',
                                        maxRotation: 0,
                                        minRotation: 0,
                                        font: { family: "'Plus Jakarta Sans', sans-serif", size: 12, weight: '700', style: 'normal' }
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(51, 65, 85, 0.4)',
                                        drawBorder: false
                                    },
                                    ticks: {
                                        color: '#cbd5e1',
                                        stepSize: 5,
                                        font: { family: "'Plus Jakarta Sans', sans-serif", size: 12, weight: '700' }
                                    }
                                }
                            }
                        }
                    });
                });
            </script>

            <!-- LAPORAN HASIL ANALISA: STU BY POS SECTION (CARD VIEW PRIMARY) -->
            @php
                $posDataList = [
                    [
                        'dealer' => 'PEKANBARU',
                        'type' => '3S',
                        'target' => 90,
                        'total_stu' => 10,
                        'pos' => [
                            ['name' => 'DEALER', 'target' => 90, 'stu' => 8],
                            ['name' => 'RIAU 2', 'target' => 30, 'stu' => 2],
                            ['name' => 'GARUDA SAKTI', 'target' => 30, 'stu' => 0],
                        ]
                    ],
                    [
                        'dealer' => 'SEI PAGAR',
                        'type' => '3S',
                        'target' => 70,
                        'total_stu' => 12,
                        'pos' => [
                            ['name' => 'DEALER', 'target' => 70, 'stu' => 7],
                            ['name' => 'LIPAT KAIN', 'target' => 15, 'stu' => 1],
                            ['name' => 'BANGKINANG', 'target' => 15, 'stu' => 2],
                            ['name' => 'PETAPAHAN', 'target' => 15, 'stu' => 2],
                        ]
                    ],
                    [
                        'dealer' => 'AIR MOLEK',
                        'type' => '1S',
                        'target' => 106,
                        'total_stu' => 22,
                        'pos' => [
                            ['name' => 'DEALER', 'target' => 106, 'stu' => 7],
                            ['name' => 'BELILAS', 'target' => 30, 'stu' => 9],
                            ['name' => 'PERANAP', 'target' => 25, 'stu' => 1],
                            ['name' => 'SUNGAI LALA', 'target' => 17, 'stu' => 5],
                        ]
                    ],
                    [
                        'dealer' => 'SOREK',
                        'type' => '3S',
                        'target' => 174,
                        'total_stu' => 23,
                        'pos' => [
                            ['name' => 'DEALER', 'target' => 174, 'stu' => 8],
                            ['name' => 'PADANG LUAS', 'target' => 25, 'stu' => 3],
                            ['name' => 'KERINCI', 'target' => 50, 'stu' => 6],
                            ['name' => 'UKUI', 'target' => 25, 'stu' => 6],
                        ]
                    ],
                    [
                        'dealer' => 'KANDIS',
                        'type' => '3S',
                        'target' => 125,
                        'total_stu' => 15,
                        'pos' => [
                            ['name' => 'DEALER', 'target' => 125, 'stu' => 3],
                            ['name' => 'PERAWANG', 'target' => 30, 'stu' => 3],
                            ['name' => 'KERINCI KANAN', 'target' => 11, 'stu' => 0],
                            ['name' => 'SIAK', 'target' => 30, 'stu' => 9],
                            ['name' => 'SABAK AUH', 'target' => 11, 'stu' => 0],
                        ]
                    ],
                    [
                        'dealer' => 'MEDAN',
                        'type' => '3S',
                        'target' => 135,
                        'total_stu' => 24,
                        'pos' => [
                            ['name' => 'DEALER', 'target' => 135, 'stu' => 21],
                            ['name' => 'PANCUR BATU', 'target' => 20, 'stu' => 3],
                        ]
                    ],
                ];

                $processedPosData = [];
                foreach ($posDataList as $item) {
                    $dbC = $cabangs->first(fn($c) => strtoupper(trim($c->nama)) === $item['dealer']);
                    $dealerTarget = $dbC && $dbC->target_reguler > 0 ? (int)$dbC->target_reguler : $item['target'];
                    $dealerTotalStu = $dbC ? (int)$dbC->acv : $item['total_stu'];

                    $posItems = [];
                    foreach ($item['pos'] as $p) {
                        $pStu = $p['stu'];
                        $pctVal = $dealerTotalStu > 0 ? round(($pStu / $dealerTotalStu) * 100) : 0;
                        $posItems[] = [
                            'name' => $p['name'],
                            'target' => $p['target'],
                            'stu' => $pStu,
                            'growth' => $pctVal . '%',
                            'pct_raw' => $pctVal
                        ];
                    }

                    $processedPosData[] = [
                        'dealer' => $item['dealer'],
                        'type' => $item['type'],
                        'target' => $dealerTarget,
                        'total_stu' => $dealerTotalStu,
                        'pos' => $posItems
                    ];
                }
            @endphp

            <div class="mt-8 mb-8 bg-slate-900/90 border border-slate-800 rounded-2xl lg:rounded-3xl p-4 lg:p-6 shadow-2xl backdrop-blur-xl">
                
                <!-- Section Header Banner -->
                <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-slate-800 pb-4 mb-6 gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="bg-gradient-to-br from-rose-600 to-amber-700 p-2.5 rounded-2xl text-yellow-300 border border-rose-500/40 shadow-lg shrink-0">
                            <i class="bi bi-diagram-3-fill text-xl lg:text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-lg lg:text-xl font-black text-white uppercase tracking-wider">LAPORAN HASIL ANALISA (STU BY POS DEALER)</h2>
                            <p class="text-xs text-rose-300 font-extrabold tracking-wide mt-0.5">ANALISA CAPAIAN POS PENJUALAN TIAP CABANG DEALER &bull; {{ \Carbon\Carbon::now()->format('d-M-y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- 1. CARD VIEW CONTAINER (DEFAULT PRIMARY VIEW) -->
                <div id="posCardsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($processedPosData as $d)
                        <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-rose-950/30 border border-rose-900/60 rounded-2xl lg:rounded-3xl p-5 lg:p-6 shadow-2xl relative overflow-hidden flex flex-col justify-between hover:border-rose-500/60 transition group">
                            <div class="absolute -right-12 -top-12 w-36 h-36 bg-rose-500/10 rounded-full blur-2xl group-hover:bg-rose-500/20 transition pointer-events-none"></div>

                            <div>
                                <!-- Card Dealer Header -->
                                <div class="flex items-center justify-between border-b border-slate-800 pb-4 mb-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-rose-600 to-amber-600 flex items-center justify-center text-white shadow-lg border border-rose-400/30 shrink-0">
                                            <i class="bi bi-building-fill text-lg"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-lg lg:text-xl font-black text-white uppercase tracking-wide">
                                                {{ $d['dealer'] }}
                                            </h3>
                                            <span class="text-xs lg:text-sm font-extrabold text-rose-400 uppercase tracking-wider">DEALER ({{ $d['type'] }})</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Summary STU Badge -->
                                    <div class="text-right bg-slate-950/90 border border-slate-800 px-3.5 py-2 rounded-2xl shadow-inner">
                                        <div class="text-[10px] lg:text-[11px] text-slate-400 uppercase font-black tracking-wider">TOTAL STU</div>
                                        <div class="text-lg lg:text-xl font-black text-emerald-400 leading-tight">
                                            {{ $d['total_stu'] }} <span class="text-xs lg:text-sm text-slate-300 font-bold">/ <span class="pos-dealer-target-val text-yellow-400 font-black" data-dealer="{{ $d['dealer'] }}">{{ $d['target'] }}</span> Tgt</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sub POS List per Dealer -->
                                <div class="space-y-3.5">
                                    @foreach($d['pos'] as $p)
                                        <div class="p-3.5 lg:p-4 rounded-2xl border transition {{ $p['name'] === 'DEALER' ? 'bg-rose-950/40 border-rose-500/40 shadow-lg' : 'bg-slate-950/80 border-slate-800/90 hover:border-slate-700' }}">
                                            
                                            <!-- Row 1: POS Name & Growth Badge -->
                                            <div class="flex items-center justify-between pb-2 mb-2.5 border-b border-slate-800/80">
                                                <div class="flex items-center space-x-2.5">
                                                    @if($p['name'] === 'DEALER')
                                                        <div class="w-7 h-7 rounded-lg bg-rose-500/20 border border-rose-500/40 flex items-center justify-center shrink-0">
                                                            <i class="bi bi-building-fill text-rose-400 text-xs"></i>
                                                        </div>
                                                        <div>
                                                            <h4 class="text-sm lg:text-base font-black text-rose-200 uppercase tracking-wide">{{ $p['name'] }}</h4>
                                                            <span class="text-[10px] lg:text-[11px] font-bold text-rose-400/80">Pos Utama Dealer</span>
                                                        </div>
                                                    @else
                                                        <div class="w-7 h-7 rounded-lg bg-amber-500/20 border border-amber-500/40 flex items-center justify-center shrink-0">
                                                            <i class="bi bi-geo-alt-fill text-amber-400 text-xs"></i>
                                                        </div>
                                                        <div>
                                                            <h4 class="text-sm lg:text-base font-black text-slate-100 tracking-wide">{{ $p['name'] }}</h4>
                                                            <span class="text-[10px] lg:text-[11px] font-medium text-slate-400">Sub-Pos Penjualan</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <span class="pos-growth-badge px-3 py-1 rounded-xl text-xs lg:text-sm font-black shadow-sm {{ $p['pct_raw'] >= 50 ? 'bg-emerald-500/25 text-emerald-300 border border-emerald-500/40' : ($p['pct_raw'] > 0 ? 'bg-amber-500/25 text-amber-300 border border-amber-500/40' : 'bg-slate-800 text-slate-400 border border-slate-700') }}">
                                                        {{ $p['growth'] }}
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Row 2: Target & STU Mini Cards -->
                                            <div class="grid grid-cols-2 gap-2 mb-2.5">
                                                <div class="group/input bg-slate-950/80 border border-slate-700/60 hover:border-rose-500/60 focus-within:border-rose-400 focus-within:ring-2 focus-within:ring-rose-500/30 px-2.5 py-1.5 rounded-xl flex items-center justify-between shadow-inner transition-all duration-200 min-w-0 space-x-1">
                                                    <div class="flex items-center space-x-1 shrink-0">
                                                        <i class="bi bi-pencil-fill text-[9px] text-rose-400/60 group-hover/input:text-rose-400 transition-colors shrink-0"></i>
                                                        <span class="text-[10px] sm:text-[11px] lg:text-xs font-extrabold text-slate-300 uppercase tracking-tight whitespace-nowrap shrink-0">TARGET</span>
                                                    </div>
                                                    <div class="flex-1 min-w-0 text-right">
                                                        <input 
                                                            type="number" 
                                                            value="{{ $p['target'] }}" 
                                                            min="0"
                                                            class="pos-target-input w-full text-right bg-transparent text-white font-extrabold text-xs lg:text-sm focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                                            data-dealer="{{ $d['dealer'] }}"
                                                            data-pos="{{ $p['name'] }}"
                                                            data-stu="{{ $p['stu'] }}"
                                                            onfocus="this.select()"
                                                            title="Klik untuk mengubah nilai target"
                                                        >
                                                    </div>
                                                </div>
                                                <div class="bg-emerald-950/30 border border-emerald-500/30 px-2.5 py-1.5 rounded-xl flex items-center justify-between min-w-0 space-x-1">
                                                    <span class="text-[10px] sm:text-[11px] lg:text-xs font-extrabold text-emerald-400 uppercase tracking-wider whitespace-nowrap shrink-0">STU</span>
                                                    <span class="text-xs lg:text-sm font-black text-emerald-400 truncate text-right flex-1 min-w-0">{{ $p['stu'] }}</span>
                                                </div>
                                            </div>

                                            <!-- Progress Bar Meter -->
                                            <div class="w-full bg-slate-900 rounded-full h-2.5 overflow-hidden border border-slate-800">
                                                <div class="pos-progress-bar bg-gradient-to-r {{ $p['name'] === 'DEALER' ? 'from-rose-500 via-amber-400 to-emerald-400' : 'from-blue-500 to-emerald-400' }} h-2.5 rounded-full transition-all duration-500" style="width: {{ min(100, max(5, $p['pct_raw'])) }}%;"></div>
                                            </div>

                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>

            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const posInputs = document.querySelectorAll('.pos-target-input');

                posInputs.forEach(input => {
                    const dealer = input.getAttribute('data-dealer');
                    const pos = input.getAttribute('data-pos');
                    const storageKey = `pos_target_${dealer}_${pos}`;

                    // Load saved target from localStorage if exists
                    const savedVal = localStorage.getItem(storageKey);
                    if (savedVal !== null && !isNaN(savedVal) && savedVal.trim() !== '') {
                        input.value = savedVal;
                    }

                    const updatePosCard = (inputEl) => {
                        const currentDealer = inputEl.getAttribute('data-dealer');
                        const currentPos = inputEl.getAttribute('data-pos');
                        const stu = parseFloat(inputEl.getAttribute('data-stu')) || 0;
                        let target = parseFloat(inputEl.value);

                        if (isNaN(target) || target < 0) target = 0;

                        // Save to localStorage
                        localStorage.setItem(`pos_target_${currentDealer}_${currentPos}`, target);

                        const posCard = inputEl.closest('.p-3\\.5, .p-4');
                        if (posCard) {
                            // Calculate percentage
                            const pct = target > 0 ? Math.round((stu / target) * 100) : 0;
                            
                            // Update growth badge
                            const growthBadge = posCard.querySelector('.pos-growth-badge');
                            if (growthBadge) {
                                growthBadge.textContent = pct + '%';
                                if (pct >= 50) {
                                    growthBadge.className = 'pos-growth-badge px-3 py-1 rounded-xl text-xs lg:text-sm font-black shadow-sm bg-emerald-500/25 text-emerald-300 border border-emerald-500/40';
                                } else if (pct > 0) {
                                    growthBadge.className = 'pos-growth-badge px-3 py-1 rounded-xl text-xs lg:text-sm font-black shadow-sm bg-amber-500/25 text-amber-300 border border-amber-500/40';
                                } else {
                                    growthBadge.className = 'pos-growth-badge px-3 py-1 rounded-xl text-xs lg:text-sm font-black shadow-sm bg-slate-800 text-slate-400 border border-slate-700';
                                }
                            }

                            // Update progress bar
                            const progressBar = posCard.querySelector('.pos-progress-bar');
                            if (progressBar) {
                                const barWidth = Math.min(100, Math.max(5, pct));
                                progressBar.style.width = barWidth + '%';
                            }
                        }

                        // Recalculate Total Dealer Target
                        const dealerCard = inputEl.closest('.bg-gradient-to-br');
                        if (dealerCard) {
                            let newDealerTarget = 0;
                            dealerCard.querySelectorAll('.pos-target-input').forEach(inp => {
                                const val = parseFloat(inp.value) || 0;
                                newDealerTarget += val;
                            });
                            const dealerTargetBadge = dealerCard.querySelector('.pos-dealer-target-val');
                            if (dealerTargetBadge) {
                                dealerTargetBadge.textContent = newDealerTarget;
                            }
                        }
                    };

                    // Initial trigger on load
                    updatePosCard(input);

                    // Listen for input & change events
                    input.addEventListener('input', function() {
                        updatePosCard(this);
                    });
                    input.addEventListener('change', function() {
                        updatePosCard(this);
                    });
                });
            });
            </script>

            <!-- ACHIEVEMENT BY LEASING SECTION (CARD VIEW & TABLE VIEW TOGGLE) -->
            @php
                $leasingBranches = [];
                $totalTargetTotal = 0;
                $totalTargetCash = 0;
                $totalTargetKredit = 0;
                $totalFincoyTarget = ['ADIRA' => 0, 'BAF' => 0, 'IMFI' => 0, 'MEGA' => 0, 'SOF' => 0];
                
                $totalAchTotal = 0;
                $totalAchCash = 0;
                $totalAchKredit = 0;
                $totalFincoyAch = ['ADIRA' => 0, 'BAF' => 0, 'IMFI' => 0, 'MEGA' => 0, 'SOF' => 0];

                $branchConfigMap = [
                    'PEKANBARU' => [
                        'target_total' => 90, 'target_cash' => 40, 'target_kredit' => 50,
                        'fincoy_target' => ['ADIRA' => 12, 'BAF' => 12, 'IMFI' => 1, 'MEGA' => 0, 'SOF' => 22]
                    ],
                    'SEI PAGAR' => [
                        'target_total' => 70, 'target_cash' => 18, 'target_kredit' => 53,
                        'fincoy_target' => ['ADIRA' => 11, 'BAF' => 8, 'IMFI' => 16, 'MEGA' => 3, 'SOF' => 16]
                    ],
                    'SOREK' => [
                        'target_total' => 174, 'target_cash' => 35, 'target_kredit' => 139,
                        'fincoy_target' => ['ADIRA' => 28, 'BAF' => 14, 'IMFI' => 35, 'MEGA' => 7, 'SOF' => 56]
                    ],
                    'KANDIS' => [
                        'target_total' => 125, 'target_cash' => 44, 'target_kredit' => 81,
                        'fincoy_target' => ['ADIRA' => 63, 'BAF' => 0, 'IMFI' => 6, 'MEGA' => 13, 'SOF' => 44]
                    ],
                    'MEDAN' => [
                        'target_total' => 135, 'target_cash' => 61, 'target_kredit' => 74,
                        'fincoy_target' => ['ADIRA' => 27, 'BAF' => 41, 'IMFI' => 14, 'MEGA' => 14, 'SOF' => 34]
                    ],
                    'AIR MOLEK' => [
                        'target_total' => 106, 'target_cash' => 21, 'target_kredit' => 85,
                        'fincoy_target' => ['ADIRA' => 27, 'BAF' => 11, 'IMFI' => 21, 'MEGA' => 5, 'SOF' => 42]
                    ],
                ];

                foreach ($cabangs as $idx => $cabang) {
                    $namaUpper = strtoupper(trim($cabang->nama));
                    $cfg = $branchConfigMap[$namaUpper] ?? [
                        'target_total' => $cabang->target_reguler > 0 ? (int)$cabang->target_reguler : (int)$cabang->target_tantangan,
                        'target_cash' => (int)round(($cabang->target_reguler ?: 100) * 0.3),
                        'target_kredit' => (int)round(($cabang->target_reguler ?: 100) * 0.7),
                        'fincoy_target' => ['ADIRA' => (int)round(($cabang->target_reguler ?: 100) * 0.2), 'BAF' => (int)round(($cabang->target_reguler ?: 100) * 0.15), 'IMFI' => (int)round(($cabang->target_reguler ?: 100) * 0.15), 'MEGA' => (int)round(($cabang->target_reguler ?: 100) * 0.05), 'SOF' => (int)round(($cabang->target_reguler ?: 100) * 0.15)]
                    ];

                    $targetTotal = $cabang->target_reguler > 0 ? (int)$cabang->target_reguler : $cfg['target_total'];
                    $targetCash = $cfg['target_cash'];
                    $targetKredit = $cfg['target_kredit'];
                    $fincoyTarget = $cfg['fincoy_target'];

                    // SOURCED DYNAMICALLY FROM REAL SPREADSHEET DATA PER RESPECTIVE CABANG
                    $achTotal = (int)$cabang->acv;
                    
                    $lData = $cabang->leasing_breakdown ?? ($cabang->stu_breakdown['leasing'] ?? null);
                    if ($lData && (isset($lData['cash']) || isset($lData['fincoy']))) {
                        $achCash = (int)($lData['cash'] ?? 0);
                        $achKredit = (int)($lData['kredit'] ?? max(0, $achTotal - $achCash));
                        $fincoyAch = [
                            'ADIRA' => (int)($lData['fincoy']['ADIRA'] ?? 0),
                            'BAF'   => (int)($lData['fincoy']['BAF'] ?? 0),
                            'IMFI'  => (int)($lData['fincoy']['IMFI'] ?? 0),
                            'MEGA'  => (int)($lData['fincoy']['MEGA'] ?? 0),
                            'SOF'   => (int)($lData['fincoy']['SOF'] ?? 0),
                        ];
                    } else {
                        // Proportionate ACH derived directly from real branch spreadsheet ACV
                        $pctCashRatio = $targetTotal > 0 ? ($targetCash / $targetTotal) : 0.35;
                        $achCash = (int)round($achTotal * $pctCashRatio);
                        $achKredit = max(0, $achTotal - $achCash);

                        $fincoyAch = [];
                        $kTargetSum = array_sum($fincoyTarget);
                        foreach (['ADIRA', 'BAF', 'IMFI', 'MEGA', 'SOF'] as $fk) {
                            $fRatio = $kTargetSum > 0 ? (($fincoyTarget[$fk] ?? 0) / $kTargetSum) : 0.2;
                            $fincoyAch[$fk] = (int)round($achKredit * $fRatio);
                        }
                        $sumF = array_sum($fincoyAch);
                        if ($sumF !== $achKredit && isset($fincoyAch['SOF'])) {
                            $fincoyAch['SOF'] += ($achKredit - $sumF);
                        }
                    }

                    $pctTarget = [
                        'target' => '100%',
                        'cash' => $targetTotal > 0 ? round(($targetCash / $targetTotal) * 100) . '%' : '0%',
                        'kredit' => $targetTotal > 0 ? round(($targetKredit / $targetTotal) * 100) . '%' : '0%',
                    ];
                    foreach (['ADIRA', 'BAF', 'IMFI', 'MEGA', 'SOF'] as $fk) {
                        $pctTarget[$fk] = $targetKredit > 0 ? round((($fincoyTarget[$fk] ?? 0) / $targetKredit) * 100) . '%' : '0%';
                    }

                    $pctAch = [
                        'target' => $targetTotal > 0 ? round(($achTotal / $targetTotal) * 100) . '%' : '0%',
                        'cash'   => $achTotal > 0 ? round(($achCash / $achTotal) * 100) . '%' : '0%',
                        'kredit' => $achTotal > 0 ? round(($achKredit / $achTotal) * 100) . '%' : '0%',
                    ];
                    foreach (['ADIRA', 'BAF', 'IMFI', 'MEGA', 'SOF'] as $fk) {
                        $pctAch[$fk] = $achKredit > 0 ? round((($fincoyAch[$fk] ?? 0) / $achKredit) * 100) . '%' : '0%';
                    }

                    $leasingBranches[] = [
                        'no' => $idx + 1,
                        'cabang' => strtoupper($cabang->nama),
                        'pct_target' => $pctTarget,
                        'target_total' => $targetTotal,
                        'target_cash' => $targetCash,
                        'target_kredit' => $targetKredit,
                        'fincoy_target' => $fincoyTarget,
                        'ach_total' => $achTotal,
                        'ach_cash' => $achCash,
                        'ach_kredit' => $achKredit,
                        'fincoy_ach' => $fincoyAch,
                        'pct_ach' => $pctAch,
                    ];

                    $totalTargetTotal += $targetTotal;
                    $totalTargetCash += $targetCash;
                    $totalTargetKredit += $targetKredit;
                    foreach (['ADIRA', 'BAF', 'IMFI', 'MEGA', 'SOF'] as $fk) {
                        $totalFincoyTarget[$fk] += ($fincoyTarget[$fk] ?? 0);
                        $totalFincoyAch[$fk] += ($fincoyAch[$fk] ?? 0);
                    }
                    $totalAchTotal += $achTotal;
                    $totalAchCash += $achCash;
                    $totalAchKredit += $achKredit;
                }

                $totalPctTarget = [
                    'target' => '100%',
                    'cash' => $totalTargetTotal > 0 ? round(($totalTargetCash / $totalTargetTotal) * 100) . '%' : '0%',
                    'kredit' => $totalTargetTotal > 0 ? round(($totalTargetKredit / $totalTargetTotal) * 100) . '%' : '0%',
                ];
                foreach (['ADIRA', 'BAF', 'IMFI', 'MEGA', 'SOF'] as $fk) {
                    $totalPctTarget[$fk] = $totalTargetKredit > 0 ? round(($totalFincoyTarget[$fk] / $totalTargetKredit) * 100) . '%' : '0%';
                }

                $totalPctAch = [
                    'target' => $totalTargetTotal > 0 ? round(($totalAchTotal / $totalTargetTotal) * 100) . '%' : '0%',
                    'cash'   => $totalAchTotal > 0 ? round(($totalAchCash / $totalAchTotal) * 100) . '%' : '0%',
                    'kredit' => $totalAchTotal > 0 ? round(($totalAchKredit / $totalAchTotal) * 100) . '%' : '0%',
                ];
                foreach (['ADIRA', 'BAF', 'IMFI', 'MEGA', 'SOF'] as $fk) {
                    $totalPctAch[$fk] = $totalAchKredit > 0 ? round(($totalFincoyAch[$fk] / $totalAchKredit) * 100) . '%' : '0%';
                }

                $leasingTotal = [
                    'no' => count($leasingBranches) + 1,
                    'cabang' => 'TOTAL REKAPITULASI',
                    'pct_target' => $totalPctTarget,
                    'target_total' => $totalTargetTotal,
                    'target_cash' => $totalTargetCash,
                    'target_kredit' => $totalTargetKredit,
                    'fincoy_target' => $totalFincoyTarget,
                    'ach_total' => $totalAchTotal,
                    'ach_cash' => $totalAchCash,
                    'ach_kredit' => $totalAchKredit,
                    'fincoy_ach' => $totalFincoyAch,
                    'pct_ach' => $totalPctAch,
                ];

                $fincoyKeys = ['ADIRA', 'BAF', 'IMFI', 'MEGA', 'SOF'];
            @endphp

            <div class="mt-8 mb-8 bg-slate-900/90 border border-blue-900/70 rounded-2xl lg:rounded-3xl p-4 lg:p-6 shadow-2xl backdrop-blur-xl">
                
                <!-- Card Section Header Banner -->
                <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-blue-900/80 pb-4 mb-6 gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="bg-gradient-to-br from-blue-600 to-indigo-800 p-2.5 rounded-2xl text-yellow-400 border border-blue-500/40 shadow-lg shrink-0">
                            <i class="bi bi-bank2 text-xl lg:text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-lg lg:text-xl font-black text-white uppercase tracking-wider">ACHIEVEMENT BY LEASING</h2>
                            <p class="text-xs text-yellow-400 font-extrabold tracking-wide mt-0.5">PT ASPACINDO KEDATON MOTOR &bull; {{ \Carbon\Carbon::now()->format('d-M-y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- 1. CARD VIEW CONTAINER -->
                <div id="leasingCardsContainer" class="space-y-6">
                    
                    <!-- Grand Total Banner Card (Row 7 Summary) -->
                    <div class="bg-gradient-to-r from-blue-950 via-slate-900 to-indigo-950 border-2 border-yellow-500/60 rounded-3xl p-5 shadow-2xl relative overflow-hidden">
                        <div class="absolute -right-16 -bottom-16 w-56 h-56 bg-yellow-500/10 rounded-full blur-3xl pointer-events-none"></div>
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between border-b border-slate-800/80 pb-4 mb-4 gap-3">
                            <div class="flex items-center space-x-3">
                                <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse shrink-0"></span>
                                <div>
                                    <h3 class="text-base sm:text-lg font-black text-white uppercase tracking-wider">TOTAL REKAPITULASI SELURUH DEALER</h3>
                                    <p class="text-xs text-slate-400 font-medium">Capaian Penjualan Leasing & Cash Gabungan 6 Cabang</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 shrink-0">
                                <span class="text-xs text-slate-400 font-bold uppercase">Pencapaian:</span>
                                <span class="bg-amber-500/20 text-yellow-400 border border-yellow-500/40 text-sm font-black px-3 py-1 rounded-xl shadow">
                                    %ACH: {{ $leasingTotal['pct_ach']['target'] }}
                                </span>
                            </div>
                        </div>

                        <!-- 3 Summary Box Grid for Total -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 mb-4">
                            <!-- Box 1: TARGET TOTAL -->
                            <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-3.5 flex flex-col justify-between">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">TARGET VS ACH TOTAL</span>
                                <div class="flex items-baseline justify-between mt-2">
                                    <span class="text-2xl font-black text-yellow-400">{{ $leasingTotal['ach_total'] }} <span class="text-xs text-slate-400 font-normal">/ {{ $leasingTotal['target_total'] }} Unit</span></span>
                                    <span class="text-xs font-black text-emerald-400 bg-emerald-500/20 px-2 py-0.5 rounded-lg border border-emerald-500/30">{{ $leasingTotal['pct_ach']['target'] }}</span>
                                </div>
                            </div>
                            <!-- Box 2: CASH -->
                            <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-3.5 flex flex-col justify-between">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">PENJUALAN CASH</span>
                                <div class="flex items-baseline justify-between mt-2">
                                    <span class="text-xl font-black text-white">{{ $leasingTotal['ach_cash'] }} <span class="text-xs text-slate-400 font-normal">/ {{ $leasingTotal['target_cash'] }} Unit ({{ $leasingTotal['pct_target']['cash'] }})</span></span>
                                    <span class="text-xs font-black text-blue-400 bg-blue-500/20 px-2 py-0.5 rounded-lg border border-blue-500/30">ACH {{ $leasingTotal['pct_ach']['cash'] }}</span>
                                </div>
                            </div>
                            <!-- Box 3: KREDIT -->
                            <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-3.5 flex flex-col justify-between">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">PENJUALAN KREDIT</span>
                                <div class="flex items-baseline justify-between mt-2">
                                    <span class="text-xl font-black text-white">{{ $leasingTotal['ach_kredit'] }} <span class="text-xs text-slate-400 font-normal">/ {{ $leasingTotal['target_kredit'] }} Unit ({{ $leasingTotal['pct_target']['kredit'] }})</span></span>
                                    <span class="text-xs font-black text-purple-400 bg-purple-500/20 px-2 py-0.5 rounded-lg border border-purple-500/30">ACH {{ $leasingTotal['pct_ach']['kredit'] }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Fincoy Breakdown Row for Total -->
                        <div>
                            <span class="text-[11px] text-slate-400 font-black uppercase tracking-wider block mb-2">RINCIAN FINCOY GABUNGAN:</span>
                            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                                @foreach($fincoyKeys as $fk)
                                    <div class="bg-slate-950/90 border border-slate-800/90 rounded-xl p-2.5 flex flex-col justify-between">
                                        <div class="flex items-center justify-between text-xs font-extrabold border-b border-slate-800/80 pb-1 mb-1">
                                            <span class="text-yellow-400">{{ $fk }}</span>
                                            <span class="text-[10px] text-slate-400">{{ $leasingTotal['pct_target'][$fk] }} Target</span>
                                        </div>
                                        <div class="flex items-baseline justify-between">
                                            <span class="text-sm font-black text-white">{{ $leasingTotal['fincoy_ach'][$fk] }} <span class="text-[10px] text-slate-400 font-normal">/ {{ $leasingTotal['fincoy_target'][$fk] }}</span></span>
                                            <span class="text-[10.5px] font-extrabold text-teal-300">{{ $leasingTotal['pct_ach'][$fk] }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- 6 Dealer Cards Grid (3 Columns on Desktop) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($leasingBranches as $b)
                            @php
                                $achPctNum = (int)filter_var($b['pct_ach']['target'], FILTER_SANITIZE_NUMBER_INT);
                            @endphp
                            <div class="bg-[#0b132b]/95 border border-blue-900/80 rounded-3xl p-4 sm:p-5 shadow-2xl backdrop-blur-md relative overflow-hidden flex flex-col justify-between hover:border-yellow-400/60 transition duration-300">
                                <div>
                                    <!-- Dealer Card Header -->
                                    <div class="flex items-center justify-between border-b border-blue-950 pb-3 mb-3.5">
                                        <div class="flex items-center space-x-2.5">
                                            <span class="w-6 h-6 rounded-full bg-blue-900/80 border border-blue-700 text-yellow-400 font-black text-xs flex items-center justify-center shrink-0 shadow">
                                                {{ $b['no'] }}
                                            </span>
                                            <h3 class="text-base font-black text-white uppercase tracking-wide truncate">{{ $b['cabang'] }}</h3>
                                        </div>
                                        <span class="bg-slate-950/90 border border-yellow-500/40 text-yellow-400 text-xs font-black px-2.5 py-1 rounded-xl shadow">
                                            %ACH: {{ $b['pct_ach']['target'] }}
                                        </span>
                                    </div>

                                    <!-- STU Target vs ACH Progress -->
                                    <div class="mb-4">
                                        <div class="flex items-center justify-between text-xs font-extrabold mb-1">
                                            <span class="text-slate-400 uppercase tracking-wider text-[10px]">TOTAL ACH VS TARGET</span>
                                            <span class="text-white font-black"><strong class="text-yellow-400 text-sm">{{ $b['ach_total'] }}</strong> / {{ $b['target_total'] }} Unit</span>
                                        </div>
                                        <div class="w-full h-2 bg-slate-950 rounded-full overflow-hidden border border-slate-800">
                                            <div class="bg-gradient-to-r from-blue-500 via-indigo-500 to-yellow-400 h-full rounded-full transition-all duration-500" style="width: {{ min(100, max(5, $achPctNum)) }}%"></div>
                                        </div>
                                    </div>

                                    <!-- Cash vs Kredit 2-Column Row -->
                                    <div class="grid grid-cols-2 gap-2.5 mb-4">
                                        <!-- Cash -->
                                        <div class="bg-slate-950/70 border border-slate-800/80 rounded-xl p-2.5">
                                            <div class="flex items-center justify-between text-[10px] text-slate-400 font-bold uppercase mb-1">
                                                <span>CASH ({{ $b['pct_target']['cash'] }})</span>
                                                <span class="text-blue-400 font-black">{{ $b['pct_ach']['cash'] }}</span>
                                            </div>
                                            <p class="text-white font-black text-sm"><strong class="text-blue-400">{{ $b['ach_cash'] }}</strong> <span class="text-xs text-slate-400 font-normal">/ {{ $b['target_cash'] }} Unit</span></p>
                                        </div>
                                        <!-- Kredit -->
                                        <div class="bg-slate-950/70 border border-slate-800/80 rounded-xl p-2.5">
                                            <div class="flex items-center justify-between text-[10px] text-slate-400 font-bold uppercase mb-1">
                                                <span>KREDIT ({{ $b['pct_target']['kredit'] }})</span>
                                                <span class="text-purple-400 font-black">{{ $b['pct_ach']['kredit'] }}</span>
                                            </div>
                                            <p class="text-white font-black text-sm"><strong class="text-purple-400">{{ $b['ach_kredit'] }}</strong> <span class="text-xs text-slate-400 font-normal">/ {{ $b['target_kredit'] }} Unit</span></p>
                                        </div>
                                    </div>

                                    <!-- Fincoy Breakdown List -->
                                    <div>
                                        <span class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider block mb-2">FINCOY LEASING BREAKDOWN:</span>
                                        <div class="space-y-1.5">
                                            @foreach($fincoyKeys as $fk)
                                                @php
                                                    $fTarget = $b['fincoy_target'][$fk];
                                                    $fAch = $b['fincoy_ach'][$fk];
                                                    $fPctTarget = $b['pct_target'][$fk];
                                                    $fPctAch = $b['pct_ach'][$fk];
                                                @endphp
                                                <div class="flex items-center justify-between text-xs py-1 px-2.5 rounded-lg bg-slate-950/80 border border-slate-800/80">
                                                    <div class="flex items-center space-x-1.5 min-w-0">
                                                        <span class="w-1.5 h-1.5 rounded-full {{ $fAch > 0 ? 'bg-teal-400' : 'bg-slate-600' }} shrink-0"></span>
                                                        <span class="text-white font-extrabold uppercase text-[11px]">{{ $fk }}</span>
                                                        <span class="text-[10px] text-slate-400 font-medium">({{ $fPctTarget }})</span>
                                                    </div>
                                                    <div class="flex items-center space-x-2 shrink-0">
                                                        <span class="text-white font-black text-[11px]">
                                                            <strong class="{{ $fAch > 0 ? 'text-teal-300' : 'text-slate-400' }}">{{ $fAch }}</strong> / {{ $fTarget }} Unit
                                                        </span>
                                                        <span class="text-[10px] font-black px-1.5 py-0.2 rounded {{ $fAch > 0 ? 'bg-teal-500/20 text-teal-300 border border-teal-500/30' : 'bg-slate-900 text-slate-500' }}">
                                                            {{ $fPctAch }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>

            </div>

    <!-- Footer Banner -->
    <footer class="bg-gradient-to-r from-blue-900 via-blue-950 to-blue-900 rounded-xl lg:rounded-2xl p-3 lg:p-4 border border-blue-800 shadow-2xl flex flex-col sm:flex-row sm:items-center sm:justify-between mt-4 lg:mt-6 text-center gap-2">
        <div class="flex items-center justify-center space-x-2 text-yellow-400 font-extrabold italic tracking-wider text-xs lg:text-sm">
            <i class="bi bi-award-fill text-sm"></i>
            <span>TERUS JAGA MOMENTUM & CAPAI TARGET!</span>
        </div>
        
        <div class="flex items-center justify-center">
            <span class="text-white font-extrabold text-[9px] lg:text-[11px] tracking-wider uppercase">PT ASPACINDO KEDATON MOTOR</span>
        </div>
    </footer>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.update-ytd-input');
    
    inputs.forEach(input => {
        input.addEventListener('change', function() {
            updateYtd(this);
        });
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.blur();
            }
        });
    });

    function updateYtd(inputEl) {
        const id = inputEl.getAttribute('data-id');
        const val = parseInt(inputEl.value) || 0;
        const targetReguler = parseInt(inputEl.getAttribute('data-target-reguler')) || 0;
        
        inputEl.classList.add('opacity-50');

        fetch(`/cabang/${id}/update-ytd`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ act_ytd_jan_2026: val })
        })
        .then(response => response.json())
        .then(data => {
            inputEl.classList.remove('opacity-50');
            if (data.success) {
                const row = inputEl.closest('tr');
                const diffCell = row.querySelector('.ytd-diff-cell');
                const percentCell = row.querySelector('.ytd-percent-cell');
                const targetPerbulanCell = row.querySelector('.ytd-target-perbulan-cell');
                
                const diff = val - targetReguler;
                const pct = targetReguler > 0 ? (val / targetReguler) * 100 : 0;
                const roundedPct = Math.round(pct);

                if (diffCell) {
                    diffCell.textContent = (diff > 0 ? '+' : '') + diff;
                    diffCell.className = `ytd-diff-cell py-2 px-2 lg:py-3.5 lg:px-4 border border-blue-900/30 bg-teal-500/5 font-extrabold ${diff < 0 ? 'text-rose-400' : 'text-emerald-400'}`;
                }

                if (percentCell) {
                    let badgeClass = 'inline-flex items-center px-2 py-0.5 rounded bg-slate-800 text-slate-300 text-xs font-bold';
                    if (pct >= 100) {
                        badgeClass = 'inline-flex items-center px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-extrabold';
                    } else if (pct < 80) {
                        badgeClass = 'inline-flex items-center px-2 py-0.5 rounded bg-rose-500/20 text-rose-300 border border-rose-500/30 text-xs font-extrabold';
                    }
                    percentCell.innerHTML = `<span class="${badgeClass}">${roundedPct}%</span>`;
                }

                if (targetPerbulanCell && data.target_perbulan_utk_2026 !== undefined) {
                    targetPerbulanCell.textContent = data.target_perbulan_utk_2026;
                }

                recalculateTotals();
            } else {
                alert('Gagal memperbarui data: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(err => {
            inputEl.classList.remove('opacity-50');
            console.error(err);
            alert('Terjadi kesalahan jaringan.');
        });
    }

    function recalculateTotals() {
        let totalAct = 0;
        let totalTarget = 0;
        let totalMonthlyTarget = 0;

        document.querySelectorAll('.update-ytd-input').forEach(input => {
            totalAct += parseInt(input.value) || 0;
            totalTarget += parseInt(input.getAttribute('data-target-reguler')) || 0;
        });

        document.querySelectorAll('.ytd-target-perbulan-cell').forEach(cell => {
            totalMonthlyTarget += parseInt(cell.textContent) || 0;
        });

        const totalDiff = totalAct - totalTarget;
        const totalPct = totalTarget > 0 ? (totalAct / totalTarget) * 100 : 0;
        const roundedTotalPct = Math.round(totalPct);

        const totalActCell = document.querySelector('.total-ytd-act-cell');
        if (totalActCell) {
            totalActCell.textContent = totalAct;
        }

        const totalDiffCell = document.querySelector('.total-ytd-diff-cell');
        if (totalDiffCell) {
            totalDiffCell.textContent = (totalDiff > 0 ? '+' : '') + totalDiff;
            totalDiffCell.className = `total-ytd-diff-cell py-2.5 px-3 lg:py-4 lg:px-4 border border-blue-900 bg-teal-900/20 ${totalDiff < 0 ? 'text-rose-400' : 'text-emerald-400'}`;
        }

        const totalPercentCell = document.querySelector('.total-ytd-percent-cell');
        if (totalPercentCell) {
            totalPercentCell.innerHTML = `<span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-500/20 text-blue-300 border border-blue-500/30 text-xs font-extrabold">${roundedTotalPct}%</span>`;
        }

        const totalMonthlyTargetCell = document.querySelector('.total-monthly-target-cell');
        if (totalMonthlyTargetCell) {
            totalMonthlyTargetCell.textContent = totalMonthlyTarget;
        }

        const highlightAct = document.getElementById('highlight-ytd-act');
        const highlightTarget = document.getElementById('highlight-ytd-target');
        const highlightPercent = document.getElementById('highlight-ytd-percent');

        if (highlightAct) highlightAct.textContent = totalAct;
        if (highlightTarget) highlightTarget.textContent = totalTarget;
        if (highlightPercent) highlightPercent.textContent = roundedTotalPct;
    }

    // Toggle breakdown rows in STOK RATIO table
    document.querySelectorAll('.branch-toggle-row').forEach(row => {
        row.addEventListener('click', function() {
            const targetClass = this.getAttribute('data-target');
            const detailRows = document.querySelectorAll('.' + targetClass);
            const icon = this.querySelector('.toggle-icon');
            
            detailRows.forEach(detailRow => {
                detailRow.classList.toggle('hidden');
            });
            
            if (icon) {
                if (icon.textContent === '-') {
                    icon.textContent = '+';
                } else {
                    icon.textContent = '-';
                }
            }
        });
    });
});
</script>

<script>
// Catatan character counter
(function () {
    const textarea = document.getElementById('catatan-textarea');
    const counter  = document.getElementById('catatan-char');
    if (!textarea || !counter) return;

    function update() {
        const len = textarea.value.length;
        counter.textContent = len + ' / 2000';
        counter.style.color = len > 1800 ? '#f87171' : '';
    }
    update(); // init on page load
    textarea.addEventListener('input', update);
})();
</script>

@endsection