@extends('layouts.app', [
'title' => 'Registrar tasa | Susan Brigitt Studio',
'pageTitle' => 'Registrar tasa'
])

@section('content')

<div class="space-y-8">
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
        <div>
            <a href="{{ route('exchange-rates.index') }}" class="text-sm font-semibold text-[#E46F8A]">
                ← Volver a tasas
            </a>

            <p class="mt-4 text-sm font-medium uppercase tracking-[0.24em] text-rose-400">
                Control cambiario
            </p>

            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">
                Registrar nueva tasa
            </h1>

            <p class="mt-2 max-w-2xl text-sm leading-6 text-zinc-500">
                Registra la tasa BCV, Binance o manual que será usada como referencia para compras y ventas.
            </p>
        </div>
    </div>

    @if ($errors->any())
    <div class="rounded-2xl border border-red-200 bg-red-50 p-5 text-sm text-red-700">
        <p class="font-bold">Revisa los datos del formulario.</p>

        <ul class="mt-2 list-inside list-disc space-y-1">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form
        action="{{ route('exchange-rates.store') }}"
        method="POST"
        class="grid gap-6 xl:grid-cols-[1fr_360px]"
        x-data="{
            source: @js(old('source', 'binance')),
            bcvRate: @js((float) old('bcv_rate', $latestRate?->bcv_rate ?? 0)),
            binanceRate: @js((float) old('binance_rate', $latestRate?->binance_rate ?? $latestRate?->used_rate ?? 0)),
            manualRate: @js((float) old('manual_rate', $latestRate?->manual_rate ?? 0)),
            get usedRate() {
                if (this.source === 'bcv') return this.bcvRate;
                if (this.source === 'manual') return this.manualRate;
                return this.binanceRate;
            },
            formatNumber(value) {
                return new Intl.NumberFormat('es-VE', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(value || 0);
            }
        }">
        @csrf

        <div class="space-y-6">
            <section class="rounded-2xl border border-black/5 bg-white shadow-sm">
                <div class="border-b border-black/5 px-6 py-5">
                    <h2 class="text-lg font-bold text-zinc-900">
                        Datos de la tasa
                    </h2>

                    <p class="mt-1 text-sm text-zinc-500">
                        Indica la fecha, las tasas disponibles y cuál será la fuente usada por el sistema.
                    </p>
                </div>

                <div class="grid gap-5 p-6 md:grid-cols-2">

                    <div class="md:col-span-2">
                        <label for="save_mode" class="mb-2 block text-sm font-semibold text-gray-700">
                            Tipo de registro <span class="text-[#E46F8A]">*</span>
                        </label>

                        <select
                            id="save_mode"
                            name="save_mode"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                            required>
                            <option value="create_new" @selected(old('save_mode', 'create_new' )==='create_new' )>
                                Nueva tasa del día
                            </option>
                            <option value="update_existing" @selected(old('save_mode')==='update_existing' )>
                                Corrección de la tasa registrada para esa fecha
                            </option>
                        </select>

                        <p class="mt-2 text-xs leading-5 text-gray-500">
                            Usa “Nueva tasa del día” cuando el dólar cambió durante el día. Usa “Corrección” si deseas reemplazar la última tasa guardada para esa fecha.
                        </p>
                    </div>
                    <div>
                        <label for="rate_date" class="mb-2 block text-sm font-semibold text-gray-700">
                            Fecha de la tasa <span class="text-[#E46F8A]">*</span>
                        </label>

                        <input
                            id="rate_date"
                            name="rate_date"
                            type="date"
                            value="{{ old('rate_date', now()->toDateString()) }}"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                            required>
                    </div>
                    <div>
                        <label for="rate_time" class="mb-2 block text-sm font-semibold text-gray-700">
                            Hora de referencia
                        </label>

                        <input
                            id="rate_time"
                            name="rate_time"
                            type="time"
                            value="{{ old('rate_time', now()->format('H:i')) }}"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                    </div>
                    <div>
                        <label for="source" class="mb-2 block text-sm font-semibold text-gray-700">
                            Fuente usada <span class="text-[#E46F8A]">*</span>
                        </label>

                        <select
                            id="source"
                            name="source"
                            x-model="source"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                            required>
                            <option value="binance">Binance</option>
                            <option value="bcv">BCV</option>
                            <option value="manual">Manual</option>
                        </select>
                    </div>

                    <div>
                        <label for="bcv_rate" class="mb-2 block text-sm font-semibold text-gray-700">
                            Tasa BCV
                        </label>

                        <input
                            id="bcv_rate"
                            name="bcv_rate"
                            type="number"
                            min="0.01"
                            step="0.0001"
                            value="{{ old('bcv_rate', $latestRate?->bcv_rate) }}"
                            x-model.number="bcvRate"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                            placeholder="Ejemplo: 36.5000">
                    </div>

                    <div>
                        <label for="binance_rate" class="mb-2 block text-sm font-semibold text-gray-700">
                            Tasa Binance
                        </label>

                        <input
                            id="binance_rate"
                            name="binance_rate"
                            type="number"
                            min="0.01"
                            step="0.0001"
                            value="{{ old('binance_rate', $latestRate?->binance_rate ?? $latestRate?->used_rate) }}"
                            x-model.number="binanceRate"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                            placeholder="Ejemplo: 37.8000">
                    </div>

                    <div>
                        <label for="manual_rate" class="mb-2 block text-sm font-semibold text-gray-700">
                            Tasa manual
                        </label>

                        <input
                            id="manual_rate"
                            name="manual_rate"
                            type="number"
                            min="0.01"
                            step="0.0001"
                            value="{{ old('manual_rate', $latestRate?->manual_rate) }}"
                            x-model.number="manualRate"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                            placeholder="Ejemplo: 38.0000">
                    </div>

                    <div>
                        <label for="status" class="mb-2 block text-sm font-semibold text-gray-700">
                            Estado <span class="text-[#E46F8A]">*</span>
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                            required>
                            <option value="active" @selected(old('status', 'active' )==='active' )>Activa</option>
                            <option value="inactive" @selected(old('status')==='inactive' )>Inactiva</option>
                        </select>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-zinc-900">
                    Observaciones
                </h2>

                <textarea
                    id="notes"
                    name="notes"
                    rows="4"
                    placeholder="Ejemplo: tasa tomada de Binance P2P, referencia BCV del día, ajuste manual por operación específica..."
                    class="mt-5 w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">{{ old('notes') }}</textarea>
            </section>
        </div>

        <aside class="space-y-6">
            <section class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-rose-400">
                    Resumen
                </p>

                <div class="mt-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <span class="text-sm text-zinc-500">Fuente seleccionada</span>
                        <span class="text-sm font-semibold text-zinc-900" x-text="source.toUpperCase()"></span>
                    </div>

                    <div>
                        <span class="text-sm text-zinc-500">Tasa que usará el sistema</span>
                        <p class="mt-2 text-4xl font-semibold tracking-tight text-zinc-900">
                            <span x-text="formatNumber(usedRate)"></span>
                        </p>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-rose-100 bg-rose-50 p-5">
                <p class="text-sm font-semibold text-zinc-900">
                    Uso operativo
                </p>

                <p class="mt-2 text-sm leading-6 text-zinc-600">
                    La última tasa activa será tomada automáticamente por el formulario de compras y, más adelante, por ventas.
                </p>
            </section>

            <button
                type="submit"
                class="w-full rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C]">
                Guardar tasa
            </button>
        </aside>
    </form>
</div>

@endsection