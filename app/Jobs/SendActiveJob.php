<?php

namespace App\Jobs;

use App\Services\CurrencyService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class SendActiveJob implements ShouldQueue
{
    use SerializesModels;

    private mixed $date;
    private mixed $currency;
    private mixed $baseCurrency;

    public function __construct($date = null, $currency = null, $baseCurrency = null)
    {
        $this->date = $date;
        $this->currency = $currency;
        $this->baseCurrency = $baseCurrency;
    }

    public function handle()
    {
        $service = app(CurrencyService::class);
        dispatch(new CountTo100Job('active', $service, $this->date, $this->currency, $this->baseCurrency));
    }
}
