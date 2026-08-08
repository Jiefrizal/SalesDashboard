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
    </style>
</head>

<body class="bg-[#04060c] font-sans antialiased text-slate-100">

<div class="flex flex-col lg:flex-row h-screen overflow-hidden bg-[#04060c]">

    <!-- Mobile Top Navigation Header -->
    <header class="bg-blue-900 text-white p-3.5 flex items-center justify-between lg:hidden shadow-md z-30 shrink-0">
        <div class="flex items-center space-x-3">
            <img src="{{ asset('logo.png') }}" alt="ASPACINDO" class="h-9 w-auto object-contain">
            <div>
                <h1 class="text-xs font-extrabold tracking-wider text-white uppercase">PT ASPACINDO KEDATON MOTOR</h1>
            </div>
        </div>
        <button id="mobile-menu-toggle" class="text-white hover:text-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded p-1">
            <i class="bi bi-list text-2xl"></i>
        </button>
    </header>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-52 bg-blue-900 text-white transform -translate-x-full lg:translate-x-0 lg:static lg:h-full transition-transform duration-300 ease-in-out shadow-2xl lg:shadow-none flex flex-col shrink-0">

        <!-- Brand -->
        <div class="flex flex-col items-center justify-center py-6 border-b border-blue-700 relative px-4 text-center shrink-0">
            <img src="{{ asset('logo.png') }}" alt="ASPACINDO" class="h-16 w-auto object-contain mb-2">
            <h1 class="text-xs font-extrabold tracking-wider text-white uppercase px-2">
                PT ASPACINDO KEDATON MOTOR
            </h1>
            <!-- Close button for mobile -->
            <button id="mobile-menu-close" class="absolute top-4 right-4 text-white hover:text-blue-300 lg:hidden">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="mt-6 flex-1 overflow-y-auto">
            @auth
                @if(auth()->user()->hasMenuAccess('dashboard'))
                    <a href="{{ url('/') }}" class="flex items-center px-6 py-3 {{ Request::is('/') ? 'bg-blue-800 border-l-4 border-yellow-400' : '' }} hover:bg-blue-800">
                        <i class="bi bi-speedometer2 mr-3"></i>
                        DASHBOARD
                    </a>
                @endif

                @if(auth()->user()->hasMenuAccess('stu_unit'))
                    <a href="{{ route('stu.index') }}" class="flex items-center px-6 py-3 {{ Request::is('stu-unit*') ? 'bg-blue-800 border-l-4 border-yellow-400' : '' }} hover:bg-blue-800">
                        <i class="bi bi-graph-up mr-3"></i>
                        STU UNIT
                    </a>
                @endif

                @if(auth()->user()->hasMenuAccess('stok_unit'))
                    <a href="{{ route('stok.index') }}" class="flex items-center px-6 py-3 {{ Request::is('stok-unit*') ? 'bg-blue-800 border-l-4 border-yellow-400' : '' }} hover:bg-blue-800">
                        <i class="bi bi-box-seam mr-3"></i>
                        STOK UNIT
                    </a>
                @endif

                @if(auth()->user()->hasMenuAccess('digital_marketing'))
                    <a href="{{ route('digital-marketing.index') }}" class="flex items-center px-6 py-3 {{ Request::is('digital-marketing*') ? 'bg-blue-800 border-l-4 border-yellow-400' : '' }} hover:bg-blue-800">
                        <i class="bi bi-instagram mr-3"></i>
                        SOSIAL MEDIA
                    </a>
                @endif



                @if(auth()->user()->hasMenuAccess('cabang'))
                    <a href="{{ route('cabang.index') }}" class="flex items-center px-6 py-3 {{ Request::is('cabang*') ? 'bg-blue-800 border-l-4 border-yellow-400' : '' }} hover:bg-blue-800">
                        <i class="bi bi-building mr-3"></i>
                        Cabang
                    </a>
                @endif

                @if(auth()->user()->hasMenuAccess('users'))
                    <a href="{{ route('users.index') }}" class="flex items-center px-6 py-3 {{ Request::is('users*') ? 'bg-blue-800 border-l-4 border-yellow-400' : '' }} hover:bg-blue-800">
                        <i class="bi bi-people-fill mr-3"></i>
                        Kelola User
                    </a>
                @endif
            @endauth

        </nav>

        <!-- User Info & Logout -->
        @auth
        <div class="p-4 border-t border-blue-800 shrink-0">
            <div class="flex items-center space-x-3 mb-3">
                <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-sm shrink-0">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-white truncate" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</p>
                </div>
            </div>
            <div class="flex items-center justify-between">
                {{-- Access Status --}}
                <span class="text-[9px] uppercase font-extrabold tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 rounded-full px-2 py-0.5">
                    <i class="bi bi-person-check-fill mr-0.5"></i>Akun Aktif
                </span>

                {{-- Logout button --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Logout"
                        class="text-blue-300 hover:text-rose-400 transition duration-150 p-1 rounded hover:bg-rose-500/10">
                        <i class="bi bi-box-arrow-right text-base"></i>
                    </button>
                </form>
            </div>
        </div>
        @endauth

        <div class="px-4 pb-3 text-center text-[10px] text-blue-300 font-bold uppercase tracking-widest shrink-0">
            v1.0.0 &copy; 2026
        </div>

    </aside>

    <!-- Overlay backdrop for mobile sidebar -->
    <div id="sidebar-backdrop" class="fixed inset-0 z-40 bg-black/50 hidden lg:hidden transition-opacity duration-300"></div>

    <!-- Content -->
    <main class="flex-1 min-w-0 h-full overflow-y-auto p-3 sm:p-4 lg:p-5 bg-[#04060c]">
        <div class="w-full mx-auto">
            @yield('content')
        </div>
    </main>

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

        if (toggleBtn && closeBtn && sidebar && backdrop) {
            toggleBtn.addEventListener('click', openSidebar);
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