@extends('layouts.app', [
'title' => 'Ver producto | Susan Brigitt Studio',
'pageTitle' => 'Ver producto'
])

@section('content')

<div class="space-y-8">
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
        <div>
            <a href="{{ route('products.index') }}" class="text-sm font-semibold text-[#E46F8A]">
                ← Volver a productos
            </a>

            <p class="mt-4 text-sm font-medium uppercase tracking-[0.24em] text-rose-400">
                Ficha del producto
            </p>

            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">
                {{ $product->name }}
            </h1>

            <p class="mt-2 max-w-2xl text-sm leading-6 text-zinc-500">
                Consulta general del producto registrado en el catálogo.
            </p>
        </div>

        <a
            href="{{ route('products.edit', $product) }}"
            class="inline-flex items-center justify-center rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C]">
            Editar producto
        </a>
    </div>

    <section class="grid gap-6 xl:grid-cols-[1fr_360px]">
        <div class="space-y-6">
            <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-zinc-900">Información principal</h2>

                <div class="mt-6 grid gap-5 md:grid-cols-2">
                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-zinc-400">Código interno</p>
                        <p class="mt-1 text-sm font-semibold text-zinc-900">{{ $product->internal_code ?: 'Sin código' }}</p>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-zinc-400">Código de barras</p>
                        <p class="mt-1 text-sm font-semibold text-zinc-900">{{ $product->barcode ?: 'Sin código de barras' }}</p>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-zinc-400">Categoría</p>
                        <p class="mt-1 text-sm font-semibold text-zinc-900">{{ $product->category?->name ?? 'Sin categoría' }}</p>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-zinc-400">Marca</p>
                        <p class="mt-1 text-sm font-semibold text-zinc-900">{{ $product->brand?->name ?? 'Sin marca' }}</p>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-zinc-400">Tono / color</p>
                        <p class="mt-1 text-sm font-semibold text-zinc-900">{{ $product->tone?->name ?? 'Sin tono' }}</p>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-zinc-400">Unidad de medida</p>
                        <p class="mt-1 text-sm font-semibold text-zinc-900">
                            {{ $product->unitMeasure?->name ?? 'Sin unidad' }}
                            @if ($product->unitMeasure?->abbreviation)
                            · {{ $product->unitMeasure->abbreviation }}
                            @endif
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-xs uppercase tracking-[0.18em] text-zinc-400">Proveedor habitual</p>
                        <p class="mt-1 text-sm font-semibold text-zinc-900">{{ $product->supplier?->name ?? 'Sin proveedor' }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-xs uppercase tracking-[0.18em] text-zinc-400">Descripción</p>
                        <p class="mt-2 text-sm leading-6 text-zinc-600">
                            {{ $product->description ?: 'Sin descripción registrada.' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-zinc-900">Notas internas</h2>

                <p class="mt-4 text-sm leading-6 text-zinc-600">
                    {{ $product->internal_notes ?: 'Sin notas internas registradas.' }}
                </p>
            </div>
        </div>

        <aside class="space-y-6 xl:sticky xl:top-28 xl:self-start">
            <section class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-rose-400">
                    Resumen comercial
                </p>

                <div class="mt-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <span class="text-sm text-zinc-500">Costo USD</span>
                        <span class="text-sm font-semibold text-zinc-900">
                            ${{ number_format((float) $product->purchase_price_usd, 2, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <span class="text-sm text-zinc-500">Venta USD</span>
                        <span class="text-sm font-semibold text-zinc-900">
                            ${{ number_format((float) $product->sale_price_usd, 2, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <span class="text-sm text-zinc-500">Ganancia</span>
                        <span class="text-sm font-semibold text-green-600">
                            ${{ number_format((float) $product->unit_profit_usd, 2, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <span class="text-sm text-zinc-500">Margen</span>
                        <span class="text-sm font-semibold text-zinc-900">
                            {{ number_format((float) $product->profit_margin, 2, ',', '.') }}%
                        </span>
                    </div>

                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <span class="text-sm text-zinc-500">Estado</span>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $product->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-zinc-100 text-zinc-500' }}">
                            {{ $product->status === 'active' ? 'Disponible' : 'Inactivo' }}
                        </span>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-rose-400">
                    Inventario
                </p>

                <div class="mt-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <span class="text-sm text-zinc-500">Stock inicial</span>
                        <span class="text-sm font-semibold text-zinc-900">
                            {{ $product->initial_stock }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <span class="text-sm text-zinc-500">Stock actual</span>
                        <span class="text-sm font-semibold text-zinc-900">
                            {{ $product->current_stock }}
                            {{ $product->unitMeasure?->abbreviation ?? $product->unitMeasure?->name ?? '' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <span class="text-sm text-zinc-500">Stock mínimo</span>
                        <span class="text-sm font-semibold text-zinc-900">
                            {{ $product->minimum_stock }}
                        </span>
                    </div>

                    <div>
                        <span class="text-sm text-zinc-500">Valor actual del inventario</span>
                        <p class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">
                            ${{ number_format((float) $product->current_stock * (float) $product->purchase_price_usd, 2, ',', '.') }}
                        </p>
                    </div>
                </div>
            </section>
        </aside>
    </section>
</div>

@endsection