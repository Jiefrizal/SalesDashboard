@extends('layouts.app')

@section('content')

@php
    $kotaMapping = [
        'Pekanbaru' => 'PEKANBARU',
        'Sei Pagar' => 'KAMPAR',
        'Air Molek' => 'INHU',
        'Sorek' => 'PELALAWAN',
        'Kandis' => 'SIAK',
        'Medan' => 'MEDAN',
    ];

    $totalStock2024 = 0;
    $totalStock2025 = 0;
    $totalStock2026 = 0;

    $dealersStock2024Map = [];
    $dealersStock2025Map = [];
    $dealersStock2026Map = [];

    $grandTotalStock = 0;
    $grandTotalTarget = 0;
    $grandTotalMaxStock = 0;

    foreach($cabangs as $cabang) {
        $stk24 = (int)$cabang->stock_2024;
        $stk25 = (int)$cabang->stock_2025;
        $stk26 = (int)$cabang->stock_2026;
        $totalStock = $stk24 + $stk25 + $stk26;

        $totalStock2024 += $stk24;
        $totalStock2025 += $stk25;
        $totalStock2026 += $stk26;

        if ($stk24 > 0) {
            $dealersStock2024Map[] = $cabang->nama . ' (' . $stk24 . ' Unit)';
        }
        if ($stk25 > 0) {
            $dealersStock2025Map[] = $cabang->nama . ' (' . $stk25 . ' Unit)';
        }
        if ($stk26 > 0) {
            $dealersStock2026Map[] = $cabang->nama . ': ' . $stk26 . ' Unit';
        }

        $targetStu = (int)$cabang->target_reguler;
        $maxStock = (int)round($targetStu * 1.2);

        $grandTotalStock += $totalStock;
        $grandTotalTarget += $targetStu;
        $grandTotalMaxStock += $maxStock;
    }

    $grandRatio = $grandTotalMaxStock > 0 ? ($grandTotalStock / $grandTotalMaxStock) : 0;
@endphp

<!-- Outer Container -->
<div class="bg-gradient-to-br from-blue-950 via-slate-900 to-blue-900 text-white rounded-2xl lg:rounded-3xl p-4 lg:p-6 shadow-2xl border border-blue-900 overflow-hidden relative">

    <!-- Header Section -->
    <header class="bg-gradient-to-r from-blue-900 via-blue-950 to-blue-900 rounded-2xl p-4 lg:p-6 border border-blue-800 shadow-2xl relative overflow-hidden flex flex-col md:flex-row md:items-center md:justify-between">
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-teal-500 rounded-full blur-3xl opacity-20"></div>

        <!-- Left: Yamaha Logo -->
        <div class="flex items-center justify-center md:justify-start z-10">
            <img src="{{ asset('yamaha_logo.png') }}" alt="YAMAHA" class="h-10 lg:h-16 w-auto object-contain drop-shadow-lg">
        </div>

        <!-- Center: Title -->
        <div class="text-center my-3 md:my-0 z-10 flex-1">
            <h1 class="text-xl lg:text-3xl font-black text-white tracking-wider drop-shadow-lg flex items-center justify-center gap-3">
                <i class="bi bi-pie-chart-fill text-teal-400"></i>
                <span>STOK RATIO PER DEALER</span>
            </h1>
            <p class="text-teal-400 font-bold tracking-widest text-[11px] lg:text-xs mt-1 uppercase">
                Per tanggal {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D-MMM-Y') }} &bull; Monitoring Rasio Stok vs Target Maksimal & Detail Stok Per Cabang
            </p>
        </div>

        <!-- Right: Actions -->
        <div class="z-10 flex flex-col items-center md:items-end space-y-2">
            <div class="bg-gradient-to-r from-teal-400 via-emerald-400 to-teal-500 text-blue-950 font-black px-3.5 py-1.5 rounded-xl text-xs shadow-xl border border-teal-300 uppercase tracking-tight flex items-center space-x-1.5 transform hover:scale-105 transition">
                <i class="bi bi-calculator text-sm"></i>
                <span>Rasio Total: {{ number_format($grandRatio, 2, ',', '.') }}</span>
            </div>
        </div>
    </header>

    <!-- Top KPI Cards (4 Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mt-6">
        <!-- Card 1: Total Stok -->
        <div class="bg-slate-900/80 border border-blue-900/60 rounded-2xl p-4 shadow-xl backdrop-blur-md relative overflow-hidden flex flex-col justify-between hover:border-teal-500/50 transition duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Total Stok Unit</p>
                    <p class="text-2xl font-black text-teal-400 mt-1"><span class="counter-animate" data-target="{{ $grandTotalStock }}">{{ number_format($grandTotalStock) }}</span> <span class="text-xs text-slate-400 font-normal">unit</span></p>
                </div>
                <div class="bg-teal-500/10 text-teal-400 p-3 rounded-xl border border-teal-500/20">
                    <i class="bi bi-boxes text-xl"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <span class="text-slate-400 font-medium">Max Stok:</span>
                <span class="font-black px-2.5 py-0.5 rounded-full bg-teal-500/20 text-teal-300 border border-teal-500/30">
                    {{ number_format($grandTotalMaxStock) }} Unit
                </span>
            </div>
        </div>

        <!-- Card 2: Stok 2024 -->
        <div class="bg-slate-900/80 border border-blue-900/60 rounded-2xl p-4 shadow-xl backdrop-blur-md relative overflow-hidden flex flex-col justify-between hover:border-amber-500/50 transition duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Stok 2024</p>
                    <p class="text-2xl font-black text-amber-400 mt-1"><span class="counter-animate" data-target="{{ $totalStock2024 }}">{{ number_format($totalStock2024) }}</span> <span class="text-xs text-slate-400 font-normal">unit</span></p>
                </div>
                <div class="bg-amber-500/10 text-amber-400 p-3 rounded-xl border border-amber-500/20">
                    <i class="bi bi-calendar-check text-xl"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <span class="text-slate-400 font-medium">Proporsi 2024:</span>
                <span class="font-black text-amber-400 bg-amber-500/20 border border-amber-500/30 px-2 py-0.5 rounded-full">
                    {{ $grandTotalStock > 0 ? round(($totalStock2024 / $grandTotalStock) * 100, 1) : 0 }}%
                </span>
            </div>
        </div>

        <!-- Card 3: Stok 2025 -->
        <div class="bg-slate-900/80 border border-blue-900/60 rounded-2xl p-4 shadow-xl backdrop-blur-md relative overflow-hidden flex flex-col justify-between hover:border-amber-500/50 transition duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Stok 2025</p>
                    <p class="text-2xl font-black text-amber-400 mt-1"><span class="counter-animate" data-target="{{ $totalStock2025 }}">{{ number_format($totalStock2025) }}</span> <span class="text-xs text-slate-400 font-normal">unit</span></p>
                </div>
                <div class="bg-amber-500/10 text-amber-400 p-3 rounded-xl border border-amber-500/20">
                    <i class="bi bi-calendar2-range text-xl"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <span class="text-slate-400 font-medium">Proporsi 2025:</span>
                <span class="font-black text-amber-400 bg-amber-500/20 border border-amber-500/30 px-2 py-0.5 rounded-full">
                    {{ $grandTotalStock > 0 ? round(($totalStock2025 / $grandTotalStock) * 100, 1) : 0 }}%
                </span>
            </div>
        </div>

        <!-- Card 4: Stok 2026 -->
        <div class="bg-slate-900/80 border border-blue-900/60 rounded-2xl p-4 shadow-xl backdrop-blur-md relative overflow-hidden flex flex-col justify-between hover:border-blue-500/50 transition duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Stok 2026</p>
                    <p class="text-2xl font-black text-slate-100 mt-1"><span class="counter-animate" data-target="{{ $totalStock2026 }}">{{ number_format($totalStock2026) }}</span> <span class="text-xs text-slate-400 font-normal">unit</span></p>
                </div>
                <div class="bg-blue-500/10 text-blue-400 p-3 rounded-xl border border-blue-500/20">
                    <i class="bi bi-calendar3 text-xl"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <span class="text-slate-400 font-medium">Proporsi 2026:</span>
                <span class="font-black text-slate-100 bg-blue-500/20 border border-blue-500/30 px-2 py-0.5 rounded-full">
                    {{ $grandTotalStock > 0 ? round(($totalStock2026 / $grandTotalStock) * 100, 1) : 0 }}%
                </span>
            </div>
        </div>
    </div>

    <!-- Dealer Frames Grid (STOK RATIO PER DEALER) -->
    <!-- Informasi Stok Ratio Seluruh Dealer (Container Style Sesuai REKAPITULASI STU SELURUH DEALER) -->
    <div class="mt-7 bg-[#0b132b]/95 border-2 border-teal-500/50 rounded-3xl p-6 lg:p-7 shadow-2xl backdrop-blur-md relative overflow-hidden">
        <div class="absolute -right-20 -bottom-20 w-72 h-72 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Top Header Row (Fully Responsive) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-blue-900/80 pb-4 mb-4 gap-3">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="bg-teal-500/20 text-teal-400 p-2 sm:p-2.5 rounded-xl border border-teal-500/40 flex items-center justify-center shrink-0 shadow-lg">
                    <i class="bi bi-pie-chart-fill text-lg sm:text-xl lg:text-2xl leading-none"></i>
                </div>
                <div>
                    <h2 class="text-sm sm:text-base lg:text-lg font-black text-white uppercase tracking-wider leading-snug">MONITORING STOK RATIO PER DEALER CABANG</h2>
                    <p class="text-[11px] sm:text-xs text-teal-400 font-bold leading-snug mt-0.5">Detail Perbandingan Total Stok vs Maksimal Stok (120% Target Reguler) & Rincian Kategori Per Dealer</p>
                </div>
            </div>

            <!-- Total Stok Badge -->
            <div class="bg-slate-950/95 border border-teal-500/40 px-4 sm:px-5 py-2 rounded-2xl flex items-center justify-between sm:justify-start space-x-3 shadow-xl shrink-0 w-full sm:w-auto">
                <span class="text-xs text-slate-400 font-extrabold uppercase tracking-wider">Total Stok Unit:</span>
                <span class="text-lg sm:text-xl lg:text-2xl font-black text-teal-400 drop-shadow-md"><span class="counter-animate" data-target="{{ $grandTotalStock }}">{{ number_format($grandTotalStock) }}</span> UNIT</span>
            </div>
        </div>

        <!-- Dealer Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-4">
            @foreach($cabangs as $cabang)
                @php
                    $kotaName = $kotaMapping[$cabang->nama] ?? strtoupper($cabang->nama);
                    $totalStock = $cabang->stock_2024 + $cabang->stock_2025 + $cabang->stock_2026;
                    $targetStu = $cabang->target_reguler;
                    $maxStock = (int)round($targetStu * 1.2);

                    if ($totalStock <= 0) {
                        continue;
                    }

                    $ratio = $maxStock > 0 ? ($totalStock / $maxStock) : 0;
                    $diffStock = $totalStock - $maxStock;

                    $breakdown = $cabang->stock_breakdown ?: [];
                    
                    $standardClasses = [
                        'PREMIUM' => 0,
                        'ATM' => 0,
                        'CLASSY' => 0,
                        'MOPED' => 0,
                        'SPORT' => 0,
                        'AT STD' => 0,
                    ];

                    foreach ($breakdown as $key => $val) {
                        $normalizedKey = strtoupper(trim($key));
                        if (array_key_exists($normalizedKey, $standardClasses)) {
                            $standardClasses[$normalizedKey] += $val;
                        } else {
                            $standardClasses[$normalizedKey] = $val;
                        }
                    }
                    $breakdown = $standardClasses;

                    $order = ['PREMIUM', 'ATM', 'CLASSY', 'MOPED', 'SPORT', 'AT STD'];
                    uksort($breakdown, function($a, $b) use ($order) {
                        $posA = array_search($a, $order);
                        $posB = array_search($b, $order);
                        if ($posA === false) return 1;
                        if ($posB === false) return -1;
                        return $posA - $posB;
                    });
                @endphp

                <!-- Dealer Card -->
                <div class="bg-[#0b132b]/90 border border-blue-900/70 rounded-3xl p-5 shadow-2xl backdrop-blur-md relative overflow-hidden flex flex-col justify-between hover:border-teal-500/60 transition duration-300 h-full">
                    <div>
                        <!-- Header: Name & Ratio Badge -->
                        <div class="flex items-center justify-between border-b border-blue-950 pb-3 mb-4">
                            <div class="flex items-center space-x-2.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-teal-400 shrink-0"></span>
                                <h3 class="text-base lg:text-lg font-black text-white uppercase tracking-wide">{{ $kotaName }}</h3>
                            </div>
                            <div class="bg-blue-950/90 border border-teal-500/40 text-teal-300 text-xs font-black px-3 py-1 rounded-xl shadow-inner">
                                Ratio: {{ number_format($ratio, 2, ',', '.') }}
                            </div>
                        </div>

                        <!-- Progress Section: STOK VS MAKSIMAL -->
                        <div>
                            <div class="flex items-center justify-between text-xs font-extrabold uppercase tracking-wider mb-1.5">
                                <span class="text-slate-400">STOK VS MAKSIMAL</span>
                                <span class="text-white font-black">{{ $totalStock }} / {{ $maxStock }} UNIT</span>
                            </div>
                            <!-- Progress bar -->
                            <div class="w-full h-2 bg-slate-900/90 rounded-full overflow-hidden border border-slate-800">
                                <div class="bg-gradient-to-r from-teal-400 via-emerald-400 to-teal-300 h-full rounded-full transition-all duration-500" style="width: {{ min(100, $maxStock > 0 ? ($totalStock / $maxStock) * 100 : 0) }}%"></div>
                            </div>
                        </div>

                        <!-- 3-Column Metrics Box -->
                        <div class="bg-slate-950/60 rounded-2xl p-3 border border-slate-800/80 text-center grid grid-cols-3 gap-2 mt-4 shadow-inner">
                            <div>
                                <p class="text-[9.5px] text-slate-400 font-extrabold uppercase tracking-wider">TARGET STU</p>
                                <p class="text-white font-black text-base mt-0.5">{{ $targetStu }}</p>
                            </div>
                            <div class="border-x border-slate-800/80 px-1">
                                <p class="text-[9.5px] text-slate-400 font-extrabold uppercase tracking-wider">MAX STOK</p>
                                <p class="text-teal-400 font-black text-base mt-0.5">{{ $maxStock }}</p>
                            </div>
                            <div>
                                <p class="text-[9.5px] text-slate-400 font-extrabold uppercase tracking-wider">-/+ STOK</p>
                                <p class="font-black text-base mt-0.5 {{ $diffStock > 0 ? 'text-rose-400' : 'text-emerald-400' }}">
                                    {{ $diffStock > 0 ? '+'.$diffStock : $diffStock }}
                                </p>
                            </div>
                        </div>

                        <!-- UNIT DETAIL Section (Rincian Stok Per Kategori) -->
                        <div class="mt-5">
                            <h4 class="text-[11px] text-slate-400 font-extrabold uppercase tracking-wider mb-3">UNIT DETAIL (STOK PER KATEGORI)</h4>

                            <div class="space-y-2">
                                @if(empty($breakdown))
                                    <div class="text-center py-3 text-slate-500 text-xs font-bold">
                                        Tidak ada data klasifikasi
                                    </div>
                                @else
                                    @foreach($breakdown as $class => $qty)
                                        @if(($qty ?? 0) <= 0)
                                            @continue
                                        @endif
                                        @php
                                            $classPct = $totalStock > 0 ? round(($qty / $totalStock) * 100) : 0;
                                            $imgName = match($class) {
                                                'PREMIUM'  => 'nmax.png',
                                                'ATM'      => 'gear ultima.png',
                                                'CLASSY'   => 'classy.png',
                                                'MOPED'    => 'moped.png',
                                                'SPORT'    => 'sport.png',
                                                'OFF ROAD' => 'wr.png',
                                                default    => 'atm.png'
                                            };
                                        @endphp
                                        <div class="bg-slate-950/50 rounded-xl p-2.5 border border-slate-800/60 flex items-center justify-between transition hover:border-teal-500/50">
                                            <div class="flex items-center space-x-3 min-w-0 flex-1">
                                                <img src="{{ asset($imgName) }}" alt="{{ $class }}" class="h-8 w-8 object-contain shrink-0">
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-white font-extrabold text-xs uppercase leading-tight">{{ $class }}</p>
                                                    <p class="text-slate-300 text-[10.5px] font-bold mt-0.5">{{ $qty }} Unit</p>
                                                </div>
                                            </div>
                                            <span class="bg-slate-900 border border-slate-800 text-slate-200 text-xs font-black px-2.5 py-1 rounded-lg shrink-0 ml-2">
                                                {{ $classPct }}%
                                            </span>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    </div>
        <!-- Grand Total Summary Card -->
        @php
            $grandDiff = $grandTotalStock - $grandTotalMaxStock;
        @endphp
        <div class="bg-gradient-to-r from-blue-950 via-slate-900 to-blue-950 border border-blue-800 rounded-2xl lg:rounded-3xl p-3.5 lg:p-5 shadow-2xl relative overflow-hidden mt-4 lg:mt-6 text-white font-semibold">
            <div class="absolute -left-16 -bottom-16 w-48 h-48 bg-blue-500/10 rounded-full blur-3xl"></div>
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <div class="bg-blue-500/10 p-2.5 rounded-xl text-blue-400 border border-blue-500/20">
                        <i class="bi bi-calculator text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-base lg:text-xl font-extrabold tracking-wider uppercase">GRAND TOTAL STOK RATIO</h4>
                        <p class="text-slate-400 text-xs lg:text-sm mt-0.5">Akumulasi performa rasio stok seluruh dealer</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-2 w-full md:w-auto text-center">
                    <div class="bg-slate-950/40 border border-blue-900/30 rounded-xl p-2.5 lg:p-3 min-w-[80px]">
                        <div class="text-[9.5px] lg:text-[11px] text-slate-400 uppercase font-extrabold tracking-wider">Total Stok</div>
                        <div class="text-sm lg:text-lg font-extrabold text-slate-100 mt-1">{{ $grandTotalStock }}</div>
                    </div>
                    <div class="bg-slate-950/40 border border-blue-900/30 rounded-xl p-2.5 lg:p-3 min-w-[80px]">
                        <div class="text-[9.5px] lg:text-[11px] text-slate-400 uppercase font-extrabold tracking-wider">Total Target</div>
                        <div class="text-sm lg:text-lg font-extrabold text-slate-100 mt-1">{{ $grandTotalTarget }}</div>
                    </div>
                    <div class="bg-slate-950/40 border border-blue-900/30 rounded-xl p-2.5 lg:p-3 min-w-[80px]">
                        <div class="text-[9.5px] lg:text-[11px] text-slate-400 uppercase font-extrabold tracking-wider">Max Stok</div>
                        <div class="text-sm lg:text-lg font-extrabold text-slate-100 mt-1">{{ $grandTotalMaxStock }}</div>
                    </div>
                    <div class="bg-slate-950/40 border border-blue-900/30 rounded-xl p-2.5 lg:p-3 min-w-[80px]">
                        <div class="text-[9.5px] lg:text-[11px] text-slate-400 uppercase font-extrabold tracking-wider">Rasio Total</div>
                        <div class="text-sm lg:text-lg font-extrabold text-blue-300 mt-1">{{ number_format($grandRatio, 2, ',', '.') }}</div>
                    </div>
                    <div class="bg-slate-950/40 border border-blue-900/30 rounded-xl p-2.5 lg:p-3 min-w-[80px] col-span-2 md:col-span-1">
                        <div class="text-[9.5px] lg:text-[11px] text-slate-400 uppercase font-extrabold tracking-wider">Diff Stok</div>
                        <div class="text-sm lg:text-lg font-extrabold mt-1 {{ $grandDiff > 0 ? 'text-rose-400' : 'text-emerald-400' }}">
                            {{ $grandDiff > 0 ? '+' : '' }}{{ $grandDiff }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stock Summary Card (By Year Breakdown) -->
    <div class="mt-6">
        <div class="bg-slate-900/80 border border-blue-900/60 rounded-2xl lg:rounded-3xl p-3.5 lg:p-5 shadow-2xl backdrop-blur-md relative overflow-hidden flex flex-col justify-start hover:border-blue-700/80 transition duration-300">
            <div class="absolute -right-16 -top-16 w-32 h-32 bg-blue-500/10 rounded-full blur-2xl"></div>

            <div class="flex items-center justify-between border-b border-blue-955 pb-2.5 mb-3.5 w-full">
                <h3 class="text-sm lg:text-base font-extrabold text-white tracking-wider uppercase flex items-center space-x-2">
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
                    <span>Ringkasan Stok Unit Per Tahun & Dealer</span>
                </h3>
            </div>
            
            @php
                $textStock2024Dealers = count($dealersStock2024Map) > 0 ? implode(', ', $dealersStock2024Map) : 'Tidak Ada Stok';
                $textStock2025Dealers = count($dealersStock2025Map) > 0 ? implode(', ', $dealersStock2025Map) : 'Tidak Ada Stok';
                $textStock2026Dealers = count($dealersStock2026Map) > 0 ? implode(', ', $dealersStock2026Map) : 'Tidak Ada Stok';
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <!-- Stock 2024 -->
                <div class="bg-slate-955/40 rounded-xl p-3.5 lg:p-4 flex flex-col items-center justify-center text-center border border-blue-900/60 hover:border-amber-500/50 hover:bg-slate-955/60 transition duration-300 shadow-md">
                    <span class="text-xs lg:text-sm font-bold text-amber-400 uppercase tracking-wider">Stok 2024</span>
                    <h3 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-amber-400 leading-none mt-2.5">{{ $totalStock2024 }}</h3>
                    <p class="text-[9px] lg:text-[11px] text-slate-400 font-bold uppercase mt-1.5 tracking-wider">Unit Tersedia</p>
                    
                    <div class="mt-2.5 pt-2 border-t border-slate-800/80 w-full flex items-center justify-center">
                        <span class="text-[10px] sm:text-[11px] text-amber-300 font-extrabold bg-amber-500/20 border border-amber-500/30 px-2.5 py-1 rounded-lg inline-flex items-center justify-center shadow">
                            <i class="bi bi-geo-alt-fill mr-1 text-amber-400"></i>Dealer: {{ $textStock2024Dealers }}
                        </span>
                    </div>
                </div>

                <!-- Stock 2025 -->
                <div class="bg-slate-955/40 rounded-xl p-3.5 lg:p-4 flex flex-col items-center justify-center text-center border border-blue-900/60 hover:border-amber-500/50 hover:bg-slate-955/60 transition duration-300 shadow-md">
                    <span class="text-xs lg:text-sm font-bold text-amber-400 uppercase tracking-wider">Stok 2025</span>
                    <h3 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-amber-400 leading-none mt-2.5">{{ $totalStock2025 }}</h3>
                    <p class="text-[9px] lg:text-[11px] text-slate-400 font-bold uppercase mt-1.5 tracking-wider">Unit Tersedia</p>

                    <div class="mt-2.5 pt-2 border-t border-slate-800/80 w-full flex items-center justify-center">
                        <span class="text-[10px] sm:text-[11px] text-amber-300 font-extrabold bg-amber-500/20 border border-amber-500/30 px-2.5 py-1 rounded-lg inline-flex items-center justify-center shadow">
                            <i class="bi bi-geo-alt-fill mr-1 text-amber-400"></i>Dealer: {{ $textStock2025Dealers }}
                        </span>
                    </div>
                </div>

                <!-- Stock 2026 -->
                <div class="bg-slate-955/40 rounded-xl p-3.5 lg:p-4 flex flex-col items-center justify-center text-center border border-blue-900/60 hover:border-blue-500/50 hover:bg-slate-955/60 transition duration-300 shadow-md">
                    <span class="text-xs lg:text-sm font-bold text-slate-300 uppercase tracking-wider">Stok 2026</span>
                    <h3 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-100 leading-none mt-2.5">{{ $totalStock2026 }}</h3>
                    <p class="text-[9px] lg:text-[11px] text-slate-400 font-bold uppercase mt-1.5 tracking-wider">Unit Tersedia</p>

                    <div class="mt-2.5 pt-2 border-t border-slate-800/80 w-full flex items-center justify-center">
                        <span class="text-[10px] sm:text-[11px] text-slate-300 font-extrabold bg-blue-500/20 border border-blue-500/30 px-2.5 py-1 rounded-lg inline-flex items-center justify-center shadow">
                            <i class="bi bi-geo-alt-fill mr-1 text-blue-400"></i>Dealer: {{ $textStock2026Dealers }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Back Navigation -->
    <div class="mt-6 flex justify-between items-center">
        <a href="{{ url('/') }}" class="inline-flex items-center space-x-2 text-xs font-bold text-gray-300 hover:text-white transition duration-200 bg-slate-900/60 border border-blue-900 hover:border-blue-700 px-4 py-2.5 rounded-xl shadow">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali ke Dashboard</span>
        </a>
    </div>

</div>

@endsection
