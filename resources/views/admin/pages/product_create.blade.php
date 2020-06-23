@extends('admin.layouts.app')
@section('title') Create Product @endsection
@section('content')
    <hr>
    <h1>Product Create</h1>
    <hr>
    <div class="content card p-2">
        <div id="error">

        </div>
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab"
                   aria-controls="general" aria-selected="true">General</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="attributes-tab" data-toggle="tab" href="#attributes" role="tab"
                   aria-controls="attributes" aria-selected="false">Attributes</a>
            </li>
        </ul>
        <form action="#" id="product_create" enctype="multipart/form-data">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                    <hr>
                    <h2>General Information</h2>
                    <hr>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" placeholder="Product Name" name="name">
                    </div>
                    <div class="form-group">
                        <label for="sku">SKU</label>
                        <input type="text" class="form-control" id="sku" placeholder="Product SKU" name="sku">
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" class="form-control" id="price" placeholder="Product Price" name="price">
                    </div>
                    <div class="form-group">
                        <label for="stock">Available Quantity</label>
                        <input type="number" class="form-control" id="stock" name="stock">
                    </div>
                    <div class="form-group">
                        <label for="main_image">Main Image</label>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="file" class="form-control" id="main_image" name="main_image">
                                <input type="hidden" id="main_image_name" name="main_image_name">
                                <img src="" alt="" id="main_image_src" width="50px">
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-danger"
                                        onclick="event.preventDefault(); upload_main_image('main_image')">Upload
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" cols="30" rows="10"
                                  name="description"></textarea>
                    </div>
                </div>

                <div class="tab-pane fade" id="attributes" role="tabpanel" aria-labelledby="attributes-tab">
                    <hr>
                    <h2>Product Attributes</h2>
                    <hr>
                    @foreach($attributes as $attribute)
                        <h3>{{$attribute->name}}</h3>
                        <input type="hidden" name="attribute_id[{{$attribute->id}}]" value="{{$attribute->id}}">
                        @foreach($attribute->values as $value)
                            <div class="form-group">
                                <label for="{{$value->value}}"><strong>{{ucwords($value->value)}}</strong></label>
                                <div class="form-group">
                                    <label for="{{$value->value}}-sku">SKU</label>
                                    <input type="text" class="form-control" id="{{$value->value}}-sku" placeholder="SKU"
                                           name="attribute_values[{{$value->id}}][sku]">
                                </div>
                                <div class="form-group">
                                    <label for="{{$value->value}}-price">Price</label>
                                    <input type="number" class="form-control" id="{{$value->value}}-price"
                                           placeholder="Price" name="attribute_values[{{$value->id}}][price]">
                                </div>
                                <div class="form-group">
                                    <label for="image">Image</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="file" class="form-control" id="attribute_{{$value->id}}_image" >
                                            <input type="hidden" id="attribute_{{$value->id}}_name" name="attribute_values[{{$value->id}}][image_src]">
                                            <img src="" alt="" id="attribute_{{$value->id}}_src" width="50px">
                                        </div>
                                        <div class="col-md-6">
                                            <button class="btn btn-danger"
                                                    onclick="event.preventDefault(); upload_attribute_image({{$value->id}})">Upload
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        @endforeach
                    @endforeach
                </div>
                <button onclick="event.preventDefault(); add()" class="btn-success btn">Save</button>
            </div>
        </form>
    </div>
@stop

@section('scripts')
    <script>
        function upload_main_image(id) {
            let main_image = document.getElementById('' + id + '').files[0]
            let data = new FormData();
            data.append('main_image', main_image, main_image.fileName);
            axios.post('/api/product/main_image/upload', data
                , {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + '{{session()->get('access_token')}}',
                        'Content-type': "multipart/form-data; charset=utf-8; boundary=" + Math.random().toString().substr(2)
                    }
                })
                .then(function (response) {
                    $('#loader').hide();
                    let main_image_name = document.getElementById('main_image_name')
                    let main_image_src = document.getElementById('main_image_src')
                    main_image_name.value = response.data
                    main_image_src.src = 'http://isho.test/' + response.data
                })
                .catch(function (error) {
                    $('#loader').hide();
                    $('#error').append('<p class="alert alert-danger ">' + error.response + '</p>');

                });
        }

        function upload_attribute_image(id) {
            let attribute_image = document.getElementById('attribute_' + id + '_image').files[0]
            let data = new FormData();
            data.append('attribute_image', attribute_image, attribute_image.fileName);
            axios.post('/api/product/attribute_image/upload', data
                , {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + '{{session()->get('access_token')}}',
                        'Content-type': "multipart/form-data; charset=utf-8; boundary=" + Math.random().toString().substr(2)
                    }
                })
                .then(function (response) {
                    $('#loader').hide();
                    let image_name = document.getElementById('attribute_'+id+'_name')
                    let image_src = document.getElementById('attribute_'+id+'_src')
                    image_name.value = response.data
                    image_src.src = 'http://isho.test/' + response.data
                })
                .catch(function (error) {
                    $('#loader').hide();
                    $('#error').append('<p class="alert alert-danger ">' + error.response + '</p>');
                });
        }

        function add() {
            $('#loader').show();
            var formData = $("#product_create").serialize()
            axios.post('/api/product/store', formData
                , {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + '{{session()->get('access_token')}}',
                    }
                })
                .then(function (response) {
                    $('#loader').hide();
                    console.log(response)
                    if (response.data.success) {
                        location.replace('/admin/products')
                    } else if (response.data.validation.name) {
                        $('#error').append('<p class="alert alert-danger ">' + response.data.validation.name + '</p>');
                    } else if (response.data.validation.sku) {
                        $('#error').append('<p class="alert alert-danger ">' + response.data.validation.sku + '</p>');
                    } else if (response.data.validation.price) {
                        $('#error').append('<p class="alert alert-danger ">' + response.data.validation.price + '</p>');
                    } else {
                        $('#error').append('<p class="alert alert-danger ">Validation Error</p>');
                    }

                })
                .catch(function (error) {
                    $('#loader').hide();
                    $('#error').append('<p class="alert alert-danger ">' + error.response + '</p>');

                });
        }

    </script>
@stop
