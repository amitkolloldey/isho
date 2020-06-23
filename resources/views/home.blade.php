@extends('layouts.app')
@section('title') Home @stop
@section('content')
    <div class="container">
        <div class="row">
            @foreach($products as $product)
            <div class="col-md-3">
                <div class="product_single " style="border: 1px solid #eee;margin-bottom: 10px; padding: 10px">
                    @if(isset($product->main_image))
                    <img src="{{config('app.url')}}/{{$product->main_image}}" width="100%" style="padding: 10px">
                    @else
                    <img src="{{config('app.url')}}/demo.jpg" width="100%" style="padding: 10px">
                    @endif
                    <a href="{{route('product_show',$product->slug)}}"><h4>{{$product->name}}</h4></a>
                        @if(session()->has('rate') && session()->has('currency'))
                            <p>Price: {{$product->price * session()->get('rate')}} {{session()->get('currency')}}</p>
                        @else
                            <p>Price: {{$product->price}}</p>
                        @endif
                    <p>SKU: {{$product->sku}}</p>
                </div>
            </div>
            @endforeach
            <div class="col-md-12">
                {{ $products->links() }}
            </div>
        </div>
    </div>
@endsection
@section('scripts')

@endsection
