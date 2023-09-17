<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;

class CollectCurrencyData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    protected $date;
    protected $rate;

    public function __construct($date, $rate)
    {
        $this->date = $date;
        $this->rate = $rate;
    }

    public function handle()
    {
        // Логика обработки данных и записи в Redis
        $data = Redis::get('currency_data');
        $data = json_decode($data, true) ?? [];

        $data[$this->date] = $this->rate;

        Redis::set('currency_data', json_encode($data));
    }
}
