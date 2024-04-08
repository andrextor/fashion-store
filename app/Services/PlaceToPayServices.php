<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class PlaceToPayServices
{

    public function requestPlaceToPay(Order $order): array
    {
        $request = [
            'auth' => $this->getAuth(),
            'buyer' => [
                'name' => $order->customer_name,
                'email' => $order->customer_email,
                'mobile' => $order->customer_mobile,
            ],
            'payment' => [
                'reference' => $order->code,
                'description' => 'Pay order fhasion store order',
                'amount' => [
                    'currency' => config('app.default_currency'),
                    'total' => $order->total,
                ],

            ],
            'expiration' => Carbon::now()->addDays(2)->format('c'),
            'returnUrl' => route('order.detail', $order->code),
            'ipAddress' => Request::ip(),
            'userAgent' => Request::userAgent(),
        ];
        logger()->info('request place to pay', [
            'request' => $request
        ]);

        $response = Http::post(config('services.placetopay.url_base') . 'api/session', $request);
        $result = $response->json();

        if ($response->ok()) {

            $order->save();
            Payment::create([
                'order_id' => $order->id,
                'request_id' => $result['requestId'],
                'process_url' => $result['processUrl'],
                'status' => $result['status']['status'],
            ]);
        } else {
            // There was some error so check the message and log it
            logger()->error('Error placeToPay connection', [
                'response' => $result,
            ]);
        }

        logger()->info('place to pay request response', [
            'response' => $result,
        ]);

        return $result;
    }

    public function findPaymentPlaceToPay(Order $order): array
    {
        $lastPayment = $order->lastPayment();

        $result = Http::post(config('services.placetopay.url_base') . "api/session/$order->order_id", [
            'auth' => $this->getAuth()
        ]);
        $response = $result->json();

        logger()->error('RESPONSE FIND', [
            'response' => $response,
        ]);

        if ($result->json()) {
            // In order to use the functions please refer to the Dnetix\Redirection\Message\RedirectInformation class

            if ($response['status']['status'] == 'APPROVED') {
                // The payment has been approved
                $order->status = Order::STATUS_PAYED;
            } elseif ($response['status']['status'] == 'REJECTED') {
                $order->status = Order::STATUS_REJECTED;
            } else {
                $order->status = Order::STATUS_PENDING;
            }

            $order->save();

            $lastPayment->status = $response['status']['status'];
            $lastPayment->save();
        } else {

            logger()->error('Error placeToPay find request id', [
                'response' => $response,
            ]);
        }

        logger()->info('place to pay query response', [
            'response' => $response,
        ]);

        return $response;
    }

    private function getAuth(): array
    {
        $nonce = Str::random();
        $seed = date('c');

        return [
            'login' => config('services.placetopay.login'),
            'tranKey' => base64_encode(
                hash(
                    'sha256',
                    $nonce . $seed . config('services.placetopay.key'),
                    true
                )
            ),
            'nonce' => base64_encode($nonce),
            'seed' => $seed,
        ];
    }
}
