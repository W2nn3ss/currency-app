<?php

namespace App\Jobs;

use App\Services\CurrencyService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

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
        $endDate = !empty($this->date) ? now()->parse($this->date) : now()->subDays(1); // Используем текущую дату или дату из параметра
        $startDate = $endDate->copy()->subDays(179);

        $currencyCode = !empty($this->currency) ? $this->currency : 'USD';

        $data = [];

        while ($endDate >= $startDate) {
            $date = $endDate->format('Y-m-d');
            $rate = $this->service->fetchData($date, $currencyCode);

            if ($rate !== null) {
                $data[$date] = $rate;
            }

            $endDate->subDay();
        }

        Cache::put('currency_data_'.$endDate.'_'.$startDate, json_encode($data), 3600);

        \Log::info('Currency data has been fetched and sent to Redis.');
    }
}
