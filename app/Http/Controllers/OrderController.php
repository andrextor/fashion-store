<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayOrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Services\PaymentService;
use Dnetix\Redirection\Message\RedirectInformation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;


class OrderController extends Controller
{

    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function create(Product $product): View
    {
        return view('order.create', compact('product'));
    }

    public function pay(Product $product, PayOrderRequest $request)
    {

        $order = Order::generate($request->all(), $product);

        return $this->sendPayment($order);
    }

    public function retryPay(Order $order)
    {
        return $this->sendPayment($order);
    }

    public function detail(Order $order): View
    {
        if (!$order->isPayed()) {
            $this->paymentService->findPaymentPlaceToPay($order);
        }

        return view('order.detail', compact('order'));
    }

    public function list(Request $request): View
    {
        $email = $request->input('email');

        $orders = Order::customer($email)->get();

        return view('order.list', compact('orders'));
    }

    public function search(): View
    {        
        return view('order.search');
    }

    private function sendPayment(Order $order): RedirectResponse
    {
        $response =  $this->paymentService->requestPlaceToPay($order);

        if ($response->isSuccessful()) {
            return redirect($response->processUrl());
        } else {
            return redirect(route('order.create', $order->product))->withErrors(trans('views.pay.error'));
        }
    }

    
}
