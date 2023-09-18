<?php

namespace App\Jobs;

use App\Models\HistoricalCurrencyRate;
use App\Services\CurrencyService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class CountTo100Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $message;

    private CurrencyService $service;

    private mixed $date;
    private mixed $currency;
    private mixed $baseCurrency;

    public function __construct($message, CurrencyService $service, $date, $currency, $baseCurrency)
    {
        $this->message = $message;
        $this->service = $service;
        $this->date = $date;
        $this->currency = $currency;
        $this->baseCurrency = $baseCurrency;
    }

    public function handle()
    {
        $currentDate = !empty($this->date) ? now()->parse($this->date) : now()->subDays(1);
        $startDate = $currentDate->copy()->subDays(179);
        $endDate = $startDate->copy()->addDays(179);

        $currencyCode = !empty($this->currency) ? $this->currency : 'USD';

        $baseCurrency = !empty($this->baseCurrency) ? $this->baseCurrency : 'RUR';

        while ($currentDate >= $startDate)
        {
            $dataDate = $currentDate->format('Y-m-d');

            $rate = $this->service->getData($dataDate, $currencyCode, $baseCurrency, 'historical_currency_rates', $endDate, $startDate);

            $currentDate->subDay();
        }
    }
}
