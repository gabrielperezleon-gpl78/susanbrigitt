@extends('layouts.app', [
'title' => 'Editar tasa | Susan Brigitt Studio',
'pageTitle' => 'Editar tasa'
])

@section('content')

<div class="space-y-8">
    <div>
        <a
            href="{{ route('exchange-rates.index') }}"
            class="text-sm font-semibold text-[#E46F8A]">
            ← Volver a tasas
        </a>

        <p class="mt-4 text-sm font-medium uppercase tracking-[0.24em] text-rose-400">
            Control cambiario
        </p>

        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">
            Editar tasa registrada
        </h1>

        <p class="mt-2 max-w-3xl text-sm leading-6 text-zinc-500">
            Puedes corregir la tasa BCV, registrar o modificar una tasa manual y seleccionar cuál será utilizada. El valor Binance permanece bloqueado porque corresponde a una consulta automática.
        </p>
    </div>

    @if ($errors->any())
    <div class="rounded-2xl border border-red-200 bg-red-50 p-5 text-sm text-red-700">
        <p class="font-bold">
            Revisa los datos del formulario.
        </p>

        <ul class="mt-2 list-inside list-disc space-y-1">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form
        action="{{ route('exchange-rates.update', $exchangeRate) }}"
        method="POST"
        class="grid gap-6 xl:grid-cols-[1fr_360px]"
        x-data="{
            source: @js(old('source', $exchangeRate->source)),
            bcvRate: @js(old('bcv_rate', $exchangeRate->bcv_rate)),
            binanceRate: @js($exchangeRate->binance_rate),
            manualRate: @js(old('manual_rate', $exchangeRate->manual_rate)),

            parseDecimal(value) {
                if (
                    value === null ||
                    value === undefined ||
                    value === ''
                ) {
                    return 0;
                }

                value = String(value)
                    .trim()
                    .replace(/\s/g, '')
                    .replace('$', '')
                    .replace('Bs.', '')
                    .replace('Bs', '');

                const lastComma = value.lastIndexOf(',');
                const lastDot = value.lastIndexOf('.');

                if (
                    lastComma !== -1 &&
                    lastDot !== -1
                ) {
                    if (lastComma > lastDot) {
                        value = value
                            .replace(/\./g, '')
                            .replace(',', '.');
                    } else {
                        value = value.replace(/,/g, '');
                    }
                } else if (lastComma !== -1) {
                    value = value.replace(',', '.');
                }

                return Number(value) || 0;
            },

            get usedRate() {
                if (this.source === 'bcv') {
                    return this.parseDecimal(this.bcvRate);
                }

                if (this.source === 'manual') {
                    return this.parseDecimal(this.manualRate);
                }

                return this.parseDecimal(this.binanceRate);
            },

            handleDecimalKey(event) {
                if (event.code !== 'NumpadDecimal') {
                    return;
                }

                event.preventDefault();

                const input = event.target;
                const start =
                    input.selectionStart ??
                    input.value.length;

                const end =
                    input.selectionEnd ??
                    input.value.length;

                input.value =
                    input.value.slice(0, start) +
                    '.' +
                    input.value.slice(end);

                input.setSelectionRange(
                    start + 1,
                    start + 1
                );

                input.dispatchEvent(
                    new Event('input', {
                        bubbles: true
                    })
                );
            },

            formatNumber(value) {
                return new Intl.NumberFormat('es-VE', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 4
                }).format(value || 0);
            }
        }">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <section class="rounded-2xl border border-black/5 bg-white shadow-sm">
                <div class="border-b border-black/5 px-6 py-5">
                    <h2 class="text-lg font-bold text-zinc-900">
                        Datos de la tasa
                    </h2>

                    <p class="mt-1 text-sm text-zinc-500">
                        Corrige los valores de referencia y selecciona la tasa que debe usar el sistema.
                    </p>
                </div>

                <div class="grid gap-5 p-6 md:grid-cols-2">
                    <div>
                        <label
                            for="rate_date"
                            class="mb-2 block text-sm font-semibold text-gray-700">
                            Fecha
                        </label>

                        <input
                            id="rate_date"
                            name="rate_date"
                            type="date"
                            value="{{ old(
                                'rate_date',
                                $exchangeRate->rate_date?->format('Y-m-d')
                            ) }}"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                            required>
                    </div>

                    <div>
                        <label
                            for="rate_time"
                            class="mb-2 block text-sm font-semibold text-gray-700">
                            Hora
                        </label>

                        <input
                            id="rate_time"
                            name="rate_time"
                            type="time"
                            value="{{ old(
                                'rate_time',
                                $exchangeRate->rate_time?->format('H:i')
                            ) }}"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                    </div>

                    <div>
                        <label
                            for="bcv_rate"
                            class="mb-2 block text-sm font-semibold text-gray-700">
                            Tasa BCV
                        </label>

                        <input
                            id="bcv_rate"
                            name="bcv_rate"
                            type="text"
                            inputmode="decimal"
                            value="{{ old(
                                'bcv_rate',
                                $exchangeRate->bcv_rate
                            ) }}"
                            x-model="bcvRate"
                            x-on:keydown="handleDecimalKey($event)"
                            placeholder="Ejemplo: 170,2500"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                    </div>

                    <div>
                        <label
                            for="binance_rate"
                            class="mb-2 block text-sm font-semibold text-gray-700">
                            Tasa Binance automática
                        </label>

                        <input
                            id="binance_rate"
                            type="text"
                            value="{{ $exchangeRate->binance_rate
                                ? number_format(
                                    (float) $exchangeRate->binance_rate,
                                    4,
                                    '.',
                                    ''
                                )
                                : '' }}"
                            class="w-full cursor-not-allowed rounded-xl border border-black/10 bg-zinc-100 px-4 py-3 text-sm text-zinc-500 outline-none"
                            readonly>

                        <p class="mt-2 text-xs leading-5 text-zinc-500">
                            Este valor se conserva como fue obtenido automáticamente y no puede editarse desde este formulario.
                        </p>
                    </div>

                    <div>
                        <label
                            for="manual_rate"
                            class="mb-2 block text-sm font-semibold text-gray-700">
                            Tasa manual
                        </label>

                        <input
                            id="manual_rate"
                            name="manual_rate"
                            type="text"
                            inputmode="decimal"
                            value="{{ old(
                                'manual_rate',
                                $exchangeRate->manual_rate
                            ) }}"
                            x-model="manualRate"
                            x-on:keydown="handleDecimalKey($event)"
                            placeholder="Ejemplo: 175,0000"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                    </div>

                    <div>
                        <label
                            for="source"
                            class="mb-2 block text-sm font-semibold text-gray-700">
                            Fuente utilizada
                        </label>

                        <select
                            id="source"
                            name="source"
                            x-model="source"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                            required>
                            <option value="bcv">
                                BCV
                            </option>

                            <option
                                value="binance"
                                @disabled(! $exchangeRate->binance_rate)
                                >
                                Binance automática
                            </option>

                            <option value="manual">
                                Manual
                            </option>
                        </select>
                    </div>

                    <div>
                        <label
                            for="status"
                            class="mb-2 block text-sm font-semibold text-gray-700">
                            Estado
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                            required>
                            <option
                                value="active"
                                @selected(
                                old( 'status' ,
                                $exchangeRate->status
                                ) === 'active'
                                )
                                >
                                Activa
                            </option>

                            <option
                                value="inactive"
                                @selected(
                                old( 'status' ,
                                $exchangeRate->status
                                ) === 'inactive'
                                )
                                >
                                Inactiva
                            </option>
                        </select>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
                <label
                    for="notes"
                    class="block text-lg font-bold text-zinc-900">
                    Observaciones
                </label>

                <textarea
                    id="notes"
                    name="notes"
                    rows="4"
                    class="mt-5 w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                    placeholder="Motivo de la corrección o referencia utilizada.">{{ old('notes', $exchangeRate->notes) }}</textarea>
            </section>
        </div>

        <aside class="space-y-6">
            <section class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-rose-400">
                    Resultado
                </p>

                <div class="mt-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <span class="text-sm text-zinc-500">
                            Fuente seleccionada
                        </span>

                        <strong
                            class="text-sm text-zinc-900"
                            x-text="source.toUpperCase()"></strong>
                    </div>

                    <div>
                        <span class="text-sm text-zinc-500">
                            Tasa que usará el sistema
                        </span>

                        <p class="mt-2 text-4xl font-semibold tracking-tight text-zinc-900">
                            <span
                                x-text="formatNumber(usedRate)"></span>
                        </p>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-blue-100 bg-blue-50 p-5">
                <p class="text-sm font-semibold text-blue-900">
                    Historial de operaciones
                </p>

                <p class="mt-2 text-sm leading-6 text-blue-700">
                    Las compras y ventas ya registradas conservarán la tasa aplicada en el momento de la operación.
                </p>
            </section>

            <div class="space-y-3">
                <button
                    type="submit"
                    class="w-full rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C]">
                    Guardar cambios
                </button>

                <a
                    href="{{ route('exchange-rates.index') }}"
                    class="block w-full rounded-xl border border-black/10 px-5 py-3 text-center text-sm font-semibold text-zinc-600 transition hover:bg-zinc-50">
                    Cancelar
                </a>
            </div>
        </aside>
    </form>
</div>

@endsection