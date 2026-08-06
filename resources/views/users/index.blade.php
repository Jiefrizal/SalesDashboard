@extends('layouts.app')

@section('content')

<!-- Outer Glassmorphic / Premium Border Card wrapper -->
<div class="bg-gradient-to-br from-blue-950 via-slate-900 to-blue-900 text-white rounded-3xl p-6 lg:p-8 shadow-2xl border border-blue-900 overflow-hidden relative">
    <!-- background glow -->
    <div class="absolute -right-16 -top-16 w-64 h-64 bg-blue-500 rounded-full blur-3xl opacity-20"></div>
    <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-indigo-500 rounded-full blur-3xl opacity-20"></div>

    <!-- Header Section -->
    <header class="mb-8 relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-blue-800 pb-6 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-wide drop-shadow-md flex items-center">
                <i class="bi bi-people-fill text-blue-400 mr-3"></i>Manajemen User & Hak Akses
            </h1>
            <p class="text-gray-300 text-sm mt-1">
                Kelola akun pengguna dan tentukan hak akses (Super Admin / Viewer) dalam sistem.
            </p>
        </div>
        <div>
            <a href="{{ route('users.create') }}"
               class="inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-bold bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white shadow-lg hover:shadow-blue-500/25 transition duration-200">
                <i class="bi bi-person-plus-fill text-base mr-2"></i>Tambah Akun Baru
            </a>
        </div>
    </header>

    @if(session('success'))
        <div class="mb-6 bg-emerald-500/20 border border-emerald-500 text-emerald-100 rounded-xl p-4 flex items-center space-x-3 text-sm z-10 relative">
            <i class="bi bi-check-circle-fill text-lg text-emerald-400"></i>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-rose-500/20 border border-rose-500 text-rose-100 rounded-xl p-4 flex items-center space-x-3 text-sm z-10 relative">
            <i class="bi bi-exclamation-triangle-fill text-lg text-rose-400"></i>
            <span class="font-semibold">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Users Table -->
    <div class="relative z-10 overflow-x-auto rounded-2xl border border-blue-800 shadow-xl bg-slate-950/40 backdrop-blur-md">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gradient-to-r from-blue-900 to-indigo-900 text-slate-200 border-b border-blue-800">
                    <th class="p-4 text-xs font-extrabold uppercase tracking-wider text-center w-12">No</th>
                    <th class="p-4 text-xs font-extrabold uppercase tracking-wider">Nama Pengguna</th>
                    <th class="p-4 text-xs font-extrabold uppercase tracking-wider">Username / Email</th>
                    <th class="p-4 text-xs font-extrabold uppercase tracking-wider">Hak Akses Menu</th>
                    <th class="p-4 text-xs font-extrabold uppercase tracking-wider text-center">Tanggal Dibuat</th>
                    <th class="p-4 text-xs font-extrabold uppercase tracking-wider text-center w-36">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-blue-900/40">
                @forelse($users as $index => $user)
                    <tr class="hover:bg-blue-900/20 transition duration-150">
                        <td class="p-4 text-sm font-semibold text-center text-gray-400">{{ $index + 1 }}</td>
                        <td class="p-4 text-sm font-bold text-white">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-blue-600/80 border border-blue-400/40 flex items-center justify-center text-white text-xs font-bold shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span>{{ $user->name }}</span>
                                @if(auth()->id() === $user->id)
                                    <span class="text-[10px] bg-blue-500/20 text-blue-300 border border-blue-500/30 rounded-full px-2 py-0.5 font-normal">(Anda)</span>
                                @endif
                            </div>
                        </td>
                        <td class="p-4 text-sm text-gray-300 font-mono">
                            {{ $user->email }}
                        </td>
                        <td class="p-4">
                            <div class="flex flex-wrap gap-1.5">
                                @php
                                    $menus = \App\Models\User::availableMenus();
                                @endphp
                                @foreach($menus as $key => $label)
                                    @if($user->hasMenuAccess($key))
                                        <span class="inline-flex items-center text-[10px] px-2 py-0.5 rounded-md bg-slate-900/80 text-blue-300 border border-blue-700/50 font-medium">
                                            <i class="bi bi-check-circle-fill text-[9px] text-emerald-400 mr-1"></i>{{ $label }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </td>
                        <td class="p-4 text-center text-xs text-gray-400">
                            {{ $user->created_at ? $user->created_at->format('d M Y H:i') : '-' }}
                        </td>
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('users.edit', $user->id) }}"
                                   class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-xs font-bold bg-blue-600/30 hover:bg-blue-600/60 text-blue-200 border border-blue-500/30 transition duration-150"
                                   title="Edit Akun">
                                    <i class="bi bi-pencil-square mr-1"></i>Edit
                                </a>
                                @if(auth()->id() !== $user->id)
                                    <form method="POST" action="{{ route('users.destroy', $user->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $user->name }}?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-xs font-bold bg-rose-600/30 hover:bg-rose-600/60 text-rose-200 border border-rose-500/30 transition duration-150"
                                                title="Hapus Akun">
                                            <i class="bi bi-trash mr-1"></i>Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-400">
                            <i class="bi bi-people text-4xl block mb-2 opacity-50"></i>
                            Belum ada akun pengguna yang terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
