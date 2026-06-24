@extends('layouts.app', [
'title' => 'Ventas | Susan Brigitt Studio',
'pageTitle' => 'Ventas'
])

@section('content')

<div class="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-center">
    <div>
        <p class="text-sm text-gray-500">
            Consulta las ventas registradas, formas de pago, tasas aplicadas, ingresos y ganancias estimadas.
        </p>
    </div>

    <a href="{{ route('sales.create') }}"
        class="inline-flex items-center justify-center rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C]">
        + Registrar venta
    </a>
</div>

<section class="grid grid-cols-1 gap-5 md:grid-cols-4">

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Ventas del mes</p>
        <h2 class="mt-3 text-3xl font-bold">$1.240,00</h2>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Ganancia estimada</p>
        <h2 class="mt-3 text-3xl font-bold text-green-600">$410,00</h2>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Unidades vendidas</p>
        <h2 class="mt-3 text-3xl font-bold">86</h2>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Ticket promedio</p>
        <h2 class="mt-3 text-3xl font-bold">$14,42</h2>
    </div>

</section>

<section class="mt-6 rounded-2xl border border-black/5 bg-white p-6 shadow-sm">

    <div class="mb-6 grid gap-4 md:grid-cols-5">

        <input type="text"
            placeholder="Buscar venta..."
            class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">

        <select class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
            <option>Producto</option>
            <option>Base líquida</option>
            <option>Labial mate</option>
            <option>Máscara de pestañas</option>
        </select>

        <select class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
            <option>Forma de pago</option>
            <option>Pago móvil</option>
            <option>Transferencia Bs</option>
            <option>Efectivo USD</option>
            <option>Binance</option>
            <option>Zelle</option>
        </select>

        <input type="date"
            class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">

        <button class="rounded-xl border border-[#E46F8A] px-5 py-3 text-sm font-semibold text-[#E46F8A] transition hover:bg-[#FFF0F4]">
            Filtrar
        </button>

    </div>

    <div class="max-w-full overflow-x-auto rounded-xl border border-black/5">
        <table class="min-w-[1180px] w-full text-left text-sm">
            <thead class="bg-[#F8F5F2] text-gray-500">
                <tr>
                    <th class="whitespace-nowrap px-5 py-4">Fecha</th>
                    <th class="whitespace-nowrap px-5 py-4">Producto</th>
                    <th class="whitespace-nowrap px-5 py-4">Marca</th>
                    <th class="whitespace-nowrap px-5 py-4">Tono</th>
                    <th class="whitespace-nowrap px-5 py-4">Cantidad</th>
                    <th class="whitespace-nowrap px-5 py-4">Precio USD</th>
                    <th class="whitespace-nowrap px-5 py-4">Total USD</th>
                    <th class="whitespace-nowrap px-5 py-4">Tasa</th>
                    <th class="whitespace-nowrap px-5 py-4">Total Bs</th>
                    <th class="whitespace-nowrap px-5 py-4">Ganancia</th>
                    <th class="whitespace-nowrap px-5 py-4">Pago</th>
                    <th class="whitespace-nowrap px-5 py-4">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-black/5">

                <tr>
                    <td class="whitespace-nowrap px-5 py-4">21/05/2024</td>
                    <td class="px-5 py-4 font-medium">Base líquida</td>
                    <td class="px-5 py-4">Vogue</td>
                    <td class="px-5 py-4">Beige claro</td>
                    <td class="px-5 py-4 font-semibold">2</td>
                    <td class="px-5 py-4">$8,00</td>
                    <td class="px-5 py-4 font-semibold">$16,00</td>
                    <td class="px-5 py-4">37,65</td>
                    <td class="px-5 py-4">Bs. 602,40</td>
                    <td class="px-5 py-4 font-semibold text-green-600">$7,00</td>
                    <td class="px-5 py-4">
                        <span class="whitespace-nowrap rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                            Pago móvil
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
                    <td class="whitespace-nowrap px-5 py-4">21/05/2024</td>
                    <td class="px-5 py-4 font-medium">Labial mate</td>
                    <td class="px-5 py-4">Valmy</td>
                    <td class="px-5 py-4">Rojo intenso</td>
                    <td class="px-5 py-4 font-semibold">1</td>
                    <td class="px-5 py-4">$4,50</td>
                    <td class="px-5 py-4 font-semibold">$4,50</td>
                    <td class="px-5 py-4">37,65</td>
                    <td class="px-5 py-4">Bs. 169,43</td>
                    <td class="px-5 py-4 font-semibold text-green-600">$2,50</td>
                    <td class="px-5 py-4">
                        <span class="rounded-full bg-purple-50 px-3 py-1 text-xs font-semibold text-purple-700">
                            Binance
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
                    <td class="whitespace-nowrap px-5 py-4">20/05/2024</td>
                    <td class="px-5 py-4 font-medium">Máscara de pestañas</td>
                    <td class="px-5 py-4">Maybelline</td>
                    <td class="px-5 py-4">Negro</td>
                    <td class="px-5 py-4 font-semibold">1</td>
                    <td class="px-5 py-4">$7,00</td>
                    <td class="px-5 py-4 font-semibold">$7,00</td>
                    <td class="px-5 py-4">36,92</td>
                    <td class="px-5 py-4">Bs. 258,44</td>
                    <td class="px-5 py-4 font-semibold text-green-600">$3,20</td>
                    <td class="px-5 py-4">
                        <span class="whitespace-nowrap rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                            Efectivo USD
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