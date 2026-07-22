@extends('layouts.app', [
'title' => 'Dashboard | Susan Brigitt Studio',
'pageTitle' => 'Dashboard'
])

@section('content')

<div class="space-y-8">
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-start">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.24em] text-rose-400">
                Resumen operativo
            </p>

            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">
                Panel administrativo
            </h1>

            <p class="mt-2 max-w-2xl text-sm leading-6 text-zinc-500">
                Vista general de inventario, productos, compras, ventas, ganancias estimadas y tasa de cambio activa.
            </p>
        </div>

        <div class="flex flex-wrap gap-3">
            <a href="{{ route('products.create') }}"
                class="inline-flex items-center justify-center rounded-xl border border-black/10 bg-white px-5 py-3 text-sm font-semibold text-zinc-800 shadow-sm transition hover:bg-zinc-50">
                + Producto
            </a>

            <a href="{{ route('purchases.create') }}"
                class="inline-flex items-center justify-center rounded-xl border border-black/10 bg-white px-5 py-3 text-sm font-semibold text-zinc-800 shadow-sm transition hover:bg-zinc-50">
                + Compra
            </a>

            <a href="{{ route('sales.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C]">
                + Venta
            </a>
        </div>
    </div>

    <section class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">Productos registrados</p>
            <h2 class="mt-3 text-3xl font-bold">{{ number_format($totalProducts, 0, ',', '.') }}</h2>
        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">Unidades disponibles</p>
            <h2 class="mt-3 text-3xl font-bold">{{ number_format($availableUnits, 0, ',', '.') }}</h2>
        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">Valor del inventario</p>
            <h2 class="mt-3 text-3xl font-bold">${{ number_format($inventoryValue, 2, ',', '.') }}</h2>
        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">Tasa activa</p>
            <h2 class="mt-3 text-3xl font-bold">
                {{ $latestExchangeRate ? number_format((float) $latestExchangeRate->used_rate, 2, ',', '.') : '—' }}
            </h2>
            <p class="mt-2 text-xs text-gray-400">
                {{ $latestExchangeRate ? strtoupper($latestExchangeRate->source) : 'Sin tasa activa' }}
            </p>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">Ventas acumuladas USD</p>
            <h2 class="mt-3 text-3xl font-bold">${{ number_format($totalSalesUsd, 2, ',', '.') }}</h2>
        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">Ventas acumuladas Bs</p>
            <h2 class="mt-3 text-3xl font-bold">Bs. {{ number_format($totalSalesBs, 2, ',', '.') }}</h2>
        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">Ganancia estimada USD</p>
            <h2 class="mt-3 text-3xl font-bold text-green-600">${{ number_format($estimatedProfitUsd, 2, ',', '.') }}</h2>
        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">Ventas de hoy</p>
            <h2 class="mt-3 text-3xl font-bold">${{ number_format($todaySalesUsd, 2, ',', '.') }}</h2>
            <p class="mt-2 text-xs text-gray-400">
                {{ number_format($todaySalesCount, 0, ',', '.') }} operaciones
            </p>
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-3">
        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-zinc-900">Productos recientes</h2>
                    <p class="mt-1 text-sm text-gray-500">Últimos productos cargados.</p>
                </div>
            </div>

            <div class="space-y-4">
                @forelse ($recentProducts as $product)
                <div class="flex items-center justify-between border-b border-black/5 pb-3 last:border-0 last:pb-0">
                    <div>
                        <p class="text-sm font-semibold text-zinc-900">{{ $product->name }}</p>
                        <p class="mt-1 text-xs text-gray-400">
                            {{ $product->brand?->name ?? 'Sin marca' }}
                            @if ($product->tone)
                            · {{ $product->tone->name }}
                            @endif
                        </p>
                    </div>

                    <span class="rounded-full bg-[#F8F5F2] px-3 py-1 text-xs font-semibold text-zinc-700">
                        Stock: {{ $product->current_stock }}
                    </span>
                </div>
                @empty
                <p class="text-sm text-gray-500">No hay productos registrados.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <div class="mb-5">
                <h2 class="text-lg font-bold text-zinc-900">Ventas recientes</h2>
                <p class="mt-1 text-sm text-gray-500">Últimas salidas registradas.</p>
            </div>

            <div class="space-y-4">
                @forelse ($recentSales as $sale)
                <div class="border-b border-black/5 pb-3 last:border-0 last:pb-0">
                    <div class="flex items-center justify-between gap-4">
                        <p class="text-sm font-semibold text-zinc-900">
                            {{ $sale->customer_name ?: 'Cliente ocasional' }}
                        </p>

                        <span class="text-sm font-bold text-green-600">
                            ${{ number_format((float) $sale->total_usd, 2, ',', '.') }}
                        </span>
                    </div>

                    <p class="mt-1 text-xs text-gray-400">
                        {{ $sale->sale_date ? $sale->sale_date->format('d/m/Y') : $sale->created_at->format('d/m/Y') }}
                        · {{ $sale->items->sum('quantity') }} unidades
                    </p>
                </div>
                @empty
                <p class="text-sm text-gray-500">No hay ventas registradas.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-rose-100 bg-rose-50 p-6 shadow-sm">
            <div class="mb-5">
                <h2 class="text-lg font-bold text-zinc-900">Alertas de stock</h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ number_format($lowStockProducts, 0, ',', '.') }} productos en stock bajo o mínimo.
                </p>
            </div>

            <div class="space-y-4">
                @forelse ($lowStockList as $product)
                <div class="flex items-center justify-between border-b border-rose-100 pb-3 last:border-0 last:pb-0">
                    <div>
                        <p class="text-sm font-semibold text-zinc-900">{{ $product->name }}</p>
                        <p class="mt-1 text-xs text-gray-500">
                            Mínimo: {{ $product->minimum_stock }}
                        </p>
                    </div>

                    <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-[#E46F8A]">
                        Stock: {{ $product->current_stock }}
                    </span>
                </div>
                @empty
                <p class="text-sm text-gray-500">No hay alertas de stock.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <div class="mb-5">
            <h2 class="text-lg font-bold text-zinc-900">Resumen financiero</h2>
            <p class="mt-1 text-sm text-gray-500">
                Comparación básica entre compras, ventas y utilidad estimada.
            </p>
        </div>

        <div class="grid gap-5 md:grid-cols-3">
            <div class="rounded-2xl bg-[#F8F5F2] p-5">
                <p class="text-sm text-gray-500">Compras acumuladas USD</p>
                <h3 class="mt-3 text-2xl font-bold">${{ number_format($totalPurchasesUsd, 2, ',', '.') }}</h3>
            </div>

            <div class="rounded-2xl bg-[#F8F5F2] p-5">
                <p class="text-sm text-gray-500">Compras acumuladas Bs</p>
                <h3 class="mt-3 text-2xl font-bold">Bs. {{ number_format($totalPurchasesBs, 2, ',', '.') }}</h3>
            </div>

            <div class="rounded-2xl bg-[#ECFDF3] p-5">
                <p class="text-sm text-gray-500">Ganancia estimada acumulada</p>
                <h3 class="mt-3 text-2xl font-bold text-green-600">${{ number_format($estimatedProfitUsd, 2, ',', '.') }}</h3>
            </div>
        </div>
    </section>
</div>

@endsection