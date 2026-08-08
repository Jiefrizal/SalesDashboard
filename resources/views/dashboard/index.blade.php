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
<div class="bg-gradient-to-br from-blue-950 via-slate-900 to-blue-900 text-white rounded-xl lg:rounded-2xl p-3 lg:p-4 shadow-2xl border border-blue-900 overflow-hidden">

    <!-- Header Section -->
    <header class="bg-gradient-to-r from-blue-900 via-blue-950 to-blue-900 rounded-xl p-3 lg:p-4 border border-blue-800 shadow-xl relative overflow-hidden flex flex-col md:flex-row md:items-center md:justify-between">
        <!-- background glow -->
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-blue-500 rounded-full blur-3xl opacity-20"></div>
        
        <!-- Left: Yamaha Logo -->
        <div class="flex items-center justify-center md:justify-start z-10">
            <img src="{{ asset('yamaha_logo.png') }}" alt="YAMAHA" class="h-8 lg:h-12 w-auto object-contain">
        </div>

        <!-- Center: Title -->
        <div class="text-center my-2 md:my-0 z-10 flex-1">
            <h1 class="text-lg lg:text-2xl font-black text-white tracking-wider drop-shadow-md">
                REPORT SALES
            </h1>
            <p class="text-yellow-400 font-bold tracking-widest text-[10px] lg:text-xs mt-0.5 uppercase">
                {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}
            </p>
        </div>

        <!-- Right: GASPOLL Badge -->
        <div class="z-10 flex flex-col items-center md:items-end">
            <div class="bg-gradient-to-r from-yellow-400 to-amber-500 text-blue-950 font-extrabold px-2.5 py-1 lg:px-3 lg:py-1.5 rounded-lg italic text-[10px] lg:text-xs shadow-lg transform hover:scale-105 transition duration-300 border border-yellow-300 uppercase tracking-tight flex items-center space-x-1">
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
                    @if(auth()->user()->canEdit())
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
                        @if(auth()->user()->canEdit())
                            <span class="font-bold text-slate-200">Koneksi Spreadsheet Cabang Terhubung (Klik "Sinkronisasi Sekarang" untuk memperbarui data realtime)</span>
                        @else
                            <span class="font-bold text-slate-200">Koneksi Spreadsheet Cabang Terhubung (Data Realtime Sales & Stok Cabang)</span>
                        @endif
                    @endauth
                </div>
                @auth
                    @if(auth()->user()->canEdit())
                        <a href="{{ route('sync.spreadsheet') }}" onclick="this.classList.add('pointer-events-none', 'opacity-75'); this.querySelector('i').classList.add('animate-spin'); this.querySelector('span').innerText = 'Memproses Realtime...';" class="mt-2 sm:mt-0 bg-blue-800 hover:bg-blue-700 text-white font-extrabold px-4 py-2.5 rounded-xl transition duration-200 inline-flex items-center space-x-2 border border-blue-700 hover:border-blue-600 shadow-md transform hover:scale-105 active:scale-95 duration-150">
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
            <div class="bg-slate-900/80 border border-blue-900/60 rounded-xl p-3.5 lg:p-4 shadow-xl backdrop-blur-md relative overflow-hidden flex flex-col justify-start hover:border-blue-700/80 transition duration-300">
                <!-- Background glow decoration -->
                <div class="absolute -right-16 -top-16 w-32 h-32 bg-blue-500/10 rounded-full blur-2xl"></div>

                <!-- Frame Header -->
                <div class="flex items-center justify-between border-b border-blue-950 pb-2 mb-3">
                    <h3 class="text-xs lg:text-sm font-black text-white tracking-wider uppercase flex items-center space-x-2">
                        <span class="inline-block w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                        <span>Ringkasan Kinerja</span>
                    </h3>
                </div>
                
                <div class="space-y-2.5">
                    <!-- STU vs Target -->
                    <div class="bg-slate-950/50 rounded-xl p-2.5 flex items-center justify-between shadow-inner border border-blue-950/80 hover:border-blue-700/50 transition duration-300">
                        <div class="flex items-center space-x-2.5 min-w-0 flex-1">
                            <div class="bg-blue-500/10 text-blue-400 p-2 rounded-lg shrink-0 border border-blue-500/20">
                                <i class="bi bi-record-circle-fill text-base"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[10px] lg:text-[11px] text-slate-200 font-bold uppercase tracking-wider leading-tight">ACV VS TARGET</p>
                                <p class="text-xs lg:text-sm text-slate-100 mt-0.5 font-extrabold leading-tight"><span class="counter-animate" data-target="{{ $totalAcv }}">{{ number_format($totalAcv) }}</span> <span class="text-slate-300 font-bold">/ <span class="counter-animate" data-target="{{ $totalTargetMin }}">{{ number_format($totalTargetMin) }}</span></span></p>
                            </div>
                        </div>
                        <div class="text-right shrink-0 pl-2">
                            <span class="counter-animate text-lg lg:text-2xl font-black tracking-tight text-blue-400 leading-none" data-target="{{ round($overallAcvPercent) }}" data-suffix="%">{{ round($overallAcvPercent) }}%</span>
                        </div>
                    </div>

                    <!-- Growth (VS LM) -->
                    <div class="bg-slate-950/50 rounded-xl p-2.5 flex items-center justify-between shadow-inner border border-blue-950/80 hover:border-emerald-700/50 transition duration-300">
                        <div class="flex items-center space-x-2.5 min-w-0 flex-1">
                            <div class="bg-emerald-500/10 text-emerald-400 p-2 rounded-lg shrink-0 border border-emerald-500/20">
                                <i class="bi bi-graph-up-arrow text-base"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[10px] lg:text-[11px] text-slate-200 font-bold uppercase tracking-wider leading-tight">VS Last Month (LM)</p>
                                <p class="text-xs lg:text-sm text-slate-100 mt-0.5 font-extrabold leading-tight"><span class="counter-animate" data-target="{{ $totalAcv }}">{{ number_format($totalAcv) }}</span> <span class="text-slate-300 font-bold">/ <span class="counter-animate" data-target="{{ $totalLm }}">{{ number_format($totalLm) }}</span></span></p>
                            </div>
                        </div>
                        <div class="text-right shrink-0 pl-2">
                            <span class="counter-animate text-lg lg:text-2xl font-black tracking-tight text-emerald-400 leading-none" data-target="{{ round($overallGrowthPercent) }}" data-suffix="%">{{ round($overallGrowthPercent) }}%</span>
                        </div>
                    </div>

                    <!-- YTD Achievement -->
                    <div class="bg-slate-950/50 rounded-xl p-2.5 flex items-center justify-between shadow-inner border border-blue-950/80 hover:border-purple-700/50 transition duration-300">
                        <div class="flex items-center space-x-2.5 min-w-0 flex-1">
                            <div class="bg-purple-500/10 text-purple-400 p-2 rounded-lg shrink-0 border border-purple-500/20">
                                <i class="bi bi-calendar2-check-fill text-base"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[10px] lg:text-[11px] text-slate-200 font-bold uppercase tracking-wider leading-tight">ACT YTD vs Target 2026</p>
                                <p class="text-xs lg:text-sm text-slate-100 mt-0.5 font-extrabold leading-tight"><span class="counter-animate" data-target="{{ $totalActYtdJan2026 }}">{{ number_format($totalActYtdJan2026) }}</span> <span class="text-slate-300 font-bold">/ <span class="counter-animate" data-target="{{ $totalTargetReguler2026 }}">{{ number_format($totalTargetReguler2026) }}</span></span></p>
                            </div>
                        </div>
                        <div class="text-right shrink-0 pl-2">
                            <span class="counter-animate text-lg lg:text-2xl font-black tracking-tight text-purple-400 leading-none" data-target="{{ round($overallYtdPercent) }}" data-suffix="%">{{ round($overallYtdPercent) }}%</span>
                        </div>
                    </div>

                    <!-- DEALER ON TARGET -->
                    <div class="bg-slate-950/50 rounded-xl p-2.5 flex items-center justify-between shadow-inner border border-blue-950/80 hover:border-yellow-700/50 transition duration-300">
                        <div class="flex items-center space-x-2.5 min-w-0 flex-1">
                            <div class="bg-yellow-500/10 text-yellow-450 p-2 rounded-lg shrink-0 border border-yellow-500/20">
                                <i class="bi bi-trophy-fill text-base text-yellow-400"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[10px] lg:text-[11px] text-slate-200 font-bold uppercase tracking-wider leading-tight">Dealer On Target</p>
                                <p class="text-[10px] text-yellow-300 mt-0.5 font-extrabold leading-tight">Capai Target</p>
                            </div>
                        </div>
                        <div class="text-right shrink-0 pl-2">
                            <span class="counter-animate text-lg lg:text-2xl font-black tracking-tight text-yellow-400 leading-none" data-target="{{ $dealersOnTarget }}">{{ $dealersOnTarget }}</span>
                        </div>
                    </div>

                    <!-- DEALER DI BAWAH TARGET -->
                    <div class="bg-slate-950/50 rounded-xl p-2.5 flex items-center justify-between shadow-inner border border-blue-950/80 hover:border-rose-700/50 transition duration-300">
                        <div class="flex items-center space-x-2.5 min-w-0 flex-1">
                            <div class="bg-rose-500/10 text-rose-455 p-2 rounded-lg shrink-0 border border-rose-500/20">
                                <i class="bi bi-graph-down-arrow text-base text-rose-400"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[10px] lg:text-[11px] text-slate-200 font-bold uppercase tracking-wider leading-tight">Dealer Below Target</p>
                                <p class="text-[10px] text-rose-300 mt-0.5 font-extrabold leading-tight">Belum Capai Target</p>
                            </div>
                        </div>
                        <div class="text-right shrink-0 pl-2">
                            <span class="counter-animate text-lg lg:text-2xl font-black tracking-tight text-rose-400 leading-none" data-target="{{ $dealersBelowTarget }}">{{ $dealersBelowTarget }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Highlights & Alerts Card (Highlight & Analisis) -->
            <div id="highlight-card" class="bg-slate-900/80 rounded-xl p-3.5 lg:p-4 shadow-xl backdrop-blur-md relative overflow-hidden flex flex-col justify-start highlight-card-animated">
                <!-- Background glow decoration -->
                <div class="absolute -right-16 -top-16 w-40 h-40 bg-blue-500/20 rounded-full blur-2xl highlight-glow-orb"></div>
                <div class="absolute -left-10 -bottom-10 w-32 h-32 bg-indigo-500/15 rounded-full blur-2xl highlight-glow-orb2"></div>

                <!-- Frame Header -->
                <div class="flex items-center justify-between border-b border-blue-950 pb-2 mb-3">
                    <h3 class="text-xs lg:text-sm font-black text-white tracking-wider uppercase flex items-center space-x-2">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-blue-500"></span>
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
                            <p class="text-[9.5px] lg:text-xs text-slate-200 font-extrabold uppercase tracking-wider leading-tight">Achieved Target</p>
                            <p class="text-xs text-yellow-400 font-bold leading-tight mt-0.5">{{ $dealersOnTarget }} Dealer Capai Target</p>
                            <p class="text-[9.5px] lg:text-[10.5px] text-slate-300 mt-1 leading-normal font-semibold">{{ !empty($dealersOnTargetNames) ? implode(', ', $dealersOnTargetNames) : 'Belum ada dealer' }}</p>
                        </div>
                    </div>

                    <!-- Older Stock Alert -->
                    @if(!empty($oldStockAlerts))
                        <div class="bg-slate-950/30 rounded-xl p-2.5 lg:p-3 flex items-start space-x-2.5 shadow-inner border border-rose-500/30 hover:border-rose-400/60 hover:bg-rose-500/5 transition duration-300 highlight-item-2 highlight-alert-blink">
                            <div class="bg-rose-500/10 text-rose-400 p-2 rounded-lg shrink-0 border border-rose-500/20">
                                <i class="bi bi-exclamation-triangle-fill text-base"></i>
                            </div>
                            <div class="min-w-0 flex-1 text-left">
                                <p class="text-[9.5px] lg:text-xs text-slate-200 font-extrabold uppercase tracking-wider leading-tight">Stock Alert</p>
                                <p class="text-xs text-rose-400 font-bold leading-tight mt-0.5">Prioritaskan Penjualan</p>
                                <p class="text-[9.5px] lg:text-[10.5px] text-slate-300 mt-1 leading-normal font-semibold">{{ implode(', ', $oldStockAlerts) }}</p>
                            </div>
                        </div>
                    @else
                        <div class="bg-slate-950/30 rounded-xl p-2.5 lg:p-3 flex items-start space-x-2.5 shadow-inner border border-emerald-500/20 hover:border-emerald-400/60 hover:bg-emerald-500/5 transition duration-300 highlight-item-2">
                            <div class="bg-emerald-500/10 text-emerald-400 p-2 rounded-lg shrink-0 border border-emerald-500/20">
                                <i class="bi bi-shield-check text-base"></i>
                            </div>
                            <div class="min-w-0 flex-1 text-left">
                                <p class="text-[9.5px] lg:text-xs text-slate-200 font-extrabold uppercase tracking-wider leading-tight">Stock Condition</p>
                                <p class="text-xs text-emerald-400 font-bold leading-tight mt-0.5">Optimal</p>
                                <p class="text-[9.5px] lg:text-[10.5px] text-slate-300 mt-1 leading-normal font-semibold">Seluruh cabang bersih dari stock lama (2024/2025).</p>
                            </div>
                        </div>
                    @endif

                    <!-- YTD Progress -->
                    <div class="bg-slate-950/30 rounded-xl p-2.5 lg:p-3 flex items-start space-x-2.5 shadow-inner border border-blue-500/20 hover:border-blue-400/60 hover:bg-blue-500/5 transition duration-300 highlight-item-3">
                        <div class="bg-blue-500/10 text-blue-400 p-2 rounded-lg shrink-0 border border-blue-500/20">
                            <i class="bi bi-bar-chart-fill text-base"></i>
                        </div>
                        <div class="min-w-0 flex-1 text-left">
                            <p class="text-[9.5px] lg:text-xs text-slate-200 font-extrabold uppercase tracking-wider leading-tight">YTD 2026 Progress</p>
                            <p class="text-xs text-blue-400 font-bold leading-tight mt-0.5">Pencapaian: <span id="highlight-ytd-percent">{{ round($overallYtdPercent) }}</span>%</p>
                            <p class="text-[9.5px] lg:text-[10.5px] text-slate-300 mt-1 leading-normal font-semibold">Total actual YTD <span id="highlight-ytd-act">{{ $totalActYtdJan2026 }}</span> unit dari target 2026 <span id="highlight-ytd-target">{{ $totalTargetReguler2026 }}</span> unit.</p>
                        </div>
                    </div>

                    <!-- Catatan — editable by super_admin, read-only for viewer -->
                    <div class="bg-slate-950/30 rounded-xl shadow-inner border border-indigo-500/20 hover:border-indigo-400/50 transition duration-300" style="animation: slide-in-up 0.5s ease-out 0.7s both; animation-fill-mode: both;">

                        @auth
                            @if(auth()->user()->canEdit())
                                {{-- Editor & Super Admin: icon header + editable textarea --}}
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
            <div class="bg-[#0e1326] border-[1.5px] border-emerald-500/60 rounded-3xl p-5 sm:p-6 shadow-2xl relative overflow-hidden flex flex-col justify-start hover:border-emerald-400 transition duration-300">
                <div class="flex items-center justify-between border-b border-emerald-500/30 pb-3.5 mb-4 gap-2">
                    <div class="flex items-start space-x-2.5 min-w-0">
                        <span class="w-3 h-3 rounded-full bg-emerald-400 shadow-[0_0_8px_#34d399] shrink-0 mt-1"></span>
                        <h4 class="text-sm sm:text-base font-black text-emerald-300 uppercase tracking-wider leading-tight">
                            Sudah Input Laporan Hari Ini
                        </h4>
                    </div>
                    <span class="bg-emerald-500/15 border border-emerald-500/60 text-emerald-300 px-3.5 py-1 rounded-full text-xs font-black shrink-0 whitespace-nowrap shadow-md">
                        {{ count($sudahInputCabangs) }} Cabang
                    </span>
                </div>

                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3.5">TANGGAL LAPORAN: {{ $reportingDay }} {{ strtoupper(\Carbon\Carbon::now()->locale('id')->isoFormat('MMMM Y')) }}</p>
                
                @if(count($sudahInputCabangs) > 0)
                    <div class="space-y-2.5">
                        @foreach($sudahInputCabangs as $c)
                            @php
                                $color = $colorPalette[$c->nama] ?? '#22c55e';
                                $valToday = $c->daily_performance[$reportingIdx] ?? $c->acv;
                            @endphp
                            <div class="flex items-center justify-between bg-[#080a14]/90 border border-emerald-500/40 hover:border-emerald-400/80 px-4 py-2.5 rounded-xl sm:rounded-2xl transition duration-200 shadow-md">
                                <div class="flex items-center space-x-3 min-w-0">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $color }}; box-shadow: 0 0 6px {{ $color }}b0;"></span>
                                    <span class="text-sm sm:text-base font-extrabold text-white tracking-wide truncate">{{ $c->nama }}</span>
                                </div>
                                <div class="flex items-center space-x-1.5 shrink-0 ml-3">
                                    <span class="text-xs sm:text-sm font-semibold text-slate-400">Tgl {{ $reportingDay }}:</span>
                                    <span class="text-sm sm:text-base font-black text-emerald-400">{{ $valToday }}</span>
                                    <i class="bi bi-check-circle-fill text-emerald-400 text-sm sm:text-base ml-0.5"></i>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-slate-950/60 border border-slate-800 rounded-2xl p-3.5 text-center text-slate-400 text-xs font-bold italic">
                        Belum ada cabang yang menginputkan laporan hari ini.
                    </div>
                @endif
            </div>

            <!-- Box 2: Belum Input Laporan Hari Ini (SAMAKAN WARNA DAN JENIS DENGAN SUDAH INPUT LAPORAN) -->
            <div class="bg-[#0e1326] border-[1.5px] border-emerald-500/60 rounded-3xl p-5 sm:p-6 shadow-2xl relative overflow-hidden flex flex-col justify-start hover:border-emerald-400 transition duration-300">
                <div class="flex items-center justify-between border-b border-emerald-500/30 pb-3.5 mb-4 gap-2">
                    <div class="flex items-start space-x-2.5 min-w-0">
                        <span class="w-3 h-3 rounded-full bg-emerald-400 shadow-[0_0_8px_#34d399] shrink-0 mt-1"></span>
                        <h4 class="text-sm sm:text-base font-black text-emerald-300 uppercase tracking-wider leading-tight">
                            Belum Input Laporan Hari Ini
                        </h4>
                    </div>
                    <span class="bg-emerald-500/15 border border-emerald-500/60 text-emerald-300 px-3.5 py-1 rounded-full text-xs font-black shrink-0 whitespace-nowrap shadow-md">
                        {{ count($belumInputCabangs) }} Cabang
                    </span>
                </div>

                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3.5">TANGGAL LAPORAN: {{ $reportingDay }} {{ strtoupper(\Carbon\Carbon::now()->locale('id')->isoFormat('MMMM Y')) }}</p>

                @if(count($belumInputCabangs) > 0)
                    <div class="space-y-2.5">
                        @foreach($belumInputCabangs as $c)
                            @php
                                $color = $colorPalette[$c->nama] ?? '#22c55e';
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
                            <div class="flex items-center justify-between bg-[#080a14]/90 border border-emerald-500/40 hover:border-emerald-400/80 px-4 py-2.5 rounded-xl sm:rounded-2xl transition duration-200 shadow-md">
                                <div class="flex items-center space-x-3 min-w-0">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $color }}; box-shadow: 0 0 6px {{ $color }}b0;"></span>
                                    <span class="text-sm sm:text-base font-extrabold text-white tracking-wide truncate">{{ $c->nama }}</span>
                                </div>
                                <div class="flex items-center space-x-1.5 shrink-0 ml-3">
                                    @if($lastDayNum > 0)
                                        <span class="text-xs sm:text-sm font-semibold text-slate-400">Tgl {{ $lastDayNum }}:</span>
                                        <span class="text-sm sm:text-base font-black text-emerald-400">{{ $lastVal }}</span>
                                    @else
                                        <span class="text-xs sm:text-sm font-bold text-rose-400">Belum Input</span>
                                    @endif
                                    <i class="bi bi-clock-history text-emerald-400 text-sm sm:text-base ml-0.5"></i>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-emerald-500/10 border border-emerald-500/30 rounded-2xl p-3.5 text-center text-emerald-300 text-xs font-bold flex items-center justify-center space-x-1.5">
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
                                <td class="sticky left-0 z-10 bg-slate-950/90 group-hover:bg-slate-900/95 py-2.5 px-3 border border-blue-900/40 text-center font-extrabold w-10 text-slate-200">{{ $index + 1 }}</td>
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
                                 <td class="ytd-act-cell py-2 px-2 lg:py-3.5 lg:px-4 border border-blue-900/30 bg-teal-500/5 text-center text-teal-300 font-bold">
                                    <span>{{ $cabang->act_ytd_jan_2026 }}</span>
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

            <!-- LAPORAN HASIL ANALISA: STU BY POS SECTION (HORIZONTAL VIEW PER CABANG BERURUTAN) -->
            @php
                $posDataList = [
                    [
                        'dealer' => 'PEKANBARU',
                        'type' => '3S',
                        'target' => 90,
                        'total_stu' => 10,
                        'pos' => [
                            ['name' => 'DEALER', 'target' => 30, 'stu' => 8],
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
                            ['name' => 'DEALER', 'target' => 25, 'stu' => 7],
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
                            ['name' => 'DEALER', 'target' => 34, 'stu' => 7],
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
                            ['name' => 'DEALER', 'target' => 74, 'stu' => 8],
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
                            ['name' => 'DEALER', 'target' => 43, 'stu' => 3],
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
                            ['name' => 'DEALER', 'target' => 115, 'stu' => 21],
                            ['name' => 'PANCUR BATU', 'target' => 20, 'stu' => 3],
                        ]
                    ],
                ];

                $processedPosData = [];
                $posMapByName = [];
                foreach ($posDataList as $pItem) {
                    $posMapByName[strtoupper(trim($pItem['dealer']))] = $pItem;
                }

                foreach ($cabangs as $dbC) {
                    $cName = strtoupper(trim($dbC->nama));
                    $item = $posMapByName[$cName] ?? null;
                    if (!$item) continue;

                    $dealerTarget = $dbC->target_reguler > 0 ? (int)$dbC->target_reguler : $item['target'];
                    $dealerTotalStu = (int)$dbC->acv;

                    $dbPosMap = (is_array($dbC->stu_breakdown) && isset($dbC->stu_breakdown['pos']))
                        ? $dbC->stu_breakdown['pos']
                        : [];

                    $posItems = [];
                    foreach ($item['pos'] as $p) {
                        $pName = $p['name'];
                        $pStu = isset($dbPosMap[$pName]) ? (int)$dbPosMap[$pName] : $p['stu'];
                        $pTarget = (int)$p['target'];
                        $pctVal = $pTarget > 0 ? round(($pStu / $pTarget) * 100) : 0;
                        $posItems[] = [
                            'name' => $pName,
                            'target' => $pTarget,
                            'stu' => $pStu,
                            'growth' => $pctVal . '%',
                            'pct_raw' => $pctVal
                        ];
                    }

                    $processedPosData[] = [
                        'dealer' => strtoupper($dbC->nama),
                        'type' => $item['type'],
                        'target' => $dealerTarget,
                        'total_stu' => $dealerTotalStu,
                        'pos' => $posItems
                    ];
                }

                // Fallback if processedPosData empty
                if (empty($processedPosData)) {
                    foreach ($posDataList as $item) {
                        $dbC = $cabangs->first(fn($c) => strtoupper(trim($c->nama)) === $item['dealer']);
                        $dealerTarget = $dbC && $dbC->target_reguler > 0 ? (int)$dbC->target_reguler : $item['target'];
                        $dealerTotalStu = $dbC ? (int)$dbC->acv : $item['total_stu'];

                        $dbPosMap = ($dbC && is_array($dbC->stu_breakdown) && isset($dbC->stu_breakdown['pos']))
                            ? $dbC->stu_breakdown['pos']
                            : [];

                        $posItems = [];
                        foreach ($item['pos'] as $p) {
                            $pName = $p['name'];
                            $pStu = isset($dbPosMap[$pName]) ? (int)$dbPosMap[$pName] : $p['stu'];
                            $pTarget = (int)$p['target'];
                            $pctVal = $pTarget > 0 ? round(($pStu / $pTarget) * 100) : 0;
                            $posItems[] = [
                                'name' => $pName,
                                'target' => $pTarget,
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
                }
            @endphp            <div class="mt-8 mb-8 bg-[#0e1326] border-[1.5px] border-[#e94560]/70 rounded-3xl p-5 sm:p-6 shadow-2xl relative overflow-hidden backdrop-blur-xl hover:border-[#e94560] transition duration-300">
                
                <!-- Section Header Banner (Matching BELUM INPUT LAPORAN style) -->
                <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-[#e94560]/30 pb-3.5 mb-5 gap-3">
                    <div class="flex items-start space-x-2.5 min-w-0">
                        <span class="w-3 h-3 rounded-full bg-[#e94560] shadow-[0_0_8px_#e94560] shrink-0 mt-1"></span>
                        <div>
                            <h2 class="text-sm sm:text-base lg:text-lg font-black text-[#f36892] uppercase tracking-wider leading-tight">
                                LAPORAN HASIL ANALISA (STU BY POS DEALER)
                            </h2>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">
                                ANALISA CAPAIAN POS PENJUALAN TIAP CABANG DEALER &bull; {{ \Carbon\Carbon::now()->format('d-M-y') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <span class="inline-flex items-center space-x-1.5 text-xs font-extrabold bg-[#e94560]/15 border border-[#e94560]/60 text-[#f36892] px-3.5 py-1 rounded-full shadow-md">
                            <i class="bi bi-diagram-3-fill text-[#f36892]"></i>
                            <span>{{ count($processedPosData) }} Cabang Dealer</span>
                        </span>
                    </div>
                </div>

                <!-- WIDE LANDSCAPE BRANCH CARDS CONTAINER (MEMANJANG KE KANAN) -->
                <div id="posCardsContainer" class="space-y-4 lg:space-y-5">
                    @foreach($processedPosData as $d)
                        @php
                            $cColor = $colorPalette[$d['dealer']] ?? '#e94560';
                        @endphp
                        <div class="dealer-card-root w-full bg-[#080a14]/90 border border-[#e94560]/40 rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-xl relative overflow-hidden hover:border-[#e94560]/80 transition duration-300 group">
                            <div class="absolute -right-12 -top-12 w-36 h-36 bg-[#e94560]/10 rounded-full blur-2xl group-hover:bg-[#e94560]/20 transition pointer-events-none"></div>

                            <!-- Header Bar Cabang (Horizontal Layout Memanjang Ke Kanan) -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-[#e94560]/30 pb-3 mb-3.5 gap-2.5">
                                <div class="flex items-center space-x-3">
                                    <span class="w-3.5 h-3.5 rounded-full shrink-0" style="background-color: {{ $cColor }}; box-shadow: 0 0 10px {{ $cColor }}b0;"></span>
                                    <h3 class="text-base sm:text-lg font-black text-white uppercase tracking-wide">
                                        {{ $d['dealer'] }}
                                    </h3>
                                    <span class="bg-[#e94560]/15 border border-[#e94560]/50 text-[#f36892] px-3 py-0.5 rounded-full text-xs font-black">DEALER ({{ $d['type'] }})</span>
                                </div>
                                
                                <!-- Summary STU Badge -->
                                <div class="flex items-center space-x-2 bg-[#0e1326] border border-[#e94560]/50 px-3.5 py-1.5 rounded-xl shadow-inner self-start sm:self-auto">
                                    <span class="text-[11px] text-slate-400 uppercase font-extrabold tracking-wider">TOTAL STU:</span>
                                    <span class="text-sm sm:text-base font-black text-emerald-400">
                                        {{ $d['total_stu'] }}
                                    </span>
                                    <span class="text-xs text-slate-400 font-bold">/ <span class="pos-dealer-target-val text-amber-400 font-black" data-dealer="{{ $d['dealer'] }}">{{ $d['target'] }}</span> Target</span>
                                </div>
                            </div>

                            <!-- Sub POS List per Dealer (Disusun Horizontal Memanjang ke Kanan) -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                                @foreach($d['pos'] as $p)
                                    <div class="p-3 sm:p-3.5 rounded-xl sm:rounded-2xl border transition bg-[#0e1326]/90 border-[#e94560]/30 hover:border-[#e94560]/70 shadow-md flex flex-col justify-between space-y-2.5">
                                        
                                        <!-- Row 1: POS Name & Growth Badge -->
                                        <div class="flex items-center justify-between pb-1.5 border-b border-slate-800/80">
                                            <div class="flex items-center space-x-2 min-w-0">
                                                @if($p['name'] === 'DEALER')
                                                    <div class="w-5 h-5 rounded-lg bg-[#e94560]/20 border border-[#e94560]/50 flex items-center justify-center shrink-0">
                                                        <i class="bi bi-building-fill text-[#f36892] text-[10px]"></i>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <h4 class="text-xs font-black text-rose-200 uppercase tracking-wide truncate">{{ $p['name'] }}</h4>
                                                        <span class="text-[9px] font-bold text-[#f36892] block leading-tight">Pos Utama</span>
                                                    </div>
                                                @else
                                                    <div class="w-5 h-5 rounded-lg bg-amber-500/20 border border-amber-500/40 flex items-center justify-center shrink-0">
                                                        <i class="bi bi-geo-alt-fill text-amber-400 text-[10px]"></i>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <h4 class="text-xs font-black text-slate-100 uppercase tracking-wide truncate">{{ $p['name'] }}</h4>
                                                        <span class="text-[9px] font-medium text-slate-400 block leading-tight">Sub-Pos</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="shrink-0 ml-1">
                                                <span class="pos-growth-badge px-2 py-0.5 rounded-md text-[11px] font-black shadow-sm {{ $p['pct_raw'] >= 50 ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : ($p['pct_raw'] > 0 ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : 'bg-slate-900 text-slate-400 border border-slate-800') }}">
                                                    {{ $p['growth'] }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Row 2: Target & STU Mini Inputs -->
                                        <div class="grid grid-cols-2 gap-1.5">
                                            <div class="group/input bg-[#04060d] border border-[#e94560]/40 hover:border-[#e94560]/80 focus-within:border-[#e94560] focus-within:ring-1 focus-within:ring-[#e94560]/40 px-2 py-1 rounded-lg flex items-center justify-between shadow-inner transition-all duration-200 min-w-0 space-x-1">
                                                <div class="flex items-center space-x-1 shrink-0">
                                                    @if(auth()->user()->canEdit())
                                                        <i class="bi bi-pencil-fill text-[8px] text-[#f36892] group-hover/input:text-[#f36892] transition-colors shrink-0"></i>
                                                    @else
                                                        <i class="bi bi-bullseye text-[8px] text-[#f36892] shrink-0"></i>
                                                    @endif
                                                    <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-tight whitespace-nowrap shrink-0">TGT</span>
                                                </div>
                                                <div class="flex-1 min-w-0 text-right">
                                                    @if(auth()->user()->canEdit())
                                                        <input 
                                                            type="number" 
                                                            value="{{ $p['target'] }}" 
                                                            min="0"
                                                            class="pos-target-input w-full text-right bg-transparent text-white font-extrabold text-xs focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                                            data-dealer="{{ $d['dealer'] }}"
                                                            data-pos="{{ $p['name'] }}"
                                                            data-stu="{{ $p['stu'] }}"
                                                            onfocus="this.select()"
                                                            title="Klik untuk mengubah nilai target"
                                                        >
                                                    @else
                                                        <span class="font-extrabold text-xs text-white select-none">{{ $p['target'] }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="bg-emerald-955/40 border border-emerald-500/40 px-2 py-1 rounded-lg flex items-center justify-between min-w-0 space-x-1 shadow-inner">
                                                <span class="text-[9px] font-extrabold text-emerald-400 uppercase tracking-wider whitespace-nowrap shrink-0">STU</span>
                                                <span class="text-xs font-black text-emerald-400 truncate text-right flex-1 min-w-0">{{ $p['stu'] }}</span>
                                            </div>
                                        </div>

                                        <!-- Progress Bar Meter -->
                                        <div class="w-full bg-[#04060d] rounded-full h-1.5 overflow-hidden border border-[#e94560]/30">
                                            <div class="pos-progress-bar bg-gradient-to-r {{ $p['name'] === 'DEALER' ? 'from-[#e94560] via-amber-400 to-emerald-400' : 'from-blue-500 to-emerald-400' }} h-1.5 rounded-full transition-all duration-500" style="width: {{ min(100, max(5, $p['pct_raw'])) }}%;"></div>
                                        </div>

                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Horizontal navigation scroll buttons for POS Cards
                const posContainer = document.getElementById('posCardsContainer');
                const btnPosLeft = document.getElementById('posScrollLeft');
                const btnPosRight = document.getElementById('posScrollRight');

                if (posContainer && btnPosLeft && btnPosRight) {
                    btnPosLeft.addEventListener('click', function() {
                        posContainer.scrollBy({ left: -390, behavior: 'smooth' });
                    });
                    btnPosRight.addEventListener('click', function() {
                        posContainer.scrollBy({ left: 390, behavior: 'smooth' });
                    });
                }

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

                        const posCard = inputEl.closest('.p-3\\.5, .p-4, .p-3');
                        if (posCard) {
                            // Calculate percentage
                            const pct = target > 0 ? Math.round((stu / target) * 100) : 0;
                            
                            // Update growth badge
                            const growthBadge = posCard.querySelector('.pos-growth-badge');
                            if (growthBadge) {
                                growthBadge.textContent = pct + '%';
                                if (pct >= 50) {
                                    growthBadge.className = 'pos-growth-badge px-2.5 py-0.5 rounded-lg text-xs font-black shadow-sm bg-emerald-500/20 text-emerald-300 border border-emerald-500/40';
                                } else if (pct > 0) {
                                    growthBadge.className = 'pos-growth-badge px-2.5 py-0.5 rounded-lg text-xs font-black shadow-sm bg-amber-500/20 text-amber-300 border border-amber-500/40';
                                } else {
                                    growthBadge.className = 'pos-growth-badge px-2.5 py-0.5 rounded-lg text-xs font-black shadow-sm bg-slate-900 text-slate-400 border border-slate-800';
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
                        const dealerCard = inputEl.closest('.dealer-card-root, .bg-gradient-to-br, .bg-\\[\\#080a14\\]\\/90');
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

            <!-- ACHIEVEMENT BY LEASING SECTION (LAYOUT MEMANJANG KE KANAN EXACTLY MATCHING STU BY POS DEALER) -->
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

                    $achTotal = (int)$cabang->acv;
                    
                    $lData = $cabang->leasing_breakdown ?? ($cabang->stu_breakdown['leasing'] ?? null);
                    if ($lData && (isset($lData['cash']) || isset($lData['fincoy']))) {
                        $achCash = (int)($lData['cash'] ?? 0);
                        $achKredit = (int)($lData['kredit'] ?? max(0, $achTotal - $achCash));
                        $megaCombined = (int)($lData['fincoy']['MEGA'] ?? 0)
                            + (int)($lData['fincoy']['MAF'] ?? 0)
                            + (int)($lData['fincoy']['MF'] ?? 0);
                        $sofCombined = (int)($lData['fincoy']['SOF'] ?? 0)
                            + (int)($lData['fincoy']['OTO'] ?? 0)
                            + (int)($lData['fincoy']['OTOBAN'] ?? 0);
                        $fincoyAch = [
                            'ADIRA' => (int)($lData['fincoy']['ADIRA'] ?? 0),
                            'BAF'   => (int)($lData['fincoy']['BAF'] ?? 0),
                            'IMFI'  => (int)($lData['fincoy']['IMFI'] ?? 0),
                            'MEGA'  => $megaCombined,
                            'SOF'   => $sofCombined,
                        ];
                    } else {
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
                        'target' => $targetTotal > 0 ? round(($achTotal / $targetTotal) * 100, 1) . '%' : '0%',
                        'cash'   => $targetCash > 0 ? round(($achCash / $targetCash) * 100, 1) . '%' : '0%',
                        'kredit' => $targetKredit > 0 ? round(($achKredit / $targetKredit) * 100, 1) . '%' : '0%',
                    ];
                    foreach (['ADIRA', 'BAF', 'IMFI', 'MEGA', 'SOF'] as $fk) {
                        $fTgt = $fincoyTarget[$fk] ?? 0;
                        $pctAch[$fk] = $fTgt > 0 ? round((($fincoyAch[$fk] ?? 0) / $fTgt) * 100, 1) . '%' : '0%';
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
                    'target' => $totalTargetTotal > 0 ? round(($totalAchTotal / $totalTargetTotal) * 100, 1) . '%' : '0%',
                    'cash'   => $totalTargetCash > 0 ? round(($totalAchCash / $totalTargetCash) * 100, 1) . '%' : '0%',
                    'kredit' => $totalTargetKredit > 0 ? round(($totalAchKredit / $totalTargetKredit) * 100, 1) . '%' : '0%',
                ];
                foreach (['ADIRA', 'BAF', 'IMFI', 'MEGA', 'SOF'] as $fk) {
                    $fTgtTot = $totalFincoyTarget[$fk] ?? 0;
                    $totalPctAch[$fk] = $fTgtTot > 0 ? round(($totalFincoyAch[$fk] / $fTgtTot) * 100, 1) . '%' : '0%';
                }

                $leasingTotal = [
                    'no' => count($leasingBranches) + 1,
                    'cabang' => 'TOTAL REKAPITULASI SELURUH DEALER',
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

            <div class="mt-8 mb-8 bg-[#0e1326] border-[1.5px] border-[#e94560]/70 rounded-3xl p-5 sm:p-6 shadow-2xl relative overflow-hidden backdrop-blur-xl hover:border-[#e94560] transition duration-300">
                
                <!-- Section Header Banner (Matching STU BY POS DEALER style) -->
                <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-[#e94560]/30 pb-3.5 mb-5 gap-3">
                    <div class="flex items-start space-x-2.5 min-w-0">
                        <span class="w-3 h-3 rounded-full bg-[#e94560] shadow-[0_0_8px_#e94560] shrink-0 mt-1"></span>
                        <div>
                            <h2 class="text-sm sm:text-base lg:text-lg font-black text-[#f36892] uppercase tracking-wider leading-tight">
                                ACHIEVEMENT BY LEASING
                            </h2>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">
                                ANALISA CAPAIAN PENJUALAN CASH & FINCOY LEASING TIAP CABANG DEALER &bull; {{ \Carbon\Carbon::now()->format('d-M-y') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <span class="inline-flex items-center space-x-1.5 text-xs font-extrabold bg-[#e94560]/15 border border-[#e94560]/60 text-[#f36892] px-3.5 py-1 rounded-full shadow-md">
                            <i class="bi bi-bank2 text-[#f36892]"></i>
                            <span>{{ count($leasingBranches) }} Cabang Dealer</span>
                        </span>
                    </div>
                </div>

                <!-- WIDE LANDSCAPE LEASING CARDS CONTAINER (MEMANJANG KE KANAN) -->
                <div id="leasingCardsContainer" class="space-y-4 lg:space-y-5">
                    
                    <!-- Grand Total Banner Card (Wide Landscape Strip) -->
                    <div class="dealer-card-root w-full bg-[#080a14]/90 border border-yellow-500/50 rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-xl relative overflow-hidden hover:border-yellow-400 transition duration-300">
                        <div class="absolute -right-12 -top-12 w-36 h-36 bg-yellow-500/10 rounded-full blur-2xl pointer-events-none"></div>

                        <!-- Header Bar Total -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-yellow-500/30 pb-3 mb-3.5 gap-2.5">
                            <div class="flex items-center space-x-3">
                                <span class="w-3.5 h-3.5 rounded-full bg-amber-400 shadow-[0_0_10px_#f59e0b] shrink-0"></span>
                                <h3 class="text-base sm:text-lg font-black text-white uppercase tracking-wide">
                                    TOTAL REKAPITULASI SELURUH DEALER
                                </h3>
                                <span class="bg-amber-500/15 border border-amber-500/50 text-amber-300 px-3 py-0.5 rounded-full text-xs font-black">GABUNGAN 6 CABANG</span>
                            </div>
                            
                            <!-- Summary STU Badge -->
                            <div class="flex items-center space-x-2 bg-[#0e1326] border border-yellow-500/50 px-3.5 py-1.5 rounded-xl shadow-inner self-start sm:self-auto">
                                <span class="text-[11px] text-slate-400 uppercase font-extrabold tracking-wider">TOTAL ACH:</span>
                                <span class="text-sm sm:text-base font-black text-amber-400">
                                    {{ $leasingTotal['ach_total'] }}
                                </span>
                                <span class="text-xs text-slate-400 font-bold">/ <span class="text-slate-200 font-black">{{ $leasingTotal['target_total'] }}</span> Target</span>
                                <span class="bg-amber-500/20 text-yellow-300 text-xs font-black px-2 py-0.5 rounded-md border border-amber-500/40 ml-1">
                                    %ACH: {{ $leasingTotal['pct_ach']['target'] }}
                                </span>
                            </div>
                        </div>

                        <!-- 2-TIER EXECUTIVE GRID: TIER 1 (CASH & KREDIT) + TIER 2 (5 FINCOY PROVIDERS) -->
                        <div class="space-y-3">
                            <!-- TIER 1: CASH & KREDIT HIGHLIGHT PILLARS -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <!-- DISTINCT CASH CARD (TOTAL) -->
                                <div class="p-3.5 rounded-xl border transition bg-gradient-to-r from-[#0c2340] via-[#09182d] to-[#040914] border-[1.5px] border-cyan-400/80 shadow-[0_0_15px_rgba(34,211,238,0.25)] hover:border-cyan-300 flex items-center justify-between space-x-3 relative overflow-hidden group/cash">
                                    <div class="absolute -right-8 -top-8 w-24 h-24 bg-cyan-500/20 rounded-full blur-xl pointer-events-none"></div>
                                    <div class="flex items-center space-x-3 min-w-0 flex-1">
                                        <div class="w-10 h-10 rounded-xl bg-cyan-500/25 border border-cyan-400/60 flex items-center justify-center shrink-0 shadow-[0_0_10px_rgba(34,211,238,0.4)]">
                                            <i class="bi bi-cash-stack text-cyan-300 text-lg"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center space-x-2">
                                                <h4 class="text-xs font-black text-cyan-100 uppercase tracking-wider">CASH (TUNAI)</h4>
                                                <span class="text-[9px] font-extrabold text-cyan-300 bg-cyan-950/80 border border-cyan-500/40 px-1.5 py-0.2 rounded">Utama</span>
                                            </div>
                                            <div class="text-xs font-bold text-slate-200 mt-1">
                                                <strong class="text-cyan-300 text-sm font-black">{{ $leasingTotal['ach_cash'] }}</strong> / {{ $leasingTotal['target_cash'] }} Unit
                                                <span class="text-[10px] text-cyan-200 font-bold ml-2">Tgt Ratio: {{ $leasingTotal['pct_target']['cash'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end shrink-0 pl-2">
                                        <span class="px-3 py-1 rounded-lg text-xs font-black bg-cyan-500/30 text-cyan-100 border border-cyan-400/70 shadow-sm mb-1.5">
                                            {{ $leasingTotal['pct_ach']['cash'] }}
                                        </span>
                                        <div class="w-24 bg-[#02050b] rounded-full h-2 overflow-hidden border border-cyan-400/40">
                                            <div class="bg-gradient-to-r from-cyan-500 to-emerald-400 h-2 rounded-full" style="width: {{ min(100, max(5, (int)filter_var($leasingTotal['pct_ach']['cash'], FILTER_SANITIZE_NUMBER_INT))) }}%;"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- DISTINCT KREDIT CARD (TOTAL) -->
                                <div class="p-3.5 rounded-xl border transition bg-gradient-to-r from-[#270e38] via-[#1a0826] to-[#040914] border-[1.5px] border-purple-400/80 shadow-[0_0_15px_rgba(168,85,247,0.25)] hover:border-purple-300 flex items-center justify-between space-x-3 relative overflow-hidden group/kredit">
                                    <div class="absolute -right-8 -top-8 w-24 h-24 bg-purple-500/20 rounded-full blur-xl pointer-events-none"></div>
                                    <div class="flex items-center space-x-3 min-w-0 flex-1">
                                        <div class="w-10 h-10 rounded-xl bg-purple-500/25 border border-purple-400/60 flex items-center justify-center shrink-0 shadow-[0_0_10px_rgba(168,85,247,0.4)]">
                                            <i class="bi bi-credit-card-2-front-fill text-purple-300 text-lg"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center space-x-2">
                                                <h4 class="text-xs font-black text-purple-100 uppercase tracking-wider">KREDIT (FINANCE)</h4>
                                                <span class="text-[9px] font-extrabold text-purple-300 bg-purple-950/80 border border-purple-500/40 px-1.5 py-0.2 rounded">Utama</span>
                                            </div>
                                            <div class="text-xs font-bold text-slate-200 mt-1">
                                                <strong class="text-purple-300 text-sm font-black">{{ $leasingTotal['ach_kredit'] }}</strong> / {{ $leasingTotal['target_kredit'] }} Unit
                                                <span class="text-[10px] text-purple-200 font-bold ml-2">Tgt Ratio: {{ $leasingTotal['pct_target']['kredit'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end shrink-0 pl-2">
                                        <span class="px-3 py-1 rounded-lg text-xs font-black bg-purple-500/30 text-purple-100 border border-purple-400/70 shadow-sm mb-1.5">
                                            {{ $leasingTotal['pct_ach']['kredit'] }}
                                        </span>
                                        <div class="w-24 bg-[#02050b] rounded-full h-2 overflow-hidden border border-purple-400/40">
                                            <div class="bg-gradient-to-r from-purple-500 to-pink-400 h-2 rounded-full" style="width: {{ min(100, max(5, (int)filter_var($leasingTotal['pct_ach']['kredit'], FILTER_SANITIZE_NUMBER_INT))) }}%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TIER 2: 5 FINCOY PROVIDER BREAKDOWN (ADIRA, BAF, IMFI, MEGA, SOF) -->
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5">
                                @foreach($fincoyKeys as $fk)
                                    @php
                                        $achVal = $leasingTotal['fincoy_ach'][$fk];
                                        $tgtVal = $leasingTotal['fincoy_target'][$fk];
                                        $pctAchStr = $leasingTotal['pct_ach'][$fk];
                                        $pctTgtStr = $leasingTotal['pct_target'][$fk];
                                        $pctNum = (int)filter_var($pctAchStr, FILTER_SANITIZE_NUMBER_INT);
                                    @endphp
                                    <div class="p-3 rounded-xl border transition bg-[#0e1326]/90 border-teal-500/30 hover:border-teal-400 shadow-md flex flex-col justify-between space-y-2">
                                        <div class="flex items-center justify-between pb-1 border-b border-slate-800/80">
                                            <div class="flex items-center space-x-1.5">
                                                <i class="bi bi-building text-teal-400 text-xs"></i>
                                                <h4 class="text-xs font-black text-teal-200 uppercase">{{ $fk }}</h4>
                                            </div>
                                            <span class="px-2 py-0.5 rounded-md text-[11px] font-black {{ $achVal > 0 ? 'bg-teal-500/20 text-teal-300 border border-teal-500/40' : 'bg-slate-900 text-slate-400 border border-slate-800' }}">
                                                {{ $pctAchStr }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="text-xs font-black text-white">
                                                <strong class="{{ $achVal > 0 ? 'text-teal-300' : 'text-slate-300' }} text-sm">{{ $achVal }}</strong> / {{ $tgtVal }} Unit
                                            </div>
                                            <span class="text-[10px] text-slate-300 font-bold block mt-0.5">Tgt Ratio: {{ $pctTgtStr }}</span>
                                        </div>
                                        <div class="w-full bg-[#04060d] rounded-full h-1.5 overflow-hidden border border-teal-500/30">
                                            <div class="bg-gradient-to-r from-teal-500 to-emerald-400 h-1.5 rounded-full" style="width: {{ min(100, max(5, $pctNum)) }}%;"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- 6 Dealer Cards (Wide Landscape Strips Memanjang ke Kanan) -->
                    @foreach($leasingBranches as $b)
                        @php
                            $cColor = $colorPalette[$b['cabang']] ?? '#e94560';
                            $achPctNum = (int)filter_var($b['pct_ach']['target'], FILTER_SANITIZE_NUMBER_INT);
                        @endphp
                        <div class="dealer-card-root w-full bg-[#080a14]/90 border border-[#e94560]/40 rounded-xl p-4 sm:p-5 shadow-xl relative overflow-hidden hover:border-[#e94560]/80 transition duration-300 group">
                            <div class="absolute -right-12 -top-12 w-36 h-36 bg-[#e94560]/10 rounded-full blur-2xl group-hover:bg-[#e94560]/20 transition pointer-events-none"></div>

                            <!-- Header Bar Cabang (Horizontal Layout Memanjang Ke Kanan) -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-[#e94560]/30 pb-3 mb-3.5 gap-2.5">
                                <div class="flex items-center space-x-3">
                                    <span class="w-3.5 h-3.5 rounded-full shrink-0" style="background-color: {{ $cColor }}; box-shadow: 0 0 10px {{ $cColor }}b0;"></span>
                                    <h3 class="text-base sm:text-lg font-black text-white uppercase tracking-wide">
                                        {{ $b['cabang'] }}
                                    </h3>
                                    <span class="bg-[#e94560]/15 border border-[#e94560]/50 text-[#f36892] px-3 py-0.5 rounded-full text-xs font-black">DEALER</span>
                                </div>
                                
                                <!-- Summary STU Badge -->
                                <div class="flex items-center space-x-2 bg-[#0e1326] border border-[#e94560]/50 px-3.5 py-1.5 rounded-xl shadow-inner self-start sm:self-auto">
                                    <span class="text-[11px] text-slate-300 uppercase font-extrabold tracking-wider">TOTAL ACH:</span>
                                    <span class="text-sm sm:text-base font-black text-emerald-400">
                                        {{ $b['ach_total'] }}
                                    </span>
                                    <span class="text-xs text-slate-300 font-bold">/ <span class="text-white font-black">{{ $b['target_total'] }}</span> Target</span>
                                    <span class="bg-[#e94560]/20 text-[#f36892] text-xs font-black px-2 py-0.5 rounded-md border border-[#e94560]/40 ml-1">
                                        %ACH: {{ $b['pct_ach']['target'] }}
                                    </span>
                                </div>
                            </div>

                            <!-- 2-TIER GRID FOR DEALER CABANG: TIER 1 (CASH & KREDIT) + TIER 2 (5 FINCOY) -->
                            <div class="space-y-3">
                                <!-- TIER 1 (CABANG): CASH & KREDIT -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <!-- CASH CABANG -->
                                    <div class="p-3 rounded-xl border transition bg-gradient-to-r from-[#0c2340] via-[#09182d] to-[#040914] border-[1.5px] border-cyan-400/80 shadow-[0_0_12px_rgba(34,211,238,0.2)] hover:border-cyan-300 flex items-center justify-between space-x-3 relative overflow-hidden group/cash">
                                        <div class="flex items-center space-x-2.5 min-w-0 flex-1">
                                            <div class="w-8 h-8 rounded-lg bg-cyan-500/25 border border-cyan-400/60 flex items-center justify-center shrink-0 shadow-[0_0_6px_rgba(34,211,238,0.3)]">
                                                <i class="bi bi-cash-stack text-cyan-300 text-sm"></i>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center space-x-1.5">
                                                    <h4 class="text-xs font-black text-cyan-100 uppercase truncate">CASH</h4>
                                                    <span class="text-[8.5px] font-extrabold text-cyan-300">Metode Utama</span>
                                                </div>
                                                <div class="text-xs font-bold text-slate-200 mt-0.5">
                                                    <strong class="text-cyan-300 text-sm font-black">{{ $b['ach_cash'] }}</strong> / {{ $b['target_cash'] }} Unit
                                                    <span class="text-[10px] text-cyan-200 font-bold ml-2">Tgt Ratio: {{ $b['pct_target']['cash'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex flex-col items-end shrink-0 pl-2">
                                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-black bg-cyan-500/30 text-cyan-100 border border-cyan-400/70 shadow-sm mb-1">
                                                {{ $b['pct_ach']['cash'] }}
                                            </span>
                                            <div class="w-20 bg-[#02050b] rounded-full h-1.5 overflow-hidden border border-cyan-400/40">
                                                <div class="bg-gradient-to-r from-cyan-500 to-emerald-400 h-1.5 rounded-full" style="width: {{ min(100, max(5, (int)filter_var($b['pct_ach']['cash'], FILTER_SANITIZE_NUMBER_INT))) }}%;"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- KREDIT CABANG -->
                                    <div class="p-3 rounded-xl border transition bg-gradient-to-r from-[#270e38] via-[#1a0826] to-[#040914] border-[1.5px] border-purple-400/80 shadow-[0_0_12px_rgba(168,85,247,0.2)] hover:border-purple-300 flex items-center justify-between space-x-3 relative overflow-hidden group/kredit">
                                        <div class="flex items-center space-x-2.5 min-w-0 flex-1">
                                            <div class="w-8 h-8 rounded-lg bg-purple-500/25 border border-purple-400/60 flex items-center justify-center shrink-0 shadow-[0_0_6px_rgba(168,85,247,0.3)]">
                                                <i class="bi bi-credit-card-2-front-fill text-purple-300 text-sm"></i>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center space-x-1.5">
                                                    <h4 class="text-xs font-black text-purple-100 uppercase truncate">KREDIT</h4>
                                                    <span class="text-[8.5px] font-extrabold text-purple-300">Metode Utama</span>
                                                </div>
                                                <div class="text-xs font-bold text-slate-200 mt-0.5">
                                                    <strong class="text-purple-300 text-sm font-black">{{ $b['ach_kredit'] }}</strong> / {{ $b['target_kredit'] }} Unit
                                                    <span class="text-[10px] text-purple-200 font-bold ml-2">Tgt Ratio: {{ $b['pct_target']['kredit'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex flex-col items-end shrink-0 pl-2">
                                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-black bg-purple-500/30 text-purple-100 border border-purple-400/70 shadow-sm mb-1">
                                                {{ $b['pct_ach']['kredit'] }}
                                            </span>
                                            <div class="w-20 bg-[#02050b] rounded-full h-1.5 overflow-hidden border border-purple-400/40">
                                                <div class="bg-gradient-to-r from-purple-500 to-pink-400 h-1.5 rounded-full" style="width: {{ min(100, max(5, (int)filter_var($b['pct_ach']['kredit'], FILTER_SANITIZE_NUMBER_INT))) }}%;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- TIER 2 (CABANG): 5 FINCOY ITEMS -->
                                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5">
                                    @foreach($fincoyKeys as $fk)
                                        @php
                                            $achVal = $b['fincoy_ach'][$fk];
                                            $tgtVal = $b['fincoy_target'][$fk];
                                            $pctAchStr = $b['pct_ach'][$fk];
                                            $pctTgtStr = $b['pct_target'][$fk];
                                            $pctNum = (int)filter_var($pctAchStr, FILTER_SANITIZE_NUMBER_INT);
                                        @endphp
                                        <div class="p-2.5 rounded-xl border transition bg-[#0e1326]/90 border-[#e94560]/30 hover:border-[#e94560]/70 shadow-md flex flex-col justify-between space-y-1.5">
                                            <div class="flex items-center justify-between pb-1 border-b border-slate-800/80">
                                                <div class="flex items-center space-x-1.5 min-w-0">
                                                    <i class="bi bi-building text-teal-400 text-xs shrink-0"></i>
                                                    <h4 class="text-xs font-black text-slate-100 uppercase truncate">{{ $fk }}</h4>
                                                </div>
                                                <span class="px-1.5 py-0.2 rounded-md text-[10.5px] font-black shrink-0 {{ $achVal > 0 ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : 'bg-slate-900 text-slate-400 border border-slate-800' }}">
                                                    {{ $pctAchStr }}
                                                </span>
                                            </div>
                                            <div>
                                                <div class="text-xs font-black text-white">
                                                    <strong class="{{ $achVal > 0 ? 'text-teal-300' : 'text-slate-300' }} text-sm">{{ $achVal }}</strong> / {{ $tgtVal }} Unit
                                                </div>
                                                <span class="text-[9.5px] text-slate-300 font-bold block mt-0.5">Tgt Ratio: {{ $pctTgtStr }}</span>
                                            </div>
                                            <div class="w-full bg-[#04060d] rounded-full h-1.5 overflow-hidden border border-[#e94560]/30">
                                                <div class="bg-gradient-to-r from-[#e94560] via-amber-400 to-emerald-400 h-1.5 rounded-full" style="width: {{ min(100, max(5, $pctNum)) }}%;"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>

            </div>

            <!-- ANALISA STU JENIS PENJUALAN SECTION (GRAFIK & VISUALISASI INTERAKTIF CHART.JS) -->
            @php
                $stuJenisList = [
                    'PEKANBARU' => [
                        'acv_pct' => 16, 'stu_total' => 14,
                        'counter' => ['pct' => 29, 'stu' => 4, 'sdm' => 3, 'avg' => 1],
                        'salesman' => ['pct' => 0, 'stu' => 0, 'sdm' => 2, 'avg' => 0],
                        'digital' => ['pct' => 0, 'stu' => 0, 'sdm' => 0, 'avg' => 0],
                        'kds' => ['pct' => 0, 'stu' => 0],
                        'broker' => ['pct' => 71, 'stu' => 10],
                    ],
                    'SEI PAGAR' => [
                        'acv_pct' => 23, 'stu_total' => 16,
                        'counter' => ['pct' => 0, 'stu' => 0, 'sdm' => 0, 'avg' => 0],
                        'salesman' => ['pct' => 56, 'stu' => 9, 'sdm' => 7, 'avg' => 1],
                        'digital' => ['pct' => 0, 'stu' => 0, 'sdm' => 0, 'avg' => 0],
                        'kds' => ['pct' => 0, 'stu' => 0],
                        'broker' => ['pct' => 44, 'stu' => 7],
                    ],
                    'AIR MOLEK' => [
                        'acv_pct' => 29, 'stu_total' => 31,
                        'counter' => ['pct' => 29, 'stu' => 9, 'sdm' => 4, 'avg' => 2],
                        'salesman' => ['pct' => 71, 'stu' => 22, 'sdm' => 21, 'avg' => 1],
                        'digital' => ['pct' => 0, 'stu' => 0, 'sdm' => 0, 'avg' => 0],
                        'kds' => ['pct' => 0, 'stu' => 0],
                        'broker' => ['pct' => 0, 'stu' => 0],
                    ],
                    'SOREK' => [
                        'acv_pct' => 23, 'stu_total' => 40,
                        'counter' => ['pct' => 40, 'stu' => 16, 'sdm' => 3, 'avg' => 5],
                        'salesman' => ['pct' => 53, 'stu' => 21, 'sdm' => 21, 'avg' => 1],
                        'digital' => ['pct' => 3, 'stu' => 1, 'sdm' => 3, 'avg' => 0],
                        'kds' => ['pct' => 5, 'stu' => 2],
                        'broker' => ['pct' => 0, 'stu' => 0],
                    ],
                    'KANDIS' => [
                        'acv_pct' => 20, 'stu_total' => 25,
                        'counter' => ['pct' => 36, 'stu' => 9, 'sdm' => 2, 'avg' => 5],
                        'salesman' => ['pct' => 64, 'stu' => 16, 'sdm' => 19, 'avg' => 1],
                        'digital' => ['pct' => 0, 'stu' => 0, 'sdm' => 2, 'avg' => 0],
                        'kds' => ['pct' => 0, 'stu' => 0],
                        'broker' => ['pct' => 0, 'stu' => 0],
                    ],
                    'MEDAN' => [
                        'acv_pct' => 27, 'stu_total' => 37,
                        'counter' => ['pct' => 32, 'stu' => 12, 'sdm' => 1, 'avg' => 12],
                        'salesman' => ['pct' => 41, 'stu' => 15, 'sdm' => 8, 'avg' => 2],
                        'digital' => ['pct' => 0, 'stu' => 0, 'sdm' => 0, 'avg' => 0],
                        'kds' => ['pct' => 0, 'stu' => 0],
                        'broker' => ['pct' => 27, 'stu' => 10],
                    ],
                ];

                $stuJenisBranches = [];
                $totStuVal = 0;
                $totCounterStu = 0; $totCounterSdm = 13;
                $totSalesmanStu = 0; $totSalesmanSdm = 78;
                $totDigitalStu = 0; $totDigitalSdm = 5;
                $totKdsStu = 0;
                $totBrokerStu = 0;

                foreach ($cabangs as $c) {
                    $cName = strtoupper(trim($c->nama));
                    $data = $stuJenisList[$cName] ?? [
                        'acv_pct' => 20, 'stu_total' => (int)$c->acv,
                        'counter' => ['pct' => 30, 'stu' => (int)round($c->acv * 0.3), 'sdm' => 2, 'avg' => 1],
                        'salesman' => ['pct' => 50, 'stu' => (int)round($c->acv * 0.5), 'sdm' => 10, 'avg' => 1],
                        'digital' => ['pct' => 0, 'stu' => 0, 'sdm' => 0, 'avg' => 0],
                        'kds' => ['pct' => 0, 'stu' => 0],
                        'broker' => ['pct' => 20, 'stu' => (int)round($c->acv * 0.2)],
                    ];

                    $stuTotal = $c->acv > 0 ? (int)$c->acv : $data['stu_total'];
                    $minTgt = $c->target_reguler > 0 ? (int)$c->target_reguler : ($c->target_tantangan > 0 ? (int)$c->target_tantangan : 100);
                    $acvPct = $minTgt > 0 ? round(($stuTotal / $minTgt) * 100) : $data['acv_pct'];

                    $stuJenisBranches[] = [
                        'nama' => strtoupper($c->nama),
                        'acv_pct' => $acvPct,
                        'stu_total' => $stuTotal,
                        'counter' => $data['counter'],
                        'salesman' => $data['salesman'],
                        'digital' => $data['digital'],
                        'kds' => $data['kds'],
                        'broker' => $data['broker'],
                    ];

                    $totStuVal += $stuTotal;
                    $totCounterStu += $data['counter']['stu'];
                    $totSalesmanStu += $data['salesman']['stu'];
                    $totDigitalStu += $data['digital']['stu'];
                    $totKdsStu += $data['kds']['stu'];
                    $totBrokerStu += $data['broker']['stu'];
                }

                $totCounterPct = $totStuVal > 0 ? round(($totCounterStu / $totStuVal) * 100) : 31;
                $totSalesmanPct = $totStuVal > 0 ? round(($totSalesmanStu / $totStuVal) * 100) : 51;
                $totDigitalPct = $totStuVal > 0 ? round(($totDigitalStu / $totStuVal) * 100) : 1;
                $totKdsPct = $totStuVal > 0 ? round(($totKdsStu / $totStuVal) * 100) : 1;
                $totBrokerPct = $totStuVal > 0 ? round(($totBrokerStu / $totStuVal) * 100) : 17;

                $totCounterAvg = $totCounterSdm > 0 ? round($totCounterStu / $totCounterSdm) : 25;
                $totSalesmanAvg = $totSalesmanSdm > 0 ? round($totSalesmanStu / $totSalesmanSdm) : 6;
                $totDigitalAvg = $totDigitalSdm > 0 ? round($totDigitalStu / $totDigitalSdm) : 0;
            @endphp

            <div class="mt-8 mb-8 bg-[#0e1326] border-[1.5px] border-[#e94560]/70 rounded-3xl p-5 sm:p-6 shadow-2xl relative overflow-hidden backdrop-blur-xl hover:border-[#e94560] transition duration-300">
                
                <!-- Section Header Banner with Mode Controllers -->
                <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-[#e94560]/30 pb-3.5 mb-5 gap-3">
                    <div class="flex items-start space-x-2.5 min-w-0">
                        <span class="w-3 h-3 rounded-full bg-[#e94560] shadow-[0_0_8px_#e94560] shrink-0 mt-1"></span>
                        <div>
                            <h2 class="text-sm sm:text-base lg:text-lg font-black text-[#f36892] uppercase tracking-wider leading-tight">
                                STU (JENIS PENJUALAN) PER TGL {{ strtoupper(\Carbon\Carbon::now()->locale('id')->isoFormat('D-MMM-Y')) }}
                            </h2>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">
                                ANALISA GRAFIK VISUAL CAPAIAN CHANNEL PENJUALAN & PRODUKTIVITAS SDM PER CABANG
                            </p>
                        </div>
                    </div>

                    <!-- Interactive Mode Switcher Buttons -->
                    <div class="flex items-center space-x-1.5 bg-[#04060d] border border-[#e94560]/40 p-1 rounded-2xl shadow-inner self-start sm:self-auto">
                        <button id="btnStuBarView" type="button" class="px-3 py-1.5 rounded-xl text-xs font-black transition-all duration-200 flex items-center space-x-1.5 bg-[#e94560] text-white shadow-lg">
                            <i class="bi bi-bar-chart-line-fill text-sm"></i>
                            <span>Grafik Batang</span>
                        </button>
                        <button id="btnStuPieView" type="button" class="px-3 py-1.5 rounded-xl text-xs font-black transition-all duration-200 flex items-center space-x-1.5 bg-[#080a14] text-slate-400 hover:text-white">
                            <i class="bi bi-pie-chart-fill text-sm"></i>
                            <span>Grafik Donut</span>
                        </button>
                        <button id="btnStuTableView" type="button" class="px-3 py-1.5 rounded-xl text-xs font-black transition-all duration-200 flex items-center space-x-1.5 bg-[#080a14] text-slate-400 hover:text-white">
                            <i class="bi bi-table text-sm"></i>
                            <span>Tabel Data</span>
                        </button>
                    </div>
                </div>

                <!-- TOP SUMMARY METRICS BANNER (4 HIGHLIGHT CARDS) -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                    <!-- Card 1: TOTAL STU -->
                    <div class="bg-[#080a14]/90 border border-amber-500/40 p-3.5 rounded-2xl shadow-md">
                        <div class="flex items-center justify-between text-[10px] text-slate-400 font-extrabold uppercase mb-1">
                            <span>TOTAL STU RESULT</span>
                            <i class="bi bi-trophy-fill text-amber-400"></i>
                        </div>
                        <div class="text-xl sm:text-2xl font-black text-amber-400 leading-none mb-1">
                            {{ $totStuVal }} <span class="text-xs text-slate-300 font-normal">Unit</span>
                        </div>
                        <span class="text-[10px] text-emerald-400 font-bold">ACV {{ round(($totStuVal / max(1, $totalTargetMin)) * 100) }}% Total Target</span>
                    </div>

                    <!-- Card 2: SALESMAN (DOMINAN) -->
                    <div class="bg-[#080a14]/90 border border-blue-500/40 p-3.5 rounded-2xl shadow-md">
                        <div class="flex items-center justify-between text-[10px] text-slate-400 font-extrabold uppercase mb-1">
                            <span>SALESMAN (DOMINAN)</span>
                            <i class="bi bi-people-fill text-blue-400"></i>
                        </div>
                        <div class="text-xl sm:text-2xl font-black text-blue-400 leading-none mb-1">
                            {{ $totSalesmanStu }} <span class="text-xs text-slate-300 font-normal">Unit</span>
                        </div>
                        <span class="text-[10px] text-blue-300 font-bold">{{ $totSalesmanPct }}% Share &bull; {{ $totSalesmanSdm }} SDM</span>
                    </div>

                    <!-- Card 3: SALES COUNTER -->
                    <div class="bg-[#080a14]/90 border border-emerald-500/40 p-3.5 rounded-2xl shadow-md">
                        <div class="flex items-center justify-between text-[10px] text-slate-400 font-extrabold uppercase mb-1">
                            <span>SALES COUNTER</span>
                            <i class="bi bi-[#10b981] bi-person-workspace text-emerald-400"></i>
                        </div>
                        <div class="text-xl sm:text-2xl font-black text-emerald-400 leading-none mb-1">
                            {{ $totCounterStu }} <span class="text-xs text-slate-300 font-normal">Unit</span>
                        </div>
                        <span class="text-[10px] text-emerald-300 font-bold">{{ $totCounterPct }}% Share &bull; {{ $totCounterSdm }} SDM</span>
                    </div>

                    <!-- Card 4: BROKER & KDS -->
                    <div class="bg-[#080a14]/90 border border-purple-500/40 p-3.5 rounded-2xl shadow-md">
                        <div class="flex items-center justify-between text-[10px] text-slate-400 font-extrabold uppercase mb-1">
                            <span>BROKER D1 & KDS D2</span>
                            <i class="bi bi-building text-purple-400"></i>
                        </div>
                        <div class="text-xl sm:text-2xl font-black text-purple-400 leading-none mb-1">
                            {{ $totBrokerStu + $totKdsStu }} <span class="text-xs text-slate-300 font-normal">Unit</span>
                        </div>
                        <span class="text-[10px] text-purple-300 font-bold">{{ $totBrokerPct + $totKdsPct }}% Share (Broker+KDS)</span>
                    </div>
                </div>

                <!-- VIEW MODE 1: GRAFIK BATANG KOMPARATIF (BAR CHART CANVAS) -->
                <div id="stuBarChartView" class="bg-[#080a14]/90 border border-[#e94560]/40 rounded-2xl p-4 sm:p-5 shadow-xl relative min-h-[400px] flex flex-col justify-between">
                    <!-- Top Title Bar -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-800 pb-3 mb-3.5 gap-2">
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 shadow-[0_0_6px_#10b981]"></span>
                            <h3 class="text-sm sm:text-base font-black text-white uppercase tracking-wide">
                                Grafik Komparasi Capaian STU Channel Penjualan per Cabang
                            </h3>
                        </div>
                        <span class="text-xs text-slate-400 font-bold bg-slate-900/80 px-3 py-1 rounded-full border border-slate-800 self-start sm:self-auto">
                            Satuan: Unit Motor (STU)
                        </span>
                    </div>

                    <!-- DEDICATED CENTERED GLOSSY LEGEND BAR (POSISI PRESISI & NYAMAN DILIHAT) -->
                    <div class="flex justify-center my-1">
                        <div class="inline-flex flex-wrap items-center justify-center gap-2 sm:gap-3 bg-[#04060d]/90 border border-slate-800/80 px-3.5 py-2 rounded-2xl sm:rounded-full shadow-lg backdrop-blur-md">
                            <!-- Sales Counter -->
                            <div class="flex items-center space-x-2 px-2.5 py-1 rounded-xl bg-emerald-950/40 border border-emerald-500/30">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#34d399] shadow-[0_0_8px_#34d399] shrink-0"></span>
                                <span class="text-xs font-black text-emerald-200 uppercase">Sales Counter</span>
                                <span class="text-[10px] font-extrabold text-emerald-400 bg-emerald-950/90 px-1.5 py-0.5 rounded-md border border-emerald-500/40">{{ $totCounterStu }} STU</span>
                            </div>

                            <!-- Salesman -->
                            <div class="flex items-center space-x-2 px-2.5 py-1 rounded-xl bg-blue-950/40 border border-blue-500/30">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#60a5fa] shadow-[0_0_8px_#60a5fa] shrink-0"></span>
                                <span class="text-xs font-black text-blue-200 uppercase">Salesman</span>
                                <span class="text-[10px] font-extrabold text-blue-400 bg-blue-950/90 px-1.5 py-0.5 rounded-md border border-blue-500/40">{{ $totSalesmanStu }} STU</span>
                            </div>

                            <!-- Sales Digital -->
                            <div class="flex items-center space-x-2 px-2.5 py-1 rounded-xl bg-rose-950/40 border border-rose-500/30">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#f43f5e] shadow-[0_0_8px_#f43f5e] shrink-0"></span>
                                <span class="text-xs font-black text-rose-200 uppercase">Sales Digital</span>
                                <span class="text-[10px] font-extrabold text-rose-400 bg-rose-950/90 px-1.5 py-0.5 rounded-md border border-rose-500/40">{{ $totDigitalStu }} STU</span>
                            </div>

                            <!-- KDS D2 -->
                            <div class="flex items-center space-x-2 px-2.5 py-1 rounded-xl bg-amber-950/40 border border-amber-500/30">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#fbbf24] shadow-[0_0_8px_#fbbf24] shrink-0"></span>
                                <span class="text-xs font-black text-amber-200 uppercase">KDS (D2)</span>
                                <span class="text-[10px] font-extrabold text-amber-400 bg-amber-950/90 px-1.5 py-0.5 rounded-md border border-amber-500/40">{{ $totKdsStu }} STU</span>
                            </div>

                            <!-- Broker D1 -->
                            <div class="flex items-center space-x-2 px-2.5 py-1 rounded-xl bg-purple-950/40 border border-purple-500/30">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#c084fc] shadow-[0_0_8px_#c084fc] shrink-0"></span>
                                <span class="text-xs font-black text-purple-200 uppercase">Broker (D1)</span>
                                <span class="text-[10px] font-extrabold text-purple-400 bg-purple-950/90 px-1.5 py-0.5 rounded-md border border-purple-500/40">{{ $totBrokerStu }} STU</span>
                            </div>
                        </div>
                    </div>

                    <div class="relative w-full h-[320px] sm:h-[350px]">
                        <canvas id="stuJenisBarCanvas"></canvas>
                    </div>
                </div>

                <!-- VIEW MODE 2: GRAFIK DONUT DISTRIBUSI CHANNEL (DOUGHNUT CHART CANVAS) -->
                <div id="stuPieChartView" class="hidden bg-[#080a14]/90 border border-[#e94560]/40 rounded-2xl p-4 sm:p-5 shadow-xl relative min-h-[360px] flex flex-col justify-between">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-400 shadow-[0_0_6px_#3b82f6]"></span>
                            <h3 class="text-sm sm:text-base font-black text-white uppercase tracking-wide">
                                Grafik Distribusi Persentase Share Channel Penjualan Total
                            </h3>
                        </div>
                        <span class="text-xs text-slate-400 font-bold">Total Share: 100% (163 STU)</span>
                    </div>

                    <div class="relative w-full h-[300px] sm:h-[340px] flex items-center justify-center">
                        <canvas id="stuJenisPieCanvas"></canvas>
                    </div>
                </div>

                <!-- VIEW MODE 3: TABEL DATA DETAIL (EXACT MATCH USER TABLE) -->
                <div id="stuTableView" class="hidden overflow-x-auto rounded-2xl border border-[#e94560]/40 shadow-xl bg-[#080a14]/90">
                    <table class="w-full text-xs sm:text-sm text-center border-collapse whitespace-nowrap">
                        <thead>
                            <tr class="bg-gradient-to-r from-amber-700 via-yellow-600 to-amber-700 text-slate-950 font-black text-sm uppercase tracking-wider">
                                <th colspan="6" class="py-2.5 px-4 border-b border-amber-500 shadow-md">
                                    STU (JENIS PENJUALAN) PER TGL {{ strtoupper(\Carbon\Carbon::now()->locale('id')->isoFormat('D-MMM-Y')) }}
                                </th>
                            </tr>
                            <tr class="text-white font-extrabold uppercase text-xs tracking-wide">
                                <th class="p-3 border border-slate-800 bg-purple-950/80 text-purple-200 w-44 text-left">CABANG / ACV / STU</th>
                                <th class="p-3 border border-slate-800 bg-emerald-950/80 text-emerald-300">SALES COUNTER</th>
                                <th class="p-3 border border-slate-800 bg-emerald-950/80 text-emerald-300">SALESMAN</th>
                                <th class="p-3 border border-slate-800 bg-emerald-950/80 text-emerald-300">SALES DIGITAL</th>
                                <th class="p-3 border border-slate-800 bg-emerald-950/80 text-emerald-300">KDS (D2)</th>
                                <th class="p-3 border border-slate-800 bg-emerald-950/80 text-emerald-300">BROKER (D1)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80 text-slate-200 font-medium">
                            @foreach($stuJenisBranches as $b)
                                @php
                                    $bColor = $colorPalette[$b['nama']] ?? '#e94560';
                                @endphp
                                <tr class="hover:bg-slate-900/60 transition duration-150">
                                    <td class="p-3 border border-slate-800/80 bg-purple-950/30 text-left">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $bColor }}; box-shadow: 0 0 6px {{ $bColor }}b0;"></span>
                                            <span class="font-black text-sm text-white uppercase">{{ $b['nama'] }}</span>
                                        </div>
                                        <div class="text-[11px] text-purple-300 font-bold">
                                            acv {{ $b['acv_pct'] }}% <span class="text-white font-extrabold ml-1">STU = {{ $b['stu_total'] }}</span>
                                        </div>
                                    </td>
                                    <td class="p-3 border border-slate-800/80 bg-slate-950/40">
                                        <div class="text-base font-black text-white mb-1">{{ $b['counter']['pct'] }}%</div>
                                        <div class="text-[10.5px] text-slate-300 leading-snug font-semibold">
                                            <div>STU = <strong class="text-emerald-400 font-black">{{ $b['counter']['stu'] }}</strong></div>
                                            <div>sdm {{ $b['counter']['sdm'] }} org</div>
                                            <div>avg {{ $b['counter']['avg'] }} unit/org</div>
                                        </div>
                                    </td>
                                    <td class="p-3 border border-slate-800/80 bg-slate-950/40">
                                        <div class="text-base font-black text-white mb-1">{{ $b['salesman']['pct'] }}%</div>
                                        <div class="text-[10.5px] text-slate-300 leading-snug font-semibold">
                                            <div>STU = <strong class="text-emerald-400 font-black">{{ $b['salesman']['stu'] }}</strong></div>
                                            <div>sdm {{ $b['salesman']['sdm'] }} org</div>
                                            <div>avg {{ $b['salesman']['avg'] }} unit/org</div>
                                        </div>
                                    </td>
                                    <td class="p-3 border border-slate-800/80 bg-slate-950/40">
                                        <div class="text-base font-black text-white mb-1">{{ $b['digital']['pct'] }}%</div>
                                        <div class="text-[10.5px] text-slate-300 leading-snug font-semibold">
                                            <div>STU = <strong class="text-emerald-400 font-black">{{ $b['digital']['stu'] }}</strong></div>
                                            <div>sdm {{ $b['digital']['sdm'] }} org</div>
                                            <div>avg {{ $b['digital']['avg'] }} unit/org</div>
                                        </div>
                                    </td>
                                    <td class="p-3 border border-slate-800/80 bg-slate-950/40 align-middle">
                                        <div class="text-base font-black text-white mb-1">{{ $b['kds']['pct'] }}%</div>
                                        <div class="text-[11px] text-slate-300 font-bold">
                                            STU = <strong class="text-emerald-400 font-black">{{ $b['kds']['stu'] }}</strong>
                                        </div>
                                    </td>
                                    <td class="p-3 border border-slate-800/80 bg-slate-950/40 align-middle">
                                        <div class="text-base font-black text-white mb-1">{{ $b['broker']['pct'] }}%</div>
                                        <div class="text-[11px] text-slate-300 font-bold">
                                            STU = <strong class="text-emerald-400 font-black">{{ $b['broker']['stu'] }}</strong>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gradient-to-r from-purple-950 via-slate-900 to-purple-950 text-white font-black border-t-2 border-amber-500/80">
                                <td class="p-3 border border-slate-800 text-left bg-purple-950">
                                    <div class="text-sm font-black text-yellow-400 uppercase">TOTAL</div>
                                    <div class="text-xs text-purple-200">
                                        acv {{ round(($totStuVal / max(1, $totalTargetMin)) * 100) }}% <span class="text-white font-extrabold ml-1">STU = {{ $totStuVal }}</span>
                                    </div>
                                </td>
                                <td class="p-3 border border-slate-800 bg-slate-950/80">
                                    <div class="text-base font-black text-yellow-400 mb-1">{{ $totCounterPct }}%</div>
                                    <div class="text-[11px] text-slate-200 leading-snug font-bold">
                                        <div>STU = <strong class="text-emerald-400 font-black">{{ $totCounterStu }}</strong></div>
                                        <div>sdm {{ $totCounterSdm }} org</div>
                                        <div>avg {{ $totCounterAvg }} unit/org</div>
                                    </div>
                                </td>
                                <td class="p-3 border border-slate-800 bg-slate-950/80">
                                    <div class="text-base font-black text-yellow-400 mb-1">{{ $totSalesmanPct }}%</div>
                                    <div class="text-[11px] text-slate-200 leading-snug font-bold">
                                        <div>STU = <strong class="text-emerald-400 font-black">{{ $totSalesmanStu }}</strong></div>
                                        <div>sdm {{ $totSalesmanSdm }} org</div>
                                        <div>avg {{ $totSalesmanAvg }} unit/org</div>
                                    </div>
                                </td>
                                <td class="p-3 border border-slate-800 bg-slate-950/80">
                                    <div class="text-base font-black text-yellow-400 mb-1">{{ $totDigitalPct }}%</div>
                                    <div class="text-[11px] text-slate-200 leading-snug font-bold">
                                        <div>STU = <strong class="text-emerald-400 font-black">{{ $totDigitalStu }}</strong></div>
                                        <div>sdm {{ $totDigitalSdm }} org</div>
                                        <div>avg {{ $totDigitalAvg }} unit/org</div>
                                    </div>
                                </td>
                                <td class="p-3 border border-slate-800 bg-slate-950/80 align-middle">
                                    <div class="text-base font-black text-yellow-400 mb-1">{{ $totKdsPct }}%</div>
                                    <div class="text-xs text-slate-200 font-extrabold">
                                        STU = <strong class="text-emerald-400 font-black">{{ $totKdsStu }}</strong>
                                    </div>
                                </td>
                                <td class="p-3 border border-slate-800 bg-slate-950/80 align-middle">
                                    <div class="text-base font-black text-yellow-400 mb-1">{{ $totBrokerPct }}%</div>
                                    <div class="text-xs text-slate-200 font-extrabold">
                                        STU = <strong class="text-emerald-400 font-black">{{ $totBrokerStu }}</strong>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>

            <!-- JAVASCRIPT FOR STU JENIS PENJUALAN INTERACTIVE CHARTS -->
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const btnBar = document.getElementById('btnStuBarView');
                const btnPie = document.getElementById('btnStuPieView');
                const btnTable = document.getElementById('btnStuTableView');
                
                const viewBar = document.getElementById('stuBarChartView');
                const viewPie = document.getElementById('stuPieChartView');
                const viewTable = document.getElementById('stuTableView');

                function setActiveMode(activeBtn, activeView) {
                    [btnBar, btnPie, btnTable].forEach(b => {
                        if (b) {
                            b.classList.remove('bg-[#e94560]', 'text-white', 'shadow-lg');
                            b.classList.add('bg-[#080a14]', 'text-slate-400', 'hover:text-white');
                        }
                    });
                    if (activeBtn) {
                        activeBtn.classList.remove('bg-[#080a14]', 'text-slate-400', 'hover:text-white');
                        activeBtn.classList.add('bg-[#e94560]', 'text-white', 'shadow-lg');
                    }

                    [viewBar, viewPie, viewTable].forEach(v => {
                        if (v) v.classList.add('hidden');
                    });
                    if (activeView) activeView.classList.remove('hidden');
                }

                if (btnBar) btnBar.addEventListener('click', () => setActiveMode(btnBar, viewBar));
                if (btnPie) btnPie.addEventListener('click', () => setActiveMode(btnPie, viewPie));
                if (btnTable) btnTable.addEventListener('click', () => setActiveMode(btnTable, viewTable));

                // Dynamic Data extraction from Blade
                const branchData = @json($stuJenisBranches);
                const chartLabels = branchData.map(b => b.nama);
                const dataCounter = branchData.map(b => b.counter ? b.counter.stu : 0);
                const dataSalesman = branchData.map(b => b.salesman ? b.salesman.stu : 0);
                const dataDigital = branchData.map(b => b.digital ? b.digital.stu : 0);
                const dataKds = branchData.map(b => b.kds ? b.kds.stu : 0);
                const dataBroker = branchData.map(b => b.broker ? b.broker.stu : 0);
                const totalsPerBranch = branchData.map(b => b.stu_total || 0);

                // 1. Chart.js Bar Chart Initialization with Executive Styling & Canvas Gradients
                const ctxBar = document.getElementById('stuJenisBarCanvas');
                if (ctxBar) {
                    const ctx = ctxBar.getContext('2d');
                    
                    // Create linear gradients for professional neon aesthetics
                    const gradCounter = ctx.createLinearGradient(0, 0, 0, 320);
                    gradCounter.addColorStop(0, '#34d399');
                    gradCounter.addColorStop(1, '#059669');

                    const gradSalesman = ctx.createLinearGradient(0, 0, 0, 320);
                    gradSalesman.addColorStop(0, '#60a5fa');
                    gradSalesman.addColorStop(1, '#1d4ed8');

                    const gradDigital = ctx.createLinearGradient(0, 0, 0, 320);
                    gradDigital.addColorStop(0, '#fb7185');
                    gradDigital.addColorStop(1, '#e11d48');

                    const gradKds = ctx.createLinearGradient(0, 0, 0, 320);
                    gradKds.addColorStop(0, '#fbbf24');
                    gradKds.addColorStop(1, '#d97706');

                    const gradBroker = ctx.createLinearGradient(0, 0, 0, 320);
                    gradBroker.addColorStop(0, '#c084fc');
                    gradBroker.addColorStop(1, '#7e22ce');

                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: chartLabels,
                            datasets: [
                                {
                                    label: 'Sales Counter',
                                    data: dataCounter,
                                    backgroundColor: gradCounter,
                                    borderColor: 'rgba(52, 211, 153, 0.6)',
                                    borderWidth: 1,
                                    categoryPercentage: 0.65,
                                    barPercentage: 0.75,
                                    borderRadius: { topLeft: 6, topRight: 6, bottomLeft: 4, bottomRight: 4 },
                                },
                                {
                                    label: 'Salesman',
                                    data: dataSalesman,
                                    backgroundColor: gradSalesman,
                                    borderColor: 'rgba(96, 165, 250, 0.6)',
                                    borderWidth: 1,
                                    categoryPercentage: 0.65,
                                    barPercentage: 0.75,
                                    borderRadius: { topLeft: 6, topRight: 6, bottomLeft: 4, bottomRight: 4 },
                                },
                                {
                                    label: 'Sales Digital',
                                    data: dataDigital,
                                    backgroundColor: gradDigital,
                                    borderColor: 'rgba(251, 113, 133, 0.8)',
                                    borderWidth: 1,
                                    categoryPercentage: 0.65,
                                    barPercentage: 0.75,
                                    borderRadius: { topLeft: 6, topRight: 6, bottomLeft: 4, bottomRight: 4 },
                                },
                                {
                                    label: 'KDS (D2)',
                                    data: dataKds,
                                    backgroundColor: gradKds,
                                    borderColor: 'rgba(251, 191, 36, 0.6)',
                                    borderWidth: 1,
                                    categoryPercentage: 0.65,
                                    barPercentage: 0.75,
                                    borderRadius: { topLeft: 6, topRight: 6, bottomLeft: 4, bottomRight: 4 },
                                },
                                {
                                    label: 'Broker (D1)',
                                    data: dataBroker,
                                    backgroundColor: gradBroker,
                                    borderColor: 'rgba(192, 132, 252, 0.6)',
                                    borderWidth: 1,
                                    categoryPercentage: 0.65,
                                    barPercentage: 0.75,
                                    borderRadius: { topLeft: 6, topRight: 6, bottomLeft: 4, bottomRight: 4 },
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            layout: {
                                padding: {
                                    top: 28,
                                    bottom: 10,
                                    left: 10,
                                    right: 10
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                    backgroundColor: 'rgba(8, 10, 20, 0.95)',
                                    titleColor: '#facc15',
                                    bodyColor: '#ffffff',
                                    borderColor: 'rgba(233, 69, 96, 0.5)',
                                    borderWidth: 1.5,
                                    padding: 12,
                                    boxPadding: 6,
                                    usePointStyle: true
                                }
                            },
                            scales: {
                                x: {
                                    stacked: true,
                                    ticks: { color: '#cbd5e1', font: { weight: 'bold', size: 11 } },
                                    grid: { color: 'rgba(255,255,255,0.04)' }
                                },
                                y: {
                                    stacked: true,
                                    suggestedMax: 48,
                                    ticks: { color: '#cbd5e1', font: { weight: 'bold', size: 11 } },
                                    grid: { color: 'rgba(255,255,255,0.06)' }
                                }
                            }
                        },
                        plugins: [{
                            id: 'stuTotalBarLabels',
                            afterDatasetsDraw(chart) {
                                const { ctx } = chart;
                                ctx.save();
                                const totals = totalsPerBranch;
                                
                                // Draw Top Badge Label on top of each bar
                                chart.data.labels.forEach((label, index) => {
                                    const totalVal = totals[index];
                                    if (totalVal > 0) {
                                        let topY = chart.chartArea.bottom;
                                        let barX = 0;
                                        chart.data.datasets.forEach((ds, dsIdx) => {
                                            const meta = chart.getDatasetMeta(dsIdx);
                                            const element = meta.data[index];
                                            if (element) {
                                                barX = element.x;
                                                if (element.y < topY) {
                                                    topY = element.y;
                                                }
                                            }
                                        });

                                        // Draw sleek pill badge background for total
                                        const textStr = totalVal + ' STU';
                                        ctx.font = '900 11.5px sans-serif';
                                        const textWidth = ctx.measureText(textStr).width;
                                        const badgeW = textWidth + 14;
                                        const badgeH = 20;
                                        const badgeX = barX - (badgeW / 2);
                                        const badgeY = topY - badgeH - 6;

                                        // Badge background frame
                                        ctx.fillStyle = 'rgba(8, 10, 20, 0.9)';
                                        ctx.strokeStyle = 'rgba(250, 204, 21, 0.8)';
                                        ctx.lineWidth = 1.2;
                                        
                                        // Round rect badge
                                        ctx.beginPath();
                                        if (ctx.roundRect) {
                                            ctx.roundRect(badgeX, badgeY, badgeW, badgeH, 6);
                                        } else {
                                            ctx.rect(badgeX, badgeY, badgeW, badgeH);
                                        }
                                        ctx.fill();
                                        ctx.stroke();

                                        // Text inside badge
                                        ctx.fillStyle = '#facc15';
                                        ctx.textAlign = 'center';
                                        ctx.textBaseline = 'middle';
                                        ctx.fillText(textStr, barX, badgeY + (badgeH / 2));
                                    }
                                });

                                // Segment numbers inside bar blocks
                                chart.data.datasets.forEach((ds, dsIdx) => {
                                    const meta = chart.getDatasetMeta(dsIdx);
                                    meta.data.forEach((barEl, index) => {
                                        const val = ds.data[index];
                                        const h = Math.abs(barEl.base - barEl.y);
                                        if (val > 0 && h >= 16) {
                                            ctx.font = '900 11px sans-serif';
                                            ctx.fillStyle = '#ffffff';
                                            ctx.textAlign = 'center';
                                            ctx.textBaseline = 'middle';
                                            const centerY = (barEl.base + barEl.y) / 2;
                                            ctx.fillText(val, barEl.x, centerY);
                                        }
                                    });
                                });

                                ctx.restore();
                            }
                        }]
                    });
                }

                // 2. Chart.js Doughnut Chart Initialization
                const ctxPie = document.getElementById('stuJenisPieCanvas');
                if (ctxPie) {
                    const totSalesman = {{ $totSalesmanStu }};
                    const totCounter = {{ $totCounterStu }};
                    const totBroker = {{ $totBrokerStu }};
                    const totKds = {{ $totKdsStu }};
                    const totDigital = {{ $totDigitalStu }};
                    const totAll = {{ max(1, $totStuVal) }};

                    const pctSalesman = Math.round((totSalesman / totAll) * 100);
                    const pctCounter = Math.round((totCounter / totAll) * 100);
                    const pctBroker = Math.round((totBroker / totAll) * 100);
                    const pctKds = Math.round((totKds / totAll) * 100);
                    const pctDigital = Math.round((totDigital / totAll) * 100);

                    new Chart(ctxPie.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: [
                                `Salesman (${pctSalesman}%)`,
                                `Sales Counter (${pctCounter}%)`,
                                `Broker D1 (${pctBroker}%)`,
                                `KDS D2 (${pctKds}%)`,
                                `Sales Digital (${pctDigital}%)`
                            ],
                            datasets: [{
                                data: [totSalesman, totCounter, totBroker, totKds, totDigital],
                                backgroundColor: ['#3b82f6', '#10b981', '#a855f7', '#fbbf24', '#f43f5e'],
                                borderColor: '#080a14',
                                borderWidth: 3
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right',
                                    labels: { color: '#f8fafc', font: { weight: 'bold', size: 12 } }
                                }
                            },
                            cutout: '68%'
                        }
                    });
                }
            });
            </script>

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
    const debounceTimers = {};
    
    inputs.forEach(input => {
        const id = input.getAttribute('data-id');
        
        input.addEventListener('input', function() {
            if (debounceTimers[id]) clearTimeout(debounceTimers[id]);
            debounceTimers[id] = setTimeout(() => {
                updateYtd(input);
            }, 350);
        });

        input.addEventListener('change', function() {
            if (debounceTimers[id]) clearTimeout(debounceTimers[id]);
            updateYtd(input);
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
                // Visual indicator for successful auto-save
                inputEl.classList.add('ring-2', 'ring-emerald-500', 'border-emerald-400');
                setTimeout(() => {
                    inputEl.classList.remove('ring-2', 'ring-emerald-500', 'border-emerald-400');
                }, 800);

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