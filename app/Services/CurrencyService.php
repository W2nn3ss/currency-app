<?php

namespace App\Services;

use App\Repositories\CurrencyRateRepository;
use App\Repositories\HistoricalCurrencyRateRepository;

class CurrencyService
{
    private CacheService $cacheService;
    private CurrencyDataProcessor $dataProcessor;
    private $rateRepository;
    private $historicalRateRepository;

    public function __construct(
        CacheService $cacheService,
        CurrencyDataProcessor $dataProcessor,
        CurrencyRateRepository $rateRepository,
        HistoricalCurrencyRateRepository $historicalRateRepository
    ) {
        $this->cacheService = $cacheService;
        $this->dataProcessor = $dataProcessor;
        $this->rateRepository = $rateRepository;
        $this->historicalRateRepository = $historicalRateRepository;
    }

    public function getData($date, $currencyCode, $baseCurrencyCode, $table, $startDate = '', $endDate = ''): bool|\Illuminate\Http\JsonResponse|string
    {
        $currentRateKey = "currency:{$currencyCode}:{$date}";
        $differenceKey = "currency:{$currencyCode}:{$date}:difference";

        if ($this->cacheService->has($currentRateKey) && $this->cacheService->has($differenceKey))
        {
            $currentRate = $this->cacheService->get($currentRateKey);
            $difference = $this->cacheService->get($differenceKey);
        }
        else
        {
            $result = $this->dataProcessor->processCurrencyData($date, $currencyCode, $table, $startDate, $endDate);

            if ($result === null) {
                return response()->json(['error' => 'Currency not found'], 404);
            }

            $currentRate = $result['rate'];
            $difference = $result['rate_difference'];

            $this->cacheService->put($currentRateKey, $currentRate, 3600);
            $this->cacheService->put($differenceKey, $difference, 3600);
        }

        return json_encode([
            'date' => $date,
            'currency_code' => $currencyCode,
            'base_currency_code' => $baseCurrencyCode,
            'rate' => $currentRate,
            'difference' => $difference,
        ]);
    }
}
