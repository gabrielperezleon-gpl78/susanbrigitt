<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use App\Services\BinanceRateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

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

    public function create(BinanceRateService $binanceRateService): View
    {
        $latestRate = ExchangeRate::query()
            ->where('status', 'active')
            ->latest('rate_date')
            ->latest('id')
            ->first();

        $binanceQuote = null;
        $binanceError = null;

        try {
            $binanceQuote = $binanceRateService->getUsdtVesRate('BUY', 5);
        } catch (Throwable $e) {
            $binanceError = 'No se pudo consultar Binance en este momento. Puedes ingresar la tasa manualmente.';
        }

        return view('exchange-rates.create', compact(
            'latestRate',
            'binanceQuote',
            'binanceError'
        ));
    }

    public function store(Request $request, BinanceRateService $binanceRateService): RedirectResponse
    {
        $request->merge([
            'bcv_rate' => $this->normalizeDecimal($request->input('bcv_rate')),
            'binance_rate' => $this->normalizeDecimal($request->input('binance_rate')),
            'manual_rate' => $this->normalizeDecimal($request->input('manual_rate')),
        ]);

        $validated = $request->validate([
            'save_mode' => ['required', 'in:create_new,update_existing'],
            'rate_date' => ['required', 'date'],
            'rate_time' => ['nullable', 'date_format:H:i'],
            'bcv_rate' => ['nullable', 'numeric', 'min:0.01'],
            'binance_rate' => ['nullable', 'numeric', 'min:0.01'],
            'manual_rate' => ['nullable', 'numeric', 'min:0.01'],
            'source' => ['required', 'in:bcv,binance,manual'],
            'status' => ['required', 'in:active,inactive'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validated['source'] === 'binance' && empty($validated['binance_rate'])) {
            try {
                $quote = $binanceRateService->getUsdtVesRate('BUY', 5);
                $validated['binance_rate'] = $quote['rate'];
            } catch (Throwable $e) {
                return back()
                    ->withErrors([
                        'binance_rate' => 'No se pudo obtener la tasa Binance. Ingresa una tasa manualmente.',
                    ])
                    ->withInput();
            }
        }

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

        $payload = [
            'rate_date' => $validated['rate_date'],
            'rate_time' => $validated['rate_time'] ?? now()->format('H:i'),
            'bcv_rate' => $validated['bcv_rate'] ?? null,
            'binance_rate' => $validated['binance_rate'] ?? null,
            'manual_rate' => $validated['manual_rate'] ?? null,
            'used_rate' => round((float) $usedRate, 4),
            'source' => $validated['source'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ];

        if ($validated['save_mode'] === 'update_existing') {
            $exchangeRate = ExchangeRate::query()
                ->whereDate('rate_date', $validated['rate_date'])
                ->latest('id')
                ->first();

            if ($exchangeRate) {
                $exchangeRate->update($payload);

                return redirect()
                    ->route('exchange-rates.index')
                    ->with('success', 'Tasa de cambio corregida correctamente para la fecha seleccionada.');
            }
        }

        ExchangeRate::create($payload);

        return redirect()
            ->route('exchange-rates.index')
            ->with('success', 'Nueva tasa de cambio registrada correctamente.');
    }

    private function normalizeDecimal(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        if ($value === '') {
            return null;
        }

        $value = str_replace(['$', 'Bs.', 'Bs', ' '], '', $value);

        $lastComma = strrpos($value, ',');
        $lastDot = strrpos($value, '.');

        if ($lastComma !== false && $lastDot !== false) {
            if ($lastComma > $lastDot) {
                $value = str_replace('.', '', $value);
                $value = str_replace(',', '.', $value);
            } else {
                $value = str_replace(',', '', $value);
            }
        } elseif ($lastComma !== false) {
            $value = str_replace(',', '.', $value);
        }

        return $value;
    }
}
