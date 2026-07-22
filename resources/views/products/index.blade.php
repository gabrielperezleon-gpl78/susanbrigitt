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

    <div class="mb-6 grid gap-4 md:grid-cols-5">

        <input type="text"
            placeholder="Buscar producto..."
            class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">

        <select class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
            <option>Marca</option>
            <option>Vogue</option>
            <option>Valmy</option>
            <option>Maybelline</option>
        </select>

        <select class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
            <option>Tono</option>
            <option>Beige claro</option>
            <option>Rojo intenso</option>
            <option>Negro</option>
        </select>

        <select class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
            <option>Categoría</option>
            <option>Maquillaje</option>
            <option>Cuidado facial</option>
            <option>Cuidado labial</option>
        </select>

        <select class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
            <option>Estado</option>
            <option>Disponible</option>
            <option>Stock bajo</option>
            <option>Agotado</option>
        </select>

    </div>

    <div class="overflow-hidden rounded-xl border border-black/5">
        <table class="min-w-[1050px] w-full text-left text-sm">
            <thead class="bg-[#F8F5F2] text-gray-500">
                <tr>
                    <th class="whitespace-nowrap px-5 py-4">Código</th>
                    <th class="whitespace-nowrap px-5 py-4">Producto</th>
                    <th class="whitespace-nowrap px-5 py-4">Marca</th>
                    <th class="whitespace-nowrap px-5 py-4">Tono</th>
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
                        @if ($product->stock_status === 'agotado')
                        <span class="whitespace-nowrap rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">
                            Agotado
                        </span>
                        @elseif ($product->stock_status === 'stock_bajo')
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
                            <button class="rounded-lg border border-black/10 px-3 py-2 text-xs hover:bg-gray-50">
                                Ver
                            </button>

                            <button class="rounded-lg border border-black/10 px-3 py-2 text-xs hover:bg-gray-50">
                                Editar
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="px-5 py-10 text-center text-gray-500">
                        No hay productos registrados.
                    </td>
                </tr>
                @endforelse

            </tbody>
        </table>
    </div>

</section>

@endsection