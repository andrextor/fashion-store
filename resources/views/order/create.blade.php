@php
 $detail = true;
@endphp
@extends('welcome')
@section('content')
<div class="content_order">
    <div class="form">
        <h1>@lang('views.orders.title')</h1>
        <form action="{{route('order.pay', $product)}}" method="POST">
            @csrf
            <div class="form__label_error">
                @if (count($errors) > 0)
                <h5>@lang('views.orders.errors')</h5>
                @foreach ($errors->all() as $error) 
                    <label  class="form__label_error">{{$error}}</label><br>
                @endforeach
                @endif
            </div>            
            <div class="form__group field">
                <input value="{{old('customer_name')}}" type="text" class="form__field" placeholder="@lang('views.orders.name')" name="customer_name" id='name' required />
                <label for="name" class="form__label">@lang('views.orders.name')</label>                
            </div>
            <div class="form__group field">
                <input value="{{old('customer_email')}}" type="email" class="form__field" placeholder="@lang('views.orders.email')" name="customer_email" id='email' required />
                <label for="email" class="form__label">@lang('views.orders.email')</label>
            </div>
            <div class="form__group field">
                <input value="{{old('customer_mobile')}}" type="text" class="form__field" placeholder="@lang('views.orders.mobile')" name="customer_mobile" id='mobile' required />
                <label for="mobile" class="form__label">@lang('views.orders.mobile')</label>
            </div>
            <div class="form__group field">
                <input value="{{old('product_quantity')}}" type="number" max="10" class="form__field" placeholder="@lang('views.orders.quantity')" name="product_quantity" id='quantity' required />
                <label for="quantity" class="form__label">@lang('views.orders.quantity')</label>
            </div>
            <button class="btn-primary btn-pay">@lang('views.buttons.pay') &rarr;</button>
        </form>
    </div>
    <div class="card">            
        @include('products.card')
    </div>
</div>
@endsection