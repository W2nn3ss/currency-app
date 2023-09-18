<?php

namespace App\Repositories;

use App\Models\HistoricalCurrencyRate;
class HistoricalCurrencyRateRepository
{
    public function findRateByDateAndCurrency($date, $currencyCode): HistoricalCurrencyRate
    {
        return HistoricalCurrencyRate::where('data_date', $date)
            ->where('currency_code', $currencyCode)
            ->first();
    }

    public function createOrUpdateRate($startDate, $endDate, $date, $currencyCode, $rate, $previousRate, $rateDifference): HistoricalCurrencyRate
    {
        return HistoricalCurrencyRate::updateOrCreate(
            [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'data_date' => $date,
                'currency_code' => $currencyCode,
            ],
            [
                'rate' => $rate,
                'previous_rate' => $previousRate,
                'rate_difference' => $rateDifference,
            ]
        );
    }
}
