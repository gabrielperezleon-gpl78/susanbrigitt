@extends('layouts.app', [
'title' => 'Catálogos | Susan Brigitt Studio',
'pageTitle' => 'Catálogos'
])

@section('content')

<div class="space-y-8">
    <div>
        <p class="text-sm font-medium uppercase tracking-[0.24em] text-rose-400">
            Datos maestros
        </p>

        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">
            Administración de catálogos
        </h1>

        <p class="mt-2 max-w-3xl text-sm leading-6 text-zinc-500">
            Administra proveedores, marcas, categorías, tonos y unidades de medida para registrar productos, compras y ventas sin duplicar información.
        </p>
    </div>

    @if (session('success'))
    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="rounded-2xl border border-red-200 bg-red-50 p-5 text-sm text-red-700">
        <p class="font-bold">Revisa los datos del formulario.</p>

        <ul class="mt-2 list-inside list-disc space-y-1">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Formularios: primera fila --}}
    <section class="grid gap-6 xl:grid-cols-3">
        {{-- Proveedor --}}
        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-zinc-900">
                Nuevo proveedor
            </h2>

            <p class="mt-1 text-sm text-zinc-500">
                Datos comerciales y de contacto.
            </p>

            <form
                action="{{ route('catalogs.suppliers.store') }}"
                method="POST"
                class="mt-5 space-y-4">
                @csrf

                <input type="hidden" name="catalog_form" value="supplier">

                <input
                    name="name"
                    type="text"
                    value="{{ old('catalog_form') === 'supplier' ? old('name') : '' }}"
                    placeholder="Nombre del proveedor"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                    required>

                <input
                    name="contact_name"
                    type="text"
                    value="{{ old('catalog_form') === 'supplier' ? old('contact_name') : '' }}"
                    placeholder="Persona de contacto"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">

                <input
                    name="phone"
                    type="text"
                    value="{{ old('catalog_form') === 'supplier' ? old('phone') : '' }}"
                    placeholder="Teléfono"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">

                <input
                    name="email"
                    type="email"
                    value="{{ old('catalog_form') === 'supplier' ? old('email') : '' }}"
                    placeholder="Correo"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">

                <textarea
                    name="address"
                    rows="2"
                    placeholder="Dirección"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">{{ old('catalog_form') === 'supplier' ? old('address') : '' }}</textarea>

                <textarea
                    name="notes"
                    rows="2"
                    placeholder="Notas"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">{{ old('catalog_form') === 'supplier' ? old('notes') : '' }}</textarea>

                <button
                    type="submit"
                    class="w-full rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C]">
                    Guardar proveedor
                </button>
            </form>
        </div>

        {{-- Marca --}}
        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-zinc-900">
                Nueva marca
            </h2>

            <p class="mt-1 text-sm text-zinc-500">
                Identifica la marca comercial del producto.
            </p>

            <form
                action="{{ route('catalogs.brands.store') }}"
                method="POST"
                class="mt-5 space-y-4">
                @csrf

                <input type="hidden" name="catalog_form" value="brand">

                <input
                    name="name"
                    type="text"
                    value="{{ old('catalog_form') === 'brand' ? old('name') : '' }}"
                    placeholder="Nombre de la marca"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                    required>

                <textarea
                    name="description"
                    rows="4"
                    placeholder="Descripción opcional"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">{{ old('catalog_form') === 'brand' ? old('description') : '' }}</textarea>

                <button
                    type="submit"
                    class="w-full rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C]">
                    Guardar marca
                </button>
            </form>
        </div>

        {{-- Categoría --}}
        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-zinc-900">
                Nueva categoría
            </h2>

            <p class="mt-1 text-sm text-zinc-500">
                Agrupa productos de características similares.
            </p>

            <form
                action="{{ route('catalogs.categories.store') }}"
                method="POST"
                class="mt-5 space-y-4">
                @csrf

                <input type="hidden" name="catalog_form" value="category">

                <input
                    name="name"
                    type="text"
                    value="{{ old('catalog_form') === 'category' ? old('name') : '' }}"
                    placeholder="Ejemplo: Maquillaje"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                    required>

                <textarea
                    name="description"
                    rows="4"
                    placeholder="Descripción opcional"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">{{ old('catalog_form') === 'category' ? old('description') : '' }}</textarea>

                <button
                    type="submit"
                    class="w-full rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C]">
                    Guardar categoría
                </button>
            </form>
        </div>
    </section>

    {{-- Formularios: segunda fila --}}
    <section class="grid gap-6 lg:grid-cols-2">
        {{-- Tono --}}
        <div
            class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm"
            x-data="{
                color: @js(
                    old('catalog_form') === 'tone'
                        ? old('hex_color', '#E46F8A')
                        : '#E46F8A'
                )
            }">
            <h2 class="text-lg font-bold text-zinc-900">
                Nuevo tono o color
            </h2>

            <p class="mt-1 text-sm text-zinc-500">
                Registra el nombre comercial y un color de referencia.
            </p>

            <form
                action="{{ route('catalogs.tones.store') }}"
                method="POST"
                class="mt-5 space-y-4">
                @csrf

                <input type="hidden" name="catalog_form" value="tone">

                <input
                    name="name"
                    type="text"
                    value="{{ old('catalog_form') === 'tone' ? old('name') : '' }}"
                    placeholder="Ejemplo: Rojo intenso"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                    required>

                <div class="grid gap-4 sm:grid-cols-[90px_1fr]">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-zinc-700">
                            Color
                        </label>

                        <input
                            type="color"
                            x-model="color"
                            class="h-12 w-full cursor-pointer rounded-xl border border-black/10 bg-white p-1">
                    </div>

                    <div>
                        <label for="hex_color" class="mb-2 block text-sm font-semibold text-zinc-700">
                            Código hexadecimal
                        </label>

                        <input
                            id="hex_color"
                            name="hex_color"
                            type="text"
                            x-model="color"
                            placeholder="#E46F8A"
                            class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm uppercase outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C]">
                    Guardar tono
                </button>
            </form>
        </div>

        {{-- Unidad --}}
        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-zinc-900">
                Nueva unidad de medida
            </h2>

            <p class="mt-1 text-sm text-zinc-500">
                Define cómo se contabiliza o presenta cada producto.
            </p>

            <form
                action="{{ route('catalogs.unit-measures.store') }}"
                method="POST"
                class="mt-5 space-y-4">
                @csrf

                <input type="hidden" name="catalog_form" value="unit_measure">

                <input
                    name="name"
                    type="text"
                    value="{{ old('catalog_form') === 'unit_measure' ? old('name') : '' }}"
                    placeholder="Unidad, Caja, Set, Paquete"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                    required>

                <input
                    name="abbreviation"
                    type="text"
                    value="{{ old('catalog_form') === 'unit_measure' ? old('abbreviation') : '' }}"
                    placeholder="Abreviatura: und, caja, set"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">

                <button
                    type="submit"
                    class="w-full rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C]">
                    Guardar unidad
                </button>
            </form>
        </div>
    </section>

    {{-- Listados: primera fila --}}
    <section class="grid gap-6 xl:grid-cols-3">
        {{-- Proveedores --}}
        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-zinc-900">
                Proveedores registrados
            </h2>

            <div class="mt-5 space-y-3">
                @forelse ($suppliers as $supplier)
                <div class="rounded-xl border border-black/5 bg-[#F8F5F2] p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="text-sm font-semibold text-zinc-900">
                                    {{ $supplier->name }}
                                </p>

                                <span class="rounded-full px-2 py-1 text-[10px] font-semibold {{ $supplier->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-zinc-200 text-zinc-500' }}">
                                    {{ $supplier->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>

                            <p class="mt-1 text-xs text-gray-500">
                                {{ $supplier->contact_name ?: 'Sin contacto' }}

                                @if ($supplier->phone)
                                · {{ $supplier->phone }}
                                @endif
                            </p>
                        </div>

                        <a
                            href="{{ route('catalogs.suppliers.edit', $supplier) }}"
                            class="shrink-0 rounded-lg border border-black/10 bg-white px-3 py-2 text-xs font-semibold text-zinc-700 hover:bg-gray-50">
                            Editar
                        </a>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500">
                    No hay proveedores registrados.
                </p>
                @endforelse
            </div>
        </div>

        {{-- Marcas --}}
        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-zinc-900">
                Marcas registradas
            </h2>

            <div class="mt-5 space-y-3">
                @forelse ($brands as $brand)
                <div class="rounded-xl border border-black/5 bg-[#F8F5F2] p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="text-sm font-semibold text-zinc-900">
                                    {{ $brand->name }}
                                </p>

                                <span class="rounded-full px-2 py-1 text-[10px] font-semibold {{ $brand->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-zinc-200 text-zinc-500' }}">
                                    {{ $brand->is_active ? 'Activa' : 'Inactiva' }}
                                </span>
                            </div>

                            <p class="mt-1 text-xs text-gray-500">
                                {{ $brand->description ?: 'Sin descripción' }}
                            </p>
                        </div>

                        <a
                            href="{{ route('catalogs.brands.edit', $brand) }}"
                            class="shrink-0 rounded-lg border border-black/10 bg-white px-3 py-2 text-xs font-semibold text-zinc-700 hover:bg-gray-50">
                            Editar
                        </a>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500">
                    No hay marcas registradas.
                </p>
                @endforelse
            </div>
        </div>

        {{-- Categorías --}}
        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-zinc-900">
                Categorías registradas
            </h2>

            <div class="mt-5 space-y-3">
                @forelse ($categories as $category)
                <div class="rounded-xl border border-black/5 bg-[#F8F5F2] p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="text-sm font-semibold text-zinc-900">
                                    {{ $category->name }}
                                </p>

                                <span class="rounded-full px-2 py-1 text-[10px] font-semibold {{ $category->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-zinc-200 text-zinc-500' }}">
                                    {{ $category->is_active ? 'Activa' : 'Inactiva' }}
                                </span>
                            </div>

                            <p class="mt-1 text-xs text-gray-500">
                                {{ $category->description ?: 'Sin descripción' }}
                            </p>
                        </div>

                        <a
                            href="{{ route('catalogs.categories.edit', $category) }}"
                            class="shrink-0 rounded-lg border border-black/10 bg-white px-3 py-2 text-xs font-semibold text-zinc-700 hover:bg-gray-50">
                            Editar
                        </a>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500">
                    No hay categorías registradas.
                </p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Listados: segunda fila --}}
    <section class="grid gap-6 lg:grid-cols-2">
        {{-- Tonos --}}
        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-zinc-900">
                Tonos registrados
            </h2>

            <div class="mt-5 space-y-3">
                @forelse ($tones as $tone)
                <div class="rounded-xl border border-black/5 bg-[#F8F5F2] p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex min-w-0 items-center gap-3">
                            <span
                                class="h-9 w-9 shrink-0 rounded-full border border-black/10 shadow-sm"
                                style="background-color: {{ $tone->hex_color ?: '#E4E4E7' }}"></span>

                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-sm font-semibold text-zinc-900">
                                        {{ $tone->name }}
                                    </p>

                                    <span class="rounded-full px-2 py-1 text-[10px] font-semibold {{ $tone->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-zinc-200 text-zinc-500' }}">
                                        {{ $tone->is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </div>

                                <p class="mt-1 text-xs uppercase text-gray-500">
                                    {{ $tone->hex_color ?: 'Sin color hexadecimal' }}
                                </p>
                            </div>
                        </div>

                        <a
                            href="{{ route('catalogs.tones.edit', $tone) }}"
                            class="shrink-0 rounded-lg border border-black/10 bg-white px-3 py-2 text-xs font-semibold text-zinc-700 hover:bg-gray-50">
                            Editar
                        </a>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500">
                    No hay tonos registrados.
                </p>
                @endforelse
            </div>
        </div>

        {{-- Unidades --}}
        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-zinc-900">
                Unidades registradas
            </h2>

            <div class="mt-5 space-y-3">
                @forelse ($unitMeasures as $unitMeasure)
                <div class="rounded-xl border border-black/5 bg-[#F8F5F2] p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="text-sm font-semibold text-zinc-900">
                                    {{ $unitMeasure->name }}
                                </p>

                                <span class="rounded-full px-2 py-1 text-[10px] font-semibold {{ $unitMeasure->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-zinc-200 text-zinc-500' }}">
                                    {{ $unitMeasure->is_active ? 'Activa' : 'Inactiva' }}
                                </span>
                            </div>

                            <p class="mt-1 text-xs text-gray-500">
                                {{ $unitMeasure->abbreviation ?: 'Sin abreviatura' }}
                            </p>
                        </div>

                        <a
                            href="{{ route('catalogs.unit-measures.edit', $unitMeasure) }}"
                            class="shrink-0 rounded-lg border border-black/10 bg-white px-3 py-2 text-xs font-semibold text-zinc-700 hover:bg-gray-50">
                            Editar
                        </a>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500">
                    No hay unidades de medida registradas.
                </p>
                @endforelse
            </div>
        </div>
    </section>
</div>

@endsection