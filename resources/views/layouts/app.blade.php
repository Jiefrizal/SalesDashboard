<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Dashboard</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('logo.png') }}">

    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Tailwind CSS CDN Fallback for Mobile Tunnel Devices -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Compiled Production Assets / Vite -->
    @if(file_exists(public_path('build/manifest.json')))
        @php
            $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
            $cssFile = $manifest['resources/css/app.css']['file'] ?? null;
            $jsFile = $manifest['resources/js/app.js']['file'] ?? null;
        @endphp
        @if($cssFile)
            <link rel="stylesheet" href="{{ asset('build/' . $cssFile) }}">
        @endif
        @if($jsFile)
            <script type="module" src="{{ asset('build/' . $jsFile) }}"></script>
        @endif
    @else
        @vite(['resources/css/app.css','resources/js/app.js'])
    @endif
    <style>
        /* Fluid Executive Typography & Asset Scaling adapted dynamically to screen width */
        @media (min-width: 1024px) {
            html {
                font-size: clamp(11px, 0.72vw + 0.1rem, 12.5px) !important;
            }
        }
        /* Utility class to hide scrollbars while preserving touch scroll functionality */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="bg-[#04060c] font-sans antialiased text-slate-100">

<div class="flex flex-col lg:flex-row h-screen overflow-hidden bg-[#04060c]">

    <!-- Mobile Top Navigation Header (Sticky Pin at Top with z-[60]) -->
    <header class="sticky top-0 left-0 right-0 bg-[#080c1d]/95 border-b border-blue-900/80 backdrop-blur-xl px-4 py-3 flex items-center justify-between lg:hidden shadow-2xl z-[60] shrink-0">
        <div class="flex items-center space-x-3 min-w-0">
            <img src="{{ asset('logo.png') }}" alt="ASPACINDO" class="h-8 w-auto object-contain shrink-0">
            <div class="min-w-0">
                <h1 class="text-xs font-black tracking-wider text-white uppercase truncate">PT ASPACINDO KEDATON MOTOR</h1>
                <span class="text-[9.5px] font-extrabold text-yellow-400 block uppercase tracking-widest leading-none mt-0.5">Sales & Stock Realtime</span>
            </div>
        </div>
        <button id="mobile-menu-toggle" type="button" aria-label="Open Navigation Menu" class="bg-blue-900/80 hover:bg-blue-700 text-white border border-blue-600/80 rounded-xl px-3 py-2 focus:outline-none shrink-0 shadow-md flex items-center space-x-1.5 active:scale-95 transition">
            <i class="bi bi-list text-xl"></i>
            <span class="text-xs font-black uppercase tracking-wider hidden sm:inline">Menu</span>
        </button>
    </header>

    <!-- Sidebar (Drawer with z-[70]) -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-[70] w-60 bg-blue-950 text-white transform -translate-x-full lg:translate-x-0 lg:static lg:h-full transition-transform duration-300 ease-in-out shadow-2xl lg:shadow-none flex flex-col shrink-0 border-r border-blue-900/60">

        <!-- Brand -->
        <div class="flex flex-col items-center justify-center py-6 border-b border-blue-800/80 relative px-4 text-center shrink-0">
            <img src="{{ asset('logo.png') }}" alt="ASPACINDO" class="h-16 w-auto object-contain mb-2">
            <h1 class="text-xs font-extrabold tracking-wider text-white uppercase px-2">
                PT ASPACINDO KEDATON MOTOR
            </h1>
            <!-- Close button for mobile -->
            <button id="mobile-menu-close" type="button" class="absolute top-4 right-4 text-white hover:text-blue-300 lg:hidden p-1">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="mt-4 flex-1 overflow-y-auto space-y-1 px-3">
            @auth
                @if(auth()->user()->hasMenuAccess('dashboard'))
                    <a href="{{ url('/') }}" class="flex items-center px-4 py-3 rounded-xl font-extrabold text-xs uppercase tracking-wider transition duration-150 {{ Request::is('/') ? 'bg-gradient-to-r from-blue-700 to-blue-800 text-white shadow-lg border-l-4 border-yellow-400' : 'text-blue-200 hover:bg-blue-900/80 hover:text-white' }}">
                        <i class="bi bi-speedometer2 text-base mr-3"></i>
                        DASHBOARD
                    </a>
                @endif


                @if(auth()->user()->hasMenuAccess('stu_unit'))
                    <a href="{{ route('stu.index') }}" class="flex items-center px-4 py-3 rounded-xl font-extrabold text-xs uppercase tracking-wider transition duration-150 {{ Request::is('stu-unit*') ? 'bg-gradient-to-r from-blue-700 to-blue-800 text-white shadow-lg border-l-4 border-yellow-400' : 'text-blue-200 hover:bg-blue-900/80 hover:text-white' }}">
                        <i class="bi bi-graph-up text-base mr-3"></i>
                        STU UNIT
                    </a>
                @endif

                @if(auth()->user()->hasMenuAccess('stok_unit'))
                    <a href="{{ route('stok.index') }}" class="flex items-center px-4 py-3 rounded-xl font-extrabold text-xs uppercase tracking-wider transition duration-150 {{ Request::is('stok-unit*') ? 'bg-gradient-to-r from-blue-700 to-blue-800 text-white shadow-lg border-l-4 border-yellow-400' : 'text-blue-200 hover:bg-blue-900/80 hover:text-white' }}">
                        <i class="bi bi-box-seam text-base mr-3"></i>
                        STOK UNIT
                    </a>
                @endif


                @if(auth()->user()->hasMenuAccess('cabang'))
                    <a href="{{ route('cabang.index') }}" class="flex items-center px-4 py-3 rounded-xl font-extrabold text-xs uppercase tracking-wider transition duration-150 {{ Request::is('cabang*') ? 'bg-gradient-to-r from-blue-700 to-blue-800 text-white shadow-lg border-l-4 border-yellow-400' : 'text-blue-200 hover:bg-blue-900/80 hover:text-white' }}">
                        <i class="bi bi-building text-base mr-3"></i>
                        Cabang
                    </a>
                @endif

                @if(auth()->user()->hasMenuAccess('users'))
                    <a href="{{ route('users.index') }}" class="flex items-center px-4 py-3 rounded-xl font-extrabold text-xs uppercase tracking-wider transition duration-150 {{ Request::is('users*') ? 'bg-gradient-to-r from-blue-700 to-blue-800 text-white shadow-lg border-l-4 border-yellow-400' : 'text-blue-200 hover:bg-blue-900/80 hover:text-white' }}">
                        <i class="bi bi-people-fill text-base mr-3"></i>
                        Kelola User
                    </a>
                @endif
            @endauth
        </nav>

        <!-- User Info & Logout -->
        @auth
        <div class="p-4 border-t border-blue-900/80 shrink-0 bg-blue-950/60">
            <div class="flex items-center space-x-3 mb-3">
                <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center text-white font-black text-sm shrink-0 shadow-md">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-white truncate" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</p>
                    <span class="text-[9px] uppercase font-extrabold tracking-wider text-emerald-400 block mt-0.5">
                        <i class="bi bi-person-check-fill mr-0.5"></i>Akun Aktif
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-between pt-1">
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" title="Logout"
                        class="w-full flex items-center justify-center space-x-2 bg-rose-950/50 hover:bg-rose-900/80 text-rose-300 border border-rose-800/60 transition duration-150 py-2 rounded-xl text-xs font-black uppercase tracking-wider shadow-sm">
                        <i class="bi bi-box-arrow-right text-sm"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
        @endauth

        <div class="px-4 pb-3 pt-2 text-center text-[10px] text-blue-300 font-bold uppercase tracking-widest shrink-0">
            v2.5.5 &copy; 2026
        </div>

    </aside>

    <!-- Overlay backdrop for mobile sidebar (z-[65]) -->
    <div id="sidebar-backdrop" class="fixed inset-0 z-[65] bg-black/70 backdrop-blur-sm hidden lg:hidden transition-opacity duration-300"></div>

    <!-- Main Content Area -->
    <main class="flex-1 min-w-0 h-full overflow-y-auto p-3 sm:p-4 lg:p-5 bg-[#04060c] pb-20 lg:pb-5">
        <div class="w-full mx-auto">
            @yield('content')
        </div>
    </main>

    <!-- Mobile Bottom App Quick Navigation Bar (App-Style Experience) -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-[60] bg-[#080c1d]/95 border-t border-blue-900/80 backdrop-blur-xl shadow-[0_-5px_20px_rgba(0,0,0,0.8)] flex items-center justify-around py-2 px-1">
        @auth
            @if(auth()->user()->hasMenuAccess('dashboard'))
                <a href="{{ url('/') }}" class="flex flex-col items-center justify-center py-1 px-2 rounded-xl transition duration-150 {{ Request::is('/') ? 'text-yellow-400 font-black scale-105' : 'text-slate-400 hover:text-white' }}">
                    <i class="bi bi-speedometer2 text-lg leading-none mb-0.5"></i>
                    <span class="text-[9px] font-extrabold uppercase tracking-tight">Dashboard</span>
                </a>
            @endif


            @if(auth()->user()->hasMenuAccess('stu_unit'))
                <a href="{{ route('stu.index') }}" class="flex flex-col items-center justify-center py-1 px-2 rounded-xl transition duration-150 {{ Request::is('stu-unit*') ? 'text-yellow-400 font-black scale-105' : 'text-slate-400 hover:text-white' }}">
                    <i class="bi bi-graph-up text-lg leading-none mb-0.5"></i>
                    <span class="text-[9px] font-extrabold uppercase tracking-tight">STU Unit</span>
                </a>
            @endif

            @if(auth()->user()->hasMenuAccess('stok_unit'))
                <a href="{{ route('stok.index') }}" class="flex flex-col items-center justify-center py-1 px-2 rounded-xl transition duration-150 {{ Request::is('stok-unit*') ? 'text-yellow-400 font-black scale-105' : 'text-slate-400 hover:text-white' }}">
                    <i class="bi bi-box-seam text-lg leading-none mb-0.5"></i>
                    <span class="text-[9px] font-extrabold uppercase tracking-tight">Stok</span>
                </a>
            @endif


            <button id="mobile-bottom-menu-toggle" type="button" class="flex flex-col items-center justify-center py-1 px-2 rounded-xl text-blue-400 hover:text-white transition duration-150">
                <i class="bi bi-grid-fill text-lg leading-none mb-0.5"></i>
                <span class="text-[9px] font-extrabold uppercase tracking-tight">Menu</span>
            </button>
        @endauth
    </nav>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('mobile-menu-toggle');
        const closeBtn = document.getElementById('mobile-menu-close');
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            backdrop.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        const bottomToggleBtn = document.getElementById('mobile-bottom-menu-toggle');

        if (toggleBtn && closeBtn && sidebar && backdrop) {
            toggleBtn.addEventListener('click', openSidebar);
            if (bottomToggleBtn) bottomToggleBtn.addEventListener('click', openSidebar);
            closeBtn.addEventListener('click', closeSidebar);
            backdrop.addEventListener('click', closeSidebar);
        }



        // Instant Counter (Disabled JS Count Up Animation Loop for Maximum Speed & Performance)
        const initCounters = () => {
            const counters = document.querySelectorAll(".counter-animate");
            counters.forEach(el => {
                const target = parseFloat(el.getAttribute("data-target") || 0);
                const prefix = el.getAttribute("data-prefix") || "";
                const suffix = el.getAttribute("data-suffix") || "";
                const decimals = parseInt(el.getAttribute("data-decimals") || 0);
                
                const finalFormatted = decimals > 0 
                    ? target.toFixed(decimals).replace('.', ',')
                    : Math.floor(target).toLocaleString('id-ID');
                
                el.textContent = prefix + finalFormatted + suffix;
            });
        };

        initCounters();
    });
</script>

</body>
</html>