<?php

namespace App\Services;

use App\Repositories\CurrencyRateRepository;
use App\Repositories\HistoricalCurrencyRateRepository;
use App\Services\CurrencyApiService;

class CurrencyDataProcessor
{
    private CurrencyRateRepository $rateRepository;
    private HistoricalCurrencyRateRepository $historicalRateRepository;
    private CurrencyApiService $apiService;

    public function __construct(
        CurrencyRateRepository $rateRepository,
        HistoricalCurrencyRateRepository $historicalRateRepository,
        CurrencyApiService $apiService
    )
    {
        $this->rateRepository = $rateRepository;
        $this->historicalRateRepository = $historicalRateRepository;
        $this->apiService = $apiService;
    }

    public function processCurrencyData($date, $currencyCode, $table, $startDate = '', $endDate = ''): ?array
    {
        if ($table == 'currency_rates')
        {
            $existingRate = $this->rateRepository->findRateByDateAndCurrency($date, $currencyCode);
        }
        else
        {
            $existingRate = $this->historicalRateRepository->findRateByDateAndCurrency($date, $currencyCode);
        }

        if ($existingRate !== null)
        {
            return [
                'date' => $date,
                'currency_code' => $currencyCode,
                'rate' => $existingRate->rate,
                'previous_rate' => $existingRate->previous_rate,
                'rate_difference' => $existingRate->rate_difference,
            ];
        }

        $currentRate = $this->apiService->fetchData($date, $currencyCode);

        if ($currentRate === null)
        {
            return null;
        }

        $previousDate = $this->getPreviousBusinessDay($date);
        $previousRate = $this->apiService->fetchData($previousDate, $currencyCode);

        $currentRate = $this->apiService->fetchData($date, $currencyCode);
        $difference = null;

        if (is_numeric($previousRate) && is_numeric($currentRate))
        {
            if ($previousRate !== null && $currentRate !== null)
            {
                if ($previousRate == 0 || $currentRate == 0)
                {
                    $difference = 0;
                }
                else
                {
                    $difference = round($currentRate - $previousRate, 4);
                }
            }
        }


        if ($table == 'currency_rates')
        {
            $this->rateRepository->createOrUpdateRate($date, $currencyCode, $currentRate, $previousRate, $difference);
        }
        else
        {
            $this->historicalRateRepository->createOrUpdateRate($startDate, $endDate, $date, $currencyCode, $currentRate, $previousRate, $difference);
        }

        return [
            'date' => $date,
            'currency_code' => $currencyCode,
            'rate' => $currentRate,
            'previous_rate' => $previousRate,
            'rate_difference' => $difference,
        ];
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
