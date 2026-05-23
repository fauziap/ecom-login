<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RajaOngkirController extends Controller
{
    public function getProvinces()
    {
        $response = Http::timeout(30)
            ->withHeaders([
                'key' => env('RAJAONGKIR_API_KEY')
            ])
            ->get(env('RAJAONGKIR_BASE_URL') . '/destination/province');

        return response()->json($response->json());
    }

    public function getCities(Request $request)
    {
        $provinceId = $request->province_id;

        $response = Http::timeout(30)
            ->withHeaders([
                'key' => env('RAJAONGKIR_API_KEY')
            ])
            ->get(env('RAJAONGKIR_BASE_URL') . '/destination/city/' . $provinceId);

        return response()->json($response->json());
    }

    public function getCost(Request $request)
{
    $response = Http::timeout(30)
        ->withHeaders([
            'key' => env('RAJAONGKIR_API_KEY'),
            'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])
        ->asForm()
        ->post(env('RAJAONGKIR_BASE_URL') . '/calculate/domestic-cost', [
            'origin' => $request->origin,
            'destination' => $request->destination,
            'weight' => (int) $request->weight,
            'courier' => strtolower($request->courier),
        ]);

    return response()->json($response->json());
}
}
