@extends('layouts.app')

@section('title', 'Service & Aftersales Performance')

@section('content')
<!-- Outer Glassmorphic Container -->
<div class="bg-gradient-to-br from-blue-950 via-slate-900 to-blue-900 text-white rounded-2xl lg:rounded-3xl p-4 lg:p-6 shadow-2xl border border-blue-900 overflow-hidden relative space-y-6">

    <!-- Page Header (Official Yamaha Design) -->
    <header class="bg-gradient-to-r from-blue-900 via-blue-950 to-blue-900 rounded-2xl p-4 lg:p-6 border border-blue-800 shadow-2xl relative overflow-hidden flex flex-col md:flex-row md:items-center md:justify-between">
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-teal-500 rounded-full blur-3xl opacity-20"></div>

        <!-- Left: Yamaha Logo -->
        <div class="flex items-center justify-center md:justify-start z-10">
            <img src="{{ asset('yamaha_logo.png') }}" alt="YAMAHA" class="h-10 lg:h-16 w-auto object-contain drop-shadow-lg">
        </div>

        <!-- Center: Title -->
        <div class="text-center my-3 md:my-0 z-10 flex-1">
            <h1 class="text-xl lg:text-3xl font-black text-white tracking-wider drop-shadow-lg flex items-center justify-center gap-3">
                <i class="bi bi-tools text-teal-400"></i>
                <span>SERVICE & AFTERSALES PERFORMANCE</span>
            </h1>
            <p class="text-teal-400 font-bold tracking-widest text-[11px] lg:text-xs mt-1 uppercase">
                Monitoring Unit Entry (UE), Service Jasa & Spareparts Per Dealer Cabang
            </p>
        </div>

        <!-- Right: Status Badge -->
        <div class="z-10 flex flex-col items-center md:items-end">
            <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl text-xs font-black bg-teal-500/20 text-teal-300 border border-teal-500/30 uppercase tracking-wider shadow-lg">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 mr-2 animate-pulse"></span>
                Workshop Online &bull; Active
            </span>
        </div>
    </header>

    <!-- Sub-menu Filter Pills Bar -->
    <div class="bg-slate-900/90 border border-blue-900/80 p-3 lg:p-4 rounded-2xl shadow-xl backdrop-blur-md flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('service.index') }}" class="px-4 py-2 rounded-xl text-xs font-black transition flex items-center space-x-2 {{ $activeTab == 'all' ? 'bg-gradient-to-r from-teal-500 to-emerald-500 text-slate-950 shadow-lg' : 'bg-slate-950/80 text-slate-300 border border-slate-800 hover:border-teal-500/50' }}">
                <i class="bi bi-grid-fill"></i>
                <span>Semua Ringkasan</span>
            </a>
            <a href="{{ route('service.index', ['tab' => 'rut-ksg-ue']) }}" class="px-4 py-2 rounded-xl text-xs font-black transition flex items-center space-x-2 {{ $activeTab == 'rut-ksg-ue' ? 'bg-gradient-to-r from-teal-500 to-emerald-500 text-slate-950 shadow-lg' : 'bg-slate-950/80 text-slate-300 border border-slate-800 hover:border-teal-500/50' }}">
                <i class="bi bi-clipboard2-data"></i>
                <span>RUT KSG dan UE</span>
            </a>
            <a href="{{ route('service.index', ['tab' => 'jasa']) }}" class="px-4 py-2 rounded-xl text-xs font-black transition flex items-center space-x-2 {{ $activeTab == 'jasa' ? 'bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 shadow-lg' : 'bg-slate-950/80 text-slate-300 border border-slate-800 hover:border-amber-500/50' }}">
                <i class="bi bi-cash-coin"></i>
                <span>JASA</span>
            </a>
            <a href="{{ route('service.index', ['tab' => 'jasa-unit']) }}" class="px-4 py-2 rounded-xl text-xs font-black transition flex items-center space-x-2 {{ $activeTab == 'jasa-unit' ? 'bg-gradient-to-r from-purple-500 to-indigo-400 text-white shadow-lg' : 'bg-slate-950/80 text-slate-300 border border-slate-800 hover:border-purple-500/50' }}">
                <i class="bi bi-calculator"></i>
                <span>JASA/UNIT</span>
            </a>
            <a href="{{ route('service.index', ['tab' => 'income-bengkel']) }}" class="px-4 py-2 rounded-xl text-xs font-black transition flex items-center space-x-2 {{ $activeTab == 'income-bengkel' ? 'bg-gradient-to-r from-emerald-500 to-teal-400 text-slate-950 shadow-lg' : 'bg-slate-950/80 text-slate-300 border border-slate-800 hover:border-emerald-500/50' }}">
                <i class="bi bi-piggy-bank-fill"></i>
                <span>INCOME BENGKEL</span>
            </a>
        </div>
        <div class="text-xs text-slate-400 font-bold uppercase tracking-wider">
            Filter: <span class="text-yellow-400 font-black">{{ strtoupper(str_replace('-', ' ', $activeTab)) }}</span>
        </div>
    </div>

    <!-- Top KPI Analytics (4 Summary Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <!-- Card 1: Total Unit Entry (UE) -->
        <div class="bg-slate-900/80 border {{ $activeTab == 'rut-ksg-ue' ? 'border-teal-400 ring-2 ring-teal-400/30' : 'border-blue-900/60' }} rounded-2xl p-4 shadow-xl backdrop-blur-md relative overflow-hidden flex flex-col justify-between hover:border-teal-500/50 transition duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Total Unit Entry (UE)</p>
                    <p class="text-2xl font-black text-teal-400 mt-1">{{ number_format($totalUe) }} <span class="text-xs text-slate-400 font-normal">Motor</span></p>
                </div>
                <div class="bg-teal-500/10 text-teal-400 p-3 rounded-xl border border-teal-500/20">
                    <i class="bi bi-wrench-adjustable-circle text-xl"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <span class="text-slate-400 font-medium">Total KSG Terlayani:</span>
                <span class="font-black px-2.5 py-0.5 rounded-full bg-teal-500/20 text-teal-300 border border-teal-500/30">
                    {{ number_format($totalKsg) }} Unit
                </span>
            </div>
        </div>

        <!-- Card 2: Total Service Jasa Revenue -->
        <div class="bg-slate-900/80 border {{ $activeTab == 'jasa' ? 'border-amber-400 ring-2 ring-amber-400/30' : 'border-blue-900/60' }} rounded-2xl p-4 shadow-xl backdrop-blur-md relative overflow-hidden flex flex-col justify-between hover:border-amber-500/50 transition duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Revenue Jasa Service</p>
                    <p class="text-2xl font-black text-amber-400 mt-1">Rp {{ number_format($totalRevenue / 1000000, 1) }}M</p>
                </div>
                <div class="bg-amber-500/10 text-amber-400 p-3 rounded-xl border border-amber-500/20">
                    <i class="bi bi-cash-stack text-xl"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <span class="text-slate-400 font-medium">Estimasi Jasa/UE:</span>
                <span class="font-black text-amber-300 px-2 py-0.5 rounded-full bg-amber-500/20 border border-amber-500/30">
                    Rp 125.000
                </span>
            </div>
        </div>

        <!-- Card 3: Spareparts Revenue -->
        <div class="bg-slate-900/80 border {{ $activeTab == 'jasa-unit' ? 'border-purple-400 ring-2 ring-purple-400/30' : 'border-blue-900/60' }} rounded-2xl p-4 shadow-xl backdrop-blur-md relative overflow-hidden flex flex-col justify-between hover:border-purple-500/50 transition duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Rata-Rata Jasa / Unit</p>
                    <p class="text-2xl font-black text-purple-300 mt-1">Rp {{ number_format($totalUe > 0 ? (int)round($totalRevenue / $totalUe) : 125000) }}</p>
                </div>
                <div class="bg-purple-500/10 text-purple-400 p-3 rounded-xl border border-purple-500/20">
                    <i class="bi bi-calculator text-xl"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <span class="text-slate-400 font-medium">Spareparts (YGP):</span>
                <span class="font-black text-purple-300 px-2.5 py-0.5 rounded-full bg-purple-500/20 border border-purple-500/30">
                    Rp {{ number_format($totalSpareparts / 1000000, 1) }}M
                </span>
            </div>
        </div>

        <!-- Card 4: Net Income Bengkel -->
        <div class="bg-slate-900/80 border {{ $activeTab == 'income-bengkel' ? 'border-emerald-400 ring-2 ring-emerald-400/30' : 'border-blue-900/60' }} rounded-2xl p-4 shadow-xl backdrop-blur-md relative overflow-hidden flex flex-col justify-between hover:border-emerald-500/50 transition duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Net Income Bengkel</p>
                    <p class="text-2xl font-black text-emerald-400 mt-1">Rp {{ number_format($totalIncomeBengkel / 1000000, 1) }}M</p>
                </div>
                <div class="bg-emerald-500/10 text-emerald-400 p-3 rounded-xl border border-emerald-500/20">
                    <i class="bi bi-piggy-bank-fill text-xl"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <span class="text-slate-400 font-medium">Profit Margin:</span>
                <span class="font-black text-emerald-400 bg-emerald-500/20 border border-emerald-500/30 px-2 py-0.5 rounded-full">
                    65% Net Margin
                </span>
            </div>
        </div>
    </div>

    @if($activeTab == 'rut-ksg-ue' || $activeTab == 'all')
        <!-- TABEL MONITORING RUT KSG DAN UNIT ENTRY (Laporan Resmi Spreadsheet) -->
        <div class="bg-[#0b132b]/95 border-2 border-teal-500/50 rounded-3xl p-4 lg:p-6 shadow-2xl backdrop-blur-md space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-blue-900/80 pb-3 gap-2">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-teal-500/20 text-teal-400 flex items-center justify-center border border-teal-500/40 shrink-0 shadow-lg">
                        <i class="bi bi-table text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-base lg:text-lg font-black text-white uppercase tracking-wider">TABEL MONITORING RUT KSG DAN UNIT ENTRY (UE)</h3>
                        <p class="text-xs text-teal-400 font-bold">Laporan Harian Bengkel Resmi Aspacindo (TGL: 5-Aug-26)</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="px-3.5 py-1.5 rounded-xl text-xs font-black bg-amber-400/20 text-amber-300 border border-amber-400/30 uppercase tracking-wider">
                        HARI KERJA: 4
                    </span>
                </div>
            </div>

            <!-- Responsive Table Container -->
            <div class="overflow-x-auto rounded-2xl border border-blue-900/80 shadow-2xl">
                <table class="w-full text-xs text-center text-slate-200 border-collapse">
                    <thead>
                        <!-- Top Header Row -->
                        <tr class="bg-slate-950 text-slate-200 uppercase font-black text-[11px] border-b border-blue-900">
                            <th rowspan="2" class="p-3 border-r border-blue-900 min-w-[110px] text-left">CABANG</th>
                            <th rowspan="2" class="p-3 border-r border-blue-900 min-w-[130px] text-left">NAMA BENGKEL</th>
                            <th rowspan="2" class="p-3 border-r border-blue-900 bg-slate-900 min-w-[60px] text-yellow-400">RUT</th>
                            <th colspan="7" class="p-2 border-r border-blue-900 bg-teal-950/70 text-teal-300 tracking-wider">UNIT ENTRI KSG</th>
                            <th colspan="7" class="p-2 bg-blue-950/70 text-blue-300 tracking-wider">UNIT ENTRI TOTAL</th>
                        </tr>
                        <!-- Sub Header Row -->
                        <tr class="bg-slate-900/90 text-[10px] font-extrabold uppercase border-b border-blue-900">
                            <!-- KSG Subheaders -->
                            <th class="p-2 border-r border-blue-900 bg-amber-400/20 text-amber-300 min-w-[65px]">HARI KERJA</th>
                            <th class="p-2 border-r border-blue-900 min-w-[60px]">Aug-26</th>
                            <th class="p-2 border-r border-blue-900 min-w-[60px]">Jul-26</th>
                            <th class="p-2 border-r border-blue-900 min-w-[70px]">Target AGUSTUS</th>
                            <th class="p-2 border-r border-blue-900 min-w-[65px]">VS LM</th>
                            <th class="p-2 border-r border-blue-900 min-w-[80px]">% VS Target</th>
                            <th class="p-2 border-r border-blue-900 bg-emerald-500/20 text-emerald-300 min-w-[75px]">Proposional Target</th>

                            <!-- TOTAL Subheaders -->
                            <th class="p-2 border-r border-blue-900 bg-amber-400/20 text-amber-300 min-w-[65px]">HARI KERJA</th>
                            <th class="p-2 border-r border-blue-900 min-w-[60px]">Aug-26</th>
                            <th class="p-2 border-r border-blue-900 min-w-[60px]">Jul-26</th>
                            <th class="p-2 border-r border-blue-900 min-w-[70px]">Target AGUSTUS</th>
                            <th class="p-2 border-r border-blue-900 min-w-[65px]">VS LM</th>
                            <th class="p-2 border-r border-blue-900 min-w-[80px]">% VS Target</th>
                            <th class="p-2 bg-emerald-500/20 text-emerald-300 min-w-[75px]">Proposional Target</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-blue-950 font-semibold">
                        @foreach($rutKsgTable as $row)
                            <tr class="{{ $row['is_subtotal'] ? 'bg-slate-950 font-black text-white border-y-2 border-teal-500/40' : 'hover:bg-slate-900/60 transition' }}">
                                <td class="p-2.5 border-r border-blue-950 text-left font-bold text-slate-200">{{ $row['cabang'] }}</td>
                                <td class="p-2.5 border-r border-blue-950 text-left {{ $row['is_subtotal'] ? 'text-teal-300 font-black uppercase' : 'text-slate-300' }}">{{ $row['bengkel'] }}</td>
                                <td class="p-2.5 border-r border-blue-950 {{ $row['rut'] >= 5.0 ? 'bg-rose-500/25 text-rose-200 font-black' : 'text-slate-200' }}">{{ number_format($row['rut'], 1) }}</td>

                                <!-- KSG Data -->
                                <td class="p-2.5 border-r border-blue-950 bg-amber-400/10 text-amber-300 font-bold">{{ $row['ksg']['hk'] }}</td>
                                <td class="p-2.5 border-r border-blue-950 font-bold text-white">{{ $row['ksg']['aug'] }}</td>
                                <td class="p-2.5 border-r border-blue-950 text-slate-400">{{ $row['ksg']['jul'] }}</td>
                                <td class="p-2.5 border-r border-blue-950 text-slate-300">{{ $row['ksg']['target'] }}</td>
                                <td class="p-2.5 border-r border-blue-950 {{ $row['ksg']['vs_lm'] < 1.0 ? 'bg-rose-500/25 text-rose-200 font-black' : 'text-emerald-400 font-bold' }}">
                                    {{ number_format($row['ksg']['vs_lm'] * 100) }}%
                                </td>
                                <td class="p-2.5 border-r border-blue-950 {{ $row['ksg']['vs_target'] < 0.20 ? 'bg-rose-500/25 text-rose-200 font-bold' : 'text-emerald-400 font-bold' }}">
                                    {{ number_format($row['ksg']['vs_target'] * 100, 2) }}%
                                </td>
                                <td class="p-2.5 border-r border-blue-950 bg-emerald-500/10 text-emerald-300 font-bold">{{ $row['ksg']['prop_target'] }}</td>

                                <!-- TOTAL Data -->
                                <td class="p-2.5 border-r border-blue-950 bg-amber-400/10 text-amber-300 font-bold">{{ $row['total']['hk'] }}</td>
                                <td class="p-2.5 border-r border-blue-950 font-bold text-white">{{ $row['total']['aug'] }}</td>
                                <td class="p-2.5 border-r border-blue-950 text-slate-400">{{ $row['total']['jul'] }}</td>
                                <td class="p-2.5 border-r border-blue-950 text-slate-300">{{ $row['total']['target'] }}</td>
                                <td class="p-2.5 border-r border-blue-950 {{ $row['total']['vs_lm'] < 1.0 ? 'bg-rose-500/25 text-rose-200 font-black' : 'text-emerald-400 font-bold' }}">
                                    {{ number_format($row['total']['vs_lm'] * 100) }}%
                                </td>
                                <td class="p-2.5 border-r border-blue-950 {{ $row['total']['vs_target'] < 0.20 ? 'bg-rose-500/25 text-rose-200 font-bold' : 'text-emerald-400 font-bold' }}">
                                    {{ number_format($row['total']['vs_target'] * 100, 2) }}%
                                </td>
                                <td class="p-2.5 bg-emerald-500/10 text-emerald-300 font-bold">{{ $row['total']['prop_target'] }}</td>
                            </tr>
                        @endforeach

                        <!-- GRAND TOTAL ROW -->
                        <tr class="bg-gradient-to-r from-teal-950 via-slate-950 to-blue-950 text-white font-black text-xs border-2 border-teal-400">
                            <td colspan="2" class="p-3 text-left font-black tracking-wider uppercase text-yellow-400">TOTAL KESELURUHAN</td>
                            <td class="p-3 border-r border-blue-950 bg-rose-500/30 text-rose-200 font-black text-sm">{{ number_format($rutKsgGrandTotal['rut'], 1) }}</td>

                            <!-- KSG Grand Total -->
                            <td class="p-3 border-r border-blue-950 bg-amber-400/20 text-amber-300 font-black text-sm">{{ $rutKsgGrandTotal['ksg']['hk'] }}</td>
                            <td class="p-3 border-r border-blue-950 text-teal-300 font-black text-sm">{{ $rutKsgGrandTotal['ksg']['aug'] }}</td>
                            <td class="p-3 border-r border-blue-950 text-slate-300 font-bold">{{ $rutKsgGrandTotal['ksg']['jul'] }}</td>
                            <td class="p-3 border-r border-blue-950 text-white font-black">{{ $rutKsgGrandTotal['ksg']['target'] }}</td>
                            <td class="p-3 border-r border-blue-950 text-emerald-300 font-black">{{ number_format($rutKsgGrandTotal['ksg']['vs_lm'] * 100) }}%</td>
                            <td class="p-3 border-r border-blue-950 text-emerald-300 font-black">{{ number_format($rutKsgGrandTotal['ksg']['vs_target'] * 100, 2) }}%</td>
                            <td class="p-3 border-r border-blue-950 bg-emerald-500/20 text-emerald-300 font-black text-sm">{{ $rutKsgGrandTotal['ksg']['prop_target'] }}</td>

                            <!-- TOTAL Grand Total -->
                            <td class="p-3 border-r border-blue-950 bg-amber-400/20 text-amber-300 font-black text-sm">{{ $rutKsgGrandTotal['total']['hk'] }}</td>
                            <td class="p-3 border-r border-blue-950 text-teal-300 font-black text-sm">{{ $rutKsgGrandTotal['total']['aug'] }}</td>
                            <td class="p-3 border-r border-blue-950 text-slate-300 font-bold">{{ $rutKsgGrandTotal['total']['jul'] }}</td>
                            <td class="p-3 border-r border-blue-950 text-white font-black">{{ $rutKsgGrandTotal['total']['target'] }}</td>
                            <td class="p-3 border-r border-blue-950 text-emerald-300 font-black">{{ number_format($rutKsgGrandTotal['total']['vs_lm'] * 100) }}%</td>
                            <td class="p-3 border-r border-blue-950 bg-rose-500/25 text-rose-200 font-black">{{ number_format($rutKsgGrandTotal['total']['vs_target'] * 100, 2) }}%</td>
                            <td class="p-3 bg-emerald-500/20 text-emerald-300 font-black text-sm">{{ $rutKsgGrandTotal['total']['prop_target'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if($activeTab == 'jasa' || $activeTab == 'all')
        <!-- TABEL MONITORING OMZET JASA MEKANIK (Laporan Resmi Spreadsheet) -->
        <div class="bg-[#0b132b]/95 border-2 border-amber-500/50 rounded-3xl p-4 lg:p-6 shadow-2xl backdrop-blur-md space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-blue-900/80 pb-3 gap-2">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center border border-amber-500/40 shrink-0 shadow-lg">
                        <i class="bi bi-cash-coin text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-base lg:text-lg font-black text-white uppercase tracking-wider">TABEL MONITORING OMZET JASA MEKANIK</h3>
                        <p class="text-xs text-amber-400 font-bold">Laporan Omzet Jasa KSG & KSB Bengkel Resmi Aspacindo (TGL: 5-Aug-26)</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="px-3.5 py-1.5 rounded-xl text-xs font-black bg-amber-400/20 text-amber-300 border border-amber-400/30 uppercase tracking-wider">
                        HARI KERJA: 4
                    </span>
                </div>
            </div>

            <!-- Responsive Table Container -->
            <div class="overflow-x-auto rounded-2xl border border-blue-900/80 shadow-2xl">
                <table class="w-full text-xs text-center text-slate-200 border-collapse">
                    <thead>
                        <!-- Top Header Row -->
                        <tr class="bg-slate-950 text-slate-200 uppercase font-black text-[11px] border-b border-blue-900">
                            <th rowspan="2" class="p-3 border-r border-blue-900 min-w-[110px] text-left">CABANG</th>
                            <th rowspan="2" class="p-3 border-r border-blue-900 min-w-[130px] text-left">NAMA BENGKEL</th>
                            <th class="p-2 border-r border-blue-900 bg-amber-400/20 text-amber-300 tracking-wider min-w-[100px]">HARI KERJA</th>
                            <th colspan="8" class="p-2 bg-blue-950/70 text-amber-300 tracking-wider">JASA MEKANIK TGL, 5-Aug-26</th>
                        </tr>
                        <!-- Sub Header Row -->
                        <tr class="bg-slate-900/90 text-[10px] font-extrabold uppercase border-b border-blue-900">
                            <th class="p-2 border-r border-blue-900 bg-amber-400/20 text-amber-300 min-w-[100px]">4</th>
                            <th class="p-2 border-r border-blue-900 min-w-[90px] text-amber-300">JASA KSG</th>
                            <th class="p-2 border-r border-blue-900 min-w-[95px] text-amber-300">JASA KSB</th>
                            <th class="p-2 border-r border-blue-900 min-w-[95px]">Aug-26</th>
                            <th class="p-2 border-r border-blue-900 min-w-[95px]">Jul-26</th>
                            <th class="p-2 border-r border-blue-900 min-w-[110px]">Target AGUSTUS</th>
                            <th class="p-2 border-r border-blue-900 min-w-[65px]">VS LM</th>
                            <th class="p-2 border-r border-blue-900 min-w-[80px]">% VS Target</th>
                            <th class="p-2 bg-emerald-500/20 text-emerald-300 min-w-[110px]">Proposional Target</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-blue-950 font-semibold">
                        @foreach($jasaMekanikTable as $row)
                            <tr class="{{ $row['is_subtotal'] ? 'bg-slate-950 font-black text-white border-y-2 border-amber-500/40' : 'hover:bg-slate-900/60 transition' }}">
                                <td class="p-2.5 border-r border-blue-950 text-left font-bold text-slate-200">{{ $row['cabang'] }}</td>
                                <td class="p-2.5 border-r border-blue-950 text-left {{ $row['is_subtotal'] ? 'text-amber-300 font-black uppercase' : 'text-slate-300' }}">{{ $row['bengkel'] }}</td>
                                <td class="p-2.5 border-r border-blue-950 bg-amber-400/10 text-amber-300 font-bold text-right px-3">{{ number_format($row['hk']) }}</td>
                                <td class="p-2.5 border-r border-blue-950 text-right px-3 text-slate-300">{{ number_format($row['ksg']) }}</td>
                                <td class="p-2.5 border-r border-blue-950 text-right px-3 text-slate-300">{{ $row['ksb'] > 0 ? number_format($row['ksb']) : '-' }}</td>
                                <td class="p-2.5 border-r border-blue-950 font-bold text-white text-right px-3">{{ number_format($row['aug']) }}</td>
                                <td class="p-2.5 border-r border-blue-950 text-slate-400 text-right px-3">{{ number_format($row['jul']) }}</td>
                                <td class="p-2.5 border-r border-blue-950 text-slate-300 text-right px-3">{{ number_format($row['target']) }}</td>
                                <td class="p-2.5 border-r border-blue-950 {{ $row['vs_lm'] < 1.0 ? 'bg-rose-500/25 text-rose-200 font-black' : 'text-emerald-400 font-bold' }}">
                                    {{ number_format($row['vs_lm'] * 100) }}%
                                </td>
                                <td class="p-2.5 border-r border-blue-950 {{ $row['vs_target'] < 0.20 ? 'bg-rose-500/25 text-rose-200 font-bold' : 'text-emerald-400 font-bold' }}">
                                    {{ number_format($row['vs_target'] * 100, 2) }}%
                                </td>
                                <td class="p-2.5 bg-emerald-500/10 text-emerald-300 font-bold text-right px-3">{{ number_format($row['prop_target']) }}</td>
                            </tr>
                        @endforeach

                        <!-- GRAND TOTAL ROW -->
                        <tr class="bg-gradient-to-r from-amber-950 via-slate-950 to-blue-950 text-white font-black text-xs border-2 border-amber-400">
                            <td colspan="2" class="p-3 text-left font-black tracking-wider uppercase text-amber-400">TOTAL KESELURUHAN</td>
                            <td class="p-3 border-r border-blue-950 bg-amber-400/20 text-amber-300 font-black text-sm text-right px-3">{{ number_format($jasaMekanikGrandTotal['hk']) }}</td>
                            <td class="p-3 border-r border-blue-950 text-amber-300 font-black text-sm text-right px-3">{{ number_format($jasaMekanikGrandTotal['ksg']) }}</td>
                            <td class="p-3 border-r border-blue-950 text-amber-300 font-black text-sm text-right px-3">{{ number_format($jasaMekanikGrandTotal['ksb']) }}</td>
                            <td class="p-3 border-r border-blue-950 text-white font-black text-sm text-right px-3">{{ number_format($jasaMekanikGrandTotal['aug']) }}</td>
                            <td class="p-3 border-r border-blue-950 text-slate-300 font-bold text-right px-3">{{ number_format($jasaMekanikGrandTotal['jul']) }}</td>
                            <td class="p-3 border-r border-blue-950 text-white font-black text-right px-3">{{ number_format($jasaMekanikGrandTotal['target']) }}</td>
                            <td class="p-3 border-r border-blue-950 text-emerald-300 font-black">{{ number_format($jasaMekanikGrandTotal['vs_lm'] * 100) }}%</td>
                            <td class="p-3 border-r border-blue-950 text-emerald-300 font-black">{{ number_format($jasaMekanikGrandTotal['vs_target'] * 100, 1) }}%</td>
                            <td class="p-3 bg-emerald-500/20 text-emerald-300 font-black text-sm text-right px-3">{{ number_format($jasaMekanikGrandTotal['prop_target']) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if($activeTab == 'jasa-unit' || $activeTab == 'all')
        <!-- TABEL MONITORING RASIO JASA PER UNIT (Laporan Resmi Spreadsheet) -->
        <div class="bg-[#0b132b]/95 border-2 border-purple-500/50 rounded-3xl p-4 lg:p-6 shadow-2xl backdrop-blur-md space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-blue-900/80 pb-3 gap-2">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center border border-purple-500/40 shrink-0 shadow-lg">
                        <i class="bi bi-calculator text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-base lg:text-lg font-black text-white uppercase tracking-wider">TABEL MONITORING RASIO JASA PER UNIT ENTRY</h3>
                        <p class="text-xs text-purple-300 font-bold">Laporan Perbandingan Jasa/Unit Agustus vs Juli (Target Minimum: Rp 75.000 / UE)</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="px-3.5 py-1.5 rounded-xl text-xs font-black bg-purple-400/20 text-purple-300 border border-purple-400/30 uppercase tracking-wider">
                        TARGET: 75k / UE
                    </span>
                </div>
            </div>

            <!-- Responsive Table Container -->
            <div class="overflow-x-auto rounded-2xl border border-blue-900/80 shadow-2xl">
                <table class="w-full text-xs text-center text-slate-200 border-collapse">
                    <thead>
                        <tr class="bg-slate-950 text-slate-200 uppercase font-black text-[11px] border-b border-blue-900">
                            <th class="p-3.5 border-r border-blue-900 min-w-[140px] text-left">CABANG</th>
                            <th class="p-3.5 border-r border-blue-900 min-w-[160px] text-left">NAMA BENGKEL</th>
                            <th class="p-3.5 border-r border-blue-900 min-w-[180px] text-purple-300 bg-purple-950/40">
                                JASA / UNIT AGUSTUS <span class="text-rose-400 font-normal ml-1">[Target 75k]</span>
                            </th>
                            <th class="p-3.5 min-w-[180px] text-purple-300 bg-purple-950/40">
                                JASA / UNIT JULI <span class="text-rose-400 font-normal ml-1">(Target 75k)</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-blue-950 font-semibold">
                        @foreach($jasaUnitTable as $row)
                            <tr class="{{ $row['is_subtotal'] ? 'bg-slate-950 font-black text-white border-y-2 border-purple-500/40' : 'hover:bg-slate-900/60 transition' }}">
                                <td class="p-3 border-r border-blue-950 text-left font-bold text-slate-200">{{ $row['cabang'] }}</td>
                                <td class="p-3 border-r border-blue-950 text-left {{ $row['is_subtotal'] ? 'text-purple-300 font-black uppercase' : 'text-slate-300' }}">{{ $row['bengkel'] }}</td>
                                
                                <!-- Agustus Jasa/Unit -->
                                <td class="p-3 border-r border-blue-950 text-right px-4 {{ $row['aug'] < 75000 ? 'bg-rose-500/25 text-rose-200 font-bold' : 'text-emerald-400 font-extrabold' }}">
                                    {{ number_format($row['aug']) }}
                                </td>
                                
                                <!-- Juli Jasa/Unit -->
                                <td class="p-3 text-right px-4 {{ $row['jul'] < 75000 ? 'bg-rose-500/25 text-rose-200 font-bold' : 'text-emerald-400 font-extrabold' }}">
                                    {{ number_format($row['jul']) }}
                                </td>
                            </tr>
                        @endforeach

                        <!-- GRAND TOTAL ROW -->
                        <tr class="bg-gradient-to-r from-purple-950 via-slate-950 to-blue-950 text-white font-black text-xs border-2 border-purple-400">
                            <td colspan="2" class="p-3.5 text-left font-black tracking-wider uppercase text-purple-300">TOTAL KESELURUHAN</td>
                            <td class="p-3.5 border-r border-blue-950 text-right px-4 text-emerald-300 font-black text-sm">
                                {{ number_format($jasaUnitGrandTotal['aug']) }}
                            </td>
                            <td class="p-3.5 text-right px-4 bg-rose-500/25 text-rose-200 font-black text-sm">
                                {{ number_format($jasaUnitGrandTotal['jul']) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if($activeTab == 'income-bengkel' || $activeTab == 'all')
        <!-- TABEL MONITORING INCOME BENGKEL (Laporan Resmi Spreadsheet) -->
        <div class="bg-[#0b132b]/95 border-2 border-emerald-500/50 rounded-3xl p-4 lg:p-6 shadow-2xl backdrop-blur-md space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-blue-900/80 pb-3 gap-2">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/40 shrink-0 shadow-lg">
                        <i class="bi bi-piggy-bank-fill text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-base lg:text-lg font-black text-white uppercase tracking-wider">TABEL MONITORING INCOME BENGKEL & RASIO PER UNIT</h3>
                        <p class="text-xs text-emerald-400 font-bold">Laporan Omzet Spareparts, Income Bengkel & Target Rasio Income/Unit (Target Minimum: Rp 200.000 / UE)</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="px-3.5 py-1.5 rounded-xl text-xs font-black bg-emerald-400/20 text-emerald-300 border border-emerald-400/30 uppercase tracking-wider">
                        TARGET: 200k / UE
                    </span>
                </div>
            </div>

            <!-- Responsive Table Container -->
            <div class="overflow-x-auto rounded-2xl border border-blue-900/80 shadow-2xl">
                <table class="w-full text-xs text-center text-slate-200 border-collapse">
                    <thead>
                        <tr class="bg-slate-950 text-slate-200 uppercase font-black text-[11px] border-b border-blue-900">
                            <th class="p-3.5 border-r border-blue-900 min-w-[120px] text-left">CABANG</th>
                            <th class="p-3.5 border-r border-blue-900 min-w-[140px] text-left">NAMA BENGKEL</th>
                            <th class="p-3.5 border-r border-blue-900 min-w-[140px] bg-amber-400/20 text-amber-300 tracking-wider">PART BENGKEL</th>
                            <th class="p-3.5 border-r border-blue-900 min-w-[140px] bg-amber-400/20 text-amber-300 tracking-wider">INCOME BENGKEL</th>
                            <th class="p-3.5 border-r border-blue-900 min-w-[140px] bg-amber-400/20 text-amber-300 tracking-wider">TARGET INCOME</th>
                            <th class="p-3.5 min-w-[160px] bg-amber-400/20 text-amber-300 tracking-wider">INCOME/UNIT (TARGET 200K)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-blue-950 font-semibold">
                        @foreach($incomeBengkelTable as $row)
                            <tr class="{{ $row['is_subtotal'] ? 'bg-slate-950 font-black text-white border-y-2 border-emerald-500/40' : 'hover:bg-slate-900/60 transition' }}">
                                <td class="p-3 border-r border-blue-950 text-left font-bold text-slate-200">{{ $row['cabang'] }}</td>
                                <td class="p-3 border-r border-blue-950 text-left {{ $row['is_subtotal'] ? 'text-emerald-300 font-black uppercase' : 'text-slate-300' }}">{{ $row['bengkel'] }}</td>
                                
                                <td class="p-3 border-r border-blue-950 text-right px-4 text-slate-300 font-bold">{{ number_format($row['part']) }}</td>
                                <td class="p-3 border-r border-blue-950 text-right px-4 font-extrabold text-white">{{ number_format($row['income']) }}</td>
                                <td class="p-3 border-r border-blue-950 text-right px-4 text-slate-400">{{ number_format($row['target']) }}</td>
                                
                                <!-- Income / Unit (Target 200k) -->
                                <td class="p-3 text-right px-4 {{ $row['income_per_unit'] < 200000 ? 'bg-rose-500/25 text-rose-200 font-bold' : 'text-emerald-400 font-extrabold' }}">
                                    {{ number_format($row['income_per_unit']) }}
                                </td>
                            </tr>
                        @endforeach

                        <!-- GRAND TOTAL ROW -->
                        <tr class="bg-gradient-to-r from-emerald-950 via-slate-950 to-blue-950 text-white font-black text-xs border-2 border-emerald-400">
                            <td colspan="2" class="p-3.5 text-left font-black tracking-wider uppercase text-emerald-400">TOTAL KESELURUHAN</td>
                            <td class="p-3.5 border-r border-blue-950 text-right px-4 text-amber-300 font-black text-sm">{{ number_format($incomeBengkelGrandTotal['part']) }}</td>
                            <td class="p-3.5 border-r border-blue-950 text-right px-4 text-white font-black text-sm">{{ number_format($incomeBengkelGrandTotal['income']) }}</td>
                            <td class="p-3.5 border-r border-blue-950 text-right px-4 text-slate-300 font-black">{{ number_format($incomeBengkelGrandTotal['target']) }}</td>
                            <td class="p-3.5 text-right px-4 text-emerald-300 font-black text-sm">
                                {{ number_format($incomeBengkelGrandTotal['income_per_unit']) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif



    <!-- Back Navigation -->
    <div class="flex justify-between items-center">
        <a href="{{ url('/') }}" class="inline-flex items-center space-x-2 text-xs font-bold text-gray-300 hover:text-white transition duration-200 bg-slate-900/60 border border-blue-900 hover:border-blue-700 px-4 py-2.5 rounded-xl shadow">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali ke Dashboard</span>
        </a>
    </div>

</div>
@endsection
