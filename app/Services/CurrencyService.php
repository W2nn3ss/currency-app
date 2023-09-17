<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CurrencyService
{
    private CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function getData($date, $currencyCode, $baseCurrencyCode): bool|string
    {
        $currentRateKey = "currency:{$currencyCode}:{$date}";
        $differenceKey = "currency:{$currencyCode}:{$date}:difference";

        if ($this->cacheService->has($currentRateKey) && $this->cacheService->has($differenceKey))
        {
            $currentRate = $this->cacheService->get($currentRateKey);
            $difference = $this->cacheService->get($differenceKey);
            $isCache = true;
        }
        else
        {
            $currentRate = $this->fetchData($date, $currencyCode);

            if ($currentRate === null) {
                return response()->json(['error' => 'Currency not found'], 404);
            }

            $previousDate = $this->getPreviousBusinessDay($date);
            $previousRate = $this->fetchData($previousDate, $currencyCode);

            $difference = $previousRate !== null ? round($currentRate - $previousRate, 4) : null;

            $this->cacheService->put($currentRateKey, $currentRate, 3600);
            $this->cacheService->put($differenceKey, $difference, 3600);
            $isCache = false;
        }
        return json_encode([
            'date' => $date,
            'currency_code' => $currencyCode,
            'base_currency_code' => $baseCurrencyCode,
            'rate' => $currentRate,
            'difference' => $difference,
            'is_cache' => $isCache
        ]);
    }

    public function fetchData($date, $currencyCode): ?float
    {
        $formattedDate = date('d/m/Y', strtotime($date));

        $url = "http://cbr.ru/scripts/XML_daily.asp?date_req={$formattedDate}";

        $response = Http::get($url);

        if ($response->successful())
        {
            $xmlData = simplexml_load_string($response->body());
            foreach ($xmlData->Valute as $valute)
            {
                if ((string) $valute->CharCode === $currencyCode)
                {
                    return (float) str_replace(',', '.', (string) $valute->Value);
                }
            }
        }

        return null;
    }

    private function getPreviousBusinessDay($date): string
    {
        $date = date_create($date);
        date_modify($date, '-1 day');

        if (date_format($date, 'N') >= 6)
        {
            date_modify($date, '-1 day');
        }

        return date_format($date, 'Y-m-d');
    }
}
