@php
 $product = $order->product;    
 $order = $order;
 $detail = true;
@endphp
@extends('welcome')
@section('content')
<div class="content_order">
    <div class="order_detail">
        <h1>@lang('views.orders.title')</h1>
        @if (!$order->isPayed())     
            <h2 >@lang('views.orders.detail.state_order'): <span class="error"> {{ trans('views.orders.status.' . $order->status) }}</span></h2>
        @else
            <h2 class="success">@lang('views.orders.detail.state_order'): <span class="success"> {{ trans('views.orders.status.' . $order->status) }}</span></h2>
        @endif
        <h2>@lang('views.orders.quantity'):  {{$order->product_quantity}}</h2>
        <h2>@lang('views.orders.detail.price_unit'): {{formatMoney($order->product_price)}}</h2>
        <h2>@lang('views.orders.detail.price_total'): {{formatMoney($order->total)}}</h2>
    </div>
     <div class="card">            
        @include('products.card')
        @if ($order->isRejected())
            <div class="card_content_pay">            
                <a  href="{{route('order.pay.retry', $order)}}" class="btn-primary" >@lang('views.buttons.pay_again') &rarr;</a>
            </div>            
        @endif        
    </div> 
</div>
@endsection