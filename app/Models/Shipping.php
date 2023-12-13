<?php

namespace App\Models;

use App\Service\MelhorEnvioVariables;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipping extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    public static function calculate($from, $to, $serviceId, $products)
    {
        $client = new Client();

        $response = $client->request('POST', MelhorEnvioVariables::$url . '/me/cart', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . MelhorEnvioVariables::$token,
                'User-Agent' => MelhorEnvioVariables::$suport,
            ],
            'json' => [
                "from" =>  $from,
                "to" =>  $to,
                "options" => [
                    "receipt" => false,
                    "own_hand" => false,
                    "reverse" => false,
                    "non_commercial" => false,
                    "invoice" => [
                        "key" => "53210602887418000198550170000001061000001079",
                    ],
                    "insurance_value" => 20,
                ],
                "service" => $serviceId,
                "products" => $products,
                // "agency" => 1,
                // "products" => [
                //     [
                //         "name" => "Omeprazol",
                //         "quantity" => "1",
                //         "unitary_value" => "10"
                //     ],
                //     [
                //         "name" => "Dipirona",
                //         "quantity" => "1",
                //         "unitary_value" => "10"
                //     ]
                // ],
                "volumes" => [
                    [
                        "height" => 20,
                        "width" => 20,
                        "length" => 20,
                        "weight" => 1
                    ]
                ]
            ],
            'http_errors' => false,
        ]);

        $response = json_decode($response->getBody());

        $error = $response->errors ?? $response->error ?? null;

        if ($error || !$response) {
            throw new Exception("Error in shipping calculation: " . json_encode($error));
        }

        return $response;
    }

    // -------------------------*-------------------------
    public static function checkout($shippingIds)
    {
        $client = new Client();

        $response = $client->request('POST', MelhorEnvioVariables::$url . '/me/shipment/checkout', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . MelhorEnvioVariables::$token,
                'User-Agent' => MelhorEnvioVariables::$suport,
            ],
            'json' => [
                "orders" => $shippingIds,
            ],
            'http_errors' => false,
        ]);

        $response = json_decode($response->getBody());

        $error = $response->errors ?? $response->error ?? null;

        if ($error || !$response) {
            throw new Exception("Error in shipping checkout: " . json_encode($error));
        }

        return $response;
    }

    // -------------------------*-------------------------
    public static function generateLabel($shippingIds)
    {
        $client = new Client();

        $response = $client->request('POST', MelhorEnvioVariables::$url . '/me/shipment/generate', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . MelhorEnvioVariables::$token,
                'User-Agent' => MelhorEnvioVariables::$suport,
            ],
            'json' => [
                "orders" => $shippingIds,
            ],
            'http_errors' => false,
        ]);

        $response = json_decode($response->getBody());

        $error = $response->errors ?? $response->error ?? null;

        if ($error || !$response) {
            throw new Exception("Error in label generation: " . json_encode($error));
        }

        return $response;
    }

    // -------------------------*-------------------------
    public static function printLabel($shippingIds)
    {
        $client = new Client();

        $response = $client->request('POST', MelhorEnvioVariables::$url . '/me/shipment/print', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . MelhorEnvioVariables::$token,
                'User-Agent' => MelhorEnvioVariables::$suport,
            ],
            'json' => [
                "orders" => $shippingIds,
            ],
            'http_errors' => false,
        ]);

        $response = json_decode($response->getBody());

        $error = $response->errors ?? $response->error ?? null;

        if ($error || !$response) {
            throw new Exception("Error in label printing: " . json_encode($error));
        }

        return $response;
    }

    // -------------------------*-------------------------
    public static function tracking($shippingId)
    {
        $client = new Client();

        $response = $client->request('POST', MelhorEnvioVariables::$url . '/me/shipment/tracking', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . MelhorEnvioVariables::$token,
                'User-Agent' => MelhorEnvioVariables::$suport,
            ],
            'json' => [
                "orders" => [$shippingId],
            ],
            'http_errors' => false,
        ]);

        $response = json_decode($response->getBody());

        $error = $response->errors ?? $response->error ?? null;

        if ($error || !$response) {
            throw new Exception("Error in shipping tracking: " . json_encode($error));
        }

        return reset($response);
    }
}
