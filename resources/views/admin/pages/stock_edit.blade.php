@extends('admin.layouts.app')
@section('title') Edit Stock @endsection
@section('content')
    <hr>
    <h1>Stock Edit</h1>
    <hr>
    <div class="content card p-2">
        <div id="error">

        </div>
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab"
                   aria-controls="general" aria-selected="true">General</a>
            </li>
        </ul>
        <form action="#" id="stock_edit">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                    <hr>
                    <h2>General Information</h2>
                    <hr>
                    <div class="form-group">
                        <label for="product_sku">Product Sku</label>
                        <input type="text" class="form-control" id="product_sku" placeholder="Product Sku" name="sku" value="{{get_sku_by_product_id($stock->product_id)}}">
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="number" class="form-control" id="quantity" placeholder="Quantity" name="quantity" value="{{$stock->quantity}}">
                    </div>
                </div>
                <button onclick="event.preventDefault(); update_stock()" class="btn-success btn">Update</button>
            </div>
        </form>
    </div>
@stop

@section('scripts')
    <script>
        function update_stock() {
            $('#loader').show();
            var formData = $("#stock_edit").serialize()
            axios.put('/api/stock/update/' +{{$stock->id}}, formData
                , {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + '{{session()->get('access_token')}}'
                    }
                })
                .then(function (response) {
                    $('#loader').hide();
                    console.log(response)
                    if (response.data.success) {
                        location.replace('/admin/stocks')
                    } else if (response.data.validation.quantity) {
                        $('#error').append('<p class="alert alert-danger ">' + response.data.validation.quantity + '</p>');
                    }else if (response.data.validation.sku) {
                        $('#error').append('<p class="alert alert-danger ">' + response.data.validation.sku + '</p>');
                    } else {
                        $('#error').append('<p class="alert alert-danger ">Error</p>');
                    }
                })
                .catch(function (error) {
                    $('#loader').hide();
                    $('#error').append('<p class="alert alert-danger ">' + error.response + '</p>');
                });
        }
    </script>
@stop
