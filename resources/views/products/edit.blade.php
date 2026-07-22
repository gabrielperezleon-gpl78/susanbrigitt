@extends('layouts.app', [
'title' => 'Editar producto | Susan Brigitt Studio',
'pageTitle' => 'Editar producto'
])

@section('content')

<div class="space-y-8">
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
        <div>
            <a href="{{ route('products.index') }}" class="text-sm font-semibold text-[#E46F8A]">
                ← Volver a productos
            </a>

            <p class="mt-4 text-sm font-medium uppercase tracking-[0.24em] text-rose-400">
                Catálogo de productos
            </p>

            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">
                Editar producto
            </h1>

            <p class="mt-2 max-w-2xl text-sm leading-6 text-zinc-500">
                Carga un producto con sus datos comerciales, proveedor, precios y stock inicial.
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
        action="{{ route('products.update', $product) }}"
        method="POST"
        class="grid gap-6 xl:grid-cols-[1fr_360px]"
        x-data="{
            purchasePriceInput: @js(old('purchase_price_usd', $product->purchase_price_usd)),
            salePriceInput: @js(old('sale_price_usd', $product->sale_price_usd)),
            initialStock: @js((int) old('initial_stock', $product->initial_stock)),
            
            parseDecimal(value) {
            if (value === null || value === undefined || value === '') return 0;

            value = String(value).trim().replace(/\s/g, '').replace('$', '').replace('Bs.', '').replace('Bs', '');

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
get purchasePrice() {
    return this.parseDecimal(this.purchasePriceInput);
},
get salePrice() {
    return this.parseDecimal(this.salePriceInput);
},
get unitProfit() {
    return this.salePrice - this.purchasePrice;
},
get profitMargin() {
    if (!this.salePrice || this.salePrice <= 0) return 0;
    return (this.unitProfit / this.salePrice) * 100;
},
get inventoryValue() {
    return this.purchasePrice * this.initialStock;
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

        @method('PUT')

        <div class="space-y-6">
            <section class="rounded-2xl border border-black/5 bg-white shadow-sm">
                <div class="border-b border-black/5 px-6 py-5">
                    <h2 class="text-lg font-bold text-zinc-900">
                        Información principal
                    </h2>

                    <p class="mt-1 text-sm text-zinc-500">
                        Datos básicos para identificar el producto dentro del catálogo.
                    </p>
                </div>

                <div class="grid gap-5 p-6 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label for="name" class="mb-2 block text-sm font-semibold text-gray-700">
                            Nombre del producto <span class="text-[#E46F8A]">*</span>
                        </label>

                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name', $product->name) }}"
                            placeholder="Ejemplo: Labial mate rojo intenso"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                            required>
                    </div>

                    <div>
                        <label for="internal_code" class="mb-2 block text-sm font-semibold text-gray-700">
                            Código interno
                        </label>

                        <input
                            id="internal_code"
                            name="internal_code"
                            type="text"
                            value="{{ old('internal_code', $product->internal_code) }}"
                            placeholder="Ejemplo: SB-LAB-001"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                    </div>

                    <div>
                        <label for="barcode" class="mb-2 block text-sm font-semibold text-gray-700">
                            Código de barras
                        </label>

                        <input
                            id="barcode"
                            name="barcode"
                            type="text"
                            value="{{ old('barcode', $product->barcode) }}"
                            placeholder="Opcional"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                    </div>

                    <div>
                        <label for="category_id" class="mb-2 block text-sm font-semibold text-gray-700">
                            Categoría
                        </label>

                        <select
                            id="category_id"
                            name="category_id"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                            <option value="">Sin categoría</option>
                            @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="brand_id" class="mb-2 block text-sm font-semibold text-gray-700">
                            Marca
                        </label>

                        <select
                            id="brand_id"
                            name="brand_id"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                            <option value="">Sin marca</option>
                            @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" @selected(old('brand_id', $product->brand_id) == $brand->id)>
                                {{ $brand->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="tone_id" class="mb-2 block text-sm font-semibold text-gray-700">
                            Tono / color
                        </label>

                        <select
                            id="tone_id"
                            name="tone_id"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                            <option value="">Sin tono</option>
                            @foreach ($tones as $tone)
                            <option value="{{ $tone->id }}" @selected(old('tone_id', $product->tone_id) == $tone->id)>
                                {{ $tone->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="unit_measure_id" class="mb-2 block text-sm font-semibold text-gray-700">
                            Unidad de medida
                        </label>

                        <select
                            id="unit_measure_id"
                            name="unit_measure_id"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                            <option value="">Sin unidad</option>
                            @foreach ($unitMeasures as $unitMeasure)
                            <option value="{{ $unitMeasure->id }}" @selected(old('unit_measure_id', $product->unit_measure_id) == $unitMeasure->id)>
                                {{ $unitMeasure->name }}
                                @if ($unitMeasure->abbreviation)
                                · {{ $unitMeasure->abbreviation }}
                                @endif
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="supplier_id" class="mb-2 block text-sm font-semibold text-gray-700">
                            Proveedor
                        </label>

                        <select
                            id="supplier_id"
                            name="supplier_id"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                            <option value="">Sin proveedor</option>
                            @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected(old('supplier_id', $product->supplier_id) == $supplier->id)>
                                {{ $supplier->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="mb-2 block text-sm font-semibold text-gray-700">
                            Descripción
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            placeholder="Descripción comercial del producto..."
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">{{ old('description', $product->description) }}</textarea>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-black/5 bg-white shadow-sm">
                <div class="border-b border-black/5 px-6 py-5">
                    <h2 class="text-lg font-bold text-zinc-900">
                        Precios e inventario
                    </h2>

                    <p class="mt-1 text-sm text-zinc-500">
                        Define precios en dólares, stock inicial y stock mínimo de alerta.
                    </p>
                </div>

                <div class="grid gap-5 p-6 md:grid-cols-2">
                    <div>
                        <label for="purchase_price_usd" class="mb-2 block text-sm font-semibold text-gray-700">
                            Costo de compra USD <span class="text-[#E46F8A]">*</span>
                        </label>

                        <input
                            id="purchase_price_usd"
                            name="purchase_price_usd"
                            type="text"
                            inputmode="decimal"
                            value="{{ old('purchase_price_usd', $product->purchase_price_usd) }}"
                            x-model="purchasePriceInput"
                            x-on:keydown="handleDecimalKey($event)"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                            required>
                    </div>

                    <div>
                        <label for="sale_price_usd" class="mb-2 block text-sm font-semibold text-gray-700">
                            Precio de venta USD <span class="text-[#E46F8A]">*</span>
                        </label>

                        <input
                            id="sale_price_usd"
                            name="sale_price_usd"
                            type="text"
                            inputmode="decimal"
                            value="{{ old('sale_price_usd', $product->sale_price_usd) }}"
                            x-model="salePriceInput"
                            x-on:keydown="handleDecimalKey($event)"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                            required>
                    </div>

                    <div>
                        <label for="initial_stock" class="mb-2 block text-sm font-semibold text-gray-700">
                            Stock inicial <span class="text-[#E46F8A]">*</span>
                        </label>

                        <input
                            id="initial_stock"
                            name="initial_stock"
                            type="number"
                            min="0"
                            step="1"
                            value="{{ old('initial_stock', $product->initial_stock) }}"
                            x-model.number="initialStock"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                            required>
                    </div>

                    <div>
                        <label for="current_stock" class="mb-2 block text-sm font-semibold text-gray-700">
                            Stock actual <span class="text-[#E46F8A]">*</span>
                        </label>

                        <input
                            id="current_stock"
                            name="current_stock"
                            type="number"
                            min="0"
                            step="1"
                            value="{{ old('current_stock', $product->current_stock) }}"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                            required>
                    </div>

                    <div>
                        <label for="minimum_stock" class="mb-2 block text-sm font-semibold text-gray-700">
                            Stock mínimo <span class="text-[#E46F8A]">*</span>
                        </label>

                        <input
                            id="minimum_stock"
                            name="minimum_stock"
                            type="number"
                            min="0"
                            step="1"
                            value="{{ old('minimum_stock', $product->minimum_stock) }}"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                            required>
                    </div>

                    <div>
                        <label for="entry_date" class="mb-2 block text-sm font-semibold text-gray-700">
                            Fecha de ingreso
                        </label>

                        <input
                            id="entry_date"
                            name="entry_date"
                            type="date"
                            value="{{ old('entry_date', optional($product->entry_date)->format('Y-m-d')) }}"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
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
                            <option value="active" @selected(old('status', $product->status) === 'active')>Activo</option>
                            <option value="inactive" @selected(old('status', $product->status) === 'inactive')>Inactivo</option>
                        </select>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-zinc-900">
                    Notas internas
                </h2>

                <textarea
                    id="internal_notes"
                    name="internal_notes"
                    rows="4"
                    placeholder="Notas privadas sobre proveedor, rotación, reposición o condiciones comerciales..."
                    class="mt-5 w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">{{ old('internal_notes', $product->internal_notes) }}</textarea>
            </section>
        </div>

        <aside class="space-y-6 xl:sticky xl:top-28 xl:self-start">
            <section class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-rose-400">
                    Resumen
                </p>

                <div class="mt-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <span class="text-sm text-zinc-500">Ganancia unitaria</span>
                        <span class="text-sm font-semibold text-zinc-900">
                            $<span x-text="formatNumber(unitProfit)"></span>
                        </span>
                    </div>

                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <span class="text-sm text-zinc-500">Margen</span>
                        <span class="text-sm font-semibold text-zinc-900">
                            <span x-text="formatNumber(profitMargin)"></span>%
                        </span>
                    </div>

                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <span class="text-sm text-zinc-500">Stock inicial</span>
                        <span class="text-sm font-semibold text-zinc-900" x-text="initialStock || 0"></span>
                    </div>

                    <div>
                        <span class="text-sm text-zinc-500">Valor inicial del inventario</span>
                        <p class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">
                            $<span x-text="formatNumber(inventoryValue)"></span>
                        </p>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-rose-100 bg-rose-50 p-5">
                <p class="text-sm font-semibold text-zinc-900">
                    Edición del producto
                </p>

                <p class="mt-2 text-sm leading-6 text-zinc-600">
                    Ajusta los datos comerciales, precios, proveedor, unidad de medida y stock actual del producto cuando necesites corregir información.
                </p>
            </section>

            <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm">
                <div class="space-y-3">
                    <button
                        type="submit"
                        class="w-full rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C]">
                        Actualizar producto
                    </button>

                    <a
                        href="{{ route('products.index') }}"
                        class="block w-full rounded-xl border border-black/10 px-5 py-3 text-center text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                        Cancelar
                    </a>
                </div>
            </div>
        </aside>
    </form>
</div>

@endsection