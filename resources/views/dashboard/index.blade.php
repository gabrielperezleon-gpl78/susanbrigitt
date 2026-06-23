@extends('layouts.app', [
'title' => 'Dashboard | Susan Brigitt Studio',
'pageTitle' => 'Hola, Susan 👋'
])

@section('content')

<section class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-5">

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Ventas del mes</p>
        <h2 class="mt-3 text-3xl font-bold">$1.240,00</h2>
        <p class="mt-2 text-sm text-green-600">↑ 12% vs mes anterior</p>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Ganancia estimada</p>
        <h2 class="mt-3 text-3xl font-bold">$410,00</h2>
        <p class="mt-2 text-sm text-green-600">↑ 8% vs mes anterior</p>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Inventario disponible</p>
        <h2 class="mt-3 text-3xl font-bold">186</h2>
        <p class="mt-2 text-sm text-[#E46F8A]">Ver inventario</p>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Capital invertido</p>
        <h2 class="mt-3 text-3xl font-bold">$2.850,00</h2>
        <p class="mt-2 text-sm text-[#E46F8A]">Ver detalle</p>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Productos bajos</p>
        <h2 class="mt-3 text-3xl font-bold">14</h2>
        <p class="mt-2 text-sm text-[#E46F8A]">Ver alertas</p>
    </div>

</section>

<section class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <div class="mb-6 flex items-center justify-between">
            <h3 class="font-bold">Ventas por día (USD)</h3>
            <span class="text-sm text-gray-400">Últimos 30 días</span>
        </div>

        <div class="flex h-64 items-end gap-2 border-b border-l border-gray-100 px-2 pb-2">
            <div class="h-[30%] flex-1 rounded-t-lg bg-[#F3C8D1]"></div>
            <div class="h-[42%] flex-1 rounded-t-lg bg-[#F3C8D1]"></div>
            <div class="h-[38%] flex-1 rounded-t-lg bg-[#F3C8D1]"></div>
            <div class="h-[70%] flex-1 rounded-t-lg bg-[#E46F8A]"></div>
            <div class="h-[50%] flex-1 rounded-t-lg bg-[#F3C8D1]"></div>
            <div class="h-[45%] flex-1 rounded-t-lg bg-[#F3C8D1]"></div>
            <div class="h-[62%] flex-1 rounded-t-lg bg-[#E46F8A]"></div>
            <div class="h-[78%] flex-1 rounded-t-lg bg-[#E46F8A]"></div>
            <div class="h-[55%] flex-1 rounded-t-lg bg-[#F3C8D1]"></div>
            <div class="h-[88%] flex-1 rounded-t-lg bg-[#E46F8A]"></div>
        </div>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <h3 class="font-bold">Ventas por categoría</h3>

        <div class="mt-8 flex items-center justify-center">
            <div class="relative h-44 w-44 rounded-full bg-[conic-gradient(#E46F8A_0_60%,#9DBBB2_60%_85%,#F2B86D_85%_100%)]">
                <div class="absolute inset-10 rounded-full bg-white"></div>
            </div>
        </div>

        <div class="mt-8 space-y-3 text-sm">
            <div class="flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded-full bg-[#E46F8A]"></span>
                    Maquillaje
                </span>
                <strong>60%</strong>
            </div>

            <div class="flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded-full bg-[#9DBBB2]"></span>
                    Cuidado facial
                </span>
                <strong>25%</strong>
            </div>

            <div class="flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded-full bg-[#F2B86D]"></span>
                    Cuidado labial
                </span>
                <strong>15%</strong>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <h3 class="font-bold">Producto más vendido</h3>

        <div class="mt-8 flex items-center gap-6">
            <div class="flex h-28 w-28 items-center justify-center rounded-2xl bg-[#F8F5F2] text-5xl">
                💄
            </div>

            <div>
                <h4 class="text-2xl font-bold">Labial Mate</h4>
                <p class="mt-1 text-gray-500">Valmy · Tono 12</p>
                <p class="mt-5 text-sm text-[#E46F8A]">Ver detalle</p>
            </div>
        </div>

        <div class="mt-8 rounded-2xl bg-[#F8F5F2] p-5">
            <p class="text-sm text-gray-500">Unidades vendidas</p>
            <h5 class="mt-2 text-3xl font-bold">48</h5>
        </div>
    </div>

</section>

<section class="mt-6 rounded-2xl border border-black/5 bg-white p-6 shadow-sm">

    <div class="mb-5 flex items-center justify-between">
        <h3 class="font-bold">Últimas ventas</h3>
        <a href="#" class="text-sm font-semibold text-[#E46F8A]">Ver todas</a>
    </div>

    <div class="overflow-hidden rounded-xl border border-black/5">
        <table class="w-full text-left text-sm">
            <thead class="bg-[#F8F5F2] text-gray-500">
                <tr>
                    <th class="px-5 py-4">Fecha</th>
                    <th class="px-5 py-4">Producto</th>
                    <th class="px-5 py-4">Marca</th>
                    <th class="px-5 py-4">Tono</th>
                    <th class="px-5 py-4">Cantidad</th>
                    <th class="px-5 py-4">Total USD</th>
                    <th class="px-5 py-4">Total Bs</th>
                    <th class="px-5 py-4">Ganancia</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-black/5">
                <tr>
                    <td class="px-5 py-4">21/05/2024</td>
                    <td class="px-5 py-4 font-medium">Base líquida</td>
                    <td class="px-5 py-4">Vogue</td>
                    <td class="px-5 py-4">Beige claro</td>
                    <td class="px-5 py-4">2</td>
                    <td class="px-5 py-4">$16,00</td>
                    <td class="px-5 py-4">Bs. 603,20</td>
                    <td class="px-5 py-4 font-semibold text-green-600">$8,00</td>
                </tr>

                <tr>
                    <td class="px-5 py-4">21/05/2024</td>
                    <td class="px-5 py-4 font-medium">Labial mate</td>
                    <td class="px-5 py-4">Valmy</td>
                    <td class="px-5 py-4">Rojo intenso</td>
                    <td class="px-5 py-4">1</td>
                    <td class="px-5 py-4">$4,50</td>
                    <td class="px-5 py-4">Bs. 169,43</td>
                    <td class="px-5 py-4 font-semibold text-green-600">$2,50</td>
                </tr>

                <tr>
                    <td class="px-5 py-4">20/05/2024</td>
                    <td class="px-5 py-4 font-medium">Máscara de pestañas</td>
                    <td class="px-5 py-4">Maybelline</td>
                    <td class="px-5 py-4">Negro</td>
                    <td class="px-5 py-4">1</td>
                    <td class="px-5 py-4">$7,00</td>
                    <td class="px-5 py-4">Bs. 263,55</td>
                    <td class="px-5 py-4 font-semibold text-green-600">$3,50</td>
                </tr>
            </tbody>
        </table>
    </div>

</section>

@endsection