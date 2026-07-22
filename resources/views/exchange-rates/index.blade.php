@extends('layouts.app', [
'title' => 'Tasas de cambio | Susan Brigitt Studio',
'pageTitle' => 'Tasas de cambio'
])

@section('content')

<div class="space-y-8">
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.24em] text-rose-400">
                Control cambiario
            </p>

            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">
                Tasas registradas
            </h1>

            <p class="mt-2 max-w-2xl text-sm leading-6 text-zinc-500">
                Consulta las tasas BCV, Binance y manuales utilizadas para valorar compras, ventas e inventario en bolívares.
            </p>
        </div>

        <a
            href="{{ route('exchange-rates.create') }}"
            class="inline-flex items-center justify-center rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C]">
            Registrar tasa
        </a>
    </div>

    @if (session('success'))
    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-[0.18em] text-zinc-400">
                Última tasa usada
            </p>
            <p class="mt-3 text-3xl font-semibold text-zinc-900">
                {{ $latestRate ? number_format((float) $latestRate->used_rate, 2, ',', '.') : '—' }}
            </p>
        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-[0.18em] text-zinc-400">
                Tasas activas
            </p>
            <p class="mt-3 text-3xl font-semibold text-zinc-900">
                {{ number_format($activeRates, 0, ',', '.') }}
            </p>
        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-[0.18em] text-zinc-400">
                Promedio usado
            </p>
            <p class="mt-3 text-3xl font-semibold text-zinc-900">
                {{ $averageUsedRate ? number_format($averageUsedRate, 2, ',', '.') : '—' }}
            </p>
        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-[0.18em] text-zinc-400">
                Binance
            </p>
            <p class="mt-3 text-3xl font-semibold text-zinc-900">
                {{ number_format($binanceRates, 0, ',', '.') }}
            </p>
        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-[0.18em] text-zinc-400">
                Manuales
            </p>
            <p class="mt-3 text-3xl font-semibold text-zinc-900">
                {{ number_format($manualRates, 0, ',', '.') }}
            </p>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-black/5 bg-white shadow-sm">
        <div class="border-b border-black/5 px-6 py-5">
            <h2 class="text-lg font-bold text-zinc-900">
                Historial de tasas
            </h2>

            <p class="mt-1 text-sm text-zinc-500">
                Registro de tasas aplicadas en operaciones del emprendimiento.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-100">
                <thead class="bg-[#F8F5F2]">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-zinc-500">Fecha</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-[0.16em] text-zinc-500">BCV</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-[0.16em] text-zinc-500">Binance</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-[0.16em] text-zinc-500">Manual</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-[0.16em] text-zinc-500">Usada</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-zinc-500">Fuente</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-zinc-500">Estado</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-[0.16em] text-zinc-500">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-zinc-100 bg-white">
                    @forelse ($exchangeRates as $rate)
                    <tr class="transition hover:bg-[#FFF0F4]/60">
                        <td class="whitespace-nowrap px-5 py-4 text-sm text-zinc-600">
                            {{ $rate->rate_date ? $rate->rate_date->format('d/m/Y') : $rate->created_at->format('d/m/Y') }}
                        </td>

                        <td class="whitespace-nowrap px-5 py-4 text-right text-sm text-zinc-600">
                            {{ $rate->bcv_rate ? number_format((float) $rate->bcv_rate, 2, ',', '.') : '—' }}
                        </td>

                        <td class="whitespace-nowrap px-5 py-4 text-right text-sm text-zinc-600">
                            {{ $rate->binance_rate ? number_format((float) $rate->binance_rate, 2, ',', '.') : '—' }}
                        </td>

                        <td class="whitespace-nowrap px-5 py-4 text-right text-sm text-zinc-600">
                            {{ $rate->manual_rate ? number_format((float) $rate->manual_rate, 2, ',', '.') : '—' }}
                        </td>

                        <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-semibold text-zinc-900">
                            {{ $rate->used_rate ? number_format((float) $rate->used_rate, 2, ',', '.') : '—' }}
                        </td>

                        <td class="whitespace-nowrap px-5 py-4 text-sm text-zinc-600">
                            @php
                            $sourceLabels = [
                            'bcv' => 'BCV',
                            'binance' => 'Binance',
                            'manual' => 'Manual',
                            ];
                            @endphp

                            {{ $sourceLabels[$rate->source] ?? $rate->source }}
                        </td>

                        <td class="whitespace-nowrap px-5 py-4 text-sm">
                            @if ($rate->status === 'active')
                            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                Activa
                            </span>
                            @else
                            <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs font-semibold text-zinc-500">
                                Inactiva
                            </span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-5 py-4 text-right">
                            <a
                                href="{{ route('exchange-rates.edit', $rate) }}"
                                class="inline-flex rounded-lg border border-black/10 bg-white px-3 py-2 text-xs font-semibold text-zinc-700 transition hover:bg-[#FFF0F4] hover:text-[#E46F8A]">
                                Editar
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center">
                            <p class="text-sm font-semibold text-zinc-900">
                                Todavía no hay tasas registradas.
                            </p>
                            <p class="mt-1 text-sm text-zinc-500">
                                Cuando registres una tasa, aparecerá en este historial.
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