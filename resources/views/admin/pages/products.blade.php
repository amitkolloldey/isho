@extends('admin.layouts.app')
@section('title') Products @endsection
@section('styles')
    <style>
        .search_wrapper {
            position: relative;
        }

        ul#product_list {
            position: absolute;
            width: 100%;
            list-style: none;
            padding: 0;
        }

        ul#product_list li {
            background: #fff;
            padding: 10px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
        }
    </style>
@stop
@section('content')
    <hr>
    <h1>Products</h1>
    <hr>
    <div class="content">
        <div id="error">

        </div>
        <div class="action_buttons mb-2">
            <!-- Button trigger modal -->
            <a href="{{route('product_create')}}" class="btn btn-primary">
                Add New
            </a>
        </div>
        <div class="filter row">
            <div class="col-md-6">
                <div class="form-group">
                    <div class="search_wrapper">
                        <input type="text" name="product_name" id="product_name" class="form-control input-lg"
                               placeholder="Enter Product Name"/>
                        <ul id="product_list">
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-right">
                <button class="btn-dark btn w-100" onclick="event.preventDefault(); search_products()">Search</button>
            </div>
        </div>
        <table class="table">
            <thead class="thead-dark">
            <tr>
                <th scope="col">Name</th>
                <th scope="col">SKU</th>
                <th scope="col">Price</th>
                <th scope="col">Created/Updated</th>
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
        function destroy(item_id) {
            $('#loader').show();
            axios.delete('/api/product/delete/' + item_id, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + '{{session()->get('access_token')}}'
                }
            })
                .then(function (response) {
                    console.log(response);
                    if (response.data.success) {
                        location.replace('/admin/products')
                    }
                    $('#loader').hide();
                })
                .catch(function (error) {
                    $('#loader').hide();
                    $('#error').append('<p class="alert alert-danger ">' + error.response.data.message + '</p>');
                });
        }

        window.onload = (event) => {
            $('#loader').show();
            axios.get('/api/products', {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + '{{session()->get('access_token')}}'
                }
            })
                .then(function (response) {
                    $.each(response.data.products, function (index, value) {
                        $('#table_data').append('<tr>' +
                            '<td>' + value.name + '</td>' +
                            '<td>' + value.sku + '</td>' +
                            '<td>' + value.price + ' BDT</td>' +
                            '<td>' + value.created_at + '</td>' +
                            '<td><a href="/admin/product/edit/' + value.id + '">Edit</a>/<a href="#" onclick="event.preventDefault(); destroy(' + value.id + ')">Delete</a></td>' +
                            '</tr>');
                    });
                    $('#loader').hide();
                })
                .catch(function (error) {
                    $('#loader').hide();
                    $('#error').append('<p class="alert alert-danger ">' + error.response.data.message + '</p>');
                });
        };

        function search_products() {
            $('#loader').show();
            let query = document.getElementById('product_name').value
            axios.get('/api/product/search/?query=' + query, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + '{{session()->get('access_token')}}'
                }
            })
                .then(function (response) {
                    $('#table_data').empty()
                    $.each(response.data.products, function (index, value) {
                        $('#table_data').append('<tr>' +
                            '<td>' + value.name + '</td>' +
                            '<td>' + value.sku + '</td>' +
                            '<td>$' + value.price + '</td>' +
                            '<td>' + value.created_at + '</td>' +
                            '<td><a href="/admin/product/edit/' + value.id + '">Edit</a>/<a href="#" onclick="event.preventDefault(); destroy(' + value.id + ')">Delete</a></td>' +
                            '</tr>');
                    });
                    $('#loader').hide();
                })
                .catch(function (error) {
                    $('#loader').hide();
                    $('#error').append('<p class="alert alert-danger ">' + error.response.data.message + '</p>');
                });
        }
    </script>
    <script>
        $(document).ready(function () {
            $('#product_name').keyup(function () {
                $('#product_list').show();
                var query = $(this).val();
                if (query != '') {
                    axios.get('/api/product/fetch/?query=' + query, {
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': 'Bearer ' + '{{session()->get('access_token')}}'
                        }
                    })
                        .then(function (response) {
                            console.log(response)
                            $('#product_list').empty()
                            if (response.data.products.length) {
                                $.each(response.data.products, function (index, value) {
                                    $('#product_list').append(
                                        '<li>' + value.name + '</li>'
                                    );
                                });
                            } else {
                                $('#product_list').append(
                                    '<li>No Result Found!</li>'
                                );
                            }
                            $('#loader').hide();
                        })
                        .catch(function (error) {
                            $('#loader').hide();
                            $('#error').append('<p class="alert alert-danger ">' + error.response.data.message + '</p>');
                        });
                }
            });

            $(document).on('click', '#product_list li', function () {
                $('#product_name').val($(this).text());
                $('#product_list').fadeOut();
            });
            $(document).on('click', 'body', function () {
                $('#product_list').fadeOut();
            });
        });
    </script>
@stop
