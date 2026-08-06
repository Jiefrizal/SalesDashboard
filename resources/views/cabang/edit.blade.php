@extends('layouts.app')

@section('content')

<!-- Outer Glassmorphic / Premium Border Card wrapper -->
<div class="bg-gradient-to-br from-blue-950 via-slate-900 to-blue-900 text-white rounded-3xl p-6 lg:p-8 shadow-2xl border border-blue-900 overflow-hidden relative max-w-4xl mx-auto">
    <!-- background glow -->
    <div class="absolute -right-16 -top-16 w-64 h-64 bg-blue-500 rounded-full blur-3xl opacity-20"></div>
    <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-indigo-500 rounded-full blur-3xl opacity-20"></div>

    <!-- Header Section -->
    <header class="mb-8 relative z-10 flex items-center justify-between border-b border-blue-800 pb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-white tracking-wide drop-shadow-md">
                <i class="bi bi-pencil-square text-blue-400 mr-2"></i>Edit URL Cabang: <span class="text-yellow-400 font-extrabold">{{ $cabang->nama }}</span>
            </h1>
            <p class="text-gray-300 text-sm mt-1">
                Atur URL spreadsheet khusus untuk menyinkronkan data cabang {{ $cabang->nama }}.
            </p>
        </div>
        <a href="{{ route('cabang.index') }}" class="hidden md:inline-flex items-center space-x-2 text-xs text-gray-400 hover:text-white transition duration-200 bg-slate-900/50 border border-blue-900 hover:border-blue-700 px-4 py-2.5 rounded-xl shadow">
            <i class="bi bi-arrow-left"></i>
            <span>Batal</span>
        </a>
    </header>

    <!-- Main Content Form -->
    <form action="{{ route('cabang.update', $cabang->id) }}" method="POST" class="relative z-10 space-y-6">
        @csrf
        @method('PUT')

        @if ($errors->any())
            <div class="bg-rose-500/20 border border-rose-500 text-rose-100 rounded-xl p-4 flex flex-col space-y-2 text-sm">
                <div class="flex items-center space-x-2 font-bold">
                    <i class="bi bi-exclamation-triangle-fill text-rose-400"></i>
                    <span>Terdapat kesalahan input:</span>
                </div>
                <ul class="list-disc list-inside text-xs text-gray-300 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-slate-950/40 backdrop-blur-md border border-blue-900 rounded-2xl p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Target Tantangan Field -->
                <div>
                    <label for="target_tantangan" class="block text-sm font-bold text-gray-200 mb-2">
                        Target Tantangan
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-blue-400">
                            <i class="bi bi-bullseye text-base"></i>
                        </div>
                        <input 
                            type="number" 
                            name="target_tantangan" 
                            id="target_tantangan" 
                            value="{{ old('target_tantangan', $cabang->target_tantangan) }}"
                            min="0"
                            required
                            class="w-full pl-10 pr-4 py-3 bg-slate-900/80 border border-blue-900/60 rounded-xl text-white font-semibold text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        >
                    </div>
                    <p class="mt-2 text-[10.5px] text-gray-400 font-medium">
                        Target tantangan bulanan yang harus dicapai cabang.
                    </p>
                </div>

                <!-- Target Reguler Field -->
                <div>
                    <label for="target_reguler" class="block text-sm font-bold text-gray-200 mb-2">
                        Target Reguler
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-purple-400">
                            <i class="bi bi-flag-fill text-base"></i>
                        </div>
                        <input 
                            type="number" 
                            name="target_reguler" 
                            id="target_reguler" 
                            value="{{ old('target_reguler', $cabang->target_reguler) }}"
                            min="0"
                            required
                            class="w-full pl-10 pr-4 py-3 bg-slate-900/80 border border-blue-900/60 rounded-xl text-white font-semibold text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                        >
                    </div>
                    <p class="mt-2 text-[10.5px] text-gray-400 font-medium">
                        Target reguler standar bulanan untuk cabang.
                    </p>
                </div>

                <!-- Target Reguler 2026 Field -->
                <div>
                    <label for="target_reguler_2026" class="block text-sm font-bold text-gray-200 mb-2">
                        Target Reguler 2026
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-teal-400">
                            <i class="bi bi-calendar-event-fill text-base"></i>
                        </div>
                        <input 
                            type="number" 
                            name="target_reguler_2026" 
                            id="target_reguler_2026" 
                            value="{{ old('target_reguler_2026', $cabang->target_reguler_2026) }}"
                            min="0"
                            required
                            class="w-full pl-10 pr-4 py-3 bg-slate-900/80 border border-blue-900/60 rounded-xl text-white font-semibold text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition"
                        >
                    </div>
                    <p class="mt-2 text-[10.5px] text-gray-400 font-medium">
                        Target reguler tahunan untuk periode 2026.
                    </p>
                </div>
            </div>

            <!-- Single Spreadsheet URL Field -->
            <div class="border-t border-blue-900/50 pt-4">
                <label for="spreadsheet_url" class="block text-sm font-bold text-gray-200 mb-2">
                    URL Google Spreadsheet Cabang (Penjualan / ACV, Stok & LM)
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-yellow-400">
                        <i class="bi bi-link-45deg text-xl"></i>
                    </div>
                    <input 
                        type="url" 
                        name="spreadsheet_url" 
                        id="spreadsheet_url" 
                        value="{{ old('spreadsheet_url', $cabang->spreadsheet_url) }}"
                        placeholder="https://docs.google.com/spreadsheets/d/your-spreadsheet-id/edit?usp=sharing"
                        class="w-full pl-10 pr-4 py-3 bg-slate-900/80 border border-blue-900/60 rounded-xl text-white font-mono text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition"
                    >
                </div>
                <p class="mt-2 text-xs text-blue-300 font-medium flex items-center gap-1.5">
                    <i class="bi bi-check-circle-fill text-emerald-400"></i>
                    Satu link spreadsheet ini digunakan secara otomatis untuk membaca tab <strong>STU (Sales)</strong>, <strong>STOK (Stock)</strong>, dan <strong>LM (Last Month)</strong> cabang.
                </p>
            </div>
        </div>

        <!-- Google Sheet instructions -->
        <div class="bg-blue-500/5 border border-blue-900/50 rounded-2xl p-6 text-xs text-gray-300 space-y-3.5 leading-relaxed">
            <h3 class="font-extrabold text-blue-300 uppercase tracking-wider text-sm">
                <i class="bi bi-info-circle mr-1.5"></i>Petunjuk Konfigurasi Google Spreadsheet
            </h3>
            <ol class="list-decimal list-inside space-y-2 text-gray-300">
                <li>Buka Spreadsheet cabang yang diinginkan di Google Sheets.</li>
                <li>Pastikan format kolom data di baris pertama persis seperti format standar (memiliki 21 kolom data dari Cabang, Target Tantangan, ACV, Target Reguler, LM, YTD, Target Perbulan, hingga Stock 2024-2026 dan Total).</li>
                <li>Klik tombol <strong class="text-white">File</strong> di menu atas, lalu pilih <strong class="text-white">Share / Bagikan</strong> &gt; <strong class="text-white">Publish to Web / Publikasikan ke web</strong>.</li>
                <li>Pilih opsi format <strong class="text-emerald-400">Comma-separated values (.csv)</strong> dari dropdown jenis file (bukan Web Page).</li>
                <li>Klik tombol <strong class="text-white">Publish</strong> dan salin link yang dihasilkan.</li>
                <li>Atau, Anda dapat menyalin link URL baris alamat di browser Anda (sistem akan secara otomatis mendeteksi dan mengubahnya menjadi format ekspor CSV).</li>
            </ol>
        </div>

        <!-- Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-end space-y-3 sm:space-y-0 sm:space-x-3 border-t border-blue-800 pt-6">
            <a href="{{ route('cabang.index') }}" class="w-full sm:w-auto text-center bg-slate-900 hover:bg-slate-800 text-gray-300 font-extrabold text-sm px-6 py-3 rounded-xl border border-blue-900 transition">
                Batal
            </a>
            <button type="submit" class="w-full sm:w-auto bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-extrabold text-sm px-8 py-3 rounded-xl transition shadow-lg hover:shadow-indigo-500/20 border border-blue-400 hover:border-blue-300 transform active:scale-95 duration-100">
                <i class="bi bi-save mr-1.5"></i> Simpan URL
            </button>
        </div>
    </form>
</div>

@endsection
