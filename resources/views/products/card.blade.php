
<div class="card_new">
    <div class="images">
      <img src="{{asset('images/'.$product->image)}}" width="200" height="200" />
    </div>
    <div class="product">
        <h1>{{$product->name}}</h1>
        <p>@lang('views.products.genre'): <strong>{{$product->genre}}</strong></p>
      <h2>{{ formatMoney($product->price)}}</h2>
      <p class="desc">{{$product->description}}</p>
      @if(isset($product) && !isset($detail))
      <div class="buttons">
        <a class="add"  href="{{route('order.create', $product)}}">@lang('views.buttons.buy') &rarr;</a>
      </div>
      @endif
    </div>
</div>
