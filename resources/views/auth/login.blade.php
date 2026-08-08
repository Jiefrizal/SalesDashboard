<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Sales Dashboard</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(ellipse at 60% 0%, rgba(30,58,138,0.45) 0%, transparent 70%),
                        radial-gradient(ellipse at 10% 100%, rgba(99,102,241,0.25) 0%, transparent 60%),
                        #020617;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Animated background grid */
        .bg-grid {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(96,165,250,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(96,165,250,0.04) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
        }

        /* Glow orbs */
        @keyframes float-orb {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50%       { transform: translate(-20px, 20px) scale(1.08); }
        }
        .orb-1 { animation: float-orb 8s ease-in-out infinite; }
        .orb-2 { animation: float-orb 10s ease-in-out infinite reverse; }

        .login-card {
            background: rgba(15,23,42,0.85);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(96,165,250,0.25);
            box-shadow:
                0 0 0 1px rgba(96,165,250,0.08),
                0 32px 64px -12px rgba(0,0,0,0.8),
                0 0 80px -20px rgba(96,165,250,0.15);
            border-radius: 1.5rem;
            width: 100%;
            max-width: 440px;
            padding: 2.5rem;
        }

        /* Input style */
        .input-field {
            width: 100%;
            background: rgba(30,41,59,0.8);
            border: 1px solid rgba(96,165,250,0.18);
            color: #e2e8f0;
            padding: 0.75rem 1rem 0.75rem 2.75rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            font-family: inherit;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
        .input-field::placeholder { color: #475569; }
        .input-field:focus {
            border-color: rgba(96,165,250,0.55);
            box-shadow: 0 0 0 3px rgba(96,165,250,0.12);
        }
        .input-field.is-error {
            border-color: rgba(244,63,94,0.6);
            box-shadow: 0 0 0 3px rgba(244,63,94,0.10);
        }

        /* Buttons */
        .btn-viewer {
            width: 100%;
            background: linear-gradient(135deg, #1d4ed8 0%, #4f46e5 100%);
            color: white;
            font-weight: 800;
            font-size: 0.95rem;
            letter-spacing: 0.04em;
            padding: 0.9rem 1.2rem;
            border-radius: 0.85rem;
            border: 1px solid rgba(129,140,248,0.3);
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 24px -4px rgba(79,70,229,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
        }
        .btn-viewer:hover {
            opacity: 0.95;
            transform: translateY(-2px);
            box-shadow: 0 8px 32px -4px rgba(79,70,229,0.7);
        }
        .btn-viewer:active { transform: translateY(0); }

        .btn-admin {
            width: 100%;
            background: rgba(30,41,59,0.7);
            color: #94a3b8;
            font-weight: 700;
            font-size: 0.85rem;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            border: 1px solid rgba(148,163,184,0.15);
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-admin:hover {
            color: #f1f5f9;
            background: rgba(30,41,59,0.95);
            border-color: rgba(96,165,250,0.4);
        }
    </style>
</head>
<body>
    <!-- Animated background grid -->
    <div class="bg-grid"></div>

    <!-- Floating glow orbs -->
    <div class="orb-1 fixed -top-32 -left-32 w-96 h-96 rounded-full bg-blue-600/10 blur-3xl pointer-events-none"></div>
    <div class="orb-2 fixed -bottom-32 -right-32 w-96 h-96 rounded-full bg-indigo-600/10 blur-3xl pointer-events-none"></div>

    <div class="login-card relative z-10">

        <!-- Logo & Brand -->
        <div class="flex flex-col items-center mb-8">
            <div class="relative mb-4">
                <img src="{{ asset('logo.png') }}" alt="ASPACINDO" class="h-16 w-auto object-contain">
            </div>
            <h1 class="text-white font-extrabold text-lg tracking-wider uppercase text-center">
                PT Aspacindo Kedaton Motor
            </h1>
            <p class="text-slate-400 text-xs mt-1 tracking-widest uppercase">Sales Dashboard</p>
            <div class="mt-3 h-px w-16 bg-gradient-to-r from-transparent via-blue-500 to-transparent"></div>
        </div>

        <!-- Error alert -->
        @if ($errors->any())
            <div class="mb-5 bg-rose-500/10 border border-rose-500/30 text-rose-300 rounded-xl px-4 py-3 flex items-center space-x-2 text-sm">
                <i class="bi bi-exclamation-triangle-fill text-rose-400 shrink-0"></i>
                <span>{{ $errors->first('username') ?? $errors->first('email') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-5 bg-rose-500/10 border border-rose-500/30 text-rose-300 rounded-xl px-4 py-3 flex items-center space-x-2 text-sm">
                <i class="bi bi-exclamation-triangle-fill text-rose-400 shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Direct 1-Click Viewer Entry -->
        <form method="POST" action="{{ route('login.viewer') }}" class="mb-6">
            @csrf
            <button type="submit" class="btn-viewer flex flex-col items-center justify-center py-3">
                <div class="flex items-center space-x-2">
                    <i class="bi bi-eye-fill text-lg"></i>
                    <span>Masuk ke Dashboard</span>
                </div>
                <span class="text-[10px] font-normal opacity-80 normal-case tracking-normal">(Akses Viewer / Hanya Lihat)</span>
            </button>
        </form>

        <!-- Divider / Toggle for Admin Login -->
        <div class="relative flex items-center justify-center my-6">
            <div class="border-t border-slate-800 w-full"></div>
            <span class="bg-[#0f172a] px-3 text-[10px] text-slate-500 uppercase tracking-widest absolute">Akses Pengelola</span>
        </div>

        <!-- Toggle Admin Form Button -->
        <button type="button" id="toggle-admin-btn" class="btn-admin flex items-center justify-center space-x-2">
            <i class="bi bi-shield-lock-fill"></i>
            <span>Login</span>
            <i class="bi bi-chevron-down text-xs ml-1 transition-transform duration-200" id="admin-chevron"></i>
        </button>

        <!-- Admin Login Form (Hidden by default, can be toggled or auto-opened if error) -->
        <div id="admin-form-container" class="mt-4 hidden transition-all duration-300">
            <form method="POST" action="{{ route('login') }}" class="space-y-3.5 bg-slate-900/60 p-4 rounded-xl border border-slate-800">
                @csrf
                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">USER NAME</label>
                    <div class="relative">
                        <i class="bi bi-person-fill absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-xs pointer-events-none"></i>
                        <input
                            type="text"
                            name="username"
                            id="admin-email"
                            placeholder="aspacindo"
                            autocomplete="username"
                            class="input-field"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Password</label>
                    <div class="relative">
                        <i class="bi bi-lock-fill absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-xs pointer-events-none"></i>
                        <input
                            type="password"
                            name="password"
                            id="admin-password"
                            placeholder="••••••••"
                            autocomplete="current-password"
                            class="input-field"
                        >
                    </div>
                </div>

                <div class="pt-1">
                    <button type="submit" id="admin-submit-btn" disabled class="w-full bg-slate-700/60 text-slate-400 border border-slate-700 font-bold text-xs py-2.5 px-4 rounded-lg transition-all flex items-center justify-center space-x-2 cursor-not-allowed opacity-60">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>Login</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center text-[10px] text-slate-600 mt-6 uppercase tracking-widest">v1.0.0 &copy; 2026 Aspacindo</p>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('toggle-admin-btn');
            const formContainer = document.getElementById('admin-form-container');
            const chevron = document.getElementById('admin-chevron');

            const adminEmail = document.getElementById('admin-email');
            const adminPassword = document.getElementById('admin-password');
            const adminSubmitBtn = document.getElementById('admin-submit-btn');

            function checkAdminInputs() {
                if (adminEmail && adminPassword && adminSubmitBtn) {
                    const emailValid = adminEmail.value.trim().length > 0;
                    const passValid = adminPassword.value.trim().length > 0;
                    if (emailValid && passValid) {
                        adminSubmitBtn.disabled = false;
                        adminSubmitBtn.className = 'w-full bg-blue-600 hover:bg-blue-500 active:scale-98 text-white font-bold text-xs py-2.5 px-4 rounded-lg transition-all border border-blue-500 flex items-center justify-center space-x-2 cursor-pointer shadow-lg shadow-blue-600/30';
                    } else {
                        adminSubmitBtn.disabled = true;
                        adminSubmitBtn.className = 'w-full bg-slate-700/60 text-slate-400 border border-slate-700 font-bold text-xs py-2.5 px-4 rounded-lg transition-all flex items-center justify-center space-x-2 cursor-not-allowed opacity-60';
                    }
                }
            }

            if (adminEmail && adminPassword) {
                adminEmail.addEventListener('input', checkAdminInputs);
                adminPassword.addEventListener('input', checkAdminInputs);
                checkAdminInputs();
            }

            // If there's an error from post, keep admin form open
            @if ($errors->any())
                if (formContainer) formContainer.classList.remove('hidden');
                if (chevron) chevron.classList.add('rotate-180');
            @endif

            if (toggleBtn && formContainer) {
                toggleBtn.addEventListener('click', function() {
                    formContainer.classList.toggle('hidden');
                    if (chevron) chevron.classList.toggle('rotate-180');
                });
            }
        });
    </script>
</body>
</html>
