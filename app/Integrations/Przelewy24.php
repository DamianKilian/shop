<?php

namespace App\Integrations;

use App\Payment\PaymentInterface;
use App\Services\AppService;
use Illuminate\Support\Facades\Http;

class Przelewy24 implements PaymentInterface
{
    public readonly string $baseUrl;
    public readonly array $config;

    public function __construct()
    {
        $this->config = config('przelewy24');
    }

    public function pay($order, $regulationAccept = false)
    {
        $urlReturn = route('order-completed', ['order' => $order->id]);
        $urlStatus = route('payment-transaction-status');
        if (app()->isLocal()) {
            $urlReturn = str_replace(config('app.url'), config('my.ngrok_url'), $urlReturn);
            $urlStatus = str_replace(config('app.url'), config('my.ngrok_url'), $urlStatus);
        }
        $sign = $this->sign($order);
        $url = $this->config['base_url'] . '/api/v1/transaction/register';
        $a = $order->address;
        $params = [
            'merchantId' => (int)$this->config['merchant_id'],
            'posId' => (int)$this->config['merchant_id'],
            'sessionId' => $order->session_id,
            'amount' => (int)AppService::toPennies($order->price),
            'currency' => $order->currency,
            'description' => 'description: ' . $order->id,
            'email' => $a->email,
            'client' => $a->name . ' ' . $a->surname,
            'address' => $a->street . ' ' . $a->house_number . ($a->apartment_number ? '/' . $a->apartment_number : ''),
            'zip' => $a->postal_code,
            'city' => $a->city,
            'country' => $a->country->code,
            'phone' => $a->areaCode->code . $a->phone,
            'language' => $this->getLanguage($a->country->code),
            // 'method' => '',
            'urlReturn' => $urlReturn,
            'urlStatus' => $urlStatus,
            // 'timeLimit' => '',
            // 'channel' => '',
            'waitForResult' => false,
            'regulationAccept' => $regulationAccept,
            'shipping' => $order->delivery_price,
            // 'transferLabel' => '',
            // 'mobileLib' => '',
            // 'sdkVersion' => '',
            'sign' => $sign,
            // 'encoding' => '',
            // 'methodRefId' => '',
            'cart' => [
                (object)[
                    'sellerId' => 'sellerId',
                    'sellerCategory' => 'sellerCategory',
                    // 'name' => '',
                    // 'description' => '',
                    // 'quantity' => '',
                    // 'price' => '',
                    // 'number' => '',
                ],
            ],
            // 'additional' => (object)[
            //     'shipping' => (object)[
            //         'type' => '',
            //         'address' => '',
            //         'zip' => '',
            //         'city' => '',
            //         'country' => '',
            //     ],
            //     'PSU' => (object)[
            //         'IP' => '',
            //         'userAgent' => '',
            //     ],
            // ],
        ];
        $response = Http::withBasicAuth($this->config['merchant_id'], $this->config['secret_id'])->post($url, $params);
    }

    protected function getLanguage($countryCode)
    {
        $l = 'pl';
        if ('PL' !== $countryCode) {
            $l = 'en';
        }
        return $l;
    }

    protected function sign($order)
    {
        $params = [
            'sessionId' => $order->session_id, // Tutaj należy umieścić unikalne wygenerowane ID sesji
            'merchantId' => $this->config['merchant_id'], // Tutaj należy umieścić ID Sprzedawcy z panelu Przelewy24
            'amount' => AppService::toPennies($order->price), // Tutaj należy umieścić kwotę transakcji w groszach, 1234 oznacza 12,34 PLN
            'currency' => $order->currency, // Tutaj należy umieścić walutę transakcji
            'crc' => $this->config['crc'], // Tutaj należy umieścić pobrany klucz CRC z panelu Przelewy24
        ];
        // Sklejanie parametrów w ciąg JSON
        $combinedString = json_encode($params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        // Hashowanie za pomocą SHA-384
        $hash = hash('sha384', $combinedString);
        // echo 'Suma kontrolna parametrów wynosi: ' . $hash;
        return $hash;
    }
}
