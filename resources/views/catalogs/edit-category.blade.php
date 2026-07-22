@extends('layouts.app', [
'title' => 'Editar categoría | Susan Brigitt Studio',
'pageTitle' => 'Editar categoría'
])

@section('content')

<div class="mx-auto max-w-3xl space-y-6">
    <div>
        <a
            href="{{ route('catalogs.index') }}"
            class="text-sm font-semibold text-[#E46F8A] hover:text-[#D75E7C]">
            ← Volver a catálogos
        </a>

        <p class="mt-6 text-sm font-medium uppercase tracking-[0.24em] text-rose-400">
            Datos maestros
        </p>

        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">
            Editar categoría
        </h1>

        <p class="mt-2 text-sm leading-6 text-zinc-500">
            Actualiza el nombre, la descripción o el estado de la categoría.
        </p>
    </div>

    @if ($errors->any())
    <div class="rounded-2xl border border-red-200 bg-red-50 p-5 text-sm text-red-700">
        <p class="font-bold">
            Revisa los datos del formulario.
        </p>

        <ul class="mt-2 list-inside list-disc space-y-1">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <section class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm sm:p-8">
        <form
            action="{{ route('catalogs.categories.update', $category) }}"
            method="POST"
            class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label
                    for="name"
                    class="mb-2 block text-sm font-semibold text-zinc-700">
                    Nombre de la categoría
                </label>

                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name', $category->name) }}"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                    required>
            </div>

            <div>
                <label
                    for="description"
                    class="mb-2 block text-sm font-semibold text-zinc-700">
                    Descripción
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                    placeholder="Descripción opcional">{{ old('description', $category->description) }}</textarea>
            </div>

            <div>
                <label
                    for="is_active"
                    class="mb-2 block text-sm font-semibold text-zinc-700">
                    Estado
                </label>

                <select
                    id="is_active"
                    name="is_active"
                    class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                    required>
                    <option
                        value="1"
                        @selected((string) old('is_active', (int) $category->is_active) === '1')
                        >
                        Activa
                    </option>

                    <option
                        value="0"
                        @selected((string) old('is_active', (int) $category->is_active) === '0')
                        >
                        Inactiva
                    </option>
                </select>

                <p class="mt-2 text-xs leading-5 text-zinc-500">
                    Una categoría inactiva permanece en el historial, pero no debería utilizarse para registrar nuevos productos.
                </p>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-black/5 pt-6 sm:flex-row sm:justify-end">
                <a
                    href="{{ route('catalogs.index') }}"
                    class="rounded-xl border border-black/10 px-5 py-3 text-center text-sm font-semibold text-zinc-700 transition hover:bg-zinc-50">
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C]">
                    Guardar cambios
                </button>
            </div>
        </form>
    </section>
</div>

@endsection