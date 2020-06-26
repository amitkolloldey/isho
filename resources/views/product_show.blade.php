@extends('layouts.app')
@section('title') {{$product->name}} @stop
@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.1/css/lightbox.min.css"
          type="text/css" media="screen"/>
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
            <div class="col-md-6">
                <div class="product_image " style="border: 1px solid #eee;margin-bottom: 10px; padding: 10px">
                    @if(isset($product->main_image))
                        <img src="{{config('app.url')}}/{{$product->main_image}}" width="100%" style="padding: 10px">
                    @else
                        <img src="{{config('app.url')}}/demo.jpg" width="100%" style="padding: 10px">
                    @endif
                    @foreach($product->attributes as $attribute)
                        <ul class="attr_list">
                            @foreach($attribute->attribute->values as $value)
                                @if(in_array($value->id, $product_attribute_value_id_list))
                                    @if(isset($product_attribute_value_image_list[$value->id]))
                                        <li><a href="{{asset('/')}}{{$product_attribute_value_image_list[$value->id]}}"
                                               data-lightbox="image-1"
                                               data-title="{{ucwords($value->value)}}">{{ucwords($value->value)}}</a>
                                        </li>
                                    @endif
                                @endif
                            @endforeach
                        </ul>
                    @endforeach
                </div>
            </div>
            <div class="col-md-6">
                <div class="product_info " style="border: 1px solid #eee;margin-bottom: 10px; padding: 10px">
                    <h4>{{$product->name}}</h4>
                    @if(session()->has('rate') && session()->has('currency'))
                        <p>Price: {{$product->price * session()->get('rate')}} {{session()->get('currency')}}</p>
                    @else
                        <p>Price: {{$product->price}} BDT</p>
                    @endif
                    <p>SKU: {{$product->sku}}</p>
                    <p>{{$product->description}}</p>
                    @if($product->stocks->last()->quantity != 0)
                        <form action="{{route('order_create')}}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="quantity">Quantity</label>
                                <input type="number" value="1" min="1"
                                       max="{{$product->stocks->last()->quantity}}"
                                       name="quantity" class="form-control" onchange="quantity_price()" id="quantity">
                            </div>
                            @foreach($product->attributes as $attribute)
                                <div class="form-group">
                                    <label
                                        for="{{seoUrl($attribute->attribute->name)}}">{{$attribute->attribute->name}}</label>
                                    <select name="{{seoUrl($attribute->attribute->name)}}"
                                            onchange="showprice({{$attribute->id}})"
                                            id="attribute_{{$attribute->id}}" class="form-control">
                                        <option value="">Select An Option</option>
                                        @foreach($attribute->attribute->values as $value)
                                            @if(in_array($value->id, $product_attribute_value_id_list))
                                                <option value="{{$value->id}}">{{ucwords($value->value)}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach
                            <div class="price">
                                <input type="hidden" id="product_id" value="{{$product->id}}" name="product_id">
                                <input type="hidden" id="total_price" name="total_price">
                                <input type="hidden" id="base_total">
                                <input type="hidden" id="attribute_value_id" name="attribute_value_id">
                                <input type="hidden" id="currency" name="currency">
                                <div id="price">
                                    Please Select An Option To See The Total Price
                                </div>
                            </div>
                            <button class="btn-primary btn">Create Order</button>
                        </form>
                    @else
                        <p class="alert-danger alert">Out Of Stock</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function quantity_price() {
            let quantity = $('#quantity').val()
            let base_total = $('#base_total').val()
            let total_price = $('#total_price').val()
            let currency = $('#currency').val()
            if (total_price === '') {
                alert('Please Select Attribute!')
                $('#price').html('<p>Please Select An Option To See The Total Price</p>')
            } else {
                $('#total_price').val((base_total * quantity))
                $('#currency').val(currency)
                $('#price').html('<p>Price: ' + (base_total * quantity) + ' ' + currency + '</p>')
            }
        }

        function showprice(product_attribute_id) {
            $('#loader').show();
            $('#total_price').val(0)
            let old_total_price = $('#total_price').val()
            let quantity = $('#quantity').val()
            if (old_total_price === '') {
                old_total_price = 0
            }
            let product_id = '{{$product->id}}'
            let value_id = $('#attribute_' + product_attribute_id).children("option:selected").val();
            if (value_id !== '') {
                axios.get('/product/show/price?product_attribute_id=' + product_attribute_id + '&value_id=' + value_id + '&product_id=' + product_id)
                    .then(function (response) {
                        $('#loader').hide();
                        let total_price = parseInt(old_total_price, 10) + response.data.total_price * quantity
                        $('#price').html('<p>Price: ' + total_price + ' ' + response.data.currency + '</p>')
                        $('#total_price').val(total_price)
                        $('#base_total').val(response.data.total_price)
                        $('#currency').val(response.data.currency)
                        $('#attribute_value_id').val(value_id)
                        console.log(response)
                    })
                    .catch(function (error) {
                        $('#loader').hide();
                        $('#error').append('<p class="alert alert-danger ">' + error.response + '</p>');
                    });
            } else {
                $('#loader').hide();
                $('#price').html('<p>Please Select An Option To See The Total Price</p>')
                $('#total_price').val('')
                $('#currency').val('')
            }
        }
    </script>
    <script type="text/javascript"
            src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.1/js/lightbox.min.js"></script>
@endsection
