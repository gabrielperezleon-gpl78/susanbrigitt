@extends('layouts.app', [
'title' => 'Editar venta | Susan Brigitt Studio',
'pageTitle' => 'Editar venta'
])

@section('content')

@php
$selectedSaleDate = old(
'sale_date',
$sale->sale_date?->format('Y-m-d')
);

$storedRateChoice =
$sale->exchange_rate_id
&& $sale->rate_source
? $sale->exchange_rate_id
. '|'
. $sale->rate_source
: '';

$initialRateChoice = old(
'exchange_rate_choice',
$storedRateChoice
);
@endphp

<div class="space-y-8">
    <div>
        <a
            href="{{ route('sales.index') }}"
            class="text-sm font-semibold text-[#E46F8A]">
            ← Volver a ventas
        </a>

        <p class="mt-4 text-sm font-medium uppercase tracking-[0.24em] text-rose-400">
            Corrección de venta
        </p>

        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">
            Editar venta registrada
        </h1>

        <p class="mt-2 max-w-2xl text-sm leading-6 text-zinc-500">
            Corrige la venta y conserva la trazabilidad de la tasa aplicada.
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

    <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm leading-6 text-amber-800">
        El sistema devolverá al inventario las unidades originales y aplicará después la nueva cantidad.
    </div>

    <form
        action="{{ route('sales.update', $sale) }}"
        method="POST"
        class="grid gap-6 xl:grid-cols-[1fr_360px]"
        x-data="{
            products: @js(
                $products->map(fn ($product) => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'available_stock' =>
                        (int) $product->current_stock
                        + (
                            $product->id === $saleItem->product_id
                                ? (int) $saleItem->quantity
                                : 0
                        ),
                    'sale_price_usd' =>
                        (float) $product->sale_price_usd,
                    'purchase_price_usd' =>
                        (float) $product->purchase_price_usd,
                    'unit_measure' =>
                        $product->unitMeasure?->abbreviation
                        ?? $product->unitMeasure?->name
                        ?? '',
                ])->values()
            ),

            rateChoices: @js($rateChoices),

            originalProductId: @js(
                (int) $saleItem->product_id
            ),

            originalUnitCostUsd: @js(
                (float) $saleItem->unit_cost_usd
            ),

            productId: @js(
                (int) old(
                    'product_id',
                    $saleItem->product_id
                )
            ),

            quantity: @js(
                (int) old(
                    'quantity',
                    $saleItem->quantity
                )
            ),

            unitPriceUsdInput: @js(
                old(
                    'unit_price_usd',
                    $saleItem->unit_price_usd
                )
            ),

            saleDate: @js($selectedSaleDate),

            exchangeRateChoice: @js(
                $initialRateChoice
            ),

            get selectedProduct() {
                return this.products.find(
                    product =>
                        product.id === Number(this.productId)
                ) || null;
            },

            get ratesForSelectedDate() {
                return this.rateChoices.filter(
                    choice =>
                        choice.rate_date === this.saleDate
                        && (
                            choice.status === 'active'
                            || choice.is_current_sale_choice
                        )
                );
            },

            get selectedRateChoice() {
                return this.rateChoices.find(
                    choice =>
                        choice.key === String(
                            this.exchangeRateChoice
                        )
                ) || null;
            },

            initializeForm() {
                this.syncRateSelection(false);
            },

            selectProduct() {
                if (! this.selectedProduct) {
                    return;
                }

                this.unitPriceUsdInput = String(
                    this.selectedProduct.sale_price_usd || ''
                );
            },

            syncRateSelection(forceDefault = false) {
                const choices = this.ratesForSelectedDate;

                const currentChoiceIsValid = choices.some(
                    choice =>
                        choice.key === String(
                            this.exchangeRateChoice
                        )
                );

                if (
                    currentChoiceIsValid
                    && ! forceDefault
                ) {
                    return;
                }

                const preferredChoice =
                    choices.find(
                        choice =>
                            choice.source === 'binance'
                    )
                    || choices[0]
                    || null;

                this.exchangeRateChoice =
                    preferredChoice
                        ? preferredChoice.key
                        : '';
            },

            parseDecimal(value) {
                if (
                    value === null
                    || value === undefined
                    || value === ''
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
                    lastComma !== -1
                    && lastDot !== -1
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

            handleDecimalKey(event) {
                if (event.code !== 'NumpadDecimal') {
                    return;
                }

                event.preventDefault();

                const input = event.target;

                const start =
                    input.selectionStart
                    ?? input.value.length;

                const end =
                    input.selectionEnd
                    ?? input.value.length;

                input.value =
                    input.value.slice(0, start)
                    + '.'
                    + input.value.slice(end);

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

            get unitPriceUsd() {
                return this.parseDecimal(
                    this.unitPriceUsdInput
                );
            },

            get exchangeRateValue() {
                return this.selectedRateChoice
                    ? Number(
                        this.selectedRateChoice.value || 0
                    )
                    : 0;
            },

            get unitCostUsd() {
                if (! this.selectedProduct) {
                    return 0;
                }

                if (
                    Number(this.selectedProduct.id)
                    === Number(this.originalProductId)
                    && Number(this.originalUnitCostUsd) > 0
                ) {
                    return Number(
                        this.originalUnitCostUsd
                    );
                }

                return Number(
                    this.selectedProduct
                        .purchase_price_usd || 0
                );
            },

            get unitProfitUsd() {
                return this.unitPriceUsd
                    - this.unitCostUsd;
            },

            get totalUsd() {
                return Number(this.quantity || 0)
                    * this.unitPriceUsd;
            },

            get totalBs() {
                return this.totalUsd
                    * this.exchangeRateValue;
            },

            get totalProfitUsd() {
                return Number(this.quantity || 0)
                    * this.unitProfitUsd;
            },

            get stockAfterSale() {
                return this.selectedProduct
                    ? Number(
                        this.selectedProduct
                            .available_stock
                    )
                    - Number(this.quantity || 0)
                    : 0;
            },

            formatNumber(value) {
                return new Intl.NumberFormat('es-VE', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(value || 0);
            },

            formatRate(value) {
                return new Intl.NumberFormat('es-VE', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(value || 0);
            },

            formatRateChoice(choice) {
                const historical =
                    choice.status !== 'active'
                        ? ' · Histórica'
                        : '';

                return `${choice.source_label} (${choice.rate_date_label}, ${choice.rate_time}) — ${this.formatRate(choice.value)}${historical}`;
            }
        }"
        x-init="initializeForm()">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <section class="rounded-2xl border border-black/5 bg-white shadow-sm">
                <div class="border-b border-black/5 px-6 py-5">
                    <h2 class="text-lg font-bold text-zinc-900">
                        Información de la venta
                    </h2>

                    <p class="mt-1 text-sm text-zinc-500">
                        Modifica el producto, la cantidad, el precio, el cliente o la fecha.
                    </p>
                </div>

                <div class="grid gap-5 p-6 md:grid-cols-2">
                    <div>
                        <label
                            for="sale_date"
                            class="mb-2 block text-sm font-semibold text-gray-700">
                            Fecha de venta
                            <span class="text-[#E46F8A]">*</span>
                        </label>

                        <input
                            id="sale_date"
                            name="sale_date"
                            type="date"
                            value="{{ $selectedSaleDate }}"
                            x-model="saleDate"
                            x-on:change="syncRateSelection(true)"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                            required>
                    </div>

                    <div>
                        <label
                            for="customer_name"
                            class="mb-2 block text-sm font-semibold text-gray-700">
                            Cliente
                        </label>

                        <input
                            id="customer_name"
                            name="customer_name"
                            type="text"
                            value="{{ old(
                                'customer_name',
                                $sale->customer_name
                            ) }}"
                            placeholder="Cliente ocasional"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                    </div>

                    <div class="md:col-span-2">
                        <label
                            for="product_id"
                            class="mb-2 block text-sm font-semibold text-gray-700">
                            Producto
                            <span class="text-[#E46F8A]">*</span>
                        </label>

                        <select
                            id="product_id"
                            name="product_id"
                            x-model.number="productId"
                            x-on:change="selectProduct()"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                            required>
                            <option value="">
                                Seleccionar producto
                            </option>

                            @foreach ($products as $product)
                            @php
                            $availableStock =
                            (int) $product->current_stock
                            + (
                            $product->id
                            === $saleItem->product_id
                            ? (int) $saleItem->quantity
                            : 0
                            );
                            @endphp

                            <option
                                value="{{ $product->id }}"
                                @selected(
                                old( 'product_id' ,
                                $saleItem->product_id
                                ) == $product->id
                                )
                                >
                                {{ $product->name }}
                                · Disponible:
                                {{ $availableStock }}
                                {{ $product->unitMeasure?->abbreviation
                                        ?? $product->unitMeasure?->name
                                        ?? '' }}
                            </option>
                            @endforeach
                        </select>

                        <p
                            class="mt-2 text-xs text-gray-400"
                            x-show="selectedProduct"
                            x-cloak>
                            Disponible para esta corrección:

                            <span
                                x-text="selectedProduct?.available_stock || 0"></span>

                            <span
                                x-text="selectedProduct?.unit_measure || 'unidades'"></span>.
                        </p>
                    </div>

                    <div>
                        <label
                            for="quantity"
                            class="mb-2 block text-sm font-semibold text-gray-700">
                            Cantidad vendida
                            <span class="text-[#E46F8A]">*</span>
                        </label>

                        <input
                            id="quantity"
                            name="quantity"
                            type="number"
                            min="1"
                            step="1"
                            value="{{ old(
                                'quantity',
                                $saleItem->quantity
                            ) }}"
                            x-model.number="quantity"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                            required>
                    </div>

                    <div>
                        <label
                            for="unit_price_usd"
                            class="mb-2 block text-sm font-semibold text-gray-700">
                            Precio unitario USD
                            <span class="text-[#E46F8A]">*</span>
                        </label>

                        <input
                            id="unit_price_usd"
                            name="unit_price_usd"
                            type="text"
                            inputmode="decimal"
                            value="{{ old(
                                'unit_price_usd',
                                $saleItem->unit_price_usd
                            ) }}"
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
                        Selecciona una tasa registrada para la misma fecha de la venta.
                    </p>
                </div>

                <div class="grid gap-5 p-6 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label
                            for="exchange_rate_choice"
                            class="mb-2 block text-sm font-semibold text-gray-700">
                            Tasa registrada para la venta
                            <span class="text-[#E46F8A]">*</span>
                        </label>

                        <select
                            id="exchange_rate_choice"
                            name="exchange_rate_choice"
                            x-model="exchangeRateChoice"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                            required>
                            <option value="">
                                Seleccionar tasa registrada
                            </option>

                            <template
                                x-for="choice in ratesForSelectedDate"
                                :key="choice.key">
                                <option
                                    :value="choice.key"
                                    x-text="formatRateChoice(choice)"></option>
                            </template>
                        </select>

                        <div
                            class="mt-3 rounded-xl border border-red-100 bg-red-50 px-4 py-3"
                            x-show="saleDate && ratesForSelectedDate.length === 0"
                            x-cloak>
                            <p class="text-sm font-semibold text-red-700">
                                No existen tasas para esta fecha.
                            </p>

                            <a
                                href="{{ route('exchange-rates.create') }}"
                                class="mt-2 inline-flex text-xs font-semibold text-red-700 underline">
                                Registrar una tasa
                            </a>
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Fuente seleccionada
                        </label>

                        <input
                            type="text"
                            :value="selectedRateChoice?.source_label || ''"
                            placeholder="Selecciona una tasa"
                            class="w-full cursor-not-allowed rounded-xl border border-black/10 bg-zinc-100 px-4 py-3 text-sm text-zinc-500 outline-none"
                            readonly>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Tasa aplicada
                        </label>

                        <input
                            type="text"
                            :value="
                                selectedRateChoice
                                    ? formatRate(
                                        selectedRateChoice.value
                                    )
                                    : ''
                            "
                            placeholder="Selecciona una tasa"
                            class="w-full cursor-not-allowed rounded-xl border border-black/10 bg-zinc-100 px-4 py-3 text-sm font-semibold text-zinc-700 outline-none"
                            readonly>
                    </div>

                    <div class="md:col-span-2">
                        <label
                            for="payment_method"
                            class="mb-2 block text-sm font-semibold text-gray-700">
                            Forma de pago
                            <span class="text-[#E46F8A]">*</span>
                        </label>

                        <select
                            id="payment_method"
                            name="payment_method"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                            required>
                            <option value="">
                                Seleccionar forma de pago
                            </option>

                            @foreach ([
                            'pago_movil' => 'Pago móvil',
                            'transferencia_bs' => 'Transferencia Bs',
                            'efectivo_usd' => 'Efectivo USD',
                            'binance' => 'Binance',
                            'zelle' => 'Zelle',
                            'mixto' => 'Mixto',
                            ] as $value => $label)
                            <option
                                value="{{ $value }}"
                                @selected(
                                old( 'payment_method' ,
                                $sale->payment_method
                                ) === $value
                                )
                                >
                                {{ $label }}
                            </option>
                            @endforeach
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
                    placeholder="Notas sobre la venta o la corrección."
                    class="mt-5 w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">{{ old('notes', $sale->notes) }}</textarea>
            </section>
        </div>

        <aside class="space-y-6 xl:sticky xl:top-28 xl:self-start">
            <section class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-rose-400">
                    Resumen corregido
                </p>

                <div class="mt-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <span class="text-sm text-zinc-500">
                            Total USD
                        </span>

                        <strong>
                            $<span x-text="formatNumber(totalUsd)"></span>
                        </strong>
                    </div>

                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <span class="text-sm text-zinc-500">
                            Tasa aplicada
                        </span>

                        <strong>
                            <span
                                x-text="
                                    selectedRateChoice
                                        ? formatRate(
                                            exchangeRateValue
                                        )
                                        : '—'
                                "></span>
                        </strong>
                    </div>

                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <span class="text-sm text-zinc-500">
                            Total Bs
                        </span>

                        <strong>
                            Bs.
                            <span x-text="formatNumber(totalBs)"></span>
                        </strong>
                    </div>

                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <span class="text-sm text-zinc-500">
                            Ganancia estimada
                        </span>

                        <strong
                            :class="
                                totalProfitUsd >= 0
                                    ? 'text-green-600'
                                    : 'text-red-600'
                            ">
                            $<span x-text="formatNumber(totalProfitUsd)"></span>
                        </strong>
                    </div>

                    <div>
                        <span class="text-sm text-zinc-500">
                            Stock resultante
                        </span>

                        <p
                            class="mt-2 text-3xl font-semibold"
                            :class="
                                stockAfterSale >= 0
                                    ? 'text-[#E46F8A]'
                                    : 'text-red-600'
                            "
                            x-text="stockAfterSale"></p>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-blue-100 bg-blue-50 p-5">
                <p class="text-sm font-semibold text-blue-900">
                    Tasa vinculada
                </p>

                <p class="mt-2 text-sm leading-6 text-blue-700">
                    La venta conservará la fuente y el valor exacto seleccionados.
                </p>
            </section>

            <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
                <p class="text-sm font-semibold text-zinc-900">
                    Corrección de inventario
                </p>

                <p class="mt-2 text-sm leading-6 text-zinc-600">
                    La venta original será revertida antes de aplicar los cambios.
                </p>
            </section>

            <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm">
                <div class="space-y-3">
                    <button
                        type="submit"
                        class="w-full rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C]">
                        Actualizar venta
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