<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CurrencyService;

class CurrencyController extends Controller
{
    private CurrencyService $service;
    public function __construct(CurrencyService $service)
    {
        $this->service = $service;
    }

    public function getExchangeRate(Request $request, $date, $currencyCode, $baseCurrencyCode = 'RUR')
    {
        $currency = $this->service->getData($date, $currencyCode, $baseCurrencyCode, 'currency_rates');

        return response()->json($currency);
    }
}

