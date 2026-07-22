@extends('layouts.app', [
'title' => 'Editar tono | Susan Brigitt Studio',
'pageTitle' => 'Editar tono'
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
            Editar tono o color
        </h1>

        <p class="mt-2 text-sm leading-6 text-zinc-500">
            Actualiza el nombre comercial, el color de referencia o su disponibilidad.
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

    <section
        class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm sm:p-8"
        x-data="{
            color: @js(old('hex_color', $tone->hex_color ?: '#E46F8A'))
        }">
        <form
            action="{{ route('catalogs.tones.update', $tone) }}"
            method="POST"
            class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label
                    for="name"
                    class="mb-2 block text-sm font-semibold text-zinc-700">
                    Nombre del tono
                </label>

                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name', $tone->name) }}"
                    class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                    required>
            </div>

            <div class="grid gap-4 sm:grid-cols-[110px_1fr]">
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
                    <label
                        for="hex_color"
                        class="mb-2 block text-sm font-semibold text-zinc-700">
                        Código hexadecimal
                    </label>

                    <input
                        id="hex_color"
                        name="hex_color"
                        type="text"
                        x-model="color"
                        placeholder="#E46F8A"
                        class="w-full rounded-xl border border-black/10 px-4 py-3 text-sm uppercase outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                </div>
            </div>

            <div
                class="flex items-center gap-4 rounded-xl border border-black/5 bg-[#F8F5F2] p-4">
                <span
                    class="h-12 w-12 shrink-0 rounded-full border border-black/10 shadow-sm"
                    :style="`background-color: ${color}`"></span>

                <div>
                    <p class="text-sm font-semibold text-zinc-900">
                        Vista previa
                    </p>

                    <p
                        class="mt-1 text-xs uppercase text-zinc-500"
                        x-text="color"></p>
                </div>
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
                        @selected((string) old('is_active', (int) $tone->is_active) === '1')
                        >
                        Activo
                    </option>

                    <option
                        value="0"
                        @selected((string) old('is_active', (int) $tone->is_active) === '0')
                        >
                        Inactivo
                    </option>
                </select>

                <p class="mt-2 text-xs leading-5 text-zinc-500">
                    Un tono inactivo se conserva en los productos históricos, pero no debería utilizarse en nuevos registros.
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