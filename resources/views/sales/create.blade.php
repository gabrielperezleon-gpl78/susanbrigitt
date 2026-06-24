@extends('layouts.app', [
'title' => 'Registrar venta | Susan Brigitt Studio',
'pageTitle' => 'Registrar venta'
])

@section('content')

<div class="mb-6">
    <a href="{{ route('sales.index') }}" class="text-sm font-semibold text-[#E46F8A]">
        ← Volver a ventas
    </a>
    <p class="mt-2 text-sm text-gray-500">
        Registra la salida de mercancía, calcula el ingreso, la ganancia y el stock resultante.
    </p>
</div>

<form class="space-y-6">

    <section class="rounded-2xl border border-black/5 bg-white shadow-sm">

        <div class="border-b border-black/5 px-6 py-5">
            <h2 class="text-lg font-bold">Información de la venta</h2>
            <p class="mt-1 text-sm text-gray-500">
                Datos principales de producto, cantidad vendida, precio y cliente.
            </p>
        </div>

        <div class="grid gap-5 p-6 md:grid-cols-2 xl:grid-cols-3">

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Fecha de venta <span class="text-[#E46F8A]">*</span>
                </label>
                <input type="date" value="2024-05-21"
                    class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Producto <span class="text-[#E46F8A]">*</span>
                </label>
                <select class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                    <option>Base líquida · Vogue · Beige claro</option>
                    <option>Labial mate · Valmy · Rojo intenso</option>
                    <option>Máscara de pestañas · Maybelline · Negro</option>
                </select>
                <p class="mt-2 text-xs text-gray-400">Stock actual seleccionado: 17 unidades.</p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Cliente
                </label>
                <input type="text" placeholder="Cliente ocasional"
                    class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Cantidad vendida <span class="text-[#E46F8A]">*</span>
                </label>
                <input type="number" value="2"
                    class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Precio unitario USD <span class="text-[#E46F8A]">*</span>
                </label>
                <input type="text" value="$8,00"
                    class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Total venta USD
                </label>
                <input type="text" value="$16,00" readonly
                    class="w-full rounded-xl border border-black/10 bg-[#F8F5F2] px-4 py-3 text-sm font-semibold outline-none">
            </div>

        </div>

    </section>

    <section class="rounded-2xl border border-black/5 bg-white shadow-sm">

        <div class="border-b border-black/5 px-6 py-5">
            <h2 class="text-lg font-bold">Pago y tasa de cambio</h2>
            <p class="mt-1 text-sm text-gray-500">
                Registra la tasa aplicada, forma de pago y equivalente en bolívares.
            </p>
        </div>

        <div class="grid gap-5 p-6 md:grid-cols-2 xl:grid-cols-4">

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Fuente de tasa <span class="text-[#E46F8A]">*</span>
                </label>
                <select class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                    <option>Binance</option>
                    <option>BCV</option>
                    <option>Manual</option>
                </select>
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Tasa aplicada <span class="text-[#E46F8A]">*</span>
                </label>
                <input type="text" value="37,65"
                    class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Total Bs
                </label>
                <input type="text" value="Bs. 602,40" readonly
                    class="w-full rounded-xl border border-black/10 bg-[#F8F5F2] px-4 py-3 text-sm font-semibold outline-none">
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Forma de pago
                </label>
                <select class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                    <option>Pago móvil</option>
                    <option>Transferencia Bs</option>
                    <option>Efectivo USD</option>
                    <option>Binance</option>
                    <option>Zelle</option>
                    <option>Pago mixto</option>
                </select>
            </div>

        </div>

    </section>

    <section class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">

        <h2 class="text-lg font-bold">Resumen de venta</h2>

        <div class="mt-6 grid gap-4 md:grid-cols-5">

            <div class="rounded-2xl bg-[#F8F5F2] p-5 text-center">
                <p class="text-sm text-gray-500">Unidades vendidas</p>
                <h3 class="mt-2 text-2xl font-bold">2</h3>
            </div>

            <div class="rounded-2xl bg-[#F8F5F2] p-5 text-center">
                <p class="text-sm text-gray-500">Total USD</p>
                <h3 class="mt-2 text-2xl font-bold">$16,00</h3>
            </div>

            <div class="rounded-2xl bg-[#F8F5F2] p-5 text-center">
                <p class="text-sm text-gray-500">Total Bs</p>
                <h3 class="mt-2 text-2xl font-bold">Bs. 602,40</h3>
            </div>

            <div class="rounded-2xl bg-[#ECFDF3] p-5 text-center">
                <p class="text-sm text-gray-500">Ganancia</p>
                <h3 class="mt-2 text-2xl font-bold text-green-600">$7,00</h3>
            </div>

            <div class="rounded-2xl bg-[#FFF0F4] p-5 text-center">
                <p class="text-sm text-gray-500">Stock resultante</p>
                <h3 class="mt-2 text-2xl font-bold text-[#E46F8A]">15</h3>
            </div>

        </div>

    </section>

    <section class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">

        <h2 class="text-lg font-bold">Observaciones</h2>

        <textarea rows="4"
            placeholder="Agrega notas sobre la venta, cliente, forma de pago o cualquier detalle relevante..."
            class="mt-5 w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"></textarea>

    </section>

    <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm">
        <div class="grid gap-4 md:grid-cols-3">
            <a href="{{ route('sales.index') }}"
                class="rounded-xl border border-black/10 px-5 py-3 text-center text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                Cancelar
            </a>

            <button type="button"
                class="rounded-xl border border-[#E46F8A] px-5 py-3 text-sm font-semibold text-[#E46F8A] transition hover:bg-[#FFF0F4]">
                Guardar borrador
            </button>

            <button type="button"
                class="rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C]">
                Registrar venta
            </button>
        </div>
    </div>

</form>

@endsection