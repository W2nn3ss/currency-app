<?php

namespace App\Console\Commands;

use App\Jobs\SendActiveJob;
use Illuminate\Console\Command;

class SendActiveMessage extends Command
{
    protected $signature = 'send:active {date?} {currency?} {base_currency?}';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $date = $this->argument('date');
        $currency = $this->argument('currency');
        $baseCurrency = $this->argument('base_currency');

        dispatch(new SendActiveJob($date, $currency, $baseCurrency));

        $this->info('Сообщение "active" отправлено в первую очередь.');
    }
}
