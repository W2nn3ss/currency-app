<?php

namespace App\Console\Commands;

use App\Services\CurrencyService;
use Illuminate\Console\Command;

class getCurrency extends Command
{
    protected $signature = 'send:get_currency {date} {currency} {base_currency?}';

    protected $description = 'Получения курса валют';

    private CurrencyService $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        parent::__construct();
        $this->currencyService = $currencyService;
    }

    public function handle()
    {
        $date = $this->argument('date');
        $currency = $this->argument('currency');
        $baseCurrency = $this->argument('base_currency');

        $result = $this->currencyService->getData($date, $currency, $baseCurrency);

        $this->info($result);
    }
}
