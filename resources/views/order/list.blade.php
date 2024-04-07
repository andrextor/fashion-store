@extends('welcome')
@section('content')

<div class="content_order_list">    
    @foreach ($orders as $order)
        
    <div>
        <div class="card_list">                        
            <div class="card_content_list">
                <h2> {{ trans('views.orders.title_list') . $order->id  }}</h2>
                <span><strong>@lang('views.orders.quantity'): {{$order->product_quantity}}</strong></span>        
                <span><strong>@lang('views.orders.detail.price_unit'): {{formatMoney($order->product_price)}}</strong></span>        
                <span><strong>@lang('views.orders.detail.price_total'): {{formatMoney($order->total)}}</strong></span>        
                <span><strong>@lang('views.orders.name'): {{$order->customer_name}}</strong></span>        
                <span><strong>@lang('views.orders.email'): {{$order->customer_email}}</strong></span>        
                <span><strong>@lang('views.orders.detail.state_order'): {{ trans('views.orders.status.' . $order->status)}}</strong></span>        
            </div>
            <img src="{{Storage::disk('public')->url($order->product->image)}}" alt="camiseta" width="200" height="200" style="padding:2em">
        </div> 
    </div>
    @endforeach
</div>

@endsection