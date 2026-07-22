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
            ->orderByDesc('rate_date')
            ->orderByDesc('rate_time')
            ->orderByDesc('id')
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

    public function create(
        BinanceRateService $binanceRateService
    ): View {
        $latestRate = ExchangeRate::query()
            ->where('status', 'active')
            ->orderByDesc('rate_date')
            ->orderByDesc('rate_time')
            ->orderByDesc('id')
            ->first();

        $binanceQuote = null;
        $binanceError = null;

        try {
            $binanceQuote = $binanceRateService
                ->getUsdtVesRate('BUY', 5);
        } catch (Throwable $e) {
            $binanceError = 'No se pudo consultar Binance en este momento. Puedes registrar una tasa BCV o manual.';
        }

        return view('exchange-rates.create', compact(
            'latestRate',
            'binanceQuote',
            'binanceError'
        ));
    }

    public function store(
        Request $request,
        BinanceRateService $binanceRateService
    ): RedirectResponse {
        $request->merge([
            'bcv_rate' => $this->normalizeDecimal(
                $request->input('bcv_rate')
            ),
            'binance_rate' => $this->normalizeDecimal(
                $request->input('binance_rate')
            ),
            'manual_rate' => $this->normalizeDecimal(
                $request->input('manual_rate')
            ),
        ]);

        $validated = $request->validate([
            'save_mode' => [
                'required',
                'in:create_new,update_existing',
            ],
            'rate_date' => ['required', 'date'],
            'rate_time' => ['nullable', 'date_format:H:i'],
            'bcv_rate' => [
                'nullable',
                'numeric',
                'min:0.01',
            ],
            'binance_rate' => [
                'nullable',
                'numeric',
                'min:0.01',
            ],
            'manual_rate' => [
                'nullable',
                'numeric',
                'min:0.01',
            ],
            'source' => [
                'required',
                'in:bcv,binance,manual',
            ],
            'status' => [
                'required',
                'in:active,inactive',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        /*
         * Cuando Binance es la fuente seleccionada, el valor se consulta
         * nuevamente al guardar. No se acepta como una tasa digitada.
         */
        if ($validated['source'] === 'binance') {
            try {
                $quote = $binanceRateService
                    ->getUsdtVesRate('BUY', 5);

                $validated['binance_rate'] = $quote['rate'];
            } catch (Throwable $e) {
                return back()
                    ->withErrors([
                        'binance_rate' => 'No se pudo obtener la tasa automática de Binance. Selecciona BCV o Manual para guardar el registro.',
                    ])
                    ->withInput();
            }
        }

        $usedRate = $this->resolveUsedRate(
            $validated['source'],
            $validated['bcv_rate'] ?? null,
            $validated['binance_rate'] ?? null,
            $validated['manual_rate'] ?? null
        );

        if (is_null($usedRate)) {
            return back()
                ->withErrors([
                    'source' => 'Debes registrar una tasa válida para la fuente seleccionada.',
                ])
                ->withInput();
        }

        $payload = [
            'rate_date' => $validated['rate_date'],
            'rate_time' => $validated['rate_time']
                ?? now()->format('H:i'),
            'bcv_rate' => $validated['bcv_rate'] ?? null,
            'binance_rate' => $validated['binance_rate']
                ?? null,
            'manual_rate' => $validated['manual_rate']
                ?? null,
            'used_rate' => round($usedRate, 4),
            'source' => $validated['source'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ];

        if ($validated['save_mode'] === 'update_existing') {
            $exchangeRate = ExchangeRate::query()
                ->whereDate(
                    'rate_date',
                    $validated['rate_date']
                )
                ->orderByDesc('rate_time')
                ->orderByDesc('id')
                ->first();

            if ($exchangeRate) {
                /*
                 * Si la corrección no utiliza Binance, se preserva la tasa
                 * automática que ya tenía el registro.
                 */
                if (
                    $validated['source'] !== 'binance' &&
                    $exchangeRate->binance_rate
                ) {
                    $payload['binance_rate'] =
                        $exchangeRate->binance_rate;
                }

                $exchangeRate->update($payload);

                return redirect()
                    ->route('exchange-rates.index')
                    ->with(
                        'success',
                        'Tasa de cambio corregida correctamente para la fecha seleccionada.'
                    );
            }
        }

        ExchangeRate::create($payload);

        return redirect()
            ->route('exchange-rates.index')
            ->with(
                'success',
                'Nueva tasa de cambio registrada correctamente.'
            );
    }

    public function edit(
        ExchangeRate $exchangeRate
    ): View {
        return view(
            'exchange-rates.edit',
            compact('exchangeRate')
        );
    }

    public function update(
        Request $request,
        ExchangeRate $exchangeRate
    ): RedirectResponse {
        $request->merge([
            'bcv_rate' => $this->normalizeDecimal(
                $request->input('bcv_rate')
            ),
            'manual_rate' => $this->normalizeDecimal(
                $request->input('manual_rate')
            ),
        ]);

        $validated = $request->validate([
            'rate_date' => ['required', 'date'],
            'rate_time' => ['nullable', 'date_format:H:i'],
            'bcv_rate' => [
                'nullable',
                'numeric',
                'min:0.01',
            ],
            'manual_rate' => [
                'nullable',
                'numeric',
                'min:0.01',
            ],
            'source' => [
                'required',
                'in:bcv,binance,manual',
            ],
            'status' => [
                'required',
                'in:active,inactive',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        /*
         * Binance se conserva exactamente como fue obtenida y guardada.
         * El formulario no puede sobrescribir este valor.
         */
        $binanceRate = $exchangeRate->binance_rate !== null
            ? (float) $exchangeRate->binance_rate
            : null;

        $usedRate = $this->resolveUsedRate(
            $validated['source'],
            $validated['bcv_rate'] ?? null,
            $binanceRate,
            $validated['manual_rate'] ?? null
        );

        if (is_null($usedRate)) {
            return back()
                ->withErrors([
                    'source' => 'La fuente seleccionada no tiene una tasa válida registrada.',
                ])
                ->withInput();
        }

        $exchangeRate->update([
            'rate_date' => $validated['rate_date'],
            'rate_time' => $validated['rate_time']
                ?? $exchangeRate->rate_time?->format('H:i')
                ?? now()->format('H:i'),
            'bcv_rate' => $validated['bcv_rate'] ?? null,
            'binance_rate' => $exchangeRate->binance_rate,
            'manual_rate' => $validated['manual_rate']
                ?? null,
            'used_rate' => round($usedRate, 4),
            'source' => $validated['source'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('exchange-rates.index')
            ->with(
                'success',
                'Tasa de cambio actualizada correctamente.'
            );
    }

    private function resolveUsedRate(
        string $source,
        mixed $bcvRate,
        mixed $binanceRate,
        mixed $manualRate
    ): ?float {
        $ratesBySource = [
            'bcv' => $bcvRate,
            'binance' => $binanceRate,
            'manual' => $manualRate,
        ];

        $usedRate = $ratesBySource[$source] ?? null;

        if (
            $usedRate === null ||
            $usedRate === '' ||
            ! is_numeric($usedRate) ||
            (float) $usedRate <= 0
        ) {
            return null;
        }

        return (float) $usedRate;
    }

    private function normalizeDecimal(
        mixed $value
    ): ?string {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $value = str_replace(
            ['$', 'Bs.', 'Bs', ' '],
            '',
            $value
        );

        $lastComma = strrpos($value, ',');
        $lastDot = strrpos($value, '.');

        if (
            $lastComma !== false &&
            $lastDot !== false
        ) {
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
