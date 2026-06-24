<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Susan Brigitt Studio' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#F8F5F2] text-[#171717]" x-data="{ sidebarOpen: true }">

    <div class="flex min-h-screen">

        <aside
            class="fixed left-0 top-0 z-30 flex h-screen flex-col bg-[#17191D] text-white transition-all duration-300"
            :class="sidebarOpen ? 'w-64' : 'w-20'">

            <div class="px-4 py-8">
                <div class="text-center">
                    <div x-show="sidebarOpen" class="text-3xl font-semibold tracking-tight">
                        Susan Brigitt
                    </div>

                    <div x-show="sidebarOpen" class="mt-1 text-xs uppercase tracking-[0.35em] text-[#C9A15D]">
                        Studio
                    </div>

                    <div x-show="!sidebarOpen" class="text-2xl font-bold text-[#C9A15D]">
                        SB
                    </div>
                </div>
            </div>

            <nav class="flex-1 space-y-1 px-4">

                <a href="{{ route('dashboard') }}"
                    class="{{ request()->routeIs('dashboard') 
            ? 'flex items-center gap-3 rounded-xl bg-[#E46F8A] px-4 py-3 text-sm font-medium text-white shadow-sm' 
            : 'flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-300 transition hover:bg-white/10 hover:text-white' }}">
                    <span>⌂</span>
                    <span x-show="sidebarOpen">Dashboard</span>
                </a>

                <a href="{{ route('products.index') }}"
                    class="{{ request()->routeIs('products.*') 
            ? 'flex items-center gap-3 rounded-xl bg-[#E46F8A] px-4 py-3 text-sm font-medium text-white shadow-sm' 
            : 'flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-300 transition hover:bg-white/10 hover:text-white' }}">
                    <span>▣</span>
                    <span x-show="sidebarOpen">Productos</span>
                </a>

                <a href="{{ route('purchases.index') }}"
                    class="{{ request()->routeIs('purchases.*') 
        ? 'flex items-center gap-3 rounded-xl bg-[#E46F8A] px-4 py-3 text-sm font-medium text-white shadow-sm' 
        : 'flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-300 transition hover:bg-white/10 hover:text-white' }}">
                    <span>▤</span>
                    <span x-show="sidebarOpen">Compras</span>
                </a>

                <a href="{{ route('sales.index') }}"
                    class="{{ request()->routeIs('sales.*') 
        ? 'flex items-center gap-3 rounded-xl bg-[#E46F8A] px-4 py-3 text-sm font-medium text-white shadow-sm' 
        : 'flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-300 transition hover:bg-white/10 hover:text-white' }}">
                    <span>↗</span>
                    <span x-show="sidebarOpen">Ventas</span>
                </a>

                <a href="{{ route('inventory.index') }}"
                    class="{{ request()->routeIs('inventory.*') 
        ? 'flex items-center gap-3 rounded-xl bg-[#E46F8A] px-4 py-3 text-sm font-medium text-white shadow-sm' 
        : 'flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-300 transition hover:bg-white/10 hover:text-white' }}">
                    <span>◈</span>
                    <span x-show="sidebarOpen">Inventario</span>
                </a>

                <a href="{{ route('exchange-rates.index') }}"
                    class="{{ request()->routeIs('exchange-rates.*') 
        ? 'flex items-center gap-3 rounded-xl bg-[#E46F8A] px-4 py-3 text-sm font-medium text-white shadow-sm' 
        : 'flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-300 transition hover:bg-white/10 hover:text-white' }}">
                    <span>$</span>
                    <span x-show="sidebarOpen">Tasas de cambio</span>
                </a>

                <a href="#"
                    class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-300 transition hover:bg-white/10 hover:text-white">
                    <span>□</span>
                    <span x-show="sidebarOpen">Reportes</span>
                </a>

                <a href="#"
                    class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-300 transition hover:bg-white/10 hover:text-white">
                    <span>⚙</span>
                    <span x-show="sidebarOpen">Configuración</span>
                </a>

            </nav>

            <div class="border-t border-white/10 p-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#F3C8D1] text-sm font-bold text-[#7C3A48]">
                        S
                    </div>

                    <div x-show="sidebarOpen">
                        <div class="text-sm font-semibold">Susan</div>
                        <div class="text-xs text-gray-400">Administradora</div>
                    </div>
                </div>
            </div>

        </aside>

        <main
            class="min-h-screen flex-1 transition-all duration-300"
            :class="sidebarOpen ? 'ml-64' : 'ml-20'">

            <header class="sticky top-0 z-20 border-b border-black/5 bg-[#F8F5F2]/90 backdrop-blur">
                <div class="flex items-center justify-between px-8 py-5">

                    <div class="flex items-center gap-4">
                        <button
                            type="button"
                            @click="sidebarOpen = !sidebarOpen"
                            class="flex h-11 w-11 items-center justify-center rounded-xl border border-black/10 bg-white text-xl shadow-sm transition hover:bg-gray-50"
                            title="Mostrar u ocultar menú">
                            ☰
                        </button>

                        <div>
                            <h1 class="text-2xl font-bold">
                                {{ $pageTitle ?? 'Dashboard' }}
                            </h1>
                            <p class="mt-1 text-sm text-gray-500">
                                Gestión administrativa de inventario, compras y ventas.
                            </p>
                        </div>
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