@extends('welcome')
@section('content')
    <h1 style="color:#286090">@lang('views.orders.title_search_list')</h1>
    <div class="content_order_list">    
        <div class="form">  
            <form action="{{route('order.list')}}" method="POST">
                @csrf
                <div class="form__group field">
                    <input type="email" class="form__field" placeholder="@lang('views.orders.search_email')" name="email" id='email' required />
                    <label for="email" class="form__label">@lang('views.orders.email')</label>                
                </div>
                <button class="btn-primary btn-pay">@lang('views.buttons.search_order') &rarr;</button>
            </form>
        </div>
    </div>
@endsection