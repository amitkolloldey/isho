@extends('admin.layouts.app')
@section('title') Stocks @endsection
@section('styles')
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css"/>
@stop
@section('content')
    <hr>
    <h1>Stock</h1>
    <hr>
    <div class="content">
        <div id="error">

        </div>
        <div class="action_buttons mb-2">
            <!-- Button trigger modal -->
            <a href="{{route('stock_create')}}" class="btn btn-primary">
                Add New
            </a>
        </div>
        <div class="filter row">
            <div class="col-md-4">
                <div class="form-group">
                    <input type="search" id="sku" class="form-control" placeholder="Enter Product Sku"/>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <input id="datepicker" class="form-control"/>
                </div>
            </div>
            <div class="col-md-4 text-right">
                <button class="btn-dark btn w-100" onclick="event.preventDefault(); search_stock()">Search</button>
            </div>

        </div>
        <div id="search_data"></div>
        <table class="table">
            <thead class="thead-dark">
            <tr>
                <th scope="col">Product Sku</th>
                <th scope="col">Quantity</th>
                <th scope="col">Updated On</th>
                <th scope="col">Action</th>
            </tr>
            </thead>
            <tbody id="table_data">

            </tbody>
        </table>
    </div>
@stop

@section('scripts')
    <script>
        function search_stock() {
            $('#loader').show();
            const sku = document.getElementById('sku').value
            const date = document.getElementById('datepicker').value
            axios.get('/api/stock/search/?sku='+sku+'&date='+date, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + '{{session()->get('access_token')}}'
                }
            })
                .then(function (response) {
                    $('#loader').hide();
                    console.log(response);
                    $('#table_data').empty()
                    if(response.data.stocks.length){
                        $.each(response.data.stocks, function (index, value) {
                            $('#table_data').append('<tr>' +
                                '<td>' +value.product.sku + '</td>' +
                                '<td>' + value.quantity + '</td>' +
                                '<td>' + value.updated_at + '</td>' +
                                '<td><a href="/admin/stock/edit/' + value.id + '">Edit</a>/<a href="#" onclick="event.preventDefault(); destroy(' + value.id + ')">Delete</a></td>' +
                                '</tr>');
                        });
                    }else{
                        $('#table_data').append('<tr><td> No Result Found!</td></tr>');
                        if(response.data.validation.sku){
                            $('#error').append('<p class="alert alert-danger ">'+response.data.validation.sku+'</p>');
                        }else if(response.data.validation.date){
                            $('#error').append('<p class="alert alert-danger ">'+response.data.validation.date+'</p>');
                        }
                    }
                })
                .catch(function (error) {
                    $('#loader').hide();
                    //$('#error').append('<p class="alert alert-danger ">' + error + '</p>');
                });
        }

        function destroy(item_id) {
            $('#loader').show();
            axios.delete('/api/stock/delete/' + item_id, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + '{{session()->get('access_token')}}'
                }
            })
                .then(function (response) {
                    $('#loader').hide();
                    console.log(response);
                    if (response.data.success) {
                        location.replace('/admin/stocks')
                    }
                })
                .catch(function (error) {
                    $('#loader').hide();
                    $('#error').append('<p class="alert alert-danger ">' + error + '</p>');
                });
        }

        window.onload = (event) => {
            $('#loader').show();
            axios.get('/api/stocks', {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + '{{session()->get('access_token')}}'
                }
            })
                .then(function (response) {
                    $.each(response.data.stocks, function (index, value) {
                        $('#table_data').append('<tr>' +
                            '<td>' + value.product.sku + '</td>' +
                            '<td>' + value.quantity + '</td>' +
                            '<td>' + value.updated_at + '</td>' +
                            '<td><a href="/admin/stock/edit/' + value.id + '">Edit</a>/<a href="#" onclick="event.preventDefault(); destroy(' + value.id + ')">Delete</a></td>' +
                            '</tr>');
                    });
                    $('#loader').hide();
                    console.log(response);
                })
                .catch(function (error) {
                    $('#loader').hide();
                    $('#error').append('<p class="alert alert-danger ">' + error + '</p>');
                });
        };
    </script>

    <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
    <script>
        $('#datepicker').datepicker({
            uiLibrary: 'bootstrap4'
        });
    </script>
@stop
