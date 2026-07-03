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

    <div class="flex gap-3">
        <a href="{{ route('products.create') }}"
            class="inline-flex items-center justify-center rounded-xl border border-[#E46F8A] px-5 py-3 text-sm font-semibold text-[#E46F8A] transition hover:bg-[#FFF0F4]">
            + Registrar producto
        </a>

        <button
            class="inline-flex items-center justify-center rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C]">
            Exportar
        </button>
    </div>
</div>

<section class="grid grid-cols-1 gap-5 md:grid-cols-5">

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Total productos</p>
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
        <p class="text-sm text-gray-500">Stock bajo</p>
        <h2 class="mt-3 text-3xl font-bold text-yellow-600">{{ $lowStockProducts }}</h2>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Valor inventario</p>
        <h2 class="mt-3 text-3xl font-bold">${{ number_format($inventoryValue, 2, ',', '.') }}</h2>
    </div>

</section>

<section class="mt-6 rounded-2xl border border-black/5 bg-white p-6 shadow-sm">

    <div class="mb-6 grid gap-4 md:grid-cols-6">

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
            <option>Estado de stock</option>
            <option>Disponible</option>
            <option>Stock bajo</option>
            <option>Agotado</option>
        </select>

        <select class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
            <option>Proveedor</option>
            <option>Proveedoría Beauty C.A.</option>
            <option>Distribuidora Glam</option>
            <option>Importadora Cosmo</option>
        </select>

    </div>

    <div class="max-w-full overflow-x-auto rounded-xl border border-black/5">
        <table class="min-w-[1120px] w-full text-left text-sm">
            <thead class="bg-[#F8F5F2] text-gray-500">
                <tr>
                    <th class="whitespace-nowrap px-5 py-4">Código</th>
                    <th class="whitespace-nowrap px-5 py-4">Producto</th>
                    <th class="whitespace-nowrap px-5 py-4">Marca</th>
                    <th class="whitespace-nowrap px-5 py-4">Tono</th>
                    <th class="whitespace-nowrap px-5 py-4">Stock</th>
                    <th class="whitespace-nowrap px-5 py-4">Stock mínimo</th>
                    <th class="whitespace-nowrap px-5 py-4">Costo USD</th>
                    <th class="whitespace-nowrap px-5 py-4">Venta USD</th>
                    <th class="whitespace-nowrap px-5 py-4">Valor inventario</th>
                    <th class="whitespace-nowrap px-5 py-4">Estado</th>
                    <th class="whitespace-nowrap px-5 py-4">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-black/5">

                @forelse ($products as $product)
                <tr>
                    <td class="whitespace-nowrap px-5 py-4 font-medium">{{ $product->internal_code }}</td>
                    <td class="px-5 py-4">{{ $product->name }}</td>
                    <td class="px-5 py-4">{{ $product->brand?->name ?? 'Sin marca' }}</td>
                    <td class="px-5 py-4">{{ $product->tone?->name ?? 'Sin tono' }}</td>
                    <td class="px-5 py-4 font-semibold">{{ $product->current_stock }}</td>
                    <td class="px-5 py-4">{{ $product->minimum_stock }}</td>
                    <td class="px-5 py-4">${{ number_format((float) $product->purchase_price_usd, 2, ',', '.') }}</td>
                    <td class="px-5 py-4">${{ number_format((float) $product->sale_price_usd, 2, ',', '.') }}</td>
                    <td class="px-5 py-4 font-semibold">
                        ${{ number_format((float) $product->current_stock * (float) $product->purchase_price_usd, 2, ',', '.') }}
                    </td>
                    <td class="px-5 py-4">
                        @if ($product->stock_status === 'agotado')
                        <span class="whitespace-nowrap rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">Agotado</span>
                        @elseif ($product->stock_status === 'stock_bajo')
                        <span class="whitespace-nowrap rounded-full bg-yellow-50 px-3 py-1 text-xs font-semibold text-yellow-700">Stock bajo</span>
                        @else
                        <span class="whitespace-nowrap rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">Disponible</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex gap-2">
                            <button class="rounded-lg border border-black/10 px-3 py-2 text-xs hover:bg-gray-50">Ver</button>
                            <button class="rounded-lg border border-black/10 px-3 py-2 text-xs hover:bg-gray-50">Ajustar</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="px-5 py-10 text-center text-gray-500">
                        No hay productos en inventario.
                    </td>
                </tr>
                @endforelse

            </tbody>
        </table>
    </div>

</section>

<section class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <h3 class="font-bold">Alertas de reposición</h3>
        <p class="mt-2 text-sm text-gray-500">
            Productos que requieren compra o revisión inmediata.
        </p>

        <div class="mt-6 space-y-4">
            <div class="flex items-center justify-between rounded-xl bg-yellow-50 p-4">
                <div>
                    <p class="font-semibold text-yellow-800">Labial mate</p>
                    <p class="text-sm text-yellow-700">Stock actual: 3 / mínimo: 5</p>
                </div>
                <span class="text-sm font-bold text-yellow-700">Reponer</span>
            </div>

            <div class="flex items-center justify-between rounded-xl bg-red-50 p-4">
                <div>
                    <p class="font-semibold text-red-800">Máscara de pestañas</p>
                    <p class="text-sm text-red-700">Stock actual: 0 / mínimo: 3</p>
                </div>
                <span class="text-sm font-bold text-red-700">Agotado</span>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <h3 class="font-bold">Valor por marca</h3>

        <div class="mt-8 space-y-5">
            <div>
                <div class="mb-2 flex justify-between text-sm">
                    <span>Vogue</span>
                    <strong>$1.140</strong>
                </div>
                <div class="h-3 rounded-full bg-[#F8F5F2]">
                    <div class="h-3 w-[70%] rounded-full bg-[#E46F8A]"></div>
                </div>
            </div>

            <div>
                <div class="mb-2 flex justify-between text-sm">
                    <span>Valmy</span>
                    <strong>$620</strong>
                </div>
                <div class="h-3 rounded-full bg-[#F8F5F2]">
                    <div class="h-3 w-[45%] rounded-full bg-[#9DBBB2]"></div>
                </div>
            </div>

            <div>
                <div class="mb-2 flex justify-between text-sm">
                    <span>Maybelline</span>
                    <strong>$460</strong>
                </div>
                <div class="h-3 rounded-full bg-[#F8F5F2]">
                    <div class="h-3 w-[34%] rounded-full bg-[#F2B86D]"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <h3 class="font-bold">Resumen operativo</h3>

        <div class="mt-6 space-y-4">
            <div class="rounded-xl bg-[#F8F5F2] p-4">
                <p class="text-sm text-gray-500">Rotación estimada</p>
                <h4 class="mt-1 text-2xl font-bold">Media</h4>
            </div>

            <div class="rounded-xl bg-[#F8F5F2] p-4">
                <p class="text-sm text-gray-500">Productos sin movimiento</p>
                <h4 class="mt-1 text-2xl font-bold">9</h4>
            </div>

            <div class="rounded-xl bg-[#F8F5F2] p-4">
                <p class="text-sm text-gray-500">Última actualización</p>
                <h4 class="mt-1 text-2xl font-bold">Hoy</h4>
            </div>
        </div>
    </div>

</section>

@endsection