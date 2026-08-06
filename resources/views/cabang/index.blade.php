@extends('layouts.app')


@section('content')

<!-- Outer Glassmorphic / Premium Border Card wrapper -->
<div class="bg-gradient-to-br from-blue-950 via-slate-900 to-blue-900 text-white rounded-3xl p-6 lg:p-8 shadow-2xl border border-blue-900 overflow-hidden relative">
    <!-- background glow -->
    <div class="absolute -right-16 -top-16 w-64 h-64 bg-blue-500 rounded-full blur-3xl opacity-20"></div>
    <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-indigo-500 rounded-full blur-3xl opacity-20"></div>

    <!-- Header Section -->
    <header class="mb-8 relative z-10 flex flex-col md:flex-row md:items-center md:justify-between border-b border-blue-800 pb-6">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-wide drop-shadow-md">
                <i class="bi bi-building-fill text-blue-400 mr-2"></i>Master Cabang
            </h1>
            <p class="text-gray-300 text-sm mt-1">
                Atur URL Spreadsheet Google Sheets secara mandiri untuk masing-masing cabang.
            </p>
        </div>
    </header>

    @if(session('success'))
        <div class="mb-6 bg-emerald-500/20 border border-emerald-500 text-emerald-100 rounded-xl p-4 flex items-center space-x-3 text-sm z-10 relative">
            <i class="bi bi-check-circle-fill text-lg text-emerald-400"></i>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Cabang Cards Grid / Elegant Table -->
    <div class="relative z-10 overflow-x-auto rounded-2xl border border-blue-800 shadow-xl bg-slate-950/40 backdrop-blur-md">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gradient-to-r from-blue-900 to-indigo-900 text-slate-200 border-b border-blue-800">
                    <th class="p-4 text-xs font-extrabold uppercase tracking-wider text-center w-16">ID</th>
                    <th class="p-4 text-xs font-extrabold uppercase tracking-wider">Nama Cabang</th>
                    <th class="p-4 text-xs font-extrabold uppercase tracking-wider text-center w-32">Target Tantangan</th>
                    <th class="p-4 text-xs font-extrabold uppercase tracking-wider text-center w-32">Target Reguler</th>
                    <th class="p-4 text-xs font-extrabold uppercase tracking-wider text-center w-32">Target Reguler 2026</th>
                    <th class="p-4 text-xs font-extrabold uppercase tracking-wider">URL Spreadsheet Google Sheets</th>
                    <th class="p-4 text-xs font-extrabold uppercase tracking-wider text-center w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-blue-900/40">
                @forelse($cabangs as $cabang)
                    <tr class="hover:bg-blue-900/20 transition duration-150">
                        <td class="p-4 text-sm font-semibold text-center text-gray-400">{{ $cabang->id }}</td>
                        <td class="p-4 text-sm font-bold text-white">
                            <div class="flex items-center space-x-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-500 shrink-0"></span>
                                <span>{{ $cabang->nama }}</span>
                            </div>
                        </td>
                        <td class="p-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-extrabold bg-blue-500/10 text-blue-300 border border-blue-500/20">
                                <i class="bi bi-bullseye mr-1 text-blue-400"></i> {{ $cabang->target_tantangan }}
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-extrabold bg-purple-500/10 text-purple-300 border border-purple-500/20">
                                <i class="bi bi-flag-fill mr-1 text-purple-400"></i> {{ $cabang->target_reguler }}
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-extrabold bg-teal-500/10 text-teal-300 border border-teal-500/20">
                                <i class="bi bi-calendar-event-fill mr-1 text-teal-400"></i> {{ $cabang->target_reguler_2026 }}
                            </span>
                        </td>
                        <td class="p-4 text-sm max-w-xs md:max-w-md">
                            <div class="flex items-center space-x-2">
                                @if($cabang->spreadsheet_url)
                                    <span class="truncate text-slate-200 font-mono text-xs bg-slate-900/80 px-3 py-1.5 rounded-xl border border-blue-900/60 block flex-1">
                                        {{ $cabang->spreadsheet_url }}
                                    </span>
                                    <a href="{{ $cabang->spreadsheet_url }}" target="_blank" class="text-yellow-400 hover:text-yellow-300 shrink-0 p-1.5 bg-blue-900/40 hover:bg-blue-800/60 rounded-lg border border-blue-700/50 transition" title="Buka Spreadsheet Google Sheets">
                                        <i class="bi bi-box-arrow-up-right text-sm"></i>
                                    </a>
                                @else
                                    <span class="text-rose-400 text-xs italic font-semibold">Belum diset</span>
                                @endif
                            </div>
                        </td>
                        <td class="p-4 text-center">
                            <a href="{{ route('cabang.edit', $cabang->id) }}" class="inline-flex items-center justify-center space-x-1.5 bg-blue-600 hover:bg-blue-500 text-white font-extrabold text-xs px-3.5 py-2 rounded-xl transition duration-200 shadow-md border border-blue-500 hover:border-blue-400 transform hover:scale-105 active:scale-95">
                                <i class="bi bi-pencil-square"></i>
                                <span>Edit URL</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-gray-400">
                            Tidak ada data cabang.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Info Section / Panduan Format Spreadsheet -->
    <div class="mt-8 relative z-10 bg-slate-950/30 border border-blue-900/60 rounded-2xl p-6 backdrop-blur-md">
        <h2 class="text-lg font-bold text-blue-400 mb-3">
            <i class="bi bi-info-circle-fill mr-2"></i>Panduan Sumber Data Cabang & Format Spreadsheet
        </h2>
        <p class="text-sm text-gray-300 mb-4 leading-relaxed">
            Sistem ini membaca data performa sales secara real-time dari Google Sheets individual milik masing-masing cabang. Untuk memastikan sinkronisasi berhasil berjalan lancar, pastikan spreadsheet Anda memiliki format kolom berikut dalam bentuk file yang dipublikasikan sebagai <strong>CSV (Comma-Separated Values)</strong>.
        </p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-xs mb-6">
            <div class="bg-slate-900/60 p-4 rounded-xl border border-blue-950">
                <span class="font-extrabold text-blue-300 block mb-2 uppercase tracking-wider">1. Target & ACV Tantangan</span>
                <ul class="space-y-1.5 text-gray-400 list-disc list-inside">
                    <li>Kolom 1: <strong class="text-white">Nama Cabang</strong> (misal: Pekanbaru)</li>
                    <li>Kolom 2: <strong class="text-white">Target Tantangan</strong> (numerik)</li>
                    <li>Kolom 3: <strong class="text-white">ACV</strong> (actual unit terjual)</li>
                </ul>
            </div>
            
            <div class="bg-slate-900/60 p-4 rounded-xl border border-blue-950">
                <span class="font-extrabold text-purple-300 block mb-2 uppercase tracking-wider">2. Target Reguler & LM</span>
                <ul class="space-y-1.5 text-gray-400 list-disc list-inside">
                    <li>Kolom 6: <strong class="text-white">Target Reguler</strong> (numerik)</li>
                    <li>Kolom 9: <strong class="text-white">LM</strong> (actual unit bulan lalu)</li>
                </ul>
            </div>
            
            <div class="bg-slate-900/60 p-4 rounded-xl border border-blue-950">
                <span class="font-extrabold text-amber-300 block mb-2 uppercase tracking-wider">3. Target 2026 & Stock Unit</span>
                <ul class="space-y-1.5 text-gray-400 list-disc list-inside">
                    <li>Kolom 13: <strong class="text-white">Target Reguler 2026</strong></li>
                    <li>Kolom 14: <strong class="text-white">Act YTD Jan 2026</strong></li>
                    <li>Kolom 17: <strong class="text-white">Target Perbulan 2026</strong></li>
                    <li>Kolom 18-20: <strong class="text-white">Stock 2024, 2025, 2026</strong></li>
                </ul>
            </div>
        </div>

        <div class="p-4 bg-blue-950/30 border border-blue-900/40 rounded-xl flex items-start space-x-3 text-xs text-blue-300">
            <i class="bi bi-patch-question-fill text-lg mt-0.5 shrink-0"></i>
            <div>
                <strong class="block mb-1">Bagaimana cara mendapatkan URL Google Sheets CSV?</strong>
                Di Google Sheets cabang Anda, klik <strong>File</strong> &rarr; <strong>Share / Bagikan</strong> &rarr; <strong>Publish to web / Publikasikan ke web</strong>. Pilih tab sheet yang berisi tabel performa, ganti formatnya menjadi <strong>Comma-separated values (.csv)</strong>, lalu klik <strong>Publish</strong>. Copy link yang muncul dan masukkan melalui tombol <strong>Edit URL</strong> di atas.
            </div>
        </div>
    </div>

    <!-- Back to Dashboard -->
    <div class="mt-6 flex justify-end z-10 relative">
        <a href="{{ url('/') }}" class="inline-flex items-center space-x-2 text-sm text-gray-400 hover:text-white transition duration-200 bg-slate-900/50 border border-blue-900 hover:border-blue-700 px-4 py-2.5 rounded-xl shadow">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali ke Dashboard</span>
        </a>
    </div>
</div>

@endsection