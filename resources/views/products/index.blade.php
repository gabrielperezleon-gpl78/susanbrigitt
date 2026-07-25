@extends('layouts.app', [
'title' => 'Productos | Susan Brigitt Studio',
'pageTitle' => 'Productos'
])

@section('content')

<div class="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-center">
    <div>
        <p class="text-sm text-gray-500">
            Administra el catálogo interno de productos, marcas, tonos, precios y disponibilidad.
        </p>
    </div>

    <a href="{{ route('products.create') }}"
        class="inline-flex items-center justify-center rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C]">
        + Registrar producto
    </a>
</div>

@if (session('success'))
<div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
    {{ session('success') }}
</div>
@endif

<section class="grid grid-cols-1 gap-5 md:grid-cols-4">

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Productos activos</p>
        <h2 class="mt-3 text-3xl font-bold">{{ $totalProducts }}</h2>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Unidades disponibles</p>
        <h2 class="mt-3 text-3xl font-bold">{{ $availableUnits }}</h2>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Productos agotados</p>
        <h2 class="mt-3 text-3xl font-bold text-[#E46F8A]">{{ $outOfStockProducts }}</h2>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Valor inventario</p>
        <h2 class="mt-3 text-3xl font-bold">${{ number_format($inventoryValue, 2, ',', '.') }}</h2>
    </div>

</section>

<section class="mt-6 rounded-2xl border border-black/5 bg-white p-6 shadow-sm">

    <form
        method="GET"
        action="{{ route('products.index') }}"
        class="mb-6 grid gap-4 md:grid-cols-2 xl:grid-cols-6">
        <div class="xl:col-span-2">
            <label
                for="search"
                class="sr-only">
                Buscar producto
            </label>

            <input
                id="search"
                name="search"
                type="text"
                value="{{ request('search') }}"
                placeholder="Buscar por producto, código o barra..."
                class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
        </div>

        <div>
            <label
                for="brand_id"
                class="sr-only">
                Marca
            </label>

            <select
                id="brand_id"
                name="brand_id"
                class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                <option value="">
                    Todas las marcas
                </option>

                @foreach ($brands as $brand)
                <option
                    value="{{ $brand->id }}"
                    @selected(
                    (string) request('brand_id')===(string) $brand->id
                    )
                    >
                    {{ $brand->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label
                for="tone_id"
                class="sr-only">
                Tono
            </label>

            <select
                id="tone_id"
                name="tone_id"
                class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                <option value="">
                    Todos los tonos
                </option>

                @foreach ($tones as $tone)
                <option
                    value="{{ $tone->id }}"
                    @selected(
                    (string) request('tone_id')===(string) $tone->id
                    )
                    >
                    {{ $tone->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label
                for="category_id"
                class="sr-only">
                Categoría
            </label>

            <select
                id="category_id"
                name="category_id"
                class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                <option value="">
                    Todas las categorías
                </option>

                @foreach ($categories as $category)
                <option
                    value="{{ $category->id }}"
                    @selected(
                    (string) request('category_id')===(string) $category->id
                    )
                    >
                    {{ $category->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label
                for="stock_status"
                class="sr-only">
                Estado del stock
            </label>

            <select
                id="stock_status"
                name="stock_status"
                class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                <option value="">
                    Todos los estados
                </option>

                <option
                    value="available"
                    @selected(
                    request('stock_status')==='available'
                    )>
                    Disponible
                </option>

                <option
                    value="low"
                    @selected(
                    request('stock_status')==='low'
                    )>
                    Stock bajo
                </option>

                <option
                    value="out"
                    @selected(
                    request('stock_status')==='out'
                    )>
                    Agotado
                </option>
            </select>
        </div>

        <div class="flex gap-2 md:col-span-2 xl:col-span-6 xl:justify-end">
            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-xl bg-zinc-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-zinc-700">
                Aplicar filtros
            </button>

            @if (
            request()->hasAny([
            'search',
            'brand_id',
            'tone_id',
            'category_id',
            'stock_status',
            ])
            )
            <a
                href="{{ route('products.index') }}"
                class="inline-flex items-center justify-center rounded-xl border border-black/10 bg-white px-5 py-3 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-50">
                Limpiar
            </a>
            @endif
        </div>
    </form>

    <div class="overflow-x-auto rounded-xl border border-black/5">
        <table class="min-w-[1100px] w-full text-left text-sm">
            <thead class="bg-[#F8F5F2] text-gray-500">
                <tr>
                    <th class="whitespace-nowrap px-5 py-4">Código</th>
                    <th class="whitespace-nowrap px-5 py-4">Producto</th>
                    <th class="whitespace-nowrap px-5 py-4">Marca</th>
                    <th class="whitespace-nowrap px-5 py-4">Tono</th>
                    <th class="whitespace-nowrap px-5 py-4">Unidad</th>
                    <th class="whitespace-nowrap px-5 py-4">Stock</th>
                    <th class="whitespace-nowrap px-5 py-4">Costo USD</th>
                    <th class="whitespace-nowrap px-5 py-4">Venta USD</th>
                    <th class="whitespace-nowrap px-5 py-4">Ganancia</th>
                    <th class="whitespace-nowrap px-5 py-4">Estado</th>
                    <th class="whitespace-nowrap px-5 py-4">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-black/5">

                @forelse ($products as $product)
                <tr>
                    <td class="whitespace-nowrap px-5 py-4 font-medium">
                        {{ $product->internal_code }}
                    </td>

                    <td class="px-5 py-4">
                        {{ $product->name }}
                    </td>

                    <td class="px-5 py-4">
                        {{ $product->brand?->name ?? 'Sin marca' }}
                    </td>

                    <td class="px-5 py-4">
                        {{ $product->tone?->name ?? 'Sin tono' }}
                    </td>

                    <td class="px-5 py-4">
                        {{ $product->unitMeasure?->abbreviation ?? $product->unitMeasure?->name ?? 'Sin unidad' }}
                    </td>

                    <td class="px-5 py-4">
                        {{ $product->current_stock }}
                    </td>

                    <td class="px-5 py-4">
                        ${{ number_format((float) $product->purchase_price_usd, 2, ',', '.') }}
                    </td>

                    <td class="px-5 py-4">
                        ${{ number_format((float) $product->sale_price_usd, 2, ',', '.') }}
                    </td>

                    <td class="px-5 py-4 font-semibold text-green-600">
                        ${{ number_format((float) $product->unit_profit_usd, 2, ',', '.') }}
                    </td>

                    <td class="px-5 py-4">
                        @php
                        $currentStock = (int) $product->current_stock;
                        $minimumStock = (int) $product->minimum_stock;
                        @endphp

                        @if ($currentStock <= 0)
                            <span class="whitespace-nowrap rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">
                            Agotado
                            </span>
                            @elseif ($currentStock <= $minimumStock)
                                <span class="whitespace-nowrap rounded-full bg-yellow-50 px-3 py-1 text-xs font-semibold text-yellow-700">
                                Stock bajo
                                </span>
                                @else
                                <span class="whitespace-nowrap rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">
                                    Disponible
                                </span>
                                @endif
                    </td>

                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            <a
                                href="{{ route('products.show', $product) }}"
                                class="rounded-lg border border-black/10 px-3 py-2 text-xs hover:bg-gray-50">
                                Ver
                            </a>

                            <a
                                href="{{ route('products.edit', $product) }}"
                                class="rounded-lg border border-black/10 px-3 py-2 text-xs hover:bg-gray-50">
                                Editar
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="px-5 py-10 text-center text-gray-500">
                        No hay productos registrados.
                    </td>
                </tr>
                @endforelse

            </tbody>
        </table>
    </div>

</section>

@endsection