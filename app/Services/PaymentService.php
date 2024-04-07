<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Dnetix\Redirection\Message\RedirectInformation;
use Dnetix\Redirection\Message\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Request;

class PaymentService
{
    private PlaceToPay $placeToPay;

    public function __construct(PlaceToPay $placeToPay)
    {
        $this->placeToPay = $placeToPay;
    }

    public function requestPlaceToPay(Order $order): RedirectResponse
    {
        $request = [
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

        $response = $this->placeToPay->request($request);

        if ($response->isSuccessful()) {

            $order->save();

            Payment::create([
                'order_id' => $order->id,
                'request_id' => $response->requestId(),
                'process_url' => $response->processUrl(),
                'status' => $response->status()->status(),
            ]);

        } else {
            // There was some error so check the message and log it 
            logger()->error('Error placeToPay connection', [
                'response' => $response->toArray(),
            ]);
        }

        logger()->info('place to pay request response', [
            'response' => $response->toArray(),
        ]);

        return $response;
    }

    public function findPaymentPlaceToPay(Order $order): RedirectInformation
    {
        $lastPayment = $order->lastPayment();

        $response = $this->placeToPay->query($lastPayment->request_id);

        if ($response->isSuccessful()) {

            // In order to use the functions please refer to the Dnetix\Redirection\Message\RedirectInformation class            
                
            if ($response->status()->isApproved()) {
                // The payment has been approved
                $order->status = Order::STATUS_PAYED;                
            } elseif ($response->status()->isRejected()) {
                $order->status = Order::STATUS_REJECTED;            
            } else {
                $order->status = Order::STATUS_PENDING;
            }

            $order->save();

            $lastPayment->status = $response->status()->status();
            $lastPayment->save();
        } else {

            logger()->error('Error placeToPay find request id', [
                'response' => $response->toArray(),
            ]);
        }

        logger()->info('place to pay query response', [
            'response' => $response->toArray(),
        ]);

        return $response;
    }
}
