@extends('layouts.app', [
'title' => 'Compras | Susan Brigitt Studio',
'pageTitle' => 'Compras'
])

@section('content')

<div
    class="w-full min-w-0 max-w-full overflow-x-hidden"
    x-data="{
        cancelModalOpen: false,
        cancelAction: '',
        purchaseReference: '',
        cancellationReason: '',

        openCancelModal(purchaseId, reference) {
            this.cancelAction =
                @js(route('purchases.cancel', ['purchase' => '__PURCHASE__']))
                    .replace('__PURCHASE__', purchaseId);

            this.purchaseReference = reference;
            this.cancellationReason = '';
            this.cancelModalOpen = true;

            this.$nextTick(() => {
                this.$refs.cancellationReason?.focus();
            });
        },

        closeCancelModal() {
            this.cancelModalOpen = false;
            this.cancelAction = '';
            this.purchaseReference = '';
            this.cancellationReason = '';
        }
    }"
    x-on:keydown.escape.window="closeCancelModal()">
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.24em] text-rose-400">
                Gestión de compras
            </p>

            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">
                Compras registradas
            </h1>

            <p class="mt-2 max-w-2xl text-sm leading-6 text-zinc-500">
                Consulta las entradas de mercancía, las tasas aplicadas y el historial de anulaciones.
            </p>
        </div>

        <a
            href="{{ route('purchases.create') }}"
            class="inline-flex items-center justify-center rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C]">
            Registrar compra
        </a>
    </div>

    @if (session('success'))
    <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-5 text-sm text-red-700">
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

    <section class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-6">
        <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-[0.18em] text-zinc-400">
                Confirmadas
            </p>

            <p class="mt-3 text-3xl font-semibold text-zinc-900">
                {{ number_format($totalPurchases, 0, ',', '.') }}
            </p>
        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-[0.18em] text-zinc-400">
                Anuladas
            </p>

            <p class="mt-3 text-3xl font-semibold text-zinc-900">
                {{ number_format($cancelledPurchasesCount, 0, ',', '.') }}
            </p>
        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-[0.18em] text-zinc-400">
                Unidades
            </p>

            <p class="mt-3 text-3xl font-semibold text-zinc-900">
                {{ number_format($totalUnits, 0, ',', '.') }}
            </p>
        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-[0.18em] text-zinc-400">
                Total USD
            </p>

            <p class="mt-3 text-3xl font-semibold text-zinc-900">
                ${{ number_format($totalUsd, 2, ',', '.') }}
            </p>
        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-[0.18em] text-zinc-400">
                Total Bs.
            </p>

            <p class="mt-3 text-3xl font-semibold text-zinc-900">
                Bs. {{ number_format($totalBs, 2, ',', '.') }}
            </p>
        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-[0.18em] text-zinc-400">
                Tasa promedio
            </p>

            <p class="mt-3 text-3xl font-semibold text-zinc-900">
                {{ $averageRate
                    ? number_format((float) $averageRate, 2, ',', '.')
                    : '—'
                }}
            </p>
        </div>
    </section>

    <section class="mt-6 w-full min-w-0 max-w-full overflow-hidden rounded-2xl border border-black/5 bg-white shadow-sm">
        <div class="border-b border-black/5 px-6 py-5">
            <h2 class="text-lg font-semibold text-zinc-900">
                Historial de compras
            </h2>

            <p class="mt-1 text-sm text-zinc-500">
                Las compras anuladas se conservan como parte del registro de auditoría.
            </p>
        </div>

        <div class="w-full min-w-0 max-w-full overflow-x-auto">
            <table class="w-full min-w-345 divide-y divide-zinc-200 text-left text-sm">
                <thead class="bg-[#F8F5F2] text-xs uppercase tracking-[0.14em] text-zinc-500">
                    <tr>
                        <th class="whitespace-nowrap px-5 py-4">
                            Estado
                        </th>

                        <th class="whitespace-nowrap px-5 py-4">
                            Fecha
                        </th>

                        <th class="whitespace-nowrap px-5 py-4">
                            Proveedor
                        </th>

                        <th class="px-5 py-4">
                            Productos
                        </th>

                        <th class="whitespace-nowrap px-5 py-4 text-right">
                            Unidades
                        </th>

                        <th class="whitespace-nowrap px-5 py-4 text-right">
                            Total USD
                        </th>

                        <th class="whitespace-nowrap px-5 py-4">
                            Fuente
                        </th>

                        <th class="whitespace-nowrap px-5 py-4 text-right">
                            Tasa
                        </th>

                        <th class="whitespace-nowrap px-5 py-4 text-right">
                            Total Bs.
                        </th>

                        <th class="whitespace-nowrap px-5 py-4">
                            Pago
                        </th>

                        <th class="whitespace-nowrap px-5 py-4 text-right">
                            Acciones
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-black/5 bg-white">
                    @forelse ($purchases as $purchase)
                    @php
                    $isCancelled = $purchase->isCancelled();

                    $purchaseDate = $purchase->purchase_date
                    ?->format('d/m/Y')
                    ?? $purchase->created_at?->format('d/m/Y')
                    ?? '—';

                    $rateSourceLabel = match ($purchase->rate_source) {
                    'bcv' => 'BCV',
                    'binance' => 'Binance',
                    'manual' => 'Manual',
                    default => '—',
                    };

                    $paymentMethodLabel = match ($purchase->payment_method) {
                    'pago_movil' => 'Pago móvil',
                    'transferencia_bs' => 'Transferencia Bs',
                    'efectivo_usd' => 'Efectivo USD',
                    'binance' => 'Binance',
                    'zelle' => 'Zelle',
                    'mixto' => 'Mixto',
                    default => '—',
                    };

                    $purchaseReference =
                    'Compra #' . $purchase->id
                    . ' · '
                    . ($purchase->supplier?->name ?? 'Sin proveedor');
                    @endphp

                    <tr
                        @class([ 'transition' , 'hover:bg-rose-50/40'=> ! $isCancelled,
                        'bg-zinc-50 text-zinc-400' => $isCancelled,
                        ])
                        >
                        <td class="whitespace-nowrap px-5 py-4 align-top">
                            @if ($isCancelled)
                            <span class="inline-flex rounded-full bg-zinc-200 px-3 py-1 text-xs font-semibold text-zinc-700">
                                Anulada
                            </span>

                            <div class="mt-3 max-w-62.5 text-xs leading-5 text-zinc-500">
                                <p>
                                    <span class="font-semibold text-zinc-600">
                                        Motivo:
                                    </span>

                                    {{ $purchase->cancellation_reason }}
                                </p>

                                @if ($purchase->cancelled_at)
                                <p class="mt-1">
                                    {{ $purchase->cancelled_at->format('d/m/Y H:i') }}
                                </p>
                                @endif

                                @if ($purchase->cancelledBy)
                                <p class="mt-1">
                                    Por: {{ $purchase->cancelledBy->name }}
                                </p>
                                @endif
                            </div>
                            @else
                            <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                Confirmada
                            </span>
                            @endif
                        </td>

                        <td class="whitespace-nowrap px-5 py-4 align-top">
                            {{ $purchaseDate }}
                        </td>

                        <td class="px-5 py-4 align-top">
                            <span
                                @class([ 'font-medium' , 'text-zinc-900'=> ! $isCancelled,
                                'text-zinc-500 line-through' => $isCancelled,
                                ])
                                >
                                {{ $purchase->supplier?->name ?? 'Sin proveedor' }}
                            </span>
                        </td>

                        <td class="px-5 py-4 align-top">
                            <div class="space-y-2">
                                @foreach ($purchase->items as $item)
                                <div
                                    @class([ 'text-zinc-600'=> ! $isCancelled,
                                    'text-zinc-400 line-through' => $isCancelled,
                                    ])
                                    >
                                    <span class="font-medium">
                                        {{ $item->product?->name ?? 'Producto eliminado' }}
                                    </span>

                                    <span class="text-zinc-400">
                                        × {{ number_format($item->quantity, 0, ',', '.') }}
                                    </span>

                                    @if ($item->product?->unitMeasure)
                                    <span class="text-zinc-400">
                                        {{ $item->product->unitMeasure->abbreviation
                                                        ?? $item->product->unitMeasure->name
                                                    }}
                                    </span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </td>

                        <td
                            @class([ 'whitespace-nowrap px-5 py-4 text-right align-top' , 'line-through'=> $isCancelled,
                            ])
                            >
                            {{ number_format(
                                    $purchase->items->sum('quantity'),
                                    0,
                                    ',',
                                    '.'
                                ) }}
                        </td>

                        <td
                            @class([ 'whitespace-nowrap px-5 py-4 text-right align-top font-medium' , 'text-zinc-900'=> ! $isCancelled,
                            'text-zinc-400 line-through' => $isCancelled,
                            ])
                            >
                            ${{ number_format(
                                    (float) $purchase->total_usd,
                                    2,
                                    ',',
                                    '.'
                                ) }}
                        </td>

                        <td
                            @class([ 'whitespace-nowrap px-5 py-4 align-top' , 'line-through'=> $isCancelled,
                            ])
                            >
                            {{ $rateSourceLabel }}
                        </td>

                        <td
                            @class([ 'whitespace-nowrap px-5 py-4 text-right align-top' , 'line-through'=> $isCancelled,
                            ])
                            >
                            {{ number_format(
                                    (float) $purchase->exchange_rate_value,
                                    2,
                                    ',',
                                    '.'
                                ) }}
                        </td>

                        <td
                            @class([ 'whitespace-nowrap px-5 py-4 text-right align-top font-medium' , 'text-zinc-900'=> ! $isCancelled,
                            'text-zinc-400 line-through' => $isCancelled,
                            ])
                            >
                            Bs. {{ number_format(
                                    (float) $purchase->total_bs,
                                    2,
                                    ',',
                                    '.'
                                ) }}
                        </td>

                        <td
                            @class([ 'whitespace-nowrap px-5 py-4 align-top' , 'line-through'=> $isCancelled,
                            ])
                            >
                            {{ $paymentMethodLabel }}
                        </td>

                        <td class="whitespace-nowrap px-5 py-4 text-right align-top">
                            @if (! $isCancelled)
                            <div class="flex justify-end gap-2">
                                <a
                                    href="{{ route('purchases.edit', $purchase) }}"
                                    class="inline-flex rounded-lg border border-black/10 bg-white px-3 py-2 text-xs font-semibold text-zinc-700 transition hover:bg-zinc-50">
                                    Editar
                                </a>

                                <button
                                    type="button"
                                    x-on:click="openCancelModal(
                                                {{ $purchase->id }},
                                                @js($purchaseReference)
                                            )"
                                    class="inline-flex rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 transition hover:bg-red-100">
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
                        <td colspan="11" class="px-5 py-12 text-center">
                            <p class="text-sm font-medium text-zinc-900">
                                Todavía no hay compras registradas.
                            </p>

                            <p class="mt-1 text-sm text-zinc-500">
                                Cuando registres una compra, aparecerá en este historial.
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div
        x-show="cancelModalOpen"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 py-8"
        role="dialog"
        aria-modal="true"
        aria-labelledby="cancel-purchase-title">
        <div
            x-on:click.self="closeCancelModal()"
            class="absolute inset-0"></div>

        <div
            x-show="cancelModalOpen"
            x-transition
            class="relative z-10 w-full max-w-lg rounded-2xl bg-white shadow-2xl">
            <form
                method="POST"
                x-bind:action="cancelAction">
                @csrf
                @method('PATCH')

                <div class="border-b border-black/5 px-6 py-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-red-500">
                        Operación irreversible
                    </p>

                    <h2
                        id="cancel-purchase-title"
                        class="mt-2 text-xl font-bold text-zinc-900">
                        Anular compra
                    </h2>

                    <p class="mt-2 text-sm leading-6 text-zinc-500">
                        La compra permanecerá en el historial, pero sus unidades serán retiradas del inventario.
                    </p>
                </div>

                <div class="space-y-5 px-6 py-5">
                    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-amber-700">
                            Compra seleccionada
                        </p>

                        <p
                            class="mt-1 text-sm font-medium text-amber-900"
                            x-text="purchaseReference"></p>
                    </div>

                    <div>
                        <label
                            for="cancellation_reason"
                            class="mb-2 block text-sm font-semibold text-zinc-700">
                            Motivo de la anulación
                            <span class="text-red-500">*</span>
                        </label>

                        <textarea
                            id="cancellation_reason"
                            name="cancellation_reason"
                            rows="4"
                            minlength="5"
                            maxlength="1000"
                            x-ref="cancellationReason"
                            x-model="cancellationReason"
                            placeholder="Ejemplo: Compra registrada por error."
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-red-400 focus:ring-4 focus:ring-red-100"
                            required></textarea>

                        <div class="mt-2 flex justify-between text-xs text-zinc-400">
                            <span>
                                Mínimo 5 caracteres
                            </span>

                            <span
                                x-text="`${cancellationReason.length}/1000`"></span>
                        </div>
                    </div>

                    <div class="rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm leading-6 text-red-700">
                        La operación se rechazará si parte de las unidades de esta compra ya fue vendida o utilizada.
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-black/5 px-6 py-5 sm:flex-row sm:justify-end">
                    <button
                        type="button"
                        x-on:click="closeCancelModal()"
                        class="rounded-xl border border-black/10 bg-white px-5 py-3 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-50">
                        Volver
                    </button>

                    <button
                        type="submit"
                        class="rounded-xl bg-red-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50"
                        x-bind:disabled="cancellationReason.trim().length < 5">
                        Confirmar anulación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection