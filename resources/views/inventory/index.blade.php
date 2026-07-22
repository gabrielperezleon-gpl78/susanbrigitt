@extends('layouts.app', [
'title' => 'Inventario | Susan Brigitt Studio',
'pageTitle' => 'Gestión de inventario'
])

@section('content')

<div class="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-center">
    <div>
        <p class="text-sm text-gray-500">
            Consulta existencias, productos agotados, alertas de reposición y valor disponible en inventario.
        </p>
    </div>

    <div class="flex flex-col gap-3 sm:flex-row">
        <a
            href="{{ route('products.create') }}"
            class="inline-flex items-center justify-center rounded-xl border border-[#E46F8A] px-5 py-3 text-sm font-semibold text-[#E46F8A] transition hover:bg-[#FFF0F4]">
            + Registrar producto
        </a>

        <button
            type="button"
            disabled
            title="La exportación se incorporará en una fase posterior."
            class="inline-flex cursor-not-allowed items-center justify-center rounded-xl bg-zinc-200 px-5 py-3 text-sm font-semibold text-zinc-500">
            Exportar
        </button>
    </div>
</div>

<section class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-5">
    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">
            Total productos
        </p>

        <h2 class="mt-3 text-3xl font-bold">
            {{ $totalProducts }}
        </h2>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">
            Unidades disponibles
        </p>

        <h2 class="mt-3 text-3xl font-bold">
            {{ $availableUnits }}
        </h2>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">
            Productos agotados
        </p>

        <h2 class="mt-3 text-3xl font-bold text-[#E46F8A]">
            {{ $outOfStockProducts }}
        </h2>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">
            Stock bajo
        </p>

        <h2 class="mt-3 text-3xl font-bold text-yellow-600">
            {{ $lowStockProducts }}
        </h2>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">
            Valor del inventario
        </p>

        <h2 class="mt-3 text-3xl font-bold">
            ${{ number_format($inventoryValue, 2, ',', '.') }}
        </h2>
    </div>
</section>

<section class="mt-6 rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
    <form
        action="{{ route('inventory.index') }}"
        method="GET"
        class="mb-6 grid gap-4 md:grid-cols-2 xl:grid-cols-6">
        <input
            name="search"
            type="text"
            value="{{ $filters['search'] }}"
            placeholder="Buscar producto..."
            class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">

        <select
            name="brand_id"
            class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
            <option value="">
                Todas las marcas
            </option>

            @foreach ($brands as $brand)
            <option
                value="{{ $brand->id }}"
                @selected((int) $filters['brand_id']===$brand->id)
                >
                {{ $brand->name }}
            </option>
            @endforeach
        </select>

        <select
            name="tone_id"
            class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
            <option value="">
                Todos los tonos
            </option>

            @foreach ($tones as $tone)
            <option
                value="{{ $tone->id }}"
                @selected((int) $filters['tone_id']===$tone->id)
                >
                {{ $tone->name }}
            </option>
            @endforeach
        </select>

        <select
            name="category_id"
            class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
            <option value="">
                Todas las categorías
            </option>

            @foreach ($categories as $category)
            <option
                value="{{ $category->id }}"
                @selected((int) $filters['category_id']===$category->id)
                >
                {{ $category->name }}
            </option>
            @endforeach
        </select>

        <select
            name="stock_status"
            class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
            <option value="">
                Todos los estados
            </option>

            <option
                value="available"
                @selected($filters['stock_status']==='available' )>
                Disponible
            </option>

            <option
                value="low"
                @selected($filters['stock_status']==='low' )>
                Stock bajo
            </option>

            <option
                value="out"
                @selected($filters['stock_status']==='out' )>
                Agotado
            </option>
        </select>

        <select
            name="supplier_id"
            class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
            <option value="">
                Todos los proveedores
            </option>

            @foreach ($suppliers as $supplier)
            <option
                value="{{ $supplier->id }}"
                @selected((int) $filters['supplier_id']===$supplier->id)
                >
                {{ $supplier->name }}
            </option>
            @endforeach
        </select>

        <div class="flex gap-3 md:col-span-2 xl:col-span-6 xl:justify-end">
            <a
                href="{{ route('inventory.index') }}"
                class="inline-flex items-center justify-center rounded-xl border border-black/10 px-5 py-3 text-sm font-semibold text-zinc-600 transition hover:bg-zinc-50">
                Limpiar filtros
            </a>

            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#D75E7C]">
                Aplicar filtros
            </button>
        </div>
    </form>

    <div class="max-w-full overflow-x-auto rounded-xl border border-black/5">
        <table class="w-full min-w-280 text-left text-sm">
            <thead class="bg-[#F8F5F2] text-gray-500">
                <tr>
                    <th class="whitespace-nowrap px-5 py-4">
                        Código
                    </th>

                    <th class="whitespace-nowrap px-5 py-4">
                        Producto
                    </th>

                    <th class="whitespace-nowrap px-5 py-4">
                        Marca
                    </th>

                    <th class="whitespace-nowrap px-5 py-4">
                        Tono
                    </th>

                    <th class="whitespace-nowrap px-5 py-4">
                        Stock
                    </th>

                    <th class="whitespace-nowrap px-5 py-4">
                        Stock mínimo
                    </th>

                    <th class="whitespace-nowrap px-5 py-4">
                        Costo USD
                    </th>

                    <th class="whitespace-nowrap px-5 py-4">
                        Venta USD
                    </th>

                    <th class="whitespace-nowrap px-5 py-4">
                        Valor inventario
                    </th>

                    <th class="whitespace-nowrap px-5 py-4">
                        Estado
                    </th>

                    <th class="whitespace-nowrap px-5 py-4">
                        Acciones
                    </th>
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

                    <td class="px-5 py-4 font-semibold">
                        {{ $product->current_stock }}
                    </td>

                    <td class="px-5 py-4">
                        {{ $product->minimum_stock }}
                    </td>

                    <td class="px-5 py-4">
                        ${{ number_format((float) $product->purchase_price_usd, 2, ',', '.') }}
                    </td>

                    <td class="px-5 py-4">
                        ${{ number_format((float) $product->sale_price_usd, 2, ',', '.') }}
                    </td>

                    <td class="px-5 py-4 font-semibold">
                        ${{ number_format(
                                (float) $product->current_stock *
                                (float) $product->purchase_price_usd,
                                2,
                                ',',
                                '.'
                            ) }}
                    </td>

                    <td class="px-5 py-4">
                        @if ($product->current_stock <= 0)
                            <span class="whitespace-nowrap rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">
                            Agotado
                            </span>
                            @elseif ($product->current_stock <= $product->minimum_stock)
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
                        <div class="flex gap-2">
                            <a
                                href="{{ route('products.show', $product) }}"
                                class="rounded-lg border border-black/10 px-3 py-2 text-xs font-semibold text-zinc-700 hover:bg-gray-50">
                                Ver
                            </a>

                            <a
                                href="{{ route('products.edit', $product) }}"
                                class="rounded-lg border border-black/10 px-3 py-2 text-xs font-semibold text-zinc-700 hover:bg-gray-50">
                                Editar
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td
                        colspan="11"
                        class="px-5 py-12 text-center text-gray-500">
                        @if (array_filter($filters))
                        No se encontraron productos con los filtros seleccionados.
                        @else
                        No hay productos en inventario.
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<section class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <h3 class="font-bold">
            Alertas de reposición
        </h3>

        <p class="mt-2 text-sm text-gray-500">
            Productos que requieren compra o revisión.
        </p>

        <div class="mt-6 space-y-4">
            @forelse ($replenishmentAlerts as $product)
            <div
                class="flex items-center justify-between gap-4 rounded-xl p-4 {{ $product->current_stock <= 0 ? 'bg-red-50' : 'bg-yellow-50' }}">
                <div class="min-w-0">
                    <p class="font-semibold {{ $product->current_stock <= 0 ? 'text-red-800' : 'text-yellow-800' }}">
                        {{ $product->name }}
                    </p>

                    <p class="mt-1 text-sm {{ $product->current_stock <= 0 ? 'text-red-700' : 'text-yellow-700' }}">
                        Stock actual:
                        {{ $product->current_stock }}
                        / mínimo:
                        {{ $product->minimum_stock }}
                    </p>

                    @if ($product->supplier)
                    <p class="mt-1 text-xs {{ $product->current_stock <= 0 ? 'text-red-600' : 'text-yellow-600' }}">
                        {{ $product->supplier->name }}
                    </p>
                    @endif
                </div>

                <span class="shrink-0 text-sm font-bold {{ $product->current_stock <= 0 ? 'text-red-700' : 'text-yellow-700' }}">
                    {{ $product->current_stock <= 0 ? 'Agotado' : 'Reponer' }}
                </span>
            </div>
            @empty
            <div class="rounded-xl bg-[#F8F5F2] p-5 text-sm text-gray-500">
                No existen alertas de reposición.
            </div>
            @endforelse
        </div>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <h3 class="font-bold">
            Valor por marca
        </h3>

        <p class="mt-2 text-sm text-gray-500">
            Valor de compra del inventario disponible.
        </p>

        <div class="mt-6 space-y-5">
            @forelse ($brandValues as $brandValue)
            @php
            $percentage = $maxBrandValue > 0
            ? min(
            100,
            ((float) $brandValue->total_value /
            $maxBrandValue) * 100
            )
            : 0;
            @endphp

            <div>
                <div class="mb-2 flex justify-between gap-4 text-sm">
                    <span>
                        {{ $brandValue->brand_name }}
                    </span>

                    <strong>
                        ${{ number_format(
                                (float) $brandValue->total_value,
                                2,
                                ',',
                                '.'
                            ) }}
                    </strong>
                </div>

                <div class="h-3 rounded-full bg-[#F8F5F2]">
                    <div
                        class="h-3 rounded-full bg-[#E46F8A]"
                        style="width: {{ $percentage }}%"></div>
                </div>
            </div>
            @empty
            <div class="rounded-xl bg-[#F8F5F2] p-5 text-sm text-gray-500">
                No existen valores de inventario por marca.
            </div>
            @endforelse
        </div>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <h3 class="font-bold">
            Resumen operativo
        </h3>

        <div class="mt-6 space-y-4">
            <div class="rounded-xl bg-[#F8F5F2] p-4">
                <p class="text-sm text-gray-500">
                    Rotación estimada
                </p>

                <h4 class="mt-1 text-2xl font-bold">
                    {{ $rotationEstimate }}
                </h4>

                <p class="mt-1 text-xs text-gray-400">
                    Basada en ventas de los últimos 30 días.
                </p>
            </div>

            <div class="rounded-xl bg-[#F8F5F2] p-4">
                <p class="text-sm text-gray-500">
                    Productos sin ventas
                </p>

                <h4 class="mt-1 text-2xl font-bold">
                    {{ $productsWithoutMovement }}
                </h4>
            </div>

            <div class="rounded-xl bg-[#F8F5F2] p-4">
                <p class="text-sm text-gray-500">
                    Última actualización
                </p>

                <h4 class="mt-1 text-xl font-bold">
                    {{ $lastUpdatedAt
                        ? $lastUpdatedAt->format('d/m/Y H:i')
                        : 'Sin registros' }}
                </h4>
            </div>
        </div>
    </div>
</section>

@endsection