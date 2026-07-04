@extends('layouts.app')

@section('title', 'Compras')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.24em] text-rose-400">
                Gestión de compras
            </p>

            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">
                Compras registradas
            </h1>

            <p class="mt-2 max-w-2xl text-sm leading-6 text-zinc-500">
                Consulta las compras realizadas, proveedores asociados, productos adquiridos, tasas aplicadas y montos convertidos a bolívares.
            </p>
        </div>

        <a
            href="{{ route('purchases.create') }}"
            class="inline-flex items-center justify-center border border-zinc-900 bg-zinc-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-zinc-700">
            Registrar compra
        </a>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
        <div class="border border-zinc-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-[0.18em] text-zinc-400">
                Compras
            </p>
            <p class="mt-3 text-3xl font-semibold text-zinc-900">
                {{ number_format($totalPurchases, 0, ',', '.') }}
            </p>
        </div>

        <div class="border border-zinc-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-[0.18em] text-zinc-400">
                Unidades compradas
            </p>
            <p class="mt-3 text-3xl font-semibold text-zinc-900">
                {{ number_format($totalUnits, 0, ',', '.') }}
            </p>
        </div>

        <div class="border border-zinc-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-[0.18em] text-zinc-400">
                Total USD
            </p>
            <p class="mt-3 text-3xl font-semibold text-zinc-900">
                ${{ number_format($totalUsd, 2, ',', '.') }}
            </p>
        </div>

        <div class="border border-zinc-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-[0.18em] text-zinc-400">
                Total Bs.
            </p>
            <p class="mt-3 text-3xl font-semibold text-zinc-900">
                Bs. {{ number_format($totalBs, 2, ',', '.') }}
            </p>
        </div>

        <div class="border border-zinc-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-[0.18em] text-zinc-400">
                Tasa promedio
            </p>
            <p class="mt-3 text-3xl font-semibold text-zinc-900">
                {{ $averageRate ? number_format($averageRate, 2, ',', '.') : '—' }}
            </p>
        </div>
    </div>

    <div class="overflow-hidden border border-zinc-200 bg-white shadow-sm">
        <div class="border-b border-zinc-200 px-5 py-4">
            <h2 class="text-base font-semibold text-zinc-900">
                Historial de compras
            </h2>

            <p class="mt-1 text-sm text-zinc-500">
                Registro operativo de entradas de mercancía al inventario.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200">
                <thead class="bg-zinc-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-zinc-500">
                            Fecha
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-zinc-500">
                            Proveedor
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-zinc-500">
                            Productos
                        </th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-[0.16em] text-zinc-500">
                            Unidades
                        </th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-[0.16em] text-zinc-500">
                            Total USD
                        </th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-[0.16em] text-zinc-500">
                            Tasa
                        </th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-[0.16em] text-zinc-500">
                            Total Bs.
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-zinc-100 bg-white">
                    @forelse ($purchases as $purchase)
                    @php
                    $purchaseDate = $purchase->purchase_date
                    ? \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y')
                    : $purchase->created_at->format('d/m/Y');
                    @endphp

                    <tr class="transition hover:bg-rose-50/40">
                        <td class="whitespace-nowrap px-5 py-4 text-sm text-zinc-600">
                            {{ $purchaseDate }}
                        </td>

                        <td class="px-5 py-4 text-sm font-medium text-zinc-900">
                            {{ $purchase->supplier->name ?? 'Sin proveedor' }}
                        </td>

                        <td class="px-5 py-4 text-sm text-zinc-600">
                            <div class="space-y-1">
                                @foreach ($purchase->items as $item)
                                <div>
                                    <span class="font-medium text-zinc-800">
                                        {{ $item->product->name ?? 'Producto eliminado' }}
                                    </span>
                                    <span class="text-zinc-400">
                                        × {{ number_format($item->quantity, 0, ',', '.') }}
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        </td>

                        <td class="whitespace-nowrap px-5 py-4 text-right text-sm text-zinc-600">
                            {{ number_format($purchase->items->sum('quantity'), 0, ',', '.') }}
                        </td>

                        <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-medium text-zinc-900">
                            ${{ number_format((float) ($purchase->total_usd ?? 0), 2, ',', '.') }}
                        </td>

                        <td class="whitespace-nowrap px-5 py-4 text-right text-sm text-zinc-600">
                            {{ $purchase->exchange_rate_value ? number_format((float) $purchase->exchange_rate_value, 2, ',', '.') : '—' }}
                        </td>

                        <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-medium text-zinc-900">
                            Bs. {{ number_format((float) ($purchase->total_bs ?? 0), 2, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center">
                            <p class="text-sm font-medium text-zinc-900">
                                Todavía no hay compras registradas.
                            </p>

                            <p class="mt-1 text-sm text-zinc-500">
                                Cuando registres compras, aparecerán en este historial.
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection