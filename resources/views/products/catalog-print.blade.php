<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo de productos | Susan Brigitt Studio</title>
    <style>
        @page { size: A4 landscape; margin: 13mm; }
        body { font: 10px Arial, sans-serif; color: #242424; margin: 20px; }
        h1 { font-size: 20px; margin: 0 0 5px; color: #bd4867; }
        p { margin: 0 0 16px; color: #555; }
        table { border-collapse: collapse; width: 100%; }
        th { background: #e46f8a; color: white; text-align: left; }
        th, td { padding: 7px 5px; border-bottom: 1px solid #dedede; overflow-wrap: anywhere; }
        tr { break-inside: avoid; }
        thead { display: table-header-group; }
        .number { text-align: right; white-space: nowrap; }
        .toolbar { margin-bottom: 18px; }
        button { background: #e46f8a; color: white; border: 0; border-radius: 6px; padding: 10px 15px; cursor: pointer; }
        @media print { body { margin: 0; } .toolbar { display: none; } }
    </style>
</head>
<body>
    <div class="toolbar"><button type="button" onclick="window.print()">Imprimir o guardar como PDF</button></div>
    <h1>Susan Brigitt Studio · Catálogo de productos</h1>
    <p>Generado el {{ $generatedAt->format('d/m/Y H:i') }} · {{ $products->count() }} {{ $products->count() === 1 ? 'ficha' : 'fichas' }} · Incluye productos activos e inactivos según los filtros aplicados.</p>
    <table>
        <thead><tr><th>Código</th><th>Producto</th><th>Marca</th><th>Tono</th><th>Unidad</th><th>Categoría</th><th>Proveedor</th><th class="number">Stock</th><th class="number">Costo USD</th><th class="number">Venta USD</th><th>Estado</th></tr></thead>
        <tbody>
            @forelse ($products as $product)
            <tr>
                <td>{{ $product->internal_code }}</td><td>{{ $product->name }}</td><td>{{ $product->brand?->name ?? 'Sin marca' }}</td>
                <td>{{ $product->tone?->name ?? 'Sin tono' }}</td><td>{{ $product->unitMeasure?->name ?? 'Sin unidad' }}</td>
                <td>{{ $product->category?->name ?? 'Sin categoría' }}</td><td>{{ $product->supplier?->name ?? 'Sin proveedor' }}</td>
                <td class="number">{{ $product->current_stock }}</td><td class="number">{{ number_format($product->purchase_price_usd, 2, ',', '.') }}</td>
                <td class="number">{{ number_format($product->sale_price_usd, 2, ',', '.') }}</td><td>{{ $product->status === 'active' ? 'Activo' : 'Inactivo' }}</td>
            </tr>
            @empty
            <tr><td colspan="11">No hay productos que coincidan con la búsqueda.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
