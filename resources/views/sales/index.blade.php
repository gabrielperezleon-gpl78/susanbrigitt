@extends('layouts.app', [
'title' => 'Ventas | Susan Brigitt Studio',
'pageTitle' => 'Ventas'
])

@section('content')

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

<div
    class="w-full min-w-0 max-w-full overflow-x-hidden"
    x-data="{
        cancelOpen: false,
        cancelAction: '',
        cancelSaleNumber: '',
        cancellationReason: ''
    }">
    <div class="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-center">
        <div>
            <p class="text-sm text-gray-500">
                Consulta las ventas confirmadas y anuladas, los ingresos, las ganancias estimadas y los movimientos de inventario.
            </p>
        </div>

        <a
            href="{{ route('sales.create') }}"
            class="inline-flex items-center justify-center rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C]">
            + Registrar venta
        </a>
    </div>

    @if (session('success'))
    <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
        <p class="font-semibold">
            No fue posible completar la operación.
        </p>

        <ul class="mt-2 list-inside list-disc space-y-1">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <section class="grid grid-cols-1 gap-5 md:grid-cols-3 xl:grid-cols-6">
        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">
                Ventas confirmadas
            </p>

            <h2 class="mt-3 text-3xl font-bold">
                {{ $totalSales }}
            </h2>
        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">
                Ventas anuladas
            </p>

            <h2 class="mt-3 text-3xl font-bold text-red-600">
                {{ $cancelledSalesCount }}
            </h2>
        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">
                Unidades vendidas
            </p>

            <h2 class="mt-3 text-3xl font-bold">
                {{ $totalUnits }}
            </h2>
        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">
                Total USD
            </p>

            <h2 class="mt-3 text-3xl font-bold">
                ${{ number_format($totalUsd, 2, ',', '.') }}
            </h2>
        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">
                Total Bs
            </p>

            <h2 class="mt-3 text-3xl font-bold">
                Bs. {{ number_format($totalBs, 2, ',', '.') }}
            </h2>
        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">
                Ganancia estimada
            </p>

            <h2 class="mt-3 text-3xl font-bold text-green-600">
                ${{ number_format($totalProfitUsd, 2, ',', '.') }}
            </h2>
        </div>
    </section>

    <section class="mt-6 w-full min-w-0 max-w-full rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h2 class="text-lg font-bold">
                Historial de ventas
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Las ventas anuladas permanecen visibles como parte del registro histórico, pero no se incluyen en los indicadores financieros.
            </p>
        </div>

        <div class="w-full min-w-0 max-w-full overflow-x-auto rounded-xl border border-black/5">
            <table class="w-full min-w-[1320px] text-left text-sm">
                <thead class="bg-[#F8F5F2] text-gray-500">
                    <tr>
                        <th class="whitespace-nowrap px-5 py-4">
                            Fecha
                        </th>

                        <th class="whitespace-nowrap px-5 py-4">
                            Cliente
                        </th>

                        <th class="whitespace-nowrap px-5 py-4">
                            Producto
                        </th>

                        <th class="whitespace-nowrap px-5 py-4 text-right">
                            Unidades
                        </th>

                        <th class="whitespace-nowrap px-5 py-4 text-right">
                            Total USD
                        </th>

                        <th class="whitespace-nowrap px-5 py-4 text-right">
                            Tasa
                        </th>

                        <th class="whitespace-nowrap px-5 py-4 text-right">
                            Total Bs
                        </th>

                        <th class="whitespace-nowrap px-5 py-4 text-right">
                            Ganancia
                        </th>

                        <th class="whitespace-nowrap px-5 py-4">
                            Pago
                        </th>

                        <th class="whitespace-nowrap px-5 py-4">
                            Estado
                        </th>

                        <th class="whitespace-nowrap px-5 py-4 text-right">
                            Acciones
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-black/5">
                    @forelse ($sales as $sale)
                    <tr @class([ 'transition' , 'bg-zinc-50 text-zinc-500'=> $sale->isCancelled(),
                        ])>
                        <td class="whitespace-nowrap px-5 py-4">
                            {{ $sale->sale_date
                                    ? $sale->sale_date->format('d/m/Y')
                                    : $sale->created_at->format('d/m/Y') }}
                        </td>

                        <td class="px-5 py-4">
                            {{ $sale->customer_name ?: 'Cliente ocasional' }}
                        </td>

                        <td class="px-5 py-4">
                            <div class="space-y-1">
                                @foreach ($sale->items as $item)
                                <div>
                                    <span class="font-medium">
                                        {{ $item->product?->name ?? 'Producto eliminado' }}
                                    </span>

                                    <span class="text-gray-400">
                                        × {{ $item->quantity }}
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        </td>

                        <td class="whitespace-nowrap px-5 py-4 text-right">
                            {{ $sale->items->sum('quantity') }}
                        </td>

                        <td
                            @class([ 'whitespace-nowrap px-5 py-4 text-right font-semibold' , 'line-through'=> $sale->isCancelled(),
                            ])
                            >
                            ${{ number_format(
                                    (float) $sale->total_usd,
                                    2,
                                    ',',
                                    '.'
                                ) }}
                        </td>

                        <td
                            @class([ 'whitespace-nowrap px-5 py-4 text-right' , 'line-through'=> $sale->isCancelled(),
                            ])
                            >
                            {{ number_format(
                                    (float) $sale->exchange_rate_value,
                                    2,
                                    ',',
                                    '.'
                                ) }}
                        </td>

                        <td
                            @class([ 'whitespace-nowrap px-5 py-4 text-right font-semibold' , 'line-through'=> $sale->isCancelled(),
                            ])
                            >
                            Bs. {{ number_format(
                                    (float) $sale->total_bs,
                                    2,
                                    ',',
                                    '.'
                                ) }}
                        </td>

                        <td
                            @class([ 'whitespace-nowrap px-5 py-4 text-right font-semibold' , 'text-green-600'=> ! $sale->isCancelled(),
                            'line-through text-zinc-400' => $sale->isCancelled(),
                            ])
                            >
                            ${{ number_format(
                                    (float) $sale->estimated_profit_usd,
                                    2,
                                    ',',
                                    '.'
                                ) }}
                        </td>

                        <td class="whitespace-nowrap px-5 py-4">
                            {{ $paymentLabels[$sale->payment_method]
                                    ?? $sale->payment_method }}
                        </td>

                        <td class="px-5 py-4">
                            @if ($sale->isCancelled())
                            <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                Anulada
                            </span>

                            <div class="mt-2 max-w-xs space-y-1 text-xs leading-5 text-zinc-500">
                                <p>
                                    {{ $sale->cancellation_reason }}
                                </p>

                                @if ($sale->cancelled_at)
                                <p>
                                    {{ $sale->cancelled_at->format('d/m/Y H:i') }}
                                </p>
                                @endif

                                @if ($sale->cancelledBy)
                                <p>
                                    Por: {{ $sale->cancelledBy->name }}
                                </p>
                                @endif
                            </div>
                            @else
                            <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                Confirmada
                            </span>
                            @endif
                        </td>

                        <td class="whitespace-nowrap px-5 py-4 text-right">
                            @if (! $sale->isCancelled())
                            <div class="flex justify-end gap-2">
                                <a
                                    href="{{ route('sales.edit', $sale) }}"
                                    class="inline-flex rounded-lg border border-black/10 bg-white px-3 py-2 text-xs font-semibold text-zinc-700 transition hover:bg-zinc-50">
                                    Editar
                                </a>

                                <button
                                    type="button"
                                    x-on:click="
                                                cancelAction = '{{ route('sales.cancel', $sale) }}';
                                                cancelSaleNumber = '{{ $sale->id }}';
                                                cancellationReason = '';
                                                cancelOpen = true;
                                                $nextTick(() => $refs.cancelReason.focus());
                                            "
                                    class="inline-flex rounded-lg border border-red-200 bg-white px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50">
                                    Anular
                                </button>
                            </div>
                            @else
                            <span class="text-xs font-medium text-zinc-400">
                                Sin acciones
                            </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td
                            colspan="11"
                            class="px-5 py-10 text-center text-gray-500">
                            No hay ventas registradas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- Modal de anulación --}}
    <div
        x-show="cancelOpen"
        x-cloak
        x-transition.opacity
        x-on:keydown.escape.window="cancelOpen = false"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        role="dialog"
        aria-modal="true">
        <div
            x-on:click.self="cancelOpen = false"
            class="absolute inset-0"></div>

        <div
            x-transition
            class="relative z-10 w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.18em] text-red-500">
                        Acción irreversible
                    </p>

                    <h2 class="mt-2 text-2xl font-bold text-zinc-900">
                        Anular venta #<span x-text="cancelSaleNumber"></span>
                    </h2>
                </div>

                <button
                    type="button"
                    x-on:click="cancelOpen = false"
                    class="rounded-lg px-3 py-2 text-zinc-400 transition hover:bg-zinc-100 hover:text-zinc-700"
                    aria-label="Cerrar">
                    ×
                </button>
            </div>

            <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm leading-6 text-amber-800">
                La venta permanecerá en el historial, pero será excluida de los ingresos y reportes. Las unidades vendidas serán restituidas automáticamente al inventario.
            </div>

            <form
                method="POST"
                x-bind:action="cancelAction"
                class="mt-6">
                @csrf
                @method('PATCH')

                <label
                    for="cancellation_reason"
                    class="mb-2 block text-sm font-semibold text-zinc-700">
                    Motivo de la anulación
                    <span class="text-red-500">*</span>
                </label>

                <textarea
                    id="cancellation_reason"
                    name="cancellation_reason"
                    x-ref="cancelReason"
                    x-model="cancellationReason"
                    rows="4"
                    minlength="5"
                    maxlength="1000"
                    required
                    placeholder="Ejemplo: El cliente desistió de la compra."
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none transition focus:border-red-400 focus:ring-4 focus:ring-red-100"></textarea>

                <p class="mt-2 text-xs text-zinc-500">
                    El motivo quedará registrado junto con el usuario y la fecha de anulación.
                </p>

                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button
                        type="button"
                        x-on:click="cancelOpen = false"
                        class="rounded-xl border border-black/10 px-5 py-3 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-50">
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="rounded-xl bg-red-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-red-700">
                        Confirmar anulación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection