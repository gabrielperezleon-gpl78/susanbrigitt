@extends('layouts.app', [
'title' => 'Registrar tasa | Susan Brigitt Studio',
'pageTitle' => 'Registrar tasa de cambio'
])

@section('content')

<div class="mb-6">
    <a href="{{ route('exchange-rates.index') }}" class="text-sm font-semibold text-[#E46F8A]">
        ← Volver a tasas
    </a>
    <p class="mt-2 text-sm text-gray-500">
        Registra la tasa de referencia para compras, ventas y reportes financieros.
    </p>
</div>

<form class="space-y-6">

    <section class="rounded-2xl border border-black/5 bg-white shadow-sm">

        <div class="border-b border-black/5 px-6 py-5">
            <h2 class="text-lg font-bold">Datos de la tasa</h2>
            <p class="mt-1 text-sm text-gray-500">
                Define las tasas disponibles y selecciona cuál será utilizada en las operaciones del día.
            </p>
        </div>

        <div class="grid gap-5 p-6 md:grid-cols-2 xl:grid-cols-4">

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Fecha <span class="text-[#E46F8A]">*</span>
                </label>
                <input type="date" value="2024-05-21"
                    class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Tasa BCV
                </label>
                <input type="text" value="36,92"
                    class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                <p class="mt-2 text-xs text-gray-400">Referencia oficial.</p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Tasa Binance
                </label>
                <input type="text" value="37,65"
                    class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                <p class="mt-2 text-xs text-gray-400">Referencia comercial.</p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Tasa manual
                </label>
                <input type="text" placeholder="Opcional"
                    class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                <p class="mt-2 text-xs text-gray-400">Usar solo si requiere ajuste interno.</p>
            </div>

        </div>

    </section>

    <section class="rounded-2xl border border-black/5 bg-white shadow-sm">

        <div class="border-b border-black/5 px-6 py-5">
            <h2 class="text-lg font-bold">Tasa aplicada</h2>
            <p class="mt-1 text-sm text-gray-500">
                Esta será la tasa sugerida al registrar compras y ventas.
            </p>
        </div>

        <div class="grid gap-5 p-6 md:grid-cols-2 xl:grid-cols-3">

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Fuente usada <span class="text-[#E46F8A]">*</span>
                </label>
                <select class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                    <option>Binance</option>
                    <option>BCV</option>
                    <option>Manual</option>
                </select>
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Tasa usada <span class="text-[#E46F8A]">*</span>
                </label>
                <input type="text" value="37,65"
                    class="w-full rounded-xl border border-black/10 bg-[#FFF0F4] px-4 py-3 text-sm font-semibold text-[#E46F8A] outline-none">
                <p class="mt-2 text-xs text-gray-400">Este valor se copiará a operaciones nuevas.</p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Estado
                </label>
                <select class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                    <option>Activa</option>
                    <option>Solo referencia</option>
                    <option>Archivada</option>
                </select>
            </div>

        </div>

    </section>

    <section class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">

        <h2 class="text-lg font-bold">Resumen de conversión</h2>

        <div class="mt-6 grid gap-4 md:grid-cols-4">

            <div class="rounded-2xl bg-[#F8F5F2] p-5 text-center">
                <p class="text-sm text-gray-500">USD referencia</p>
                <h3 class="mt-2 text-2xl font-bold">$1,00</h3>
            </div>

            <div class="rounded-2xl bg-[#F8F5F2] p-5 text-center">
                <p class="text-sm text-gray-500">BCV</p>
                <h3 class="mt-2 text-2xl font-bold">Bs. 36,92</h3>
            </div>

            <div class="rounded-2xl bg-[#F8F5F2] p-5 text-center">
                <p class="text-sm text-gray-500">Binance</p>
                <h3 class="mt-2 text-2xl font-bold">Bs. 37,65</h3>
            </div>

            <div class="rounded-2xl bg-[#FFF0F4] p-5 text-center">
                <p class="text-sm text-gray-500">Tasa aplicada</p>
                <h3 class="mt-2 text-2xl font-bold text-[#E46F8A]">Bs. 37,65</h3>
            </div>

        </div>

    </section>

    <section class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">

        <h2 class="text-lg font-bold">Observaciones</h2>

        <textarea rows="4"
            placeholder="Agrega una nota sobre la fuente de la tasa, criterio usado o alguna condición comercial del día..."
            class="mt-5 w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"></textarea>

    </section>

    <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm">
        <div class="grid gap-4 md:grid-cols-3">
            <a href="{{ route('exchange-rates.index') }}"
                class="rounded-xl border border-black/10 px-5 py-3 text-center text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                Cancelar
            </a>

            <button type="button"
                class="rounded-xl border border-[#E46F8A] px-5 py-3 text-sm font-semibold text-[#E46F8A] transition hover:bg-[#FFF0F4]">
                Guardar como referencia
            </button>

            <button type="button"
                class="rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C]">
                Guardar tasa activa
            </button>
        </div>
    </div>

</form>

@endsection