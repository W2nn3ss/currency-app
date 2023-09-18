<?php

namespace App\Console\Commands;

use App\Services\CacheService;
use Illuminate\Console\Command;
use App\Repositories\HistoricalCurrencyRateRepository;

class GetHistoricalCurrencyData extends Command
{
    protected $signature = 'currency:historical-data {date} {currency}';

    protected $description = 'Получение значений из таблицы historical_currency_rates';

    private CacheService $cacheService;
    private HistoricalCurrencyRateRepository $historicalRateRepository;

    public function __construct(CacheService $cacheService, HistoricalCurrencyRateRepository $historicalRateRepository)
    {
        parent::__construct();
        $this->cacheService = $cacheService;
        $this->historicalRateRepository = $historicalRateRepository;
    }

    public function handle()
    {
        $date = $this->argument('date');
        $currencyCode = $this->argument('currency');

        $currentRateKey = "currency:{$currencyCode}:{$date}";
        $differenceKey = "currency:{$currencyCode}:{$date}:difference";

        $cachedData = [
            'date' => $this->cacheService->get($date),
            'rate' => $this->cacheService->get($currentRateKey),
            'difference' => $this->cacheService->get($differenceKey),
        ];

        if ($cachedData['rate'] !== null && $cachedData['difference'] !== null)
        {
            $this->info(json_encode($cachedData));
            return;
        }

        $historicalData = $this->historicalRateRepository->findRateByDateAndCurrency($date, $currencyCode);

        if ($historicalData)
        {
            $data = [
                'date' => $date,
                'currency_code' => $currencyCode,
                'rate' => $historicalData->rate,
                'previous_rate' => $historicalData->previous_rate,
                'rate_difference' => $historicalData->rate_difference,
            ];

            $json = json_encode($data);
            $this->info($json);

            $this->cacheService->put($currentRateKey, $data, 3600);
            $this->cacheService->put($differenceKey, $data['rate_difference'], 3600);
        }
        else
        {
            $this->error('Данные не были найдены');
        }
    }
}
