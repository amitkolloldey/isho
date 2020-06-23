@extends('admin.layouts.app')
@section('title') Show Order @endsection
@section('content')
    <hr>
    <h1>Show Order</h1>
    <hr>
    <div class="content card p-2" id="order_details">
        <table class="table" >
            <tbody>
            <tr>
                <th scope="row">Order Number</th>
                <td>#{{$order->number}}</td>
            </tr>

            <tr>
                <th scope="row">Name</th>
                <td>{{$order->name}}</td>
            </tr>

            <tr>
                <th scope="row">Email</th>
                <td>{{$order->email}}</td>
            </tr>

            <tr>
                <th scope="row">Phone</th>
                <td>{{$order->phone}}</td>
            </tr>

            <tr>
                <th scope="row">Address</th>
                <td>{{$order->address}}</td>
            </tr>

            <tr>
                <th scope="row">Product Ordered</th>
                <td> </td>
            </tr>
            @foreach($order->products as $product)
                <tr>
                    <th scope="row">SKU: {{$product->product->sku}}</th>
                    <td>Name: {{$product->product->name}}</td>
                </tr>
            @endforeach
            <tr>
                <th scope="row">{{$order->attribute_name}}</th>
                <td>{{$order->attribute_value}}</td>
            </tr>
            <tr>
                <th scope="row">Quantity</th>
                <td>{{$order->quantity}}</td>
            </tr>
            <tr>
                <th scope="row">Total Price</th>
                <td>{{$order->total_price}} {{$order->currency}}</td>
            </tr>
            </tbody>
        </table>

    </div>
    <div class="text-center p-2">
        <button onclick="print_order()" class="btn-primary btn">Print Invoice</button>
    </div>
@stop
@section('scripts')
    <script>
        function print_order() {
            var printContents = document.getElementById("order_details").innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
    </script>
@stop

