@extends('layouts.app', [
'title' => 'Editar proveedor | Susan Brigitt Studio',
'pageTitle' => 'Editar proveedor'
])

@section('content')

<div class="max-w-3xl space-y-6">
    <div>
        <a href="{{ route('catalogs.index') }}" class="text-sm font-semibold text-[#E46F8A]">
            ← Volver a catálogos
        </a>

        <h1 class="mt-4 text-3xl font-semibold tracking-tight text-zinc-900">
            Editar proveedor
        </h1>

        <p class="mt-2 text-sm leading-6 text-zinc-500">
            Corrige los datos del proveedor sin eliminar su historial asociado.
        </p>
    </div>

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

    <form action="{{ route('catalogs.suppliers.update', $supplier) }}" method="POST"
        class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        @csrf
        @method('PUT')

        <div class="grid gap-5 md:grid-cols-2">
            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-semibold text-gray-700">Nombre</label>
                <input name="name" type="text" value="{{ old('name', $supplier->name) }}"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                    required>
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">Persona de contacto</label>
                <input name="contact_name" type="text" value="{{ old('contact_name', $supplier->contact_name) }}"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">Teléfono</label>
                <input name="phone" type="text" value="{{ old('phone', $supplier->phone) }}"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
            </div>

            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-semibold text-gray-700">Correo</label>
                <input name="email" type="email" value="{{ old('email', $supplier->email) }}"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
            </div>

            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-semibold text-gray-700">Dirección</label>
                <textarea name="address" rows="3"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">{{ old('address', $supplier->address) }}</textarea>
            </div>

            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-semibold text-gray-700">Notas</label>
                <textarea name="notes" rows="3"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">{{ old('notes', $supplier->notes) }}</textarea>
            </div>

            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-semibold text-gray-700">Estado</label>
                <select name="is_active"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                    required>
                    <option value="1" @selected(old('is_active', $supplier->is_active) == 1)>Activo</option>
                    <option value="0" @selected(old('is_active', $supplier->is_active) == 0)>Inactivo</option>
                </select>
            </div>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit"
                class="rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#D75E7C]">
                Actualizar proveedor
            </button>

            <a href="{{ route('catalogs.index') }}"
                class="rounded-xl border border-black/10 px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                Cancelar
            </a>
        </div>
    </form>
</div>

@endsection