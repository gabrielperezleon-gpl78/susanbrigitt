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
            Proveedores, marcas y unidades
        </h1>

        <p class="mt-2 max-w-3xl text-sm leading-6 text-zinc-500">
            Administra los catálogos base que se usan para registrar productos, compras y ventas sin repetir información.
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

    <section class="grid gap-6 xl:grid-cols-3">
        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-zinc-900">Nuevo proveedor</h2>

            <form action="{{ route('catalogs.suppliers.store') }}" method="POST" class="mt-5 space-y-4">
                @csrf

                <input name="name" type="text" placeholder="Nombre del proveedor"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                    required>

                <input name="contact_name" type="text" placeholder="Persona de contacto"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">

                <input name="phone" type="text" placeholder="Teléfono"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">

                <input name="email" type="email" placeholder="Correo"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">

                <textarea name="address" rows="2" placeholder="Dirección"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"></textarea>

                <textarea name="notes" rows="2" placeholder="Notas"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"></textarea>

                <button type="submit"
                    class="w-full rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C]">
                    Guardar proveedor
                </button>
            </form>
        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-zinc-900">Nueva marca</h2>

            <form action="{{ route('catalogs.brands.store') }}" method="POST" class="mt-5 space-y-4">
                @csrf

                <input name="name" type="text" placeholder="Nombre de la marca"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                    required>

                <textarea name="description" rows="3" placeholder="Descripción opcional"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"></textarea>

                <button type="submit"
                    class="w-full rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C]">
                    Guardar marca
                </button>
            </form>
        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-zinc-900">Nueva unidad de medida</h2>

            <form action="{{ route('catalogs.unit-measures.store') }}" method="POST" class="mt-5 space-y-4">
                @csrf

                <input name="name" type="text" placeholder="Nombre: Unidad, Caja, Set, Paquete"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                    required>

                <input name="abbreviation" type="text" placeholder="Abreviatura: und, caja, set"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">

                <button type="submit"
                    class="w-full rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C]">
                    Guardar unidad
                </button>
            </form>
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-3">
        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-zinc-900">Proveedores registrados</h2>

            <div class="mt-5 space-y-3">
                @forelse ($suppliers as $supplier)
                <div class="rounded-xl border border-black/5 bg-[#F8F5F2] p-4">
                    <p class="text-sm font-semibold text-zinc-900">{{ $supplier->name }}</p>
                    <p class="mt-1 text-xs text-gray-500">
                        {{ $supplier->contact_name ?: 'Sin contacto' }}
                        @if ($supplier->phone)
                        · {{ $supplier->phone }}
                        @endif
                    </p>
                </div>
                @empty
                <p class="text-sm text-gray-500">No hay proveedores registrados.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-zinc-900">Marcas registradas</h2>

            <div class="mt-5 space-y-3">
                @forelse ($brands as $brand)
                <div class="rounded-xl border border-black/5 bg-[#F8F5F2] p-4">
                    <p class="text-sm font-semibold text-zinc-900">{{ $brand->name }}</p>
                    <p class="mt-1 text-xs text-gray-500">{{ $brand->description ?: 'Sin descripción' }}</p>
                </div>
                @empty
                <p class="text-sm text-gray-500">No hay marcas registradas.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-zinc-900">Unidades registradas</h2>

            <div class="mt-5 space-y-3">
                @forelse ($unitMeasures as $unitMeasure)
                <div class="rounded-xl border border-black/5 bg-[#F8F5F2] p-4">
                    <p class="text-sm font-semibold text-zinc-900">{{ $unitMeasure->name }}</p>
                    <p class="mt-1 text-xs text-gray-500">
                        {{ $unitMeasure->abbreviation ?: 'Sin abreviatura' }}
                    </p>
                </div>
                @empty
                <p class="text-sm text-gray-500">No hay unidades de medida registradas.</p>
                @endforelse
            </div>
        </div>
    </section>
</div>

@endsection