<?php

namespace App\Repositories;

use App\Models\CurrencyRate;
class CurrencyRateRepository
{
    public function findRateByDateAndCurrency($date, $currencyCode): CurrencyRate|null
    {
        return CurrencyRate::where('date', $date)
            ->where('currency_code', $currencyCode)
            ->first();
    }

    public function createOrUpdateRate($date, $currencyCode, $rate, $previousRate, $rateDifference): CurrencyRate|null
    {
        return CurrencyRate::updateOrCreate(
            ['date' => $date, 'currency_code' => $currencyCode],
            [
                'rate' => $rate,
                'previous_rate' => $previousRate,
                'rate_difference' => $rateDifference,
            ]
        );
    }
}
