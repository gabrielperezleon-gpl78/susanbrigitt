@extends('layouts.app', ['title' => 'Catálogo de productos | Susan Brigitt Studio', 'pageTitle' => 'Catálogo de productos'])

@section('content')
<div class="mb-6 flex flex-wrap items-start justify-between gap-4">
    <div>
        <p class="text-sm text-zinc-600">Todas las fichas registradas, incluidas las inactivas y las variantes por tono o unidad.</p>
        <p class="mt-1 text-sm font-semibold text-zinc-800">{{ $products->total() }} {{ $products->total() === 1 ? 'ficha encontrada' : 'fichas encontradas' }}.</p>
    </div>
    <a href="{{ route('products.index') }}" class="rounded-xl border border-zinc-200 bg-white px-5 py-3 text-sm font-semibold text-zinc-700">Volver a Productos</a>
</div>

<section class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
    <form method="GET" action="{{ route('products.catalog') }}" class="grid gap-3 md:grid-cols-[minmax(0,1fr)_200px_auto]">
        <label class="sr-only" for="catalog-search">Buscar producto</label>
        <input id="catalog-search" name="search" value="{{ request('search') }}" maxlength="180" placeholder="Nombre, código o código de barras" class="rounded-xl border border-black/10 px-4 py-3 text-sm">
        <label class="sr-only" for="catalog-status">Estado del registro</label>
        <select id="catalog-status" name="status" class="rounded-xl border border-black/10 px-4 py-3 text-sm">
            <option value="">Activos e inactivos</option>
            <option value="active" @selected(request('status') === 'active')>Solo activos</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Solo inactivos</option>
        </select>
        <button type="submit" class="rounded-xl bg-zinc-900 px-5 py-3 text-sm font-semibold text-white">Buscar</button>
    </form>
    <div class="mt-4 flex flex-wrap items-center gap-3">
        <a href="{{ route('products.catalog.xlsx', request()->only('search', 'status')) }}" class="rounded-xl bg-[#E46F8A] px-5 py-3 text-sm font-semibold text-white">Descargar Excel (.xlsx)</a>
        <a href="{{ route('products.catalog.print', request()->only('search', 'status')) }}" target="_blank" rel="noopener" class="rounded-xl border border-[#E46F8A] px-5 py-3 text-sm font-semibold text-[#E46F8A]">Guardar como PDF</a>
        <span class="text-xs text-zinc-500">Los archivos incluyen todas las fichas que coincidan con la búsqueda, aunque ocupen varias páginas.</span>
    </div>

    <div class="mt-6 overflow-x-auto rounded-xl border border-black/5">
        <table class="w-full min-w-[1120px] text-left text-sm">
            <thead class="bg-[#F8F5F2] text-zinc-600">
                <tr>
                    <th class="px-4 py-3">Código</th><th class="px-4 py-3">Producto</th><th class="px-4 py-3">Marca</th>
                    <th class="px-4 py-3">Tono</th><th class="px-4 py-3">Unidad</th><th class="px-4 py-3">Categoría</th>
                    <th class="px-4 py-3">Proveedor</th><th class="px-4 py-3 text-right">Stock</th>
                    <th class="px-4 py-3 text-right">Costo USD</th><th class="px-4 py-3 text-right">Venta USD</th><th class="px-4 py-3">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @forelse ($products as $product)
                <tr>
                    <td class="px-4 py-3 font-semibold"><a class="text-[#C94F70] hover:underline" href="{{ route('products.show', $product) }}">{{ $product->internal_code }}</a></td>
                    <td class="px-4 py-3">{{ $product->name }}</td>
                    <td class="px-4 py-3">{{ $product->brand?->name ?? 'Sin marca' }}</td>
                    <td class="px-4 py-3">{{ $product->tone?->name ?? 'Sin tono' }}</td>
                    <td class="px-4 py-3">{{ $product->unitMeasure?->name ?? 'Sin unidad' }}</td>
                    <td class="px-4 py-3">{{ $product->category?->name ?? 'Sin categoría' }}</td>
                    <td class="px-4 py-3">{{ $product->supplier?->name ?? 'Sin proveedor' }}</td>
                    <td class="px-4 py-3 text-right">{{ $product->current_stock }}</td>
                    <td class="px-4 py-3 text-right">${{ number_format($product->purchase_price_usd, 2, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right">${{ number_format($product->sale_price_usd, 2, ',', '.') }}</td>
                    <td class="px-4 py-3">{{ $product->status === 'active' ? 'Activo' : 'Inactivo' }}</td>
                </tr>
                @empty
                <tr><td colspan="11" class="px-4 py-10 text-center text-zinc-500">No hay productos que coincidan con la búsqueda.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-5">{{ $products->links() }}</div>
</section>
@endsection
