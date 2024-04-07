@extends('welcome')
@section('content')
<div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg"> 
    <h1 >@lang('views.products.title')</h1>                    
</div>
<div class="content">
   @foreach ($products as $product)
   <div class="card">       
       @include('products.card')       
    </div>
   @endforeach      
</div>             
@endsection