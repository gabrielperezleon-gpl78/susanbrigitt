<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class ProductCatalogController extends Controller
{
    private const HEADERS = ['Código', 'Producto', 'Marca', 'Tono', 'Unidad', 'Categoría', 'Proveedor', 'Stock', 'Costo USD', 'Venta USD', 'Estado'];

    public function index(Request $request): View
    {
        $products = $this->query($request)->paginate(50)->withQueryString();

        return view('products.catalog', compact('products'));
    }

    public function print(Request $request): View
    {
        return view('products.catalog-print', [
            'products' => $this->query($request)->get(),
            'generatedAt' => now(),
        ]);
    }

    public function xlsx(Request $request): BinaryFileResponse
    {
        abort_unless(class_exists(ZipArchive::class), 503, 'La extensión ZIP de PHP es necesaria para exportar Excel.');

        $path = tempnam(sys_get_temp_dir(), 'catalogo-productos-');
        abort_if($path === false, 500, 'No se pudo preparar el archivo Excel.');

        $zip = new ZipArchive();
        if ($zip->open($path, ZipArchive::OVERWRITE) !== true) {
            @unlink($path);
            abort(500, 'No se pudo crear el archivo Excel.');
        }

        try {
            $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/></Types>');
            $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>');
            $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Productos" sheetId="1" r:id="rId1"/></sheets></workbook>');
            $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>');
            $zip->addFromString('xl/styles.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><color rgb="FFFFFFFF"/><sz val="11"/><name val="Calibri"/></font></fonts><fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FFE46F8A"/><bgColor indexed="64"/></patternFill></fill></fills><borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders><cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs><cellXfs count="3"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/><xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1"/><xf numFmtId="2" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/></cellXfs></styleSheet>');

            $sheet = fopen('php://temp/maxmemory:2097152', 'w+');
            if ($sheet === false) {
                throw new \RuntimeException('No se pudo preparar la hoja.');
            }
            try {
                fwrite($sheet, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetViews><sheetView workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews><cols><col min="1" max="1" width="19" customWidth="1"/><col min="2" max="2" width="32" customWidth="1"/><col min="3" max="7" width="19" customWidth="1"/><col min="8" max="11" width="15" customWidth="1"/></cols><sheetData>');
                fwrite($sheet, '<row r="1">');
                foreach (self::HEADERS as $i => $header) {
                    fwrite($sheet, $this->stringCell($i, 1, $header, 1));
                }
                fwrite($sheet, '</row>');

                $row = 1;
                foreach ($this->query($request)->lazy(200) as $product) {
                    $row++;
                    $values = $this->values($product);
                    fwrite($sheet, '<row r="'.$row.'">');
                    foreach ($values as $i => $value) {
                        fwrite($sheet, in_array($i, [7, 8, 9], true)
                            ? $this->numberCell($i, $row, $value, $i === 7 ? 0 : 2)
                            : $this->stringCell($i, $row, $value));
                    }
                    fwrite($sheet, '</row>');
                }
                fwrite($sheet, '</sheetData><autoFilter ref="A1:K'.$row.'"/></worksheet>');
                rewind($sheet);
                if (!$zip->addFromString('xl/worksheets/sheet1.xml', stream_get_contents($sheet))) {
                    throw new \RuntimeException('No se pudo escribir la hoja.');
                }
            } finally {
                fclose($sheet);
            }
        } catch (\Throwable $e) {
            $zip->close();
            @unlink($path);
            throw $e;
        }

        if (!$zip->close()) {
            @unlink($path);
            abort(500, 'No se pudo finalizar el archivo Excel.');
        }

        return response()->download($path, 'catalogo-productos-'.now()->format('Y-m-d').'.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    private function query(Request $request): Builder
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:180'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);
        $search = trim($filters['search'] ?? '');

        return Product::query()
            ->with(['brand', 'tone', 'unitMeasure', 'category', 'supplier'])
            ->when($search !== '', fn (Builder $query) => $query->where(fn (Builder $q) => $q
                ->where('name', 'like', '%'.$search.'%')
                ->orWhere('internal_code', 'like', '%'.$search.'%')
                ->orWhere('barcode', 'like', '%'.$search.'%')))
            ->when(!empty($filters['status']), fn (Builder $query) => $query->where('status', $filters['status']))
            ->orderBy('name')->orderBy('id');
    }

    private function values(Product $product): array
    {
        return [
            $product->internal_code ?? '', $product->name, $product->brand?->name ?? 'Sin marca',
            $product->tone?->name ?? 'Sin tono', $product->unitMeasure?->name ?? 'Sin unidad',
            $product->category?->name ?? 'Sin categoría', $product->supplier?->name ?? 'Sin proveedor',
            (int) $product->current_stock, (float) $product->purchase_price_usd,
            (float) $product->sale_price_usd, $product->status === 'active' ? 'Activo' : 'Inactivo',
        ];
    }

    private function stringCell(int $column, int $row, string $value, int $style = 0): string
    {
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $value);
        return '<c r="'.chr(65 + $column).$row.'" s="'.$style.'" t="inlineStr"><is><t xml:space="preserve">'.htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8').'</t></is></c>';
    }

    private function numberCell(int $column, int $row, float|int $value, int $style): string
    {
        return '<c r="'.chr(65 + $column).$row.'" s="'.$style.'"><v>'.$value.'</v></c>';
    }
}
