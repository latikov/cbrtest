<?php


namespace App\Services;


use App\Models\CbrCache;
use http\Exception\RuntimeException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class CbrRuService
{
    protected const CBR_RU_ENDPOINT = 'https://www.cbr.ru/scripts/XML_daily.asp';

    /**
     * Request rates array from cbr.ru
     * @param Carbon $carbonDate
     * @return array
     * @throws \Exception
     */
    protected function requestCbrRu(Carbon $carbonDate) : array
    {
        $response = Http::get(self::CBR_RU_ENDPOINT, [
            'date_req' => $carbonDate->format('d/m/Y')
        ]);

        if($response->failed()){
            throw new RuntimeException("Cbr.ru access error");
        }

        $dayData = [];

        $simpleXml = new \SimpleXMLElement($response->body());

        foreach ($simpleXml->Valute as $valuteXml){
            $dayData[(string)$valuteXml->CharCode] = (string)$valuteXml->Value;
        }

        return $dayData;
    }

    /**
     * Return rates array for $day using db caching
     * @param string $day
     * @return array
     */
    public function fetchByDay(string $day) : array
    {
        $carbonDate = Carbon::createFromFormat("Y-m-d", $day);
        $cbrCache = CbrCache::query()
            ->where('day', $carbonDate->toDateString())
            ->first();
        if($cbrCache){
            $rates = $cbrCache->rates;
        } else {
            $rates = $this->requestCbrRu($carbonDate);
            (new CbrCache([
                'day' => $day,
                'rates' => $rates
            ]))->save();
        }
        return $rates;
    }
}