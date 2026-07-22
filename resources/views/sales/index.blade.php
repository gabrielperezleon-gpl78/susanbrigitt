@extends('layouts.app', [
'title' => 'Ventas | Susan Brigitt Studio',
'pageTitle' => 'Ventas'
])

@section('content')

<div class="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-center">
    <div>
        <p class="text-sm text-gray-500">
            Consulta las ventas registradas, ingresos, ganancias estimadas y salidas de inventario.
        </p>
    </div>

    <a href="{{ route('sales.create') }}"
        class="inline-flex items-center justify-center rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C]">
        + Registrar venta
    </a>
</div>

@if (session('success'))
<div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
    {{ session('success') }}
</div>
@endif

<section class="grid grid-cols-1 gap-5 md:grid-cols-5">
    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Ventas</p>
        <h2 class="mt-3 text-3xl font-bold">{{ $totalSales }}</h2>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Unidades vendidas</p>
        <h2 class="mt-3 text-3xl font-bold">{{ $totalUnits }}</h2>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Total USD</p>
        <h2 class="mt-3 text-3xl font-bold">${{ number_format($totalUsd, 2, ',', '.') }}</h2>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Total Bs</p>
        <h2 class="mt-3 text-3xl font-bold">Bs. {{ number_format($totalBs, 2, ',', '.') }}</h2>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Ganancia estimada</p>
        <h2 class="mt-3 text-3xl font-bold text-green-600">${{ number_format($totalProfitUsd, 2, ',', '.') }}</h2>
    </div>
</section>

<section class="mt-6 rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
    <div class="mb-6">
        <h2 class="text-lg font-bold">Historial de ventas</h2>
        <p class="mt-1 text-sm text-gray-500">
            Registro operativo de ventas y descuentos de inventario.
        </p>
    </div>

    <div class="overflow-hidden rounded-xl border border-black/5">
        <table class="min-w-262.5 w-full text-left text-sm">
            <thead class="bg-[#F8F5F2] text-gray-500">
                <tr>
                    <th class="whitespace-nowrap px-5 py-4">Fecha</th>
                    <th class="whitespace-nowrap px-5 py-4">Cliente</th>
                    <th class="whitespace-nowrap px-5 py-4">Producto</th>
                    <th class="whitespace-nowrap px-5 py-4 text-right">Unidades</th>
                    <th class="whitespace-nowrap px-5 py-4 text-right">Total USD</th>
                    <th class="whitespace-nowrap px-5 py-4 text-right">Tasa</th>
                    <th class="whitespace-nowrap px-5 py-4 text-right">Total Bs</th>
                    <th class="whitespace-nowrap px-5 py-4 text-right">Ganancia</th>
                    <th class="whitespace-nowrap px-5 py-4">Pago</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-black/5">
                @forelse ($sales as $sale)
                <tr>
                    <td class="whitespace-nowrap px-5 py-4">
                        {{ $sale->sale_date ? $sale->sale_date->format('d/m/Y') : $sale->created_at->format('d/m/Y') }}
                    </td>

                    <td class="px-5 py-4">
                        {{ $sale->customer_name ?: 'Cliente ocasional' }}
                    </td>

                    <td class="px-5 py-4">
                        <div class="space-y-1">
                            @foreach ($sale->items as $item)
                            <div>
                                <span class="font-medium">{{ $item->product?->name ?? 'Producto eliminado' }}</span>
                                <span class="text-gray-400">× {{ $item->quantity }}</span>
                            </div>
                            @endforeach
                        </div>
                    </td>

                    <td class="whitespace-nowrap px-5 py-4 text-right">
                        {{ $sale->items->sum('quantity') }}
                    </td>

                    <td class="whitespace-nowrap px-5 py-4 text-right font-semibold">
                        ${{ number_format((float) $sale->total_usd, 2, ',', '.') }}
                    </td>

                    <td class="whitespace-nowrap px-5 py-4 text-right">
                        {{ number_format((float) $sale->exchange_rate_value, 2, ',', '.') }}
                    </td>

                    <td class="whitespace-nowrap px-5 py-4 text-right font-semibold">
                        Bs. {{ number_format((float) $sale->total_bs, 2, ',', '.') }}
                    </td>

                    <td class="whitespace-nowrap px-5 py-4 text-right font-semibold text-green-600">
                        ${{ number_format((float) $sale->estimated_profit_usd, 2, ',', '.') }}
                    </td>

                    <td class="whitespace-nowrap px-5 py-4">
                        @php
                        $paymentLabels = [
                        'pago_movil' => 'Pago móvil',
                        'transferencia_bs' => 'Transferencia Bs',
                        'efectivo_usd' => 'Efectivo USD',
                        'binance' => 'Binance',
                        'zelle' => 'Zelle',
                        'mixto' => 'Mixto',
                        ];
                        @endphp

                        {{ $paymentLabels[$sale->payment_method] ?? $sale->payment_method }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-5 py-10 text-center text-gray-500">
                        No hay ventas registradas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

@endsection