@extends('layouts.app', [
'title' => 'Reportes | Susan Brigitt Studio',
'pageTitle' => 'Reportes'
])

@section('content')

<div class="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-center">
    <div>
        <p class="text-sm text-gray-500">
            Consulta indicadores financieros, movimiento de inventario, rentabilidad y desempeño comercial.
        </p>
    </div>

    <div class="flex gap-3">
        <button class="inline-flex items-center justify-center rounded-xl border border-[#E46F8A] px-5 py-3 text-sm font-semibold text-[#E46F8A] transition hover:bg-[#FFF0F4]">
            Exportar Excel
        </button>

        <button class="inline-flex items-center justify-center rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C]">
            Exportar PDF
        </button>
    </div>
</div>

<section class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">

    <div class="grid gap-4 md:grid-cols-5">

        <select class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
            <option>Este mes</option>
            <option>Mes anterior</option>
            <option>Últimos 3 meses</option>
            <option>Este año</option>
        </select>

        <input type="date"
            class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">

        <input type="date"
            class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">

        <select class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
            <option>Todas las categorías</option>
            <option>Maquillaje</option>
            <option>Cuidado facial</option>
            <option>Cuidado labial</option>
        </select>

        <button class="rounded-xl border border-[#E46F8A] px-5 py-3 text-sm font-semibold text-[#E46F8A] transition hover:bg-[#FFF0F4]">
            Aplicar filtros
        </button>

    </div>

</section>

<section class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Ventas del período</p>
        <h2 class="mt-3 text-3xl font-bold">$1.240,00</h2>
        <p class="mt-2 text-sm text-green-600">↑ 12% vs período anterior</p>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Ganancia estimada</p>
        <h2 class="mt-3 text-3xl font-bold text-green-600">$410,00</h2>
        <p class="mt-2 text-sm text-green-600">↑ 8% vs período anterior</p>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Compras del período</p>
        <h2 class="mt-3 text-3xl font-bold">$820,00</h2>
        <p class="mt-2 text-sm text-gray-400">134 unidades ingresadas</p>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Valor del inventario</p>
        <h2 class="mt-3 text-3xl font-bold">$2.850,00</h2>
        <p class="mt-2 text-sm text-gray-400">186 unidades disponibles</p>
    </div>

</section>

<section class="mt-6 grid grid-cols-1 gap-6 2xl:grid-cols-[minmax(0,1fr)_360px]">

    <div class="space-y-6">

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">

            <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold">Ventas por mes</h2>
                        <p class="mt-1 text-sm text-gray-500">Evolución de ingresos en USD.</p>
                    </div>
                </div>

                <div class="flex h-72 items-end gap-3 border-b border-l border-gray-100 px-3 pb-3">
                    <div class="h-[35%] flex-1 rounded-t-lg bg-[#F3C8D1]"></div>
                    <div class="h-[48%] flex-1 rounded-t-lg bg-[#F3C8D1]"></div>
                    <div class="h-[62%] flex-1 rounded-t-lg bg-[#E46F8A]"></div>
                    <div class="h-[52%] flex-1 rounded-t-lg bg-[#F3C8D1]"></div>
                    <div class="h-[77%] flex-1 rounded-t-lg bg-[#E46F8A]"></div>
                    <div class="h-[68%] flex-1 rounded-t-lg bg-[#E46F8A]"></div>
                </div>

                <div class="mt-4 grid grid-cols-6 text-center text-xs text-gray-400">
                    <span>Ene</span>
                    <span>Feb</span>
                    <span>Mar</span>
                    <span>Abr</span>
                    <span>May</span>
                    <span>Jun</span>
                </div>
            </div>

            <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
                <div class="mb-6">
                    <h2 class="text-lg font-bold">Ganancia por mes</h2>
                    <p class="mt-1 text-sm text-gray-500">Rentabilidad estimada por período.</p>
                </div>

                <div class="space-y-5">

                    <div>
                        <div class="mb-2 flex justify-between text-sm">
                            <span>Enero</span>
                            <strong>$180</strong>
                        </div>
                        <div class="h-3 rounded-full bg-[#F8F5F2]">
                            <div class="h-3 w-[38%] rounded-full bg-[#F3C8D1]"></div>
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 flex justify-between text-sm">
                            <span>Febrero</span>
                            <strong>$240</strong>
                        </div>
                        <div class="h-3 rounded-full bg-[#F8F5F2]">
                            <div class="h-3 w-[50%] rounded-full bg-[#F3C8D1]"></div>
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 flex justify-between text-sm">
                            <span>Marzo</span>
                            <strong>$360</strong>
                        </div>
                        <div class="h-3 rounded-full bg-[#F8F5F2]">
                            <div class="h-3 w-[70%] rounded-full bg-[#E46F8A]"></div>
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 flex justify-between text-sm">
                            <span>Abril</span>
                            <strong>$310</strong>
                        </div>
                        <div class="h-3 rounded-full bg-[#F8F5F2]">
                            <div class="h-3 w-[62%] rounded-full bg-[#E46F8A]"></div>
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 flex justify-between text-sm">
                            <span>Mayo</span>
                            <strong>$410</strong>
                        </div>
                        <div class="h-3 rounded-full bg-[#F8F5F2]">
                            <div class="h-3 w-[82%] rounded-full bg-[#E46F8A]"></div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">

            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold">Resumen financiero</h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Estado general del movimiento comercial.
                    </p>
                </div>

                <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">
                    Saludable
                </span>
            </div>

            <div class="max-w-full overflow-x-auto rounded-xl border border-black/5">
                <table class="min-w-[860px] w-full text-left text-sm">
                    <thead class="bg-[#F8F5F2] text-gray-500">
                        <tr>
                            <th class="whitespace-nowrap px-5 py-4">Concepto</th>
                            <th class="whitespace-nowrap px-5 py-4">USD</th>
                            <th class="whitespace-nowrap px-5 py-4">Bs</th>
                            <th class="whitespace-nowrap px-5 py-4">Participación</th>
                            <th class="whitespace-nowrap px-5 py-4">Observación</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-black/5">
                        <tr>
                            <td class="px-5 py-4 font-medium">Ventas</td>
                            <td class="px-5 py-4 font-semibold">$1.240,00</td>
                            <td class="px-5 py-4">Bs. 46.686,00</td>
                            <td class="px-5 py-4">100%</td>
                            <td class="px-5 py-4 text-gray-600">Ingresos brutos del período.</td>
                        </tr>

                        <tr>
                            <td class="px-5 py-4 font-medium">Costo de mercancía vendida</td>
                            <td class="px-5 py-4 font-semibold">$830,00</td>
                            <td class="px-5 py-4">Bs. 31.249,50</td>
                            <td class="px-5 py-4">66,9%</td>
                            <td class="px-5 py-4 text-gray-600">Costo estimado de productos vendidos.</td>
                        </tr>

                        <tr>
                            <td class="px-5 py-4 font-medium">Ganancia estimada</td>
                            <td class="px-5 py-4 font-semibold text-green-600">$410,00</td>
                            <td class="px-5 py-4">Bs. 15.436,50</td>
                            <td class="px-5 py-4">33,1%</td>
                            <td class="px-5 py-4 text-gray-600">Margen operativo bruto.</td>
                        </tr>

                        <tr>
                            <td class="px-5 py-4 font-medium">Inventario disponible</td>
                            <td class="px-5 py-4 font-semibold">$2.850,00</td>
                            <td class="px-5 py-4">Bs. 107.302,50</td>
                            <td class="px-5 py-4">—</td>
                            <td class="px-5 py-4 text-gray-600">Valor de mercancía en existencia.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <aside class="space-y-6">

        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold">Productos más vendidos</h2>

            <div class="mt-6 space-y-5">

                <div>
                    <div class="mb-2 flex justify-between text-sm">
                        <span>Labial mate · Valmy</span>
                        <strong>48 uds</strong>
                    </div>
                    <div class="h-3 rounded-full bg-[#F8F5F2]">
                        <div class="h-3 w-[90%] rounded-full bg-[#E46F8A]"></div>
                    </div>
                </div>

                <div>
                    <div class="mb-2 flex justify-between text-sm">
                        <span>Base líquida · Vogue</span>
                        <strong>32 uds</strong>
                    </div>
                    <div class="h-3 rounded-full bg-[#F8F5F2]">
                        <div class="h-3 w-[65%] rounded-full bg-[#F3C8D1]"></div>
                    </div>
                </div>

                <div>
                    <div class="mb-2 flex justify-between text-sm">
                        <span>Corrector líquido</span>
                        <strong>19 uds</strong>
                    </div>
                    <div class="h-3 rounded-full bg-[#F8F5F2]">
                        <div class="h-3 w-[42%] rounded-full bg-[#9DBBB2]"></div>
                    </div>
                </div>

            </div>
        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold">Alertas importantes</h2>

            <div class="mt-6 space-y-4">
                <div class="rounded-xl bg-yellow-50 p-4">
                    <p class="font-semibold text-yellow-800">Stock bajo</p>
                    <p class="mt-1 text-sm text-yellow-700">14 productos requieren reposición.</p>
                </div>

                <div class="rounded-xl bg-red-50 p-4">
                    <p class="font-semibold text-red-800">Productos agotados</p>
                    <p class="mt-1 text-sm text-red-700">6 productos no tienen disponibilidad.</p>
                </div>

                <div class="rounded-xl bg-blue-50 p-4">
                    <p class="font-semibold text-blue-800">Tasa pendiente</p>
                    <p class="mt-1 text-sm text-blue-700">Verifica si la tasa de hoy fue actualizada.</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold">Indicadores clave</h2>

            <div class="mt-6 space-y-4">
                <div class="flex items-center justify-between rounded-xl bg-[#F8F5F2] p-4">
                    <span class="text-sm text-gray-600">Margen estimado</span>
                    <strong>33,1%</strong>
                </div>

                <div class="flex items-center justify-between rounded-xl bg-[#F8F5F2] p-4">
                    <span class="text-sm text-gray-600">Rotación</span>
                    <strong>Media</strong>
                </div>

                <div class="flex items-center justify-between rounded-xl bg-[#F8F5F2] p-4">
                    <span class="text-sm text-gray-600">Ticket promedio</span>
                    <strong>$14,42</strong>
                </div>
            </div>
        </div>

    </aside>

</section>

@endsection