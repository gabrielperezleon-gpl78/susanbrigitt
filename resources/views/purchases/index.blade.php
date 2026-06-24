@extends('layouts.app', [
'title' => 'Compras | Susan Brigitt Studio',
'pageTitle' => 'Compras'
])

@section('content')

<div class="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-center">
    <div>
        <p class="text-sm text-gray-500">
            Registra ingresos de mercancía, costos de compra, proveedor y tasa de cambio aplicada.
        </p>
    </div>

    <a href="{{ route('purchases.create') }}"
        class="inline-flex items-center justify-center rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C]">
        + Registrar compra
    </a>
</div>

<section class="grid grid-cols-1 gap-5 md:grid-cols-4">

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Compras del mes</p>
        <h2 class="mt-3 text-3xl font-bold">$820,00</h2>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Unidades ingresadas</p>
        <h2 class="mt-3 text-3xl font-bold">134</h2>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Proveedores activos</p>
        <h2 class="mt-3 text-3xl font-bold">8</h2>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Última tasa usada</p>
        <h2 class="mt-3 text-3xl font-bold">37,65</h2>
    </div>

</section>

<section class="mt-6 rounded-2xl border border-black/5 bg-white p-6 shadow-sm">

    <div class="mb-6 grid gap-4 md:grid-cols-5">

        <input type="text"
            placeholder="Buscar compra..."
            class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">

        <select class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
            <option>Proveedor</option>
            <option>Proveedoría Beauty C.A.</option>
            <option>Distribuidora Glam</option>
            <option>Importadora Cosmo</option>
        </select>

        <select class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
            <option>Fuente tasa</option>
            <option>BCV</option>
            <option>Binance</option>
            <option>Manual</option>
        </select>

        <input type="date"
            class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">

        <button class="rounded-xl border border-[#E46F8A] px-5 py-3 text-sm font-semibold text-[#E46F8A] transition hover:bg-[#FFF0F4]">
            Filtrar
        </button>

    </div>

    <div class="max-w-full overflow-x-auto rounded-xl border border-black/5">
        <table class="min-w-[1120px] w-full text-left text-sm">
            <thead class="bg-[#F8F5F2] text-gray-500">
                <tr>
                    <th class="whitespace-nowrap px-5 py-4">Fecha</th>
                    <th class="whitespace-nowrap px-5 py-4">Proveedor</th>
                    <th class="whitespace-nowrap px-5 py-4">Producto</th>
                    <th class="whitespace-nowrap px-5 py-4">Marca</th>
                    <th class="whitespace-nowrap px-5 py-4">Cantidad</th>
                    <th class="whitespace-nowrap px-5 py-4">Costo unit. USD</th>
                    <th class="whitespace-nowrap px-5 py-4">Total USD</th>
                    <th class="whitespace-nowrap px-5 py-4">Tasa</th>
                    <th class="whitespace-nowrap px-5 py-4">Total Bs</th>
                    <th class="whitespace-nowrap px-5 py-4">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-black/5">

                <tr>
                    <td class="whitespace-nowrap px-5 py-4">21/05/2024</td>
                    <td class="px-5 py-4">Proveedoría Beauty C.A.</td>
                    <td class="px-5 py-4 font-medium">Base líquida</td>
                    <td class="px-5 py-4">Vogue</td>
                    <td class="px-5 py-4 font-semibold">20</td>
                    <td class="px-5 py-4">$4,50</td>
                    <td class="px-5 py-4 font-semibold">$90,00</td>
                    <td class="px-5 py-4">37,65</td>
                    <td class="px-5 py-4">Bs. 3.388,50</td>
                    <td class="px-5 py-4">
                        <div class="flex gap-2">
                            <button class="rounded-lg border border-black/10 px-3 py-2 text-xs hover:bg-gray-50">Ver</button>
                            <button class="rounded-lg border border-black/10 px-3 py-2 text-xs hover:bg-gray-50">Editar</button>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class="whitespace-nowrap px-5 py-4">20/05/2024</td>
                    <td class="px-5 py-4">Distribuidora Glam</td>
                    <td class="px-5 py-4 font-medium">Labial mate</td>
                    <td class="px-5 py-4">Valmy</td>
                    <td class="px-5 py-4 font-semibold">30</td>
                    <td class="px-5 py-4">$2,00</td>
                    <td class="px-5 py-4 font-semibold">$60,00</td>
                    <td class="px-5 py-4">37,65</td>
                    <td class="px-5 py-4">Bs. 2.259,00</td>
                    <td class="px-5 py-4">
                        <div class="flex gap-2">
                            <button class="rounded-lg border border-black/10 px-3 py-2 text-xs hover:bg-gray-50">Ver</button>
                            <button class="rounded-lg border border-black/10 px-3 py-2 text-xs hover:bg-gray-50">Editar</button>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class="whitespace-nowrap px-5 py-4">18/05/2024</td>
                    <td class="px-5 py-4">Importadora Cosmo</td>
                    <td class="px-5 py-4 font-medium">Máscara de pestañas</td>
                    <td class="px-5 py-4">Maybelline</td>
                    <td class="px-5 py-4 font-semibold">15</td>
                    <td class="px-5 py-4">$3,80</td>
                    <td class="px-5 py-4 font-semibold">$57,00</td>
                    <td class="px-5 py-4">36,92</td>
                    <td class="px-5 py-4">Bs. 2.104,44</td>
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