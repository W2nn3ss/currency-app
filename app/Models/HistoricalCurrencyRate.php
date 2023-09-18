<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricalCurrencyRate extends Model
{
    use HasFactory;

    protected $table = 'historical_currency_rates';

    protected $fillable = [
        'start_date',
        'end_date',
        'data_date',
        'currency_code',
        'rate',
        'previous_rate',
        'rate_difference',
    ];
}
