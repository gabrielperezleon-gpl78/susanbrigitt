<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Tone;
use App\Models\UnitMeasure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(): View
    {
        $suppliers = Supplier::query()
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        $brands = Brand::query()
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        $categories = Category::query()
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        $tones = Tone::query()
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        $unitMeasures = UnitMeasure::query()
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        return view('catalogs.index', compact(
            'suppliers',
            'brands',
            'categories',
            'tones',
            'unitMeasures'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Proveedores
    |--------------------------------------------------------------------------
    */

    public function storeSupplier(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:180',
                'unique:suppliers,name',
            ],
            'contact_name' => ['nullable', 'string', 'max:180'],
            'phone' => ['nullable', 'string', 'max:80'],
            'email' => ['nullable', 'email', 'max:180'],
            'address' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        Supplier::create([
            'name' => $validated['name'],
            'contact_name' => $validated['contact_name'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'is_active' => true,
        ]);

        return redirect()
            ->route('catalogs.index')
            ->with('success', 'Proveedor registrado correctamente.');
    }

    public function editSupplier(Supplier $supplier): View
    {
        return view('catalogs.edit-supplier', compact('supplier'));
    }

    public function updateSupplier(
        Request $request,
        Supplier $supplier
    ): RedirectResponse {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:180',
                'unique:suppliers,name,' . $supplier->id,
            ],
            'contact_name' => ['nullable', 'string', 'max:180'],
            'phone' => ['nullable', 'string', 'max:80'],
            'email' => ['nullable', 'email', 'max:180'],
            'address' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['required', 'boolean'],
        ]);

        $supplier->update([
            'name' => $validated['name'],
            'contact_name' => $validated['contact_name'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'is_active' => (bool) $validated['is_active'],
        ]);

        return redirect()
            ->route('catalogs.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    /*
    |--------------------------------------------------------------------------
    | Marcas
    |--------------------------------------------------------------------------
    */

    public function storeBrand(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:180',
                'unique:brands,name',
            ],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        Brand::create([
            'name' => $validated['name'],
            'slug' => $this->generateUniqueSlug(
                Brand::class,
                $validated['name']
            ),
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        return redirect()
            ->route('catalogs.index')
            ->with('success', 'Marca registrada correctamente.');
    }

    public function editBrand(Brand $brand): View
    {
        return view('catalogs.edit-brand', compact('brand'));
    }

    public function updateBrand(
        Request $request,
        Brand $brand
    ): RedirectResponse {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:180',
                'unique:brands,name,' . $brand->id,
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['required', 'boolean'],
        ]);

        $brand->update([
            'name' => $validated['name'],
            'slug' => $this->generateUniqueSlug(
                Brand::class,
                $validated['name'],
                $brand->id
            ),
            'description' => $validated['description'] ?? null,
            'is_active' => (bool) $validated['is_active'],
        ]);

        return redirect()
            ->route('catalogs.index')
            ->with('success', 'Marca actualizada correctamente.');
    }

    /*
    |--------------------------------------------------------------------------
    | Categorías
    |--------------------------------------------------------------------------
    */

    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:180',
                'unique:categories,name',
            ],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        Category::create([
            'name' => $validated['name'],
            'slug' => $this->generateUniqueSlug(
                Category::class,
                $validated['name']
            ),
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        return redirect()
            ->route('catalogs.index')
            ->with('success', 'Categoría registrada correctamente.');
    }

    public function editCategory(Category $category): View
    {
        return view('catalogs.edit-category', compact('category'));
    }

    public function updateCategory(
        Request $request,
        Category $category
    ): RedirectResponse {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:180',
                'unique:categories,name,' . $category->id,
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['required', 'boolean'],
        ]);

        $category->update([
            'name' => $validated['name'],
            'slug' => $this->generateUniqueSlug(
                Category::class,
                $validated['name'],
                $category->id
            ),
            'description' => $validated['description'] ?? null,
            'is_active' => (bool) $validated['is_active'],
        ]);

        return redirect()
            ->route('catalogs.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    /*
    |--------------------------------------------------------------------------
    | Tonos y colores
    |--------------------------------------------------------------------------
    */

    public function storeTone(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:180',
                'unique:tones,name',
            ],
            'hex_color' => [
                'nullable',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
        ], [
            'hex_color.regex' => 'El color debe tener un formato hexadecimal válido, por ejemplo #E46F8A.',
        ]);

        Tone::create([
            'name' => $validated['name'],
            'slug' => $this->generateUniqueSlug(
                Tone::class,
                $validated['name']
            ),
            'hex_color' => isset($validated['hex_color'])
                ? strtoupper($validated['hex_color'])
                : null,
            'is_active' => true,
        ]);

        return redirect()
            ->route('catalogs.index')
            ->with('success', 'Tono registrado correctamente.');
    }

    public function editTone(Tone $tone): View
    {
        return view('catalogs.edit-tone', compact('tone'));
    }

    public function updateTone(
        Request $request,
        Tone $tone
    ): RedirectResponse {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:180',
                'unique:tones,name,' . $tone->id,
            ],
            'hex_color' => [
                'nullable',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
            'is_active' => ['required', 'boolean'],
        ], [
            'hex_color.regex' => 'El color debe tener un formato hexadecimal válido, por ejemplo #E46F8A.',
        ]);

        $tone->update([
            'name' => $validated['name'],
            'slug' => $this->generateUniqueSlug(
                Tone::class,
                $validated['name'],
                $tone->id
            ),
            'hex_color' => isset($validated['hex_color'])
                ? strtoupper($validated['hex_color'])
                : null,
            'is_active' => (bool) $validated['is_active'],
        ]);

        return redirect()
            ->route('catalogs.index')
            ->with('success', 'Tono actualizado correctamente.');
    }

    /*
    |--------------------------------------------------------------------------
    | Unidades de medida
    |--------------------------------------------------------------------------
    */

    public function storeUnitMeasure(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:120',
                'unique:unit_measures,name',
            ],
            'abbreviation' => ['nullable', 'string', 'max:20'],
        ]);

        UnitMeasure::create([
            'name' => $validated['name'],
            'slug' => $this->generateUniqueSlug(
                UnitMeasure::class,
                $validated['name']
            ),
            'abbreviation' => $validated['abbreviation'] ?? null,
            'is_active' => true,
        ]);

        return redirect()
            ->route('catalogs.index')
            ->with('success', 'Unidad de medida registrada correctamente.');
    }

    public function editUnitMeasure(UnitMeasure $unitMeasure): View
    {
        return view(
            'catalogs.edit-unit-measure',
            compact('unitMeasure')
        );
    }

    public function updateUnitMeasure(
        Request $request,
        UnitMeasure $unitMeasure
    ): RedirectResponse {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:120',
                'unique:unit_measures,name,' . $unitMeasure->id,
            ],
            'abbreviation' => ['nullable', 'string', 'max:20'],
            'is_active' => ['required', 'boolean'],
        ]);

        $unitMeasure->update([
            'name' => $validated['name'],
            'slug' => $this->generateUniqueSlug(
                UnitMeasure::class,
                $validated['name'],
                $unitMeasure->id
            ),
            'abbreviation' => $validated['abbreviation'] ?? null,
            'is_active' => (bool) $validated['is_active'],
        ]);

        return redirect()
            ->route('catalogs.index')
            ->with('success', 'Unidad de medida actualizada correctamente.');
    }

    private function generateUniqueSlug(
        string $modelClass,
        string $name,
        ?int $ignoreId = null
    ): string {
        $baseSlug = Str::slug($name);

        if ($baseSlug === '') {
            $baseSlug = 'registro';
        }

        $slug = $baseSlug;
        $counter = 2;

        while (
            $modelClass::query()
            ->where('slug', $slug)
            ->when(
                $ignoreId,
                fn($query) => $query->where(
                    'id',
                    '!=',
                    $ignoreId
                )
            )
            ->exists()
        ) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
