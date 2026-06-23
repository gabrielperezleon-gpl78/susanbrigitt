<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Susan Brigitt Studio' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#F8F5F2] text-[#171717]">

    <div class="flex min-h-screen">

        <aside class="fixed left-0 top-0 z-30 flex h-screen w-64 flex-col bg-[#17191D] text-white">

            <div class="px-8 py-8">
                <div class="text-center">
                    <div class="text-3xl font-semibold tracking-tight">
                        Susan Brigitt
                    </div>
                    <div class="mt-1 text-xs uppercase tracking-[0.35em] text-[#C9A15D]">
                        Studio
                    </div>
                </div>
            </div>

            <nav class="flex-1 space-y-1 px-4">

                <a href="{{ route('dashboard') }}"
                    class="{{ request()->routeIs('dashboard') 
            ? 'flex items-center gap-3 rounded-xl bg-[#E46F8A] px-4 py-3 text-sm font-medium text-white shadow-sm' 
            : 'flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-300 transition hover:bg-white/10 hover:text-white' }}">
                    <span>⌂</span>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('products.index') }}"
                    class="{{ request()->routeIs('products.*') 
            ? 'flex items-center gap-3 rounded-xl bg-[#E46F8A] px-4 py-3 text-sm font-medium text-white shadow-sm' 
            : 'flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-300 transition hover:bg-white/10 hover:text-white' }}">
                    <span>▣</span>
                    <span>Productos</span>
                </a>

                <a href="#"
                    class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-300 transition hover:bg-white/10 hover:text-white">
                    <span>▤</span>
                    <span>Compras</span>
                </a>

                <a href="#"
                    class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-300 transition hover:bg-white/10 hover:text-white">
                    <span>↗</span>
                    <span>Ventas</span>
                </a>

                <a href="#"
                    class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-300 transition hover:bg-white/10 hover:text-white">
                    <span>◈</span>
                    <span>Inventario</span>
                </a>

                <a href="#"
                    class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-300 transition hover:bg-white/10 hover:text-white">
                    <span>$</span>
                    <span>Tasas de cambio</span>
                </a>

                <a href="#"
                    class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-300 transition hover:bg-white/10 hover:text-white">
                    <span>□</span>
                    <span>Reportes</span>
                </a>

                <a href="#"
                    class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-300 transition hover:bg-white/10 hover:text-white">
                    <span>⚙</span>
                    <span>Configuración</span>
                </a>

            </nav>

            <div class="border-t border-white/10 p-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#F3C8D1] text-sm font-bold text-[#7C3A48]">
                        S
                    </div>
                    <div>
                        <div class="text-sm font-semibold">Susan</div>
                        <div class="text-xs text-gray-400">Administradora</div>
                    </div>
                </div>
            </div>

        </aside>

        <main class="ml-64 min-h-screen flex-1">

            <header class="sticky top-0 z-20 border-b border-black/5 bg-[#F8F5F2]/90 backdrop-blur">
                <div class="flex items-center justify-between px-8 py-5">

                    <div>
                        <h1 class="text-2xl font-bold">
                            {{ $pageTitle ?? 'Dashboard' }}
                        </h1>
                        <p class="mt-1 text-sm text-gray-500">
                            Gestión administrativa de inventario, compras y ventas.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">

                        <div class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm shadow-sm">
                            21 de mayo de 2024
                        </div>

                        <div class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm shadow-sm">
                            BCV: <strong>36,92</strong>
                        </div>

                        <div class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm shadow-sm">
                            Binance: <strong>37,65</strong>
                        </div>

                        <button class="rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C]">
                            + Nueva venta
                        </button>

                    </div>

                </div>
            </header>

            <div class="p-8">
                @yield('content')
            </div>

        </main>

    </div>

</body>

</html>