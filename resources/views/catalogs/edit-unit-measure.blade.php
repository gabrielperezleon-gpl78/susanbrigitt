@extends('layouts.app', [
'title' => 'Editar unidad | Susan Brigitt Studio',
'pageTitle' => 'Editar unidad'
])

@section('content')

<div class="max-w-3xl space-y-6">
    <div>
        <a href="{{ route('catalogs.index') }}" class="text-sm font-semibold text-[#E46F8A]">
            ← Volver a catálogos
        </a>

        <h1 class="mt-4 text-3xl font-semibold tracking-tight text-zinc-900">
            Editar unidad de medida
        </h1>
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

    <form action="{{ route('catalogs.unit-measures.update', $unitMeasure) }}" method="POST"
        class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        @csrf
        @method('PUT')

        <div class="space-y-5">
            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">Nombre</label>
                <input name="name" type="text" value="{{ old('name', $unitMeasure->name) }}"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                    required>
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">Abreviatura</label>
                <input name="abbreviation" type="text" value="{{ old('abbreviation', $unitMeasure->abbreviation) }}"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">Estado</label>
                <select name="is_active"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                    required>
                    <option value="1" @selected(old('is_active', $unitMeasure->is_active) == 1)>Activa</option>
                    <option value="0" @selected(old('is_active', $unitMeasure->is_active) == 0)>Inactiva</option>
                </select>
            </div>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit"
                class="rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#D75E7C]">
                Actualizar unidad
            </button>

            <a href="{{ route('catalogs.index') }}"
                class="rounded-xl border border-black/10 px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                Cancelar
            </a>
        </div>
    </form>
</div>

@endsection