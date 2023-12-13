<?php

namespace App\Http\Controllers;

use App\Service\MelhorEnvioVariables;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FreightController extends Controller
{
    public function calculate(Request $request)
    {
        DB::beginTransaction();

        try {
            // $data = $request->validated();

            $client = new Client();
            $response = $client->request('POST', MelhorEnvioVariables::$url . '/me/shipment/calculate', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . MelhorEnvioVariables::$token,
                ],
                'json' => [
                    "from" => [
                        "postal_code" => "15706086"
                    ],
                    "to" => [
                        "postal_code" => "04884992"
                    ],
                    "products" => [
                        [
                            "id" => "x",
                            "width" => 1,
                            "height" => 1,
                            "length" => 1,
                            "weight" => 0.1,
                            "insurance_value" => 10,
                            "quantity" => 1
                        ]
                    ],
                    "options" => [
                        "receipt" => false,
                        "own_hand" => false
                    ]
                ],
                'http_errors' => false,
            ]);

            $response = json_decode($response->getBody());

            DB::commit();
            return response()->json([
                'prices' => $response
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error.',
                'Exception' => $e,
            ]);
        }
    }
}
