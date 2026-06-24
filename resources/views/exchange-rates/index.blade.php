@extends('layouts.app', [
'title' => 'Tasas de cambio | Susan Brigitt Studio',
'pageTitle' => 'Tasas de cambio'
])

@section('content')

<div class="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-center">
    <div>
        <p class="text-sm text-gray-500">
            Registra y consulta las tasas utilizadas para convertir operaciones entre dólares y bolívares.
        </p>
    </div>

    <a href="{{ route('exchange-rates.create') }}"
        class="inline-flex items-center justify-center rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C]">
        + Registrar tasa
    </a>
</div>

<section class="grid grid-cols-1 gap-5 md:grid-cols-4">

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Tasa BCV actual</p>
        <h2 class="mt-3 text-3xl font-bold">36,92</h2>
        <p class="mt-2 text-sm text-gray-400">Bs / USD</p>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Tasa Binance actual</p>
        <h2 class="mt-3 text-3xl font-bold">37,65</h2>
        <p class="mt-2 text-sm text-gray-400">Bs / USD</p>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Tasa usada hoy</p>
        <h2 class="mt-3 text-3xl font-bold text-[#E46F8A]">37,65</h2>
        <p class="mt-2 text-sm text-gray-400">Binance</p>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Registros del mes</p>
        <h2 class="mt-3 text-3xl font-bold">21</h2>
        <p class="mt-2 text-sm text-gray-400">Actualizaciones</p>
    </div>

</section>

<section class="mt-6 grid grid-cols-1 gap-6 2xl:grid-cols-[minmax(0,1fr)_340px]">

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">

        <div class="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div>
                <h2 class="text-lg font-bold">Histórico de tasas</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Consulta las tasas registradas y la fuente usada en cada fecha.
                </p>
            </div>

            <div class="flex gap-3">
                <input type="month"
                    class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">

                <button class="rounded-xl border border-[#E46F8A] px-5 py-3 text-sm font-semibold text-[#E46F8A] transition hover:bg-[#FFF0F4]">
                    Filtrar
                </button>
            </div>
        </div>

        <div class="max-w-full overflow-x-auto rounded-xl border border-black/5">
            <table class="min-w-[860px] w-full text-left text-sm">
                <thead class="bg-[#F8F5F2] text-gray-500">
                    <tr>
                        <th class="whitespace-nowrap px-5 py-4">Fecha</th>
                        <th class="whitespace-nowrap px-5 py-4">BCV</th>
                        <th class="whitespace-nowrap px-5 py-4">Binance</th>
                        <th class="whitespace-nowrap px-5 py-4">Manual</th>
                        <th class="whitespace-nowrap px-5 py-4">Tasa usada</th>
                        <th class="whitespace-nowrap px-5 py-4">Fuente</th>
                        <th class="whitespace-nowrap px-5 py-4">Observación</th>
                        <th class="whitespace-nowrap px-5 py-4">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-black/5">

                    <tr>
                        <td class="whitespace-nowrap px-5 py-4">21/05/2024</td>
                        <td class="px-5 py-4">36,92</td>
                        <td class="px-5 py-4">37,65</td>
                        <td class="px-5 py-4">—</td>
                        <td class="px-5 py-4 font-semibold text-[#E46F8A]">37,65</td>
                        <td class="px-5 py-4">
                            <span class="whitespace-nowrap rounded-full bg-purple-50 px-3 py-1 text-xs font-semibold text-purple-700">
                                Binance
                            </span>
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-600">Compras y ventas del día.</td>
                        <td class="px-5 py-4">
                            <div class="flex gap-2">
                                <button class="rounded-lg border border-black/10 px-3 py-2 text-xs hover:bg-gray-50">Ver</button>
                                <button class="rounded-lg border border-black/10 px-3 py-2 text-xs hover:bg-gray-50">Editar</button>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td class="whitespace-nowrap px-5 py-4">20/05/2024</td>
                        <td class="px-5 py-4">36,80</td>
                        <td class="px-5 py-4">37,40</td>
                        <td class="px-5 py-4">37,50</td>
                        <td class="px-5 py-4 font-semibold text-[#E46F8A]">37,50</td>
                        <td class="px-5 py-4">
                            <span class="whitespace-nowrap rounded-full bg-pink-50 px-3 py-1 text-xs font-semibold text-pink-700">
                                Manual
                            </span>
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-600">Reporte formal.</td>
                        <td class="px-5 py-4">
                            <div class="flex gap-2">
                                <button class="rounded-lg border border-black/10 px-3 py-2 text-xs hover:bg-gray-50">Ver</button>
                                <button class="rounded-lg border border-black/10 px-3 py-2 text-xs hover:bg-gray-50">Editar</button>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td class="whitespace-nowrap px-5 py-4">19/05/2024</td>
                        <td class="px-5 py-4">36,70</td>
                        <td class="px-5 py-4">37,25</td>
                        <td class="px-5 py-4">—</td>
                        <td class="px-5 py-4 font-semibold text-[#E46F8A]">36,70</td>
                        <td class="px-5 py-4">
                            <span class="whitespace-nowrap rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                BCV
                            </span>
                        </td>
                        <td class="px-5 py-4">Usada para reporte formal de operaciones.</td>
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

    </div>

    <aside class="space-y-6">

        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold">Uso recomendado</h2>

            <div class="mt-5 space-y-4 text-sm text-gray-600">
                <p>
                    Registra la tasa del día antes de cargar compras o ventas. Esto permite que cada operación conserve su equivalencia real en bolívares.
                </p>

                <p>
                    La tasa usada puede ser BCV, Binance o manual. La tasa manual permite ajustar casos especiales según la operación comercial.
                </p>
            </div>
        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold">Resumen del mes</h2>

            <div class="mt-6 space-y-4">
                <div class="rounded-xl bg-[#F8F5F2] p-4">
                    <p class="text-sm text-gray-500">Promedio BCV</p>
                    <h3 class="mt-1 text-2xl font-bold">36,71</h3>
                </div>

                <div class="rounded-xl bg-[#F8F5F2] p-4">
                    <p class="text-sm text-gray-500">Promedio Binance</p>
                    <h3 class="mt-1 text-2xl font-bold">37,48</h3>
                </div>

                <div class="rounded-xl bg-[#FFF0F4] p-4">
                    <p class="text-sm text-gray-500">Diferencia promedio</p>
                    <h3 class="mt-1 text-2xl font-bold text-[#E46F8A]">+2,10%</h3>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold">Impacto operativo</h2>

            <div class="mt-6 space-y-4">
                <div class="flex items-center justify-between rounded-xl bg-[#F8F5F2] p-4">
                    <span class="text-sm text-gray-600">Compras con Binance</span>
                    <strong>14</strong>
                </div>

                <div class="flex items-center justify-between rounded-xl bg-[#F8F5F2] p-4">
                    <span class="text-sm text-gray-600">Ventas con BCV</span>
                    <strong>6</strong>
                </div>

                <div class="flex items-center justify-between rounded-xl bg-[#F8F5F2] p-4">
                    <span class="text-sm text-gray-600">Tasas manuales</span>
                    <strong>3</strong>
                </div>
            </div>
        </div>

    </aside>

</section>

@endsection