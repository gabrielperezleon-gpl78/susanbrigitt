@extends('layouts.app', [
'title' => 'Registrar producto | Susan Brigitt Studio',
'pageTitle' => 'Registrar producto'
])

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-[#E46F8A]">
            ← Volver al dashboard
        </a>
        <p class="mt-2 text-sm text-gray-500">
            Registra productos con costo, precio de venta, inventario inicial y datos comerciales.
        </p>
    </div>

</div>

<form class="space-y-6">

    <section class="rounded-2xl border border-black/5 bg-white shadow-sm">

        <div class="border-b border-black/5 px-6 py-5">
            <h2 class="text-lg font-bold">Información del producto</h2>
            <p class="mt-1 text-sm text-gray-500">
                Datos principales para identificar y administrar el producto.
            </p>
        </div>

        <div class="grid gap-8 p-6 xl:grid-cols-[1fr_320px]">

            <div class="grid gap-5 md:grid-cols-2">

                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">
                        Código interno <span class="text-[#E46F8A]">*</span>
                    </label>
                    <input type="text" value="SB-0041"
                        class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                    <p class="mt-2 text-xs text-gray-400">Código único para identificar el producto.</p>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">
                        Proveedor
                    </label>
                    <input type="text" value="Proveedoría Beauty C.A."
                        class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                    <p class="mt-2 text-xs text-gray-400">Proveedor o distribuidor principal.</p>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">
                        Nombre del producto <span class="text-[#E46F8A]">*</span>
                    </label>
                    <input type="text" value="Base Líquida"
                        class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                    <p class="mt-2 text-xs text-gray-400">Nombre comercial del producto.</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Costo compra USD <span class="text-[#E46F8A]">*</span>
                        </label>
                        <input type="text" value="$4,50"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Precio venta USD <span class="text-[#E46F8A]">*</span>
                        </label>
                        <input type="text" value="$8,00"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">
                        Categoría <span class="text-[#E46F8A]">*</span>
                    </label>
                    <select class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                        <option>Maquillaje</option>
                        <option>Cuidado facial</option>
                        <option>Cuidado labial</option>
                        <option>Accesorios</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Ganancia USD
                        </label>
                        <input type="text" value="$3,50" readonly
                            class="w-full rounded-xl border border-black/10 bg-[#F8F5F2] px-4 py-3 text-sm font-semibold text-[#E46F8A] outline-none">
                        <p class="mt-2 text-xs text-gray-400">Se calcula automáticamente.</p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Margen %
                        </label>
                        <input type="text" value="43,75%" readonly
                            class="w-full rounded-xl border border-black/10 bg-[#F8F5F2] px-4 py-3 text-sm font-semibold text-[#171717] outline-none">
                        <p class="mt-2 text-xs text-gray-400">Se calcula automáticamente.</p>
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">
                        Marca <span class="text-[#E46F8A]">*</span>
                    </label>
                    <select class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                        <option>Vogue</option>
                        <option>Valmy</option>
                        <option>Maybelline</option>
                        <option>L'Oréal</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Stock inicial <span class="text-[#E46F8A]">*</span>
                        </label>
                        <input type="number" value="20"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Stock mínimo <span class="text-[#E46F8A]">*</span>
                        </label>
                        <input type="number" value="5"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">
                        Tono <span class="text-[#E46F8A]">*</span>
                    </label>
                    <select class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                        <option>Beige claro</option>
                        <option>Beige natural</option>
                        <option>Rojo intenso</option>
                        <option>Negro</option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">
                        Fecha de ingreso <span class="text-[#E46F8A]">*</span>
                    </label>
                    <input type="date" value="2024-05-21"
                        class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">
                        Descripción breve
                    </label>
                    <textarea rows="5"
                        class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">Base líquida de cobertura media, acabado natural.</textarea>
                    <p class="mt-2 text-xs text-gray-400">Máximo 200 caracteres.</p>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">
                        Estado <span class="text-[#E46F8A]">*</span>
                    </label>
                    <select class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                        <option>Activo</option>
                        <option>Inactivo</option>
                        <option>Agotado</option>
                    </select>
                </div>

            </div>

            <aside>
                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Imagen del producto
                </label>

                <div class="flex h-72 items-center justify-center rounded-2xl border border-dashed border-black/20 bg-[#F8F5F2]">
                    <div class="text-center">
                        <div class="mx-auto flex h-36 w-24 items-center justify-center rounded-2xl bg-white text-5xl shadow-sm">
                            🧴
                        </div>
                        <p class="mt-4 text-sm font-semibold text-gray-700">Producto</p>
                        <p class="mt-1 text-xs text-gray-400">Vista previa</p>
                    </div>
                </div>

                <button type="button"
                    class="mt-4 w-full rounded-xl border border-[#E46F8A] px-5 py-3 text-sm font-semibold text-[#E46F8A] transition hover:bg-[#E46F8A] hover:text-white">
                    Cambiar imagen
                </button>

                <p class="mt-3 text-center text-xs text-gray-400">
                    Formatos: JPG, PNG o WEBP. Máx. 2MB.
                </p>
            </aside>

        </div>

    </section>

    <section class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-bold">Resumen financiero</h2>

        <div class="mt-6 grid gap-4 md:grid-cols-4">
            <div class="rounded-2xl bg-[#F8F5F2] p-5 text-center">
                <p class="text-sm text-gray-500">Costo compra</p>
                <h3 class="mt-2 text-2xl font-bold">$4,50</h3>
            </div>

            <div class="rounded-2xl bg-[#F8F5F2] p-5 text-center">
                <p class="text-sm text-gray-500">Precio venta</p>
                <h3 class="mt-2 text-2xl font-bold">$8,00</h3>
            </div>

            <div class="rounded-2xl bg-[#FFF0F4] p-5 text-center">
                <p class="text-sm text-gray-500">Ganancia</p>
                <h3 class="mt-2 text-2xl font-bold text-[#E46F8A]">$3,50</h3>
            </div>

            <div class="rounded-2xl bg-[#F8F5F2] p-5 text-center">
                <p class="text-sm text-gray-500">Margen</p>
                <h3 class="mt-2 text-2xl font-bold">43,75%</h3>
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-bold">Información adicional</h2>

        <div class="mt-6 grid gap-5 md:grid-cols-3">
            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">Proveedor alternativo</label>
                <input type="text" placeholder="Opcional"
                    class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">Código de barras</label>
                <input type="text" placeholder="EAN, UPC o código interno"
                    class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">Etiquetas</label>
                <input type="text" placeholder="nuevo, favorito, promoción"
                    class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
            </div>
        </div>

        <div class="mt-5">
            <label class="mb-2 block text-sm font-semibold text-gray-700">Notas internas</label>
            <textarea rows="4" placeholder="Escribe aquí cualquier detalle adicional sobre el producto..."
                class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"></textarea>
        </div>
    </section>

    <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm">
        <div class="grid gap-4 md:grid-cols-3">
            <a href="{{ route('dashboard') }}"
                class="rounded-xl border border-black/10 px-5 py-3 text-center text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                Cancelar
            </a>

            <button type="button"
                class="rounded-xl border border-[#E46F8A] px-5 py-3 text-sm font-semibold text-[#E46F8A] transition hover:bg-[#FFF0F4]">
                Guardar producto
            </button>

            <button type="button"
                class="rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C]">
                Guardar y registrar compra
            </button>
        </div>
    </div>

</form>

@endsection