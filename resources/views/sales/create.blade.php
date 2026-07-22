@extends('layouts.app', [
'title' => 'Registrar venta | Susan Brigitt Studio',
'pageTitle' => 'Registrar venta'
])

@section('content')

<div class="space-y-8">
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
        <div>
            <a href="{{ route('sales.index') }}" class="text-sm font-semibold text-[#E46F8A]">
                ← Volver a ventas
            </a>

            <p class="mt-4 text-sm font-medium uppercase tracking-[0.24em] text-rose-400">
                Gestión de ventas
            </p>

            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">
                Registrar nueva venta
            </h1>

            <p class="mt-2 max-w-2xl text-sm leading-6 text-zinc-500">
                Registra la salida de mercancía, calcula ingresos, ganancia estimada y stock resultante.
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
        action="{{ route('sales.store') }}"
        method="POST"
        class="grid gap-6 xl:grid-cols-[1fr_360px]"
        x-data="{
        products: @js($products->map(fn ($product) => [
            'id' => $product->id,
            'name' => $product->name,
            'stock' => (int) $product->current_stock,
            'sale_price_usd' => (float) $product->sale_price_usd,
            'purchase_price_usd' => (float) $product->purchase_price_usd,
        ])->values()),

        productId: @js((int) old('product_id', 0)),
        quantity: @js((int) old('quantity', 1)),
        unitPriceUsdInput: @js(old('unit_price_usd', '')),
        exchangeRateValueInput: @js(old('exchange_rate_value', $latestExchangeRate?->used_rate ?? '')),

        get selectedProduct() {
            return this.products.find(product => product.id === Number(this.productId)) || null;
        },

        selectProduct() {
            if (this.selectedProduct) {
                this.unitPriceUsdInput = String(this.selectedProduct.sale_price_usd || '');
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

        get unitPriceUsd() {
            return this.parseDecimal(this.unitPriceUsdInput);
        },

        get exchangeRateValue() {
            return this.parseDecimal(this.exchangeRateValueInput);
        },

        get unitCostUsd() {
            return this.selectedProduct ? Number(this.selectedProduct.purchase_price_usd || 0) : 0;
        },

        get unitProfitUsd() {
            return this.unitPriceUsd - this.unitCostUsd;
        },

        get totalUsd() {
            return this.quantity * this.unitPriceUsd;
        },

        get totalBs() {
            return this.totalUsd * this.exchangeRateValue;
        },

        get totalProfitUsd() {
            return this.quantity * this.unitProfitUsd;
        },

        get stockAfterSale() {
            return this.selectedProduct ? this.selectedProduct.stock - this.quantity : 0;
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
                        Información de la venta
                    </h2>

                    <p class="mt-1 text-sm text-zinc-500">
                        Datos principales de producto, cantidad vendida, precio y cliente.
                    </p>
                </div>

                <div class="grid gap-5 p-6 md:grid-cols-2">
                    <div>
                        <label for="sale_date" class="mb-2 block text-sm font-semibold text-gray-700">
                            Fecha de venta <span class="text-[#E46F8A]">*</span>
                        </label>

                        <input
                            id="sale_date"
                            name="sale_date"
                            type="date"
                            value="{{ old('sale_date', now()->toDateString()) }}"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                            required>
                    </div>

                    <div>
                        <label for="customer_name" class="mb-2 block text-sm font-semibold text-gray-700">
                            Cliente
                        </label>

                        <input
                            id="customer_name"
                            name="customer_name"
                            type="text"
                            value="{{ old('customer_name') }}"
                            placeholder="Cliente ocasional"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                    </div>

                    <div class="md:col-span-2">
                        <label for="product_id" class="mb-2 block text-sm font-semibold text-gray-700">
                            Producto <span class="text-[#E46F8A]">*</span>
                        </label>

                        <select
                            id="product_id"
                            name="product_id"
                            x-model.number="productId"
                            x-on:change="selectProduct()"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                            required>
                            <option value="">Seleccionar producto</option>
                            @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected(old('product_id')==$product->id)>
                                {{ $product->name }} · Stock: {{ $product->current_stock }} · Venta: ${{ number_format((float) $product->sale_price_usd, 2, ',', '.') }}
                            </option>
                            @endforeach
                        </select>

                        <p class="mt-2 text-xs text-gray-400" x-show="selectedProduct">
                            Stock actual seleccionado: <span x-text="selectedProduct?.stock || 0"></span> unidades.
                        </p>
                    </div>

                    <div>
                        <label for="quantity" class="mb-2 block text-sm font-semibold text-gray-700">
                            Cantidad vendida <span class="text-[#E46F8A]">*</span>
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
                        <label for="unit_price_usd" class="mb-2 block text-sm font-semibold text-gray-700">
                            Precio unitario USD <span class="text-[#E46F8A]">*</span>
                        </label>

                        <input
                            id="unit_price_usd"
                            name="unit_price_usd"
                            type="text"
                            inputmode="decimal"
                            value="{{ old('unit_price_usd') }}"
                            x-model="unitPriceUsdInput"
                            x-on:keydown="handleDecimalKey($event)"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                            required>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-black/5 bg-white shadow-sm">
                <div class="border-b border-black/5 px-6 py-5">
                    <h2 class="text-lg font-bold text-zinc-900">
                        Pago y tasa de cambio
                    </h2>

                    <p class="mt-1 text-sm text-zinc-500">
                        Registra la tasa aplicada, forma de pago y equivalente en bolívares.
                    </p>
                </div>

                <div class="grid gap-5 p-6 md:grid-cols-2">
                    <input
                        type="hidden"
                        name="exchange_rate_id"
                        value="{{ old('exchange_rate_id', $latestExchangeRate?->id) }}">

                    @php
                    $selectedRateSource = old('rate_source', $latestExchangeRate?->source ?? 'binance');
                    $selectedPaymentMethod = old('payment_method');
                    @endphp

                    <div>
                        <label for="rate_source" class="mb-2 block text-sm font-semibold text-gray-700">
                            Fuente de tasa <span class="text-[#E46F8A]">*</span>
                        </label>

                        <select
                            id="rate_source"
                            name="rate_source"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                            required>
                            <option value="binance" @selected($selectedRateSource==='binance' )>Binance</option>
                            <option value="bcv" @selected($selectedRateSource==='bcv' )>BCV</option>
                            <option value="manual" @selected($selectedRateSource==='manual' )>Manual</option>
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
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                            required>
                    </div>

                    <div class="md:col-span-2">
                        <label for="payment_method" class="mb-2 block text-sm font-semibold text-gray-700">
                            Forma de pago <span class="text-[#E46F8A]">*</span>
                        </label>

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
                <h2 class="text-lg font-bold text-zinc-900">
                    Observaciones
                </h2>

                <textarea
                    id="notes"
                    name="notes"
                    rows="4"
                    placeholder="Agrega notas sobre la venta, cliente, forma de pago o cualquier detalle relevante..."
                    class="mt-5 w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">{{ old('notes') }}</textarea>
            </section>
        </div>

        <aside class="space-y-6 xl:sticky xl:top-28 xl:self-start">
            <section class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-rose-400">
                    Resumen
                </p>

                <div class="mt-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <span class="text-sm text-zinc-500">Total USD</span>
                        <span class="text-sm font-semibold text-zinc-900">
                            $<span x-text="formatNumber(totalUsd)"></span>
                        </span>
                    </div>

                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <span class="text-sm text-zinc-500">Total Bs</span>
                        <span class="text-sm font-semibold text-zinc-900">
                            Bs. <span x-text="formatNumber(totalBs)"></span>
                        </span>
                    </div>

                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <span class="text-sm text-zinc-500">Ganancia estimada</span>
                        <span class="text-sm font-semibold text-green-600">
                            $<span x-text="formatNumber(totalProfitUsd)"></span>
                        </span>
                    </div>

                    <div>
                        <span class="text-sm text-zinc-500">Stock resultante</span>
                        <p class="mt-2 text-3xl font-semibold tracking-tight text-[#E46F8A]" x-text="stockAfterSale"></p>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-rose-100 bg-rose-50 p-5">
                <p class="text-sm font-semibold text-zinc-900">
                    Impacto en inventario
                </p>

                <p class="mt-2 text-sm leading-6 text-zinc-600">
                    Al guardar esta venta, el stock del producto se descontará automáticamente y se registrará un movimiento tipo venta.
                </p>
            </section>

            <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm">
                <div class="space-y-3">
                    <button
                        type="submit"
                        class="w-full rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C]">
                        Registrar venta
                    </button>

                    <a
                        href="{{ route('sales.index') }}"
                        class="block w-full rounded-xl border border-black/10 px-5 py-3 text-center text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                        Cancelar
                    </a>
                </div>
            </div>
        </aside>
    </form>
</div>

@endsection