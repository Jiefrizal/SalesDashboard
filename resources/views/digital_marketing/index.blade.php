@extends('layouts.app')

@section('title', 'Sosial Media & Executive Analytics')

@section('content')
<!-- Outer Glassmorphic Container -->
<div class="bg-gradient-to-br from-blue-950 via-slate-900 to-blue-900 text-white rounded-2xl lg:rounded-3xl p-4 lg:p-6 shadow-2xl border border-blue-900 overflow-hidden relative space-y-6">

    <!-- Page Header (Official Yamaha Design) -->
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
                <i class="bi bi-instagram text-yellow-400"></i>
                <span>SOSIAL MEDIA & EXECUTIVE ANALYTICS</span>
            </h1>
            <p class="text-yellow-400 font-bold tracking-widest text-[11px] lg:text-xs mt-1 uppercase">
                Digital Marketing Performance Management System
            </p>
        </div>

        <!-- Right: Status Badge -->
        <div class="z-10 flex flex-col items-center md:items-end">
            <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl text-xs font-black bg-yellow-500/20 text-yellow-300 border border-yellow-500/30 uppercase tracking-wider shadow-lg">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 mr-2 animate-pulse"></span>
                System: {{ $metrics['status_badge']['label'] }} ({{ $metrics['overall_achievement'] }}%)
            </span>
        </div>
    </header>

    <!-- Filter Bar Card -->
    <div class="bg-slate-900/90 border border-blue-900/80 p-4 lg:p-5 rounded-2xl shadow-xl backdrop-blur-md">
        <form action="{{ route('digital-marketing.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Tahun</label>
                <select name="tahun" class="w-full bg-slate-950/80 border border-blue-800 text-slate-100 text-sm rounded-xl focus:ring-yellow-400 focus:border-yellow-400 p-2.5 font-semibold">
                    @for($y = 2024; $y <= 2028; $y++)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Bulan</label>
                <select name="bulan" class="w-full bg-slate-950/80 border border-blue-800 text-slate-100 text-sm rounded-xl focus:ring-yellow-400 focus:border-yellow-400 p-2.5 font-semibold">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Filter Cabang</label>
                <select name="cabang_id" class="w-full bg-slate-950/80 border border-blue-800 text-slate-100 text-sm rounded-xl focus:ring-yellow-400 focus:border-yellow-400 p-2.5 font-semibold">
                    <option value="">-- Semua Cabang --</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ $cabangId == $b->id ? 'selected' : '' }}>{{ $b->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <button type="submit" class="w-full bg-gradient-to-r from-yellow-400 via-amber-400 to-yellow-500 hover:from-yellow-500 hover:to-amber-600 text-blue-950 font-black text-sm py-2.5 px-4 rounded-xl transition duration-200 shadow-lg border border-yellow-300 flex items-center justify-center gap-2 transform hover:scale-[1.02] active:scale-95">
                    <i class="bi bi-funnel-fill"></i> Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Quick Statistics Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Stat Card 1 -->
        <div class="bg-slate-900/80 border border-blue-900/60 p-5 rounded-2xl shadow-xl backdrop-blur-md flex items-center justify-between hover:border-yellow-500/50 transition duration-300">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Total Cabang</p>
                <h3 class="text-2xl font-black text-white mt-1">{{ $metrics['total_branches'] }} <span class="text-xs font-normal text-slate-400">Cabang</span></h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-yellow-400 border border-yellow-500/20 flex items-center justify-center text-xl">
                <i class="bi bi-building"></i>
            </div>
        </div>

        <!-- Stat Card 2 -->
        <div class="bg-slate-900/80 border border-blue-900/60 p-5 rounded-2xl shadow-xl backdrop-blur-md flex items-center justify-between hover:border-rose-500/50 transition duration-300">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Belum Input Hari Ini</p>
                <h3 class="text-2xl font-black {{ $metrics['missing_count_today'] > 0 ? 'text-rose-400' : 'text-emerald-400' }} mt-1">
                    {{ $metrics['missing_count_today'] }} <span class="text-xs font-normal text-slate-400">Cabang</span>
                </h3>
            </div>
            <div class="w-12 h-12 rounded-xl {{ $metrics['missing_count_today'] > 0 ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' }} flex items-center justify-center text-xl">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
        </div>

        <!-- Stat Card 3 -->
        <div class="bg-slate-900/80 border border-blue-900/60 p-5 rounded-2xl shadow-xl backdrop-blur-md flex items-center justify-between hover:border-emerald-500/50 transition duration-300">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Achievement KPI</p>
                <h3 class="text-2xl font-black text-emerald-400 mt-1">{{ $metrics['overall_achievement'] }}%</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center justify-center text-xl">
                <i class="bi bi-trophy"></i>
            </div>
        </div>

        <!-- Stat Card 4 -->
        <div class="bg-slate-900/80 border border-blue-900/60 p-5 rounded-2xl shadow-xl backdrop-blur-md flex items-center justify-between hover:border-cyan-500/50 transition duration-300">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Total Content Posts</p>
                <h3 class="text-2xl font-black text-cyan-300 mt-1">{{ number_format($metrics['total_posts']) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center text-xl">
                <i class="bi bi-send"></i>
            </div>
        </div>

        <!-- Stat Card 5 -->
        <div class="bg-slate-900/80 border border-blue-900/60 p-5 rounded-2xl shadow-xl backdrop-blur-md flex items-center justify-between hover:border-emerald-500/50 transition duration-300">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Followers Growth</p>
                <h3 class="text-2xl font-black text-emerald-400 mt-1">+{{ number_format($metrics['followers_growth']) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center justify-center text-xl">
                <i class="bi bi-person-plus"></i>
            </div>
        </div>

        <!-- Stat Card 6 -->
        <div class="bg-slate-900/80 border border-blue-900/60 p-5 rounded-2xl shadow-xl backdrop-blur-md flex items-center justify-between hover:border-amber-500/50 transition duration-300">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Total Views</p>
                <h3 class="text-2xl font-black text-amber-300 mt-1">{{ number_format($metrics['total_views']) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-400 border border-amber-500/20 flex items-center justify-center text-xl">
                <i class="bi bi-eye"></i>
            </div>
        </div>

        <!-- Stat Card 7 -->
        <div class="bg-slate-900/80 border border-blue-900/60 p-5 rounded-2xl shadow-xl backdrop-blur-md flex items-center justify-between col-span-1 sm:col-span-2 hover:border-indigo-500/50 transition duration-300">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Google Business Rating</p>
                <h3 class="text-2xl font-black text-white mt-1">
                    <span class="text-yellow-400">⭐</span> {{ $metrics['avg_google_rating'] }} <span class="text-xs font-normal text-slate-400">/ 5.0</span>
                </h3>
                <p class="text-xs text-indigo-300 font-semibold mt-1">+{{ number_format($metrics['google_reviews']) }} Ulasan Baru</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 flex items-center justify-center text-xl">
                <i class="bi bi-google"></i>
            </div>
        </div>

    </div>

    <!-- Missing Daily Report Alert Widget -->
    @if($metrics['missing_count_today'] > 0)
    <div class="bg-rose-950/80 border-l-4 border-rose-500 p-4 rounded-2xl shadow-xl border border-rose-900/60 flex flex-col md:flex-row md:items-center justify-between gap-4 backdrop-blur-md">
        <div>
            <h4 class="text-sm font-extrabold text-rose-300 flex items-center gap-2">
                <i class="bi bi-bell-fill text-rose-400"></i> PERINGATAN: CABANG BELUM INPUT DAILY REPORT HARI INI
            </h4>
            <p class="text-xs text-rose-200 mt-1">
                Terdapat <strong>{{ $metrics['missing_count_today'] }} cabang</strong> yang belum mengirimkan Laporan Harian untuk tanggal berjalan:
            </p>
            <div class="mt-2.5 flex flex-wrap gap-2">
                @foreach($metrics['missing_branches_today'] as $mb)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-extrabold bg-rose-500/20 text-rose-200 border border-rose-500/40">
                        <i class="bi bi-building mr-1"></i> {{ $mb->nama }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Analytics Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Line Chart: Trend Activity Posting (Harian) -->
        <div class="lg:col-span-2 bg-slate-900/80 border border-blue-900/60 p-5 rounded-2xl shadow-xl backdrop-blur-md">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-extrabold text-white flex items-center gap-2">
                    <i class="bi bi-activity text-yellow-400"></i> Trend Activity Posting (Harian)
                </h3>
                <div class="flex items-center gap-3 text-xs">
                    <span class="inline-flex items-center text-rose-400 font-bold"><span class="w-2.5 h-2.5 rounded-full bg-rose-500 mr-1.5"></span> IG</span>
                    <span class="inline-flex items-center text-blue-400 font-bold"><span class="w-2.5 h-2.5 rounded-full bg-blue-500 mr-1.5"></span> FB</span>
                    <span class="inline-flex items-center text-emerald-400 font-bold"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mr-1.5"></span> TikTok</span>
                </div>
            </div>
            <div class="relative h-72">
                <canvas id="postingTrendChart"></canvas>
            </div>
        </div>

        <!-- Donut Chart: Views Platform Distribution -->
        <div class="bg-slate-900/80 border border-blue-900/60 p-5 rounded-2xl shadow-xl backdrop-blur-md">
            <h3 class="text-base font-extrabold text-white mb-4 flex items-center gap-2">
                <i class="bi bi-pie-chart-fill text-rose-400"></i> Distribusi Platform Views
            </h3>
            <div class="relative h-64 flex items-center justify-center">
                <canvas id="platformPieChart"></canvas>
            </div>
        </div>

    </div>

    <!-- Leaderboard Summary Section -->
    <div class="bg-slate-900/80 border border-blue-900/60 rounded-2xl shadow-xl backdrop-blur-md overflow-hidden">
        <div class="p-5 border-b border-blue-900/80 flex items-center justify-between">
            <h3 class="text-base font-extrabold text-white flex items-center gap-2">
                <i class="bi bi-award-fill text-yellow-400"></i> Leaderboard Achievement Cabang
            </h3>
            <span class="text-xs text-slate-400 font-semibold">Urutan Berdasarkan Pencapaian KPI</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-blue-950/80 text-[11px] font-extrabold text-yellow-400 uppercase tracking-wider border-b border-blue-800">
                        <th class="py-3.5 px-4">Rank</th>
                        <th class="py-3.5 px-4">Kode</th>
                        <th class="py-3.5 px-4">Nama Cabang</th>
                        <th class="py-3.5 px-4">Total Post</th>
                        <th class="py-3.5 px-4">Followers Gained</th>
                        <th class="py-3.5 px-4">Achievement KPI</th>
                        <th class="py-3.5 px-4">Status Tier</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-blue-900/40 text-sm">
                    @forelse($metrics['leaderboard'] as $item)
                    <tr class="hover:bg-blue-900/30 transition duration-150">
                        <td class="py-3.5 px-4 font-bold">
                            @if($item['rank'] == 1)
                                <span class="w-7 h-7 rounded-full bg-yellow-400 text-yellow-950 flex items-center justify-center font-black text-xs shadow-sm">🥇 1</span>
                            @elseif($item['rank'] == 2)
                                <span class="w-7 h-7 rounded-full bg-slate-300 text-slate-900 flex items-center justify-center font-black text-xs">🥈 2</span>
                            @elseif($item['rank'] == 3)
                                <span class="w-7 h-7 rounded-full bg-amber-600 text-white flex items-center justify-center font-black text-xs">🥉 3</span>
                            @else
                                <span class="text-slate-400 ml-2 font-bold">#{{ $item['rank'] }}</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 font-extrabold text-yellow-400">{{ $item['kode'] }}</td>
                        <td class="py-3.5 px-4 font-bold text-white">{{ $item['nama_cabang'] }}</td>
                        <td class="py-3.5 px-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                {{ $item['total_posts'] }} Post
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-emerald-400 font-extrabold">
                            +{{ number_format($item['total_followers_gained']) }}
                        </td>
                        <td class="py-3.5 px-4 min-w-[180px]">
                            <div class="flex items-center gap-3">
                                <div class="flex-1 bg-slate-950 rounded-full h-2.5 overflow-hidden border border-blue-900">
                                    <div class="h-full rounded-full transition-all duration-500" style="width: {{ min(100, $item['achievement_pct']) }}%; background-color: {{ $item['badge']['hex'] }};"></div>
                                </div>
                                <span class="font-black text-xs text-slate-200 min-w-[45px] text-right">{{ $item['achievement_pct'] }}%</span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold {{ $item['badge']['badge_tailwind'] }}">
                                {{ $item['badge']['label'] }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400 text-sm">
                            Belum ada data laporan digital marketing untuk periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Chart Visualizations Scripts -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Line Chart: Posting Trend
        const ctxPosting = document.getElementById('postingTrendChart');
        if (ctxPosting) {
            new Chart(ctxPosting.getContext('2d'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [
                        {
                            label: 'Instagram Posts',
                            data: {!! json_encode($igPostsSeries) !!},
                            borderColor: '#f43f5e',
                            backgroundColor: 'rgba(244, 63, 94, 0.12)',
                            borderWidth: 2.5,
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'Facebook Posts',
                            data: {!! json_encode($fbPostsSeries) !!},
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.12)',
                            borderWidth: 2.5,
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'TikTok Posts',
                            data: {!! json_encode($tiktokPostsSeries) !!},
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.12)',
                            borderWidth: 2.5,
                            tension: 0.3,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(255, 255, 255, 0.05)' },
                            ticks: { color: '#94a3b8', font: { weight: 'bold' } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#94a3b8', font: { weight: 'bold' } }
                        }
                    }
                }
            });
        }

        // Doughnut Chart: Platform Views
        const ctxPie = document.getElementById('platformPieChart');
        if (ctxPie) {
            new Chart(ctxPie.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Instagram Views', 'Facebook Views', 'TikTok Views'],
                    datasets: [{
                        data: [
                            {{ $platformDistribution['Instagram Views'] }},
                            {{ $platformDistribution['Facebook Views'] }},
                            {{ $platformDistribution['TikTok Views'] }}
                        ],
                        backgroundColor: ['#f43f5e', '#3b82f6', '#10b981'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { color: '#cbd5e1', font: { weight: 'bold' } }
                        }
                    },
                    cutout: '70%'
                }
            });
        }
    });
</script>
@endsection
