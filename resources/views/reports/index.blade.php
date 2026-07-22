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

        <p class="mt-1 text-xs text-gray-400">
            Período analizado:
            {{ $dateFrom->format('d/m/Y') }}
            al
            {{ $dateTo->format('d/m/Y') }}.
        </p>
    </div>

    <div class="flex flex-col gap-3 sm:flex-row">
        <button
            type="button"
            disabled
            title="La exportación a Excel se incorporará posteriormente."
            class="cursor-not-allowed rounded-xl border border-zinc-200 px-5 py-3 text-sm font-semibold text-zinc-400">
            Exportar Excel
        </button>

        <button
            type="button"
            disabled
            title="La exportación a PDF se incorporará posteriormente."
            class="cursor-not-allowed rounded-xl bg-zinc-200 px-5 py-3 text-sm font-semibold text-zinc-500">
            Exportar PDF
        </button>
    </div>
</div>

<section class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
    <form
        action="{{ route('reports.index') }}"
        method="GET"
        class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
        <select
            name="period"
            class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
            <option
                value="this_month"
                @selected($period==='this_month' )>
                Este mes
            </option>

            <option
                value="previous_month"
                @selected($period==='previous_month' )>
                Mes anterior
            </option>

            <option
                value="last_3_months"
                @selected($period==='last_3_months' )>
                Últimos 3 meses
            </option>

            <option
                value="this_year"
                @selected($period==='this_year' )>
                Este año
            </option>

            <option
                value="custom"
                @selected($period==='custom' )>
                Rango personalizado
            </option>
        </select>

        <input
            name="date_from"
            type="date"
            value="{{ request('date_from') }}"
            class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">

        <input
            name="date_to"
            type="date"
            value="{{ request('date_to') }}"
            class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">

        <select
            name="category_id"
            class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
            <option value="">
                Todas las categorías
            </option>

            @foreach ($categories as $category)
            <option
                value="{{ $category->id }}"
                @selected($categoryId===$category->id)
                >
                {{ $category->name }}
                {{ $category->is_active ? '' : '(Inactiva)' }}
            </option>
            @endforeach
        </select>

        <button
            type="submit"
            class="rounded-xl border border-[#E46F8A] px-5 py-3 text-sm font-semibold text-[#E46F8A] transition hover:bg-[#FFF0F4]">
            Aplicar filtros
        </button>

        <div class="md:col-span-2 xl:col-span-5 xl:text-right">
            <a
                href="{{ route('reports.index') }}"
                class="text-sm font-semibold text-zinc-500 hover:text-[#E46F8A]">
                Limpiar filtros
            </a>
        </div>
    </form>
</section>

<section class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">
            Ventas del período
        </p>

        <h2 class="mt-3 text-3xl font-bold">
            ${{ number_format($salesTotal, 2, ',', '.') }}
        </h2>

        @if (is_null($salesChange))
        <p class="mt-2 text-sm text-gray-400">
            Sin base comparativa
        </p>
        @else
        <p class="mt-2 text-sm {{ $salesChange >= 0 ? 'text-green-600' : 'text-red-600' }}">
            {{ $salesChange >= 0 ? '↑' : '↓' }}
            {{ number_format(abs($salesChange), 1, ',', '.') }}%
            frente al período anterior
        </p>
        @endif
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">
            Ganancia estimada
        </p>

        <h2 class="mt-3 text-3xl font-bold text-green-600">
            ${{ number_format($profitTotal, 2, ',', '.') }}
        </h2>

        @if (is_null($profitChange))
        <p class="mt-2 text-sm text-gray-400">
            Sin base comparativa
        </p>
        @else
        <p class="mt-2 text-sm {{ $profitChange >= 0 ? 'text-green-600' : 'text-red-600' }}">
            {{ $profitChange >= 0 ? '↑' : '↓' }}
            {{ number_format(abs($profitChange), 1, ',', '.') }}%
            frente al período anterior
        </p>
        @endif
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">
            Compras del período
        </p>

        <h2 class="mt-3 text-3xl font-bold">
            ${{ number_format($purchasesTotal, 2, ',', '.') }}
        </h2>

        <p class="mt-2 text-sm text-gray-400">
            {{ $purchasedUnits }}
            {{ $purchasedUnits === 1 ? 'unidad ingresada' : 'unidades ingresadas' }}
        </p>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">
            Valor del inventario
        </p>

        <h2 class="mt-3 text-3xl font-bold">
            ${{ number_format($inventoryValue, 2, ',', '.') }}
        </h2>

        <p class="mt-2 text-sm text-gray-400">
            {{ $availableUnits }}
            {{ $availableUnits === 1 ? 'unidad disponible' : 'unidades disponibles' }}
        </p>
    </div>
</section>

<section class="mt-6 grid grid-cols-1 gap-6 2xl:grid-cols-[minmax(0,1fr)_360px]">
    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
                <div class="mb-6">
                    <h2 class="text-lg font-bold">
                        Ventas por mes
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Evolución de ingresos en USD durante los últimos seis meses.
                    </p>
                </div>

                @if ($monthlyMaxSales > 0)
                <div class="flex h-72 items-end gap-3 border-b border-l border-gray-100 px-3 pb-3">
                    @foreach ($monthlySeries as $month)
                    @php
                    $height = $monthlyMaxSales > 0
                    ? max(
                    4,
                    ($month['sales'] / $monthlyMaxSales) * 100
                    )
                    : 0;
                    @endphp

                    <div class="flex h-full flex-1 items-end">
                        <div
                            class="w-full rounded-t-lg bg-[#E46F8A]"
                            style="height: {{ $height }}%"
                            title="{{ $month['name'] }}: ${{ number_format($month['sales'], 2, ',', '.') }}"></div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-4 grid grid-cols-6 text-center text-xs text-gray-400">
                    @foreach ($monthlySeries as $month)
                    <span>
                        {{ $month['short_name'] }}
                    </span>
                    @endforeach
                </div>
                @else
                <div class="flex h-72 items-center justify-center rounded-xl bg-[#F8F5F2] px-6 text-center text-sm text-gray-500">
                    No existen ventas para representar en el gráfico.
                </div>
                @endif
            </div>

            <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
                <div class="mb-6">
                    <h2 class="text-lg font-bold">
                        Ganancia por mes
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Rentabilidad estimada durante los últimos seis meses.
                    </p>
                </div>

                @if ($monthlyMaxProfit > 0)
                <div class="space-y-5">
                    @foreach ($monthlySeries as $month)
                    @php
                    $width = $monthlyMaxProfit > 0
                    ? ($month['profit'] / $monthlyMaxProfit) * 100
                    : 0;
                    @endphp

                    <div>
                        <div class="mb-2 flex justify-between gap-4 text-sm">
                            <span>
                                {{ $month['name'] }}
                            </span>

                            <strong>
                                ${{ number_format($month['profit'], 2, ',', '.') }}
                            </strong>
                        </div>

                        <div class="h-3 rounded-full bg-[#F8F5F2]">
                            <div
                                class="h-3 rounded-full bg-[#E46F8A]"
                                style="width: {{ $width }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="flex h-72 items-center justify-center rounded-xl bg-[#F8F5F2] px-6 text-center text-sm text-gray-500">
                    No existen ganancias para representar en el gráfico.
                </div>
                @endif
            </div>
        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <div class="mb-6 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                <div>
                    <h2 class="text-lg font-bold">
                        Resumen financiero
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Estado general del movimiento comercial del período.
                    </p>
                </div>

                <span class="w-fit rounded-full px-3 py-1 text-xs font-semibold
                    {{ $financialStatus === 'Positivo'
                        ? 'bg-green-50 text-green-700'
                        : ($financialStatus === 'Atención'
                            ? 'bg-red-50 text-red-700'
                            : 'bg-zinc-100 text-zinc-500') }}">
                    {{ $financialStatus }}
                </span>
            </div>

            <div class="max-w-full overflow-x-auto rounded-xl border border-black/5">
                <table class="w-full min-w-215 text-left text-sm">
                    <thead class="bg-[#F8F5F2] text-gray-500">
                        <tr>
                            <th class="whitespace-nowrap px-5 py-4">
                                Concepto
                            </th>

                            <th class="whitespace-nowrap px-5 py-4">
                                USD
                            </th>

                            <th class="whitespace-nowrap px-5 py-4">
                                Bs
                            </th>

                            <th class="whitespace-nowrap px-5 py-4">
                                Participación
                            </th>

                            <th class="whitespace-nowrap px-5 py-4">
                                Observación
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-black/5">
                        <tr>
                            <td class="px-5 py-4 font-medium">
                                Ventas
                            </td>

                            <td class="px-5 py-4 font-semibold">
                                ${{ number_format($salesTotal, 2, ',', '.') }}
                            </td>

                            <td class="px-5 py-4">
                                Bs. {{ number_format($salesBs, 2, ',', '.') }}
                            </td>

                            <td class="px-5 py-4">
                                {{ $salesTotal > 0 ? '100%' : '—' }}
                            </td>

                            <td class="px-5 py-4 text-gray-600">
                                Ingresos brutos del período.
                            </td>
                        </tr>

                        <tr>
                            <td class="px-5 py-4 font-medium">
                                Costo de mercancía vendida
                            </td>

                            <td class="px-5 py-4 font-semibold">
                                ${{ number_format($costOfGoodsSold, 2, ',', '.') }}
                            </td>

                            <td class="px-5 py-4">
                                Bs. {{ number_format($costOfGoodsSoldBs, 2, ',', '.') }}
                            </td>

                            <td class="px-5 py-4">
                                {{ $salesTotal > 0
                                    ? number_format(
                                        ($costOfGoodsSold / $salesTotal) * 100,
                                        1,
                                        ',',
                                        '.'
                                    ) . '%'
                                    : '—' }}
                            </td>

                            <td class="px-5 py-4 text-gray-600">
                                Costo registrado de los productos vendidos.
                            </td>
                        </tr>

                        <tr>
                            <td class="px-5 py-4 font-medium">
                                Ganancia estimada
                            </td>

                            <td class="px-5 py-4 font-semibold text-green-600">
                                ${{ number_format($profitTotal, 2, ',', '.') }}
                            </td>

                            <td class="px-5 py-4">
                                Bs. {{ number_format($profitBs, 2, ',', '.') }}
                            </td>

                            <td class="px-5 py-4">
                                {{ $salesTotal > 0
                                    ? number_format(
                                        $profitMargin,
                                        1,
                                        ',',
                                        '.'
                                    ) . '%'
                                    : '—' }}
                            </td>

                            <td class="px-5 py-4 text-gray-600">
                                Margen bruto estimado.
                            </td>
                        </tr>

                        <tr>
                            <td class="px-5 py-4 font-medium">
                                Compras
                            </td>

                            <td class="px-5 py-4 font-semibold">
                                ${{ number_format($purchasesTotal, 2, ',', '.') }}
                            </td>

                            <td class="px-5 py-4">
                                Bs. {{ number_format($purchasesBs, 2, ',', '.') }}
                            </td>

                            <td class="px-5 py-4">
                                —
                            </td>

                            <td class="px-5 py-4 text-gray-600">
                                Mercancía ingresada durante el período.
                            </td>
                        </tr>

                        <tr>
                            <td class="px-5 py-4 font-medium">
                                Inventario disponible
                            </td>

                            <td class="px-5 py-4 font-semibold">
                                ${{ number_format($inventoryValue, 2, ',', '.') }}
                            </td>

                            <td class="px-5 py-4">
                                @if (is_null($inventoryValueBs))
                                —
                                @else
                                Bs. {{ number_format($inventoryValueBs, 2, ',', '.') }}
                                @endif
                            </td>

                            <td class="px-5 py-4">
                                —
                            </td>

                            <td class="px-5 py-4 text-gray-600">
                                Valor actual al costo de compra.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <aside class="space-y-6">
        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold">
                Productos más vendidos
            </h2>

            <div class="mt-6 space-y-5">
                @forelse ($topProducts as $product)
                @php
                $width = $maxTopProductUnits > 0
                ? ((int) $product->units_sold /
                $maxTopProductUnits) * 100
                : 0;
                @endphp

                <div>
                    <div class="mb-2 flex justify-between gap-4 text-sm">
                        <span>
                            {{ $product->name }}

                            @if ($product->brand_name)
                            · {{ $product->brand_name }}
                            @endif
                        </span>

                        <strong class="whitespace-nowrap">
                            {{ $product->units_sold }} uds
                        </strong>
                    </div>

                    <div class="h-3 rounded-full bg-[#F8F5F2]">
                        <div
                            class="h-3 rounded-full bg-[#E46F8A]"
                            style="width: {{ $width }}%"></div>
                    </div>
                </div>
                @empty
                <div class="rounded-xl bg-[#F8F5F2] p-5 text-sm text-gray-500">
                    No existen productos vendidos en el período.
                </div>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold">
                Alertas importantes
            </h2>

            <div class="mt-6 space-y-4">
                <div class="rounded-xl bg-yellow-50 p-4">
                    <p class="font-semibold text-yellow-800">
                        Stock bajo
                    </p>

                    <p class="mt-1 text-sm text-yellow-700">
                        {{ $lowStockCount }}
                        {{ $lowStockCount === 1
                            ? 'producto requiere reposición.'
                            : 'productos requieren reposición.' }}
                    </p>
                </div>

                <div class="rounded-xl bg-red-50 p-4">
                    <p class="font-semibold text-red-800">
                        Productos agotados
                    </p>

                    <p class="mt-1 text-sm text-red-700">
                        {{ $outOfStockCount }}
                        {{ $outOfStockCount === 1
                            ? 'producto no tiene disponibilidad.'
                            : 'productos no tienen disponibilidad.' }}
                    </p>
                </div>

                <div class="rounded-xl bg-blue-50 p-4">
                    <p class="font-semibold text-blue-800">
                        Tasa del día
                    </p>

                    <p class="mt-1 text-sm text-blue-700">
                        {{ $todayRateExists
                            ? 'Existe al menos una tasa registrada hoy.'
                            : 'Todavía no se ha registrado una tasa para hoy.' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold">
                Indicadores clave
            </h2>

            <div class="mt-6 space-y-4">
                <div class="flex items-center justify-between gap-4 rounded-xl bg-[#F8F5F2] p-4">
                    <span class="text-sm text-gray-600">
                        Margen estimado
                    </span>

                    <strong>
                        {{ $salesTotal > 0
                            ? number_format(
                                $profitMargin,
                                1,
                                ',',
                                '.'
                            ) . '%'
                            : '—' }}
                    </strong>
                </div>

                <div class="flex items-center justify-between gap-4 rounded-xl bg-[#F8F5F2] p-4">
                    <span class="text-sm text-gray-600">
                        Rotación
                    </span>

                    <strong>
                        {{ $rotationEstimate }}
                    </strong>
                </div>

                <div class="flex items-center justify-between gap-4 rounded-xl bg-[#F8F5F2] p-4">
                    <span class="text-sm text-gray-600">
                        Ticket promedio
                    </span>

                    <strong>
                        ${{ number_format($ticketAverage, 2, ',', '.') }}
                    </strong>
                </div>

                <div class="flex items-center justify-between gap-4 rounded-xl bg-[#F8F5F2] p-4">
                    <span class="text-sm text-gray-600">
                        Operaciones de venta
                    </span>

                    <strong>
                        {{ $salesCount }}
                    </strong>
                </div>

                <div class="flex items-center justify-between gap-4 rounded-xl bg-[#F8F5F2] p-4">
                    <span class="text-sm text-gray-600">
                        Productos registrados
                    </span>

                    <strong>
                        {{ $totalProducts }}
                    </strong>
                </div>
            </div>
        </div>
    </aside>
</section>

@endsection