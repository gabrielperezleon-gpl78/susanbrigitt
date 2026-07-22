<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class BinanceRateService
{
    private const ENDPOINT = 'https://p2p.binance.com/bapi/c2c/v2/friendly/c2c/adv/search';

    public function getUsdtVesRate(string $tradeType = 'BUY', int $rows = 5): array
    {
        $response = Http::timeout(15)
            ->acceptJson()
            ->asJson()
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0',
            ])
            ->post(self::ENDPOINT, [
                'asset' => 'USDT',
                'fiat' => 'VES',
                'tradeType' => strtoupper($tradeType),
                'page' => 1,
                'rows' => $rows,
                'payTypes' => [],
                'countries' => [],
                'publisherType' => null,
                'proMerchantAds' => false,
                'shieldMerchantAds' => false,
            ]);

        if ($response->failed()) {
            throw new RuntimeException('No se pudo consultar Binance P2P.');
        }

        $items = $response->json('data', []);

        $prices = collect($items)
            ->map(fn($item) => (float) data_get($item, 'adv.price'))
            ->filter(fn($price) => $price > 0)
            ->values();

        if ($prices->isEmpty()) {
            throw new RuntimeException('Binance no devolvió precios válidos para USDT/VES.');
        }

        $sortedPrices = $prices->sort()->values();
        $middle = intdiv($sortedPrices->count(), 2);

        $median = $sortedPrices->count() % 2
            ? $sortedPrices[$middle]
            : ($sortedPrices[$middle - 1] + $sortedPrices[$middle]) / 2;

        return [
            'rate' => round((float) $median, 4),
            'average' => round((float) $prices->avg(), 4),
            'min' => round((float) $prices->min(), 4),
            'max' => round((float) $prices->max(), 4),
            'count' => $prices->count(),
            'trade_type' => strtoupper($tradeType),
            'fetched_at' => now()->format('d/m/Y H:i'),
        ];
    }
}
