@extends('layouts.app', [
'title' => 'Registrar compra | Susan Brigitt Studio',
'pageTitle' => 'Registrar compra'
])

@section('content')

@php
$selectedPurchaseDate = old(
'purchase_date',
now()->toDateString()
);

$initialRateChoice = old('exchange_rate_choice', '');
$selectedPaymentMethod = old('payment_method', 'efectivo_usd');
@endphp

<div class="space-y-8">
    <div>
        <a
            href="{{ route('purchases.index') }}"
            class="text-sm font-semibold text-[#E46F8A]">
            ← Volver a compras
        </a>

        <p class="mt-4 text-sm font-medium uppercase tracking-[0.24em] text-rose-400">
            Gestión de compras
        </p>

        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">
            Registrar nueva compra
        </h1>

        <p class="mt-2 max-w-2xl text-sm leading-6 text-zinc-500">
            Registra la entrada de mercancía y el costo de adquisición en dólares. Para pagos en bolívares o mixtos, selecciona una tasa de cambio.
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
        action="{{ route('purchases.store') }}"
        method="POST"
        class="grid min-w-0 gap-6 xl:grid-cols-[minmax(0,1fr)_360px]"
        x-data="{
            products: @js(
                $products->map(fn ($product) => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'current_stock' => (int) $product->current_stock,
                    'purchase_price_usd' => (float) $product->purchase_price_usd,
                    'unit_measure' =>
                        $product->unitMeasure?->abbreviation
                        ?? $product->unitMeasure?->name
                        ?? '',
                ])->values()
            ),

            rateChoices: @js($rateChoices),

            productId: @js((int) old('product_id', 0)),

            quantity: @js((int) old('quantity', 1)),

            unitCostUsdInput: @js(
                old('unit_cost_usd', '')
            ),

            purchaseDate: @js($selectedPurchaseDate),

            exchangeRateChoice: @js($initialRateChoice),

            paymentMethod: @js($selectedPaymentMethod),

            get requiresRate() {
                return ['pago_movil', 'transferencia_bs', 'mixto'].includes(this.paymentMethod);
            },

            get showRate() {
                return this.requiresRate || Boolean(this.selectedRateChoice);
            },

            changePaymentMethod() {
                if (this.requiresRate) {
                    this.syncRateSelection(false);
                } else {
                    this.exchangeRateChoice = '';
                }
            },

            get selectedProduct() {
                return this.products.find(
                    product =>
                        product.id === Number(this.productId)
                ) || null;
            },

            get ratesForSelectedDate() {
                return this.rateChoices.filter(
                    choice =>
                        choice.rate_date === this.purchaseDate
                        && choice.status === 'active'
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
                if (
                    this.productId
                    && String(this.unitCostUsdInput).trim() === ''
                ) {
                    this.selectProduct();
                }

                this.syncRateSelection(false);
            },

            selectProduct() {
                if (! this.selectedProduct) {
                    this.unitCostUsdInput = '';
                    return;
                }

                const storedCost = Number(
                    this.selectedProduct.purchase_price_usd || 0
                );

                this.unitCostUsdInput =
                    storedCost > 0
                        ? String(storedCost)
                        : '';
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

                if (! this.requiresRate) {
                    this.exchangeRateChoice = '';
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

            get unitCostUsd() {
                return this.parseDecimal(
                    this.unitCostUsdInput
                );
            },

            get exchangeRateValue() {
                return this.selectedRateChoice
                    ? Number(
                        this.selectedRateChoice.value || 0
                    )
                    : 0;
            },

            get totalUsd() {
                return Number(this.quantity || 0)
                    * this.unitCostUsd;
            },

            get totalBs() {
                return this.totalUsd
                    * this.exchangeRateValue;
            },

            get resultingStock() {
                return this.selectedProduct
                    ? Number(
                        this.selectedProduct.current_stock
                    ) + Number(this.quantity || 0)
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
                return `${choice.source_label} (${choice.rate_date_label}, ${choice.rate_time}) — ${this.formatRate(choice.value)}`;
            }
        }"
        x-init="initializeForm()">
        @csrf

        <div class="min-w-0 space-y-6">
            <section class="rounded-2xl border border-black/5 bg-white shadow-sm">
                <div class="border-b border-black/5 px-6 py-5">
                    <h2 class="text-lg font-bold text-zinc-900">
                        Información de la compra
                    </h2>

                    <p class="mt-1 text-sm text-zinc-500">
                        Selecciona el proveedor, el producto y la cantidad adquirida.
                    </p>
                </div>

                <div class="grid gap-5 p-6 md:grid-cols-2">
                    <div>
                        <label
                            for="purchase_date"
                            class="mb-2 block text-sm font-semibold text-gray-700">
                            Fecha de compra
                            <span class="text-[#E46F8A]">*</span>
                        </label>

                        <input
                            id="purchase_date"
                            name="purchase_date"
                            type="date"
                            value="{{ $selectedPurchaseDate }}"
                            x-model="purchaseDate"
                            x-on:change="syncRateSelection(true)"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                            required>
                    </div>

                    <div>
                        <label
                            for="supplier_id"
                            class="mb-2 block text-sm font-semibold text-gray-700">
                            Proveedor
                            <span class="text-[#E46F8A]">*</span>
                        </label>

                        <select
                            id="supplier_id"
                            name="supplier_id"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                            required>
                            <option value="">
                                Seleccionar proveedor
                            </option>

                            @foreach ($suppliers as $supplier)
                            <option
                                value="{{ $supplier->id }}"
                                @selected(
                                old('supplier_id')==$supplier->id
                                )
                                >
                                {{ $supplier->name }}
                            </option>
                            @endforeach
                        </select>

                        @if ($suppliers->isEmpty())
                        <p class="mt-2 text-xs text-amber-700">
                            Debes registrar primero un proveedor en
                            <a
                                href="{{ route('catalogs.index') }}"
                                class="font-semibold underline">
                                Catálogos
                            </a>.
                        </p>
                        @endif
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
                            <option
                                value="{{ $product->id }}"
                                @selected(
                                old('product_id')==$product->id
                                )
                                >
                                {{ $product->internal_code }} · {{ $product->name }}
                                · Tono: {{ $product->tone?->name ?? 'Sin tono' }}
                                · Stock:
                                {{ $product->current_stock }}
                                {{ $product->unitMeasure?->abbreviation
                                        ?? $product->unitMeasure?->name
                                        ?? '' }}
                            </option>
                            @endforeach
                        </select>

                        @if ($products->isEmpty())
                        <p class="mt-2 text-xs text-amber-700">
                            Debes registrar primero un producto.
                            <a
                                href="{{ route('products.create') }}"
                                class="font-semibold underline">
                                Registrar producto
                            </a>
                        </p>
                        @endif

                        <p
                            class="mt-2 text-xs text-zinc-500"
                            x-show="selectedProduct"
                            x-cloak>
                            Stock actual:

                            <span
                                x-text="selectedProduct?.current_stock || 0"></span>

                            <span
                                x-text="selectedProduct?.unit_measure || 'unidades'"></span>.
                        </p>
                    </div>

                    <div>
                        <label
                            for="quantity"
                            class="mb-2 block text-sm font-semibold text-gray-700">
                            Cantidad comprada
                            <span class="text-[#E46F8A]">*</span>
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
                        <label
                            for="unit_cost_usd"
                            class="mb-2 block text-sm font-semibold text-gray-700">
                            Costo unitario USD
                            <span class="text-[#E46F8A]">*</span>
                        </label>

                        <input
                            id="unit_cost_usd"
                            name="unit_cost_usd"
                            type="text"
                            inputmode="decimal"
                            value="{{ old('unit_cost_usd') }}"
                            x-model="unitCostUsdInput"
                            placeholder="0,00"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                            required>

                        <p class="mt-2 text-xs text-zinc-500">
                            Este valor actualizará el costo de compra del producto.
                        </p>
                    </div>
                </div>
            </section>

            @include('partials.operation-payment', ['operationLabel' => 'compra'])

            <section class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-zinc-900">
                    Observaciones
                </h2>

                <textarea
                    id="notes"
                    name="notes"
                    rows="4"
                    placeholder="Notas sobre la compra, proveedor, pago o mercancía recibida."
                    class="mt-5 w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">{{ old('notes') }}</textarea>
            </section>
        </div>

        <aside class="min-w-0 space-y-6 xl:sticky xl:top-28 xl:self-start">
            <section class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-rose-400">
                    Resumen
                </p>

                <div class="mt-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <span class="text-sm text-zinc-500">
                            Total USD
                        </span>

                        <span class="text-sm font-semibold text-zinc-900">
                            $<span x-text="formatNumber(totalUsd)"></span>
                        </span>
                    </div>

                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <span class="text-sm text-zinc-500">
                            Tasa aplicada
                        </span>

                        <span class="text-sm font-semibold text-zinc-900">
                            <span
                                x-text="
                                    selectedRateChoice
                                        ? formatRate(
                                            exchangeRateValue
                                        )
                                        : '—'
                                "></span>
                        </span>
                    </div>

                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <span class="text-sm text-zinc-500">
                            Total Bs
                        </span>

                        <span class="text-sm font-semibold text-zinc-900">
                            <span x-text="selectedRateChoice ? 'Bs. ' + formatNumber(totalBs) : '—'"></span>
                        </span>
                    </div>

                    <div>
                        <span class="text-sm text-zinc-500">
                            Stock resultante
                        </span>

                        <p
                            class="mt-2 text-3xl font-semibold tracking-tight text-[#E46F8A]"
                            x-text="resultingStock"></p>

                        <p
                            class="mt-1 text-xs text-zinc-500"
                            x-show="selectedProduct"
                            x-cloak
                            x-text="selectedProduct?.unit_measure || 'unidades'"></p>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-rose-100 bg-rose-50 p-5">
                <p class="text-sm font-semibold text-zinc-900">
                    Impacto en inventario
                </p>

                <p class="mt-2 text-sm leading-6 text-zinc-600">
                    Al guardar la compra, la cantidad será agregada automáticamente al stock del producto.
                </p>
            </section>

            <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm">
                <div class="space-y-3">
                    <button
                        type="submit"
                        class="w-full rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C]">
                        Registrar compra
                    </button>

                    <a
                        href="{{ route('purchases.index') }}"
                        class="block w-full rounded-xl border border-black/10 px-5 py-3 text-center text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                        Cancelar
                    </a>
                </div>
            </div>
        </aside>
    </form>
</div>

@endsection
