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

<section class="grid grid-cols-1 gap-5 md:grid-cols-4">

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Productos activos</p>
        <h2 class="mt-3 text-3xl font-bold">96</h2>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Unidades disponibles</p>
        <h2 class="mt-3 text-3xl font-bold">186</h2>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Productos agotados</p>
        <h2 class="mt-3 text-3xl font-bold text-[#E46F8A]">6</h2>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Valor inventario</p>
        <h2 class="mt-3 text-3xl font-bold">$2.850,00</h2>
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

                <tr>
                    <td class="whitespace-nowrap px-5 py-4 font-medium">SB-0001</td>
                    <td class="px-5 py-4">Base líquida</td>
                    <td class="px-5 py-4">Vogue</td>
                    <td class="px-5 py-4">Beige claro</td>
                    <td class="px-5 py-4">17</td>
                    <td class="px-5 py-4">$4,50</td>
                    <td class="px-5 py-4">$8,00</td>
                    <td class="px-5 py-4 font-semibold text-green-600">$3,50</td>
                    <td class="px-5 py-4">
                        <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">
                            Disponible
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex gap-2">
                            <button class="rounded-lg border border-black/10 px-3 py-2 text-xs hover:bg-gray-50">Ver</button>
                            <button class="rounded-lg border border-black/10 px-3 py-2 text-xs hover:bg-gray-50">Editar</button>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class="whitespace-nowrap px-5 py-4 font-medium">SB-0002</td>
                    <td class="px-5 py-4">Labial mate</td>
                    <td class="px-5 py-4">Valmy</td>
                    <td class="px-5 py-4">Rojo intenso</td>
                    <td class="px-5 py-4 text-[#E46F8A]">3</td>
                    <td class="px-5 py-4">$2,00</td>
                    <td class="px-5 py-4">$4,50</td>
                    <td class="px-5 py-4 font-semibold text-green-600">$2,50</td>
                    <td class="px-5 py-4">
                        <span class="rounded-full bg-yellow-50 px-3 py-1 text-xs font-semibold text-yellow-700">
                            Stock bajo
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex gap-2">
                            <button class="rounded-lg border border-black/10 px-3 py-2 text-xs hover:bg-gray-50">Ver</button>
                            <button class="rounded-lg border border-black/10 px-3 py-2 text-xs hover:bg-gray-50">Editar</button>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class="whitespace-nowrap px-5 py-4 font-medium">SB-0003</td>
                    <td class="px-5 py-4">Máscara de pestañas</td>
                    <td class="px-5 py-4">Maybelline</td>
                    <td class="px-5 py-4">Negro</td>
                    <td class="px-5 py-4 text-[#E46F8A]">0</td>
                    <td class="px-5 py-4">$3,80</td>
                    <td class="px-5 py-4">$7,00</td>
                    <td class="px-5 py-4 font-semibold text-green-600">$3,20</td>
                    <td class="px-5 py-4">
                        <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">
                            Agotado
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex gap-2">
                            <button class="rounded-lg border border-black/10 px-3 py-2 text-xs hover:bg-gray-50">Ver</button>
                            <button class="rounded-lg border border-black/10 px-3 py-2 text-xs hover:bg-gray-50">Editar</button>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class="whitespace-nowrap px-5 py-4 font-medium">SB-0004</td>
                    <td class="px-5 py-4">Corrector líquido</td>
                    <td class="px-5 py-4">Vogue</td>
                    <td class="px-5 py-4">Beige natural</td>
                    <td class="px-5 py-4">8</td>
                    <td class="px-5 py-4">$2,80</td>
                    <td class="px-5 py-4">$5,50</td>
                    <td class="px-5 py-4 font-semibold text-green-600">$2,70</td>
                    <td class="px-5 py-4">
                        <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">
                            Disponible
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex gap-2">
                            <button class="rounded-lg border border-black/10 px-3 py-2 text-xs hover:bg-gray-50">Ver</button>
                            <button class="rounded-lg border border-black/10 px-3 py-2 text-xs hover:bg-gray-50">Editar</button>
                        </div>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>

</section>

@endsection