<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExchangeRateController extends Controller
{
    public function index(): View
    {
        $exchangeRates = ExchangeRate::query()
            ->latest('rate_date')
            ->latest('id')
            ->get();

        $latestRate = $exchangeRates->first();

        $activeRates = $exchangeRates
            ->where('status', 'active')
            ->count();

        $averageUsedRate = $exchangeRates
            ->whereNotNull('used_rate')
            ->avg('used_rate');

        $binanceRates = $exchangeRates
            ->where('source', 'binance')
            ->count();

        $manualRates = $exchangeRates
            ->where('source', 'manual')
            ->count();

        return view('exchange-rates.index', compact(
            'exchangeRates',
            'latestRate',
            'activeRates',
            'averageUsedRate',
            'binanceRates',
            'manualRates'
        ));
    }

    public function create(): View
    {
        $latestRate = ExchangeRate::query()
            ->where('status', 'active')
            ->latest('rate_date')
            ->latest('id')
            ->first();

        return view('exchange-rates.create', compact('latestRate'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'rate_date' => ['required', 'date'],
            'bcv_rate' => ['nullable', 'numeric', 'min:0.01'],
            'binance_rate' => ['nullable', 'numeric', 'min:0.01'],
            'manual_rate' => ['nullable', 'numeric', 'min:0.01'],
            'source' => ['required', 'in:bcv,binance,manual'],
            'status' => ['required', 'in:active,inactive'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $ratesBySource = [
            'bcv' => $validated['bcv_rate'] ?? null,
            'binance' => $validated['binance_rate'] ?? null,
            'manual' => $validated['manual_rate'] ?? null,
        ];

        $usedRate = $ratesBySource[$validated['source']] ?? null;

        if (is_null($usedRate)) {
            return back()
                ->withErrors([
                    'source' => 'Debes registrar una tasa válida para la fuente seleccionada.',
                ])
                ->withInput();
        }

        ExchangeRate::create([
            'rate_date' => $validated['rate_date'],
            'bcv_rate' => $validated['bcv_rate'] ?? null,
            'binance_rate' => $validated['binance_rate'] ?? null,
            'manual_rate' => $validated['manual_rate'] ?? null,
            'used_rate' => round((float) $usedRate, 4),
            'source' => $validated['source'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('exchange-rates.index')
            ->with('success', 'Tasa de cambio registrada correctamente.');
    }
}
