<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayOrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Services\PaymentService;
use App\Services\PlaceToPayServices;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;


class OrderController extends Controller
{

    // private PaymentService $paymentService;

    private PlaceToPayServices $placeToPayServices;

    public function __construct(PlaceToPayServices $placeToPayServices)
    {
        //$this->paymentService = $paymentService;
        $this->placeToPayServices = $placeToPayServices;
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
            $this->placeToPayServices->findPaymentPlaceToPay($order);
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
        $response = $this->placeToPayServices->requestPlaceToPay($order);

        if ($response['status']['status'] == 'OK') {
            return redirect($response['processUrl']);
        } else {
            return redirect(route('order.create', $order->product))->withErrors(trans('views.pay.error'));
        }
    }
}
