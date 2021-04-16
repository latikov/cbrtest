<?php


namespace App\Http\Controllers;


use App\Http\Requests\SavePresetCommentRequest;
use App\Http\Requests\SavePresetRequest;
use App\Http\Requests\RatesRequest;
use App\Models\Preset;
use App\Services\CbrRuService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    public function rates(RatesRequest $request, CbrRuService $cbrRuService)
    {
        $day = $request->get('date');
        $rates = $cbrRuService->fetchByDay($day);

        $presetName = $request->get('preset');
        if($presetName){
            $preset = Preset::query()->where('key', $presetName)->firstOrFail();
            $requestCodes = $preset->codes;
        } else {
            $requestCodes = $request->get('codes');
        }

        if($requestCodes){
            $requestCodes = array_map('strtoupper', $requestCodes);

            $selectedRates = array_filter(
                $rates,
                fn($code) => in_array($code, $requestCodes),
                ARRAY_FILTER_USE_KEY
            );
        } else {
            $selectedRates = $rates;
        }


        return [
            'rates' => $selectedRates
        ];
    }

    public function savePreset(SavePresetRequest $request)
    {
        $requestCodes = $request->get('codes');
        $preset = new Preset([
            'key' => Str::random(16),
            'codes' => $requestCodes
        ]);
        $preset->save();
        return [
            'preset' => $preset->toArray()
        ];
    }

    public function savePresetComment(SavePresetCommentRequest $request)
    {
        $preset = Preset::query()->where('key',$request->get('preset'))->firstOrFail();
        $preset->fill(
            $request->only(['comment'])
        )->save();
        return [
            'preset' => $preset->toArray()
        ];
    }
}