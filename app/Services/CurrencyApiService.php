<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CurrencyApiService
{
    public function fetchData($date, $currencyCode): ?float
    {
        $formattedDate = date('d/m/Y', strtotime($date));

        $url = "http://cbr.ru/scripts/XML_daily.asp?date_req={$formattedDate}";

        $response = Http::get($url);

        if ($response->successful())
        {
            $xmlData = simplexml_load_string($response->body());
            foreach ($xmlData->Valute as $valute)
            {
                if ((string) $valute->CharCode === $currencyCode)
                {
                    return (float) str_replace(',', '.', (string) $valute->Value);
                }
            }
        }

        return null;
    }
}
