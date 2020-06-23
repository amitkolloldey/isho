@extends('admin.layouts.app')
@section('title') Orders @endsection
@section('content')
    <hr>
    <h1>Orders</h1>
    <hr>
    <div class="content">
        <div id="error">

        </div>
        <table class="table">
            <thead class="thead-dark">
            <tr>
                <th scope="col">Number</th>
                <th scope="col">Name</th>
                <th scope="col">Phone</th>
                <th scope="col">Total Price</th>
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
            axios.delete('/api/order/delete/'+item_id, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer '+'{{session()->get('access_token')}}'
                }
            })
                .then(function (response) {
                    $('#loader').hide();
                    console.log(response);
                    if(response.data.success){
                        location.replace('/admin/orders')
                    }
                })
                .catch(function (error) {
                    $('#loader').hide();
                    $('#error').append('<p class="alert alert-danger ">'+error.response.data.message+'</p>');
                });
        }

        window.onload = (event) => {
            $('#loader').show();
            axios.get('/api/orders', {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer '+'{{session()->get('access_token')}}'
                }
            })
                .then(function (response) {
                    $.each(response.data.orders, function( index, value ) {
                        $('#table_data').append('<tr>' +
                            '<td>'+ value.number +'</td>' +
                            '<td>'+ value.name +'</td>' +
                            '<td>'+ value.phone +'</td>' +
                            '<td>'+ value.total_price + ' ' + value.currency+'</td>' +
                            '<td>'+ value.created_at +'</td>' +
                            '<td><a href="/admin/order/show/'+value.id+'">View</a>/<a href="#" onclick="event.preventDefault(); destroy('+value.id+')">Delete</a></td>' +
                            '</tr>');
                    });
                    $('#loader').hide();
                    console.log(response);
                })
                .catch(function (error) {
                    $('#loader').hide();
                    $('#error').append('<p class="alert alert-danger ">'+error.response.data.message+'</p>');
                });
        };
    </script>
@stop
