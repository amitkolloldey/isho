@extends('layouts.app')
@section('title') Order Create @stop
@section('styles')

@stop
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (Session::has('success'))
                    <div class="alert alert-success">
                        <ul>
                            <li>{!! Session::get('success') !!}</li>
                        </ul>
                    </div>
                @endif
            </div>
            <div class="col-md-12">
                <h2>Create Order</h2>
                <hr>
            </div>
            <div class="col-md-4">
                <h4>Product Details</h4>
                <hr>
                <div class="product_single " style="border: 1px solid #eee;margin-bottom: 10px; padding: 10px">
                    @if(isset($product->main_image))
                        <img src="{{config('app.url')}}/{{$product->main_image}}" width="100%" style="padding: 10px">
                    @else
                        <img src="{{config('app.url')}}/demo.jpg" width="100%" style="padding: 10px">
                    @endif
                    <a href="{{route('product_show',$product->slug)}}"><h4>{{$product->name}}</h4></a>
                    @if(session()->has('rate') && session()->has('currency'))
                        <p><strong>Price: </strong> {{$product->price * session()->get('rate')}} {{session()->get('currency')}}</p>
                    @else
                        <p><strong>Price: </strong> {{$product->price}}</p>
                    @endif
                    <p><strong>SKU: </strong> {{$product->sku}}</p>
                    <p><strong>Option: </strong> {{get_attribute_value_by_id(session()->get('order_items')['attribute_value_id'])->value}}</p>
                    <p><strong>Quantity</strong> {{session()->get('order_items')['quantity']}}</p>
                        <hr>
                    <p><strong>Total Price</strong> {{session()->get('order_items')['total_price']}} {{session()->get('order_items')['currency']}}</p>
                </div>
            </div>
            <div class="col-md-8">
                <h4>Personal Information</h4>
                <hr>
                <form action="{{route('order_store')}}" method="post">
                    @csrf
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" placeholder="Enter Your Name" id="name" class="form-control" value="{{old('name')}}">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" placeholder="Enter Your Email" id="email" class="form-control"  value="{{old('email')}}">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone No</label>
                        <input type="tel" name="phone" placeholder="Enter Your Phone No" id="name" class="form-control"  value="{{old('phone')}}">
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea name="address" id="address" class="form-control" cols="30" rows="10">{{old('address')}}</textarea>
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="product_id" value="{{$product->id}}">
                        <input type="hidden" name="attribute_name" value="{{get_attribute_name_by_id(get_attribute_value_by_id(session()->get('order_items')['attribute_value_id'])->attribute_id)['name']}}">
                        <input type="hidden" name="attribute_value" value="{{get_attribute_value_by_id(session()->get('order_items')['attribute_value_id'])->value}}">
                        <input type="hidden" name="currency" value="{{session()->get('currency')}}">
                        <input type="hidden" name="quantity" value="{{session()->get('order_items')['quantity']}}">
                        <input type="hidden" name="total_price" value="{{session()->get('order_items')['total_price']}}">
                       <button class="btn-primary btn">Complete Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')

@endsection
