@extends('layouts.app', [
'title' => 'Registrar compra | Susan Brigitt Studio',
'pageTitle' => 'Registrar compra'
])

@section('content')

<div class="mb-6">
    <a href="{{ route('purchases.index') }}" class="text-sm font-semibold text-[#E46F8A]">
        ← Volver a compras
    </a>
    <p class="mt-2 text-sm text-gray-500">
        Registra el ingreso de mercancía y actualiza el inventario disponible.
    </p>
</div>

@if ($errors->any())
<div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-5 text-sm text-red-700">
    <p class="font-bold">Revisa los datos del formulario.</p>

    <ul class="mt-2 list-inside list-disc space-y-1">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form
    action="{{ route('purchases.store') }}"
    method="POST"
    class="space-y-6"
    x-data="{
        quantity: @js((float) old('quantity', 1)),
        unitCostUsdInput: @js(old('unit_cost_usd', '')),
        exchangeRateValueInput: @js(old('exchange_rate_value', $latestExchangeRate?->used_rate ?? '')),

        rateSource: @js(old('rate_source', $latestExchangeRate?->source ?? 'binance')),
        rateOptions: {
            bcv: @js((string) ($latestExchangeRate?->bcv_rate ?? '')),
            binance: @js((string) ($latestExchangeRate?->binance_rate ?? $latestExchangeRate?->used_rate ?? '')),
            manual: @js((string) ($latestExchangeRate?->manual_rate ?? $latestExchangeRate?->used_rate ?? '')),
        },

        updateExchangeRateValue() {
            const value = this.rateOptions[this.rateSource] || '';
                if (value !== '') {
                    this.exchangeRateValueInput = String(value);
                }
        },

        parseDecimal(value) {
            if (value === null || value === undefined || value === '') return 0;

            value = String(value)
                .trim()
                .replace(/\s/g, '')
                .replace('$', '')
                .replace('Bs.', '')
                .replace('Bs', '');

            const lastComma = value.lastIndexOf(',');
            const lastDot = value.lastIndexOf('.');

            if (lastComma !== -1 && lastDot !== -1) {
                if (lastComma > lastDot) {
                    value = value.replace(/\./g, '').replace(',', '.');
                } else {
                    value = value.replace(/,/g, '');
                }
            } else if (lastComma !== -1) {
                value = value.replace(',', '.');
            }

            return Number(value) || 0;
        },

        handleDecimalKey(event) {
            if (event.code !== 'NumpadDecimal') return;

            event.preventDefault();

            const input = event.target;
            const separator = '.';
            const start = input.selectionStart ?? input.value.length;
            const end = input.selectionEnd ?? input.value.length;

            input.value = input.value.slice(0, start) + separator + input.value.slice(end);
            input.setSelectionRange(start + 1, start + 1);
            input.dispatchEvent(new Event('input', { bubbles: true }));
        },

        get unitCostUsd() {
            return this.parseDecimal(this.unitCostUsdInput);
        },

        get exchangeRateValue() {
            return this.parseDecimal(this.exchangeRateValueInput);
        },

        get totalUsd() {
            return this.quantity * this.unitCostUsd;
        },
        get totalBs() {
            return this.totalUsd * this.exchangeRateValue;
        },
        formatNumber(value) {
            return new Intl.NumberFormat('es-VE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(value || 0);
        }
    }">
    @csrf

    <section class="rounded-2xl border border-black/5 bg-white shadow-sm">

        <div class="border-b border-black/5 px-6 py-5">
            <h2 class="text-lg font-bold">Información de la compra</h2>
            <p class="mt-1 text-sm text-gray-500">
                Datos principales de proveedor, producto, cantidad y costo.
            </p>
        </div>

        <div class="grid gap-5 p-6 md:grid-cols-2 xl:grid-cols-3">

            <div>
                <label for="purchase_date" class="mb-2 block text-sm font-semibold text-gray-700">
                    Fecha de compra <span class="text-[#E46F8A]">*</span>
                </label>
                <input
                    id="purchase_date"
                    name="purchase_date"
                    type="date"
                    value="{{ old('purchase_date', now()->toDateString()) }}"
                    class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                    required>
            </div>

            <div>
                <label for="supplier_id" class="mb-2 block text-sm font-semibold text-gray-700">
                    Proveedor <span class="text-[#E46F8A]">*</span>
                </label>
                <select
                    id="supplier_id"
                    name="supplier_id"
                    class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                    required>
                    <option value="">Seleccionar proveedor</option>
                    @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" @selected(old('supplier_id')==$supplier->id)>
                        {{ $supplier->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="product_id" class="mb-2 block text-sm font-semibold text-gray-700">
                    Producto <span class="text-[#E46F8A]">*</span>
                </label>
                <select
                    id="product_id"
                    name="product_id"
                    class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                    required>
                    <option value="">Seleccionar producto</option>
                    @foreach ($products as $product)
                    <option value="{{ $product->id }}" @selected(old('product_id')==$product->id)>
                        {{ $product->name }} · Stock actual: {{ $product->current_stock }} {{ $product->unitMeasure?->abbreviation ?? $product->unitMeasure?->name ?? '' }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="quantity" class="mb-2 block text-sm font-semibold text-gray-700">
                    Cantidad comprada <span class="text-[#E46F8A]">*</span>
                </label>
                <input
                    id="quantity"
                    name="quantity"
                    type="number"
                    min="1"
                    step="1"
                    value="{{ old('quantity', 1) }}"
                    x-model.number="quantity"
                    class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                    required>
            </div>

            <div>
                <label for="unit_cost_usd" class="mb-2 block text-sm font-semibold text-gray-700">
                    Costo unitario USD <span class="text-[#E46F8A]">*</span>
                </label>
                <input
                    id="unit_cost_usd"
                    name="unit_cost_usd"
                    type="text"
                    inputmode="decimal"
                    value="{{ old('unit_cost_usd') }}"
                    x-model="unitCostUsdInput"
                    x-on:keydown="handleDecimalKey($event)"
                    class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 outline-none transition focus:border-zinc-900"
                    required>
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Total compra USD
                </label>
                <div class="w-full rounded-xl border border-black/10 bg-[#F8F5F2] px-4 py-3 text-sm font-semibold">
                    $<span x-text="formatNumber(totalUsd)"></span>
                </div>
            </div>

        </div>

    </section>

    <section class="rounded-2xl border border-black/5 bg-white shadow-sm">

        <div class="border-b border-black/5 px-6 py-5">
            <h2 class="text-lg font-bold">Tasa de cambio</h2>
            <p class="mt-1 text-sm text-gray-500">
                Registra la tasa aplicada para mantener trazabilidad entre USD y bolívares.
            </p>
        </div>

        <div class="grid gap-5 p-6 md:grid-cols-2 xl:grid-cols-4">

            <input
                type="hidden"
                name="exchange_rate_id"
                value="{{ old('exchange_rate_id', $latestExchangeRate?->id) }}">

            <div>
                <label for="rate_source" class="mb-2 block text-sm font-semibold text-gray-700">
                    Fuente de tasa <span class="text-[#E46F8A]">*</span>
                </label>

                @php
                $selectedRateSource = old('rate_source', $latestExchangeRate?->source ?? 'manual');
                @endphp

                <select
                    id="rate_source"
                    name="rate_source"
                    x-model="rateSource"
                    x-on:change="updateExchangeRateValue()"
                    class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 outline-none transition focus:border-zinc-900"
                    required>
                    <option value="bcv">BCV</option>
                    <option value="binance">Binance</option>
                    <option value="manual">Manual</option>
                </select>
            </div>

            <div>
                <label for="exchange_rate_value" class="mb-2 block text-sm font-semibold text-gray-700">
                    Tasa aplicada <span class="text-[#E46F8A]">*</span>
                </label>
                <input
                    id="exchange_rate_value"
                    name="exchange_rate_value"
                    type="text"
                    inputmode="decimal"
                    value="{{ old('exchange_rate_value', $latestExchangeRate?->used_rate) }}"
                    x-model="exchangeRateValueInput"
                    x-on:keydown="handleDecimalKey($event)"
                    class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 outline-none transition focus:border-zinc-900"
                    required>
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Total Bs
                </label>
                <div class="w-full rounded-xl border border-black/10 bg-[#F8F5F2] px-4 py-3 text-sm font-semibold">
                    Bs. <span x-text="formatNumber(totalBs)"></span>
                </div>
            </div>

            <div>
                <label for="payment_method" class="mb-2 block text-sm font-semibold text-gray-700">
                    Forma de pago <span class="text-[#E46F8A]">*</span>
                </label>

                @php
                $selectedPaymentMethod = old('payment_method');
                @endphp

                <select
                    id="payment_method"
                    name="payment_method"
                    class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                    required>
                    <option value="">Seleccionar forma de pago</option>
                    <option value="pago_movil" @selected($selectedPaymentMethod==='pago_movil' )>Pago móvil</option>
                    <option value="transferencia_bs" @selected($selectedPaymentMethod==='transferencia_bs' )>Transferencia Bs</option>
                    <option value="efectivo_usd" @selected($selectedPaymentMethod==='efectivo_usd' )>Efectivo USD</option>
                    <option value="binance" @selected($selectedPaymentMethod==='binance' )>Binance</option>
                    <option value="zelle" @selected($selectedPaymentMethod==='zelle' )>Zelle</option>
                    <option value="mixto" @selected($selectedPaymentMethod==='mixto' )>Mixto</option>
                </select>
            </div>

        </div>

    </section>

    <section class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">

        <h2 class="text-lg font-bold">Resumen de ingreso</h2>

        <div class="mt-6 grid gap-4 md:grid-cols-3">

            <div class="rounded-2xl bg-[#F8F5F2] p-5 text-center">
                <p class="text-sm text-gray-500">Unidades ingresadas</p>
                <h3 class="mt-2 text-2xl font-bold" x-text="quantity || 0"></h3>
            </div>

            <div class="rounded-2xl bg-[#F8F5F2] p-5 text-center">
                <p class="text-sm text-gray-500">Total USD</p>
                <h3 class="mt-2 text-2xl font-bold">
                    $<span x-text="formatNumber(totalUsd)"></span>
                </h3>
            </div>

            <div class="rounded-2xl bg-[#FFF0F4] p-5 text-center">
                <p class="text-sm text-gray-500">Total Bs</p>
                <h3 class="mt-2 text-2xl font-bold text-[#E46F8A]">
                    Bs. <span x-text="formatNumber(totalBs)"></span>
                </h3>
            </div>

        </div>

    </section>

    <section class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">

        <h2 class="text-lg font-bold">Observaciones</h2>

        <textarea
            id="notes"
            name="notes"
            rows="4"
            placeholder="Agrega notas sobre esta compra, proveedor, condiciones de pago o cualquier detalle relevante..."
            class="mt-5 w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">{{ old('notes') }}</textarea>

    </section>

    <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm">
        <div class="grid gap-4 md:grid-cols-3">
            <a
                href="{{ route('purchases.index') }}"
                class="rounded-xl border border-black/10 px-5 py-3 text-center text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                Cancelar
            </a>

            <button
                type="button"
                disabled
                class="rounded-xl border border-black/10 px-5 py-3 text-sm font-semibold text-gray-400"
                title="Se implementará más adelante">
                Guardar borrador
            </button>

            <button
                type="submit"
                class="rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C]">
                Registrar compra
            </button>
        </div>
    </div>

</form>

@endsection