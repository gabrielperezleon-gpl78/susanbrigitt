<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Supplier;
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

        $unitMeasures = UnitMeasure::query()
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        return view('catalogs.index', compact(
            'suppliers',
            'brands',
            'unitMeasures'
        ));
    }

    public function storeSupplier(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:180', 'unique:suppliers,name'],
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

    public function storeBrand(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:180', 'unique:brands,name'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        Brand::create([
            'name' => $validated['name'],
            'slug' => $this->generateUniqueSlug(Brand::class, $validated['name']),
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        return redirect()
            ->route('catalogs.index')
            ->with('success', 'Marca registrada correctamente.');
    }

    public function storeUnitMeasure(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:unit_measures,name'],
            'abbreviation' => ['nullable', 'string', 'max:20'],
        ]);

        UnitMeasure::create([
            'name' => $validated['name'],
            'slug' => $this->generateUniqueSlug(UnitMeasure::class, $validated['name']),
            'abbreviation' => $validated['abbreviation'] ?? null,
            'is_active' => true,
        ]);

        return redirect()
            ->route('catalogs.index')
            ->with('success', 'Unidad de medida registrada correctamente.');
    }

    private function generateUniqueSlug(string $modelClass, string $name): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 2;

        while ($modelClass::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
