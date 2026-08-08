@extends('layouts.app')

@section('content')

<div class="max-w-2xl mx-auto bg-gradient-to-br from-blue-950 via-slate-900 to-blue-900 text-white rounded-3xl p-6 lg:p-8 shadow-2xl border border-blue-900 relative overflow-hidden">
    <!-- background glow -->
    <div class="absolute -right-16 -top-16 w-64 h-64 bg-blue-500 rounded-full blur-3xl opacity-20"></div>

    <header class="mb-6 relative z-10 border-b border-blue-800 pb-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-wide flex items-center">
                <i class="bi bi-person-plus-fill text-blue-400 mr-2"></i>Tambah Akun Baru
            </h1>
            <p class="text-gray-300 text-xs mt-1">Buat akun baru dan tentukan hak akses penggunanya.</p>
        </div>
        <a href="{{ route('users.index') }}" class="inline-flex items-center text-xs text-blue-300 hover:text-white bg-blue-900/50 px-3 py-1.5 rounded-lg border border-blue-700/50">
            <i class="bi bi-arrow-left mr-1"></i> Kembali
        </a>
    </header>

    @if ($errors->any())
        <div class="mb-6 bg-rose-500/20 border border-rose-500/50 text-rose-100 rounded-xl p-4 text-xs space-y-1 relative z-10">
            <div class="font-bold flex items-center text-sm text-rose-300">
                <i class="bi bi-exclamation-triangle-fill mr-1.5"></i> Terjadi Kesalahan Input:
            </div>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('users.store') }}" class="space-y-5 relative z-10">
        @csrf

        <div>
            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-blue-300 mb-1.5">Nama Pengguna</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Ahmad Subagyo"
                   class="w-full bg-slate-950/60 border border-blue-800 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
        </div>

        <div>
            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-blue-300 mb-1.5">Username / Email</label>
            <input type="text" name="email" id="email" value="{{ old('email') }}" required placeholder="Contoh: ahmad@aspacindo.com atau ahmad"
                   class="w-full bg-slate-950/60 border border-blue-800 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            <p class="text-[11px] text-gray-400 mt-1">Gunakan username atau email unik yang akan digunakan untuk log in.</p>
        </div>

        <div class="border-t border-blue-900/80 pt-4 mb-4">
            <label class="block text-xs font-bold uppercase tracking-wider text-blue-300 mb-2">
                <i class="bi bi-person-badge-fill mr-1 text-teal-400"></i> Jenis / Peran Pengguna (Role)
            </label>
            <p class="text-[11px] text-gray-400 mb-3">Tentukan tingkat kewenangan akun dalam mengedit data atau mengelola sistem:</p>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <label class="flex flex-col p-3 rounded-2xl bg-slate-950/60 border border-blue-900/60 hover:border-purple-500/60 cursor-pointer transition">
                    <div class="flex items-center space-x-2 mb-1">
                        <input type="radio" name="role" value="super_admin" {{ old('role', 'super_admin') === 'super_admin' ? 'checked' : '' }} class="w-4 h-4 text-purple-600 focus:ring-purple-500 bg-slate-950 border-purple-700">
                        <span class="text-xs font-extrabold text-purple-300 flex items-center">
                            <i class="bi bi-shield-check mr-1 text-purple-400"></i> Super Admin
                        </span>
                    </div>
                    <span class="text-[10px] text-gray-400">Akses penuh ke semua menu & kelola akun user.</span>
                </label>

                <label class="flex flex-col p-3 rounded-2xl bg-slate-950/60 border border-blue-900/60 hover:border-amber-500/60 cursor-pointer transition">
                    <div class="flex items-center space-x-2 mb-1">
                        <input type="radio" name="role" value="editor" {{ old('role') === 'editor' ? 'checked' : '' }} class="w-4 h-4 text-amber-600 focus:ring-amber-500 bg-slate-950 border-amber-700">
                        <span class="text-xs font-extrabold text-amber-300 flex items-center">
                            <i class="bi bi-pencil-square mr-1 text-amber-400"></i> Editor
                        </span>
                    </div>
                    <span class="text-[10px] text-gray-400">Dapat menginput & mengedit data di menu yang diizinkan.</span>
                </label>

                <label class="flex flex-col p-3 rounded-2xl bg-slate-950/60 border border-blue-900/60 hover:border-slate-500/60 cursor-pointer transition">
                    <div class="flex items-center space-x-2 mb-1">
                        <input type="radio" name="role" value="viewer" {{ old('role') === 'viewer' ? 'checked' : '' }} class="w-4 h-4 text-slate-400 focus:ring-slate-400 bg-slate-950 border-slate-600">
                        <span class="text-xs font-extrabold text-slate-300 flex items-center">
                            <i class="bi bi-eye mr-1 text-slate-400"></i> Viewer
                        </span>
                    </div>
                    <span class="text-[10px] text-gray-400">Hanya dapat melihat data (Read-only).</span>
                </label>
            </div>
        </div>

        <div class="border-t border-blue-900/80 pt-4">
            <label class="block text-xs font-bold uppercase tracking-wider text-blue-300 mb-2">
                <i class="bi bi-shield-lock-fill mr-1 text-yellow-400"></i> Pilihan Hak Akses Menu
            </label>
            <p class="text-[11px] text-gray-400 mb-3">Centang menu-menu di bawah ini yang dapat diakses oleh akun pengguna ini:</p>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-slate-950/40 p-4 rounded-2xl border border-blue-900/60">
                @php
                    $oldMenus = old('allowed_menus', ['dashboard', 'stu_unit', 'stok_unit', 'digital_marketing']);
                @endphp
                
                <label class="flex items-center space-x-3 p-2.5 rounded-xl bg-slate-900/60 border border-blue-900/40 hover:border-blue-700/60 cursor-pointer transition">
                    <input type="checkbox" name="allowed_menus[]" value="dashboard" {{ in_array('dashboard', $oldMenus) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500 bg-slate-950 border-blue-700">
                    <span class="text-xs font-semibold text-white flex items-center">
                        <i class="bi bi-speedometer2 text-blue-400 mr-2"></i> DASHBOARD
                    </span>
                </label>

                <label class="flex items-center space-x-3 p-2.5 rounded-xl bg-slate-900/60 border border-blue-900/40 hover:border-blue-700/60 cursor-pointer transition">
                    <input type="checkbox" name="allowed_menus[]" value="stu_unit" {{ in_array('stu_unit', $oldMenus) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500 bg-slate-950 border-blue-700">
                    <span class="text-xs font-semibold text-white flex items-center">
                        <i class="bi bi-graph-up text-emerald-400 mr-2"></i> STU UNIT
                    </span>
                </label>

                <label class="flex items-center space-x-3 p-2.5 rounded-xl bg-slate-900/60 border border-blue-900/40 hover:border-blue-700/60 cursor-pointer transition">
                    <input type="checkbox" name="allowed_menus[]" value="stok_unit" {{ in_array('stok_unit', $oldMenus) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500 bg-slate-950 border-blue-700">
                    <span class="text-xs font-semibold text-white flex items-center">
                        <i class="bi bi-box-seam text-amber-400 mr-2"></i> STOK UNIT
                    </span>
                </label>

                <label class="flex items-center space-x-3 p-2.5 rounded-xl bg-slate-900/60 border border-blue-900/40 hover:border-blue-700/60 cursor-pointer transition">
                    <input type="checkbox" name="allowed_menus[]" value="digital_marketing" {{ in_array('digital_marketing', $oldMenus) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500 bg-slate-950 border-blue-700">
                    <span class="text-xs font-semibold text-white flex items-center">
                        <i class="bi bi-instagram text-pink-400 mr-2"></i> SOSIAL MEDIA
                    </span>
                </label>



                <label class="flex items-center space-x-3 p-2.5 rounded-xl bg-slate-900/60 border border-blue-900/40 hover:border-blue-700/60 cursor-pointer transition">
                    <input type="checkbox" name="allowed_menus[]" value="cabang" {{ in_array('cabang', $oldMenus) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500 bg-slate-950 border-blue-700">
                    <span class="text-xs font-semibold text-white flex items-center">
                        <i class="bi bi-building text-yellow-400 mr-2"></i> CABANG
                    </span>
                </label>

                <label class="flex items-center space-x-3 p-2.5 rounded-xl bg-slate-900/60 border border-blue-900/40 hover:border-blue-700/60 cursor-pointer transition sm:col-span-2">
                    <input type="checkbox" name="allowed_menus[]" value="users" {{ in_array('users', $oldMenus) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500 bg-slate-950 border-blue-700">
                    <span class="text-xs font-semibold text-white flex items-center">
                        <i class="bi bi-people-fill text-indigo-400 mr-2"></i> KELOLA USER
                    </span>
                </label>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-blue-300 mb-1.5">Password</label>
                <input type="password" name="password" id="password" required placeholder="Minimal 6 karakter"
                       class="w-full bg-slate-950/60 border border-blue-800 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-blue-300 mb-1.5">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Ulangi password"
                       class="w-full bg-slate-950/60 border border-blue-800 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>
        </div>

        <div class="pt-4 flex items-center justify-end space-x-3">
            <a href="{{ route('users.index') }}" class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-300 hover:text-white bg-slate-800/60 hover:bg-slate-800 transition">
                Batal
            </a>
            <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white shadow-lg hover:shadow-blue-500/25 transition">
                <i class="bi bi-check-lg mr-1"></i> Simpan Akun
            </button>
        </div>
    </form>
</div>

@endsection
