@extends('admin.layouts.app')
@section('title') Edit Attribute @endsection
@section('content')
    <hr>
    <h1>Attribute Edit</h1>
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
        <form action="#" id="attribute_create">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                    <hr>
                    <h2>General Information</h2>
                    <hr>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" placeholder="Attribute Name" name="name"
                               value="{{$attribute->name}}">
                    </div>
                    <div class="form-group text-right">
                        <button type="button" id="add" class="btn btn-success">Add Value
                        </button>
                    </div>
                    <table class="table table-bordered" id="dynamicvalue">
                        <tr>
                            <th>Value</th>
                            <th>Action</th>
                        </tr>
                        @php
                            $count = 1
                        @endphp
                        @foreach($attribute->values as $value)
                            <tr>
                                <td>
                                    <input type="hidden" name="value[{{$count}}][id]"
                                           value="{{$value->id}}"/>
                                    <input type="text" name="value[{{$count}}][value]" placeholder="Enter Value"
                                           class="form-control" id="value{{$count}}"
                                           value="{{$value->value}}"/>
                                </td>
                                <td>
                                    <button type="button" class="remove_old_option btn btn-danger"
                                            data-oid="{{$value->id}}">
                                        Remove
                                    </button>
                                </td>
                            </tr>
                            @php
                                $count++
                            @endphp
                        @endforeach

                    </table>
                </div>
                <button onclick="event.preventDefault(); update_attr()" class="btn-success btn">Save</button>
            </div>
        </form>
    </div>
@stop

@section('scripts')
    <script>
        function update_attr() {
            $('#loader').show();
            var formData = $("#attribute_create").serialize()
            axios.put('/api/attribute/update/' +{{$attribute->id}}, formData
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
                        location.replace('/admin/attributes')
                    } else if (response.data.validation.name) {
                        $('#error').append('<p class="alert alert-danger ">' + response.data.validation.name + '</p>');
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
    <script type="text/javascript">
        var i = {{count($attribute->values)}}
        $("#add").click(function () {
            ++i;
            $("#dynamicvalue").append('<tr><td><input type="text" name="value[' + i + '][value]" placeholder="Enter Value" class="form-control" /></td><td><button type="button" class="btn btn-danger remove-tr">Remove</button></td></tr>');
        });
        $(document).on('click', '.remove-tr', function () {
            $(this).parents('tr').remove();
        });
        $(".remove_old_option").click(function () {
            var oid = $(this).data("oid");
            $('#loader').show();
            axios.delete('/api/value/delete/' + oid
                , {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + '{{session()->get('access_token')}}'
                    }
                })
                .then(function (response) {
                    $('#loader').hide();
                    console.log(response)
                    location.reload()
                })
                .catch(function (error) {
                    $('#loader').hide();
                    $('#error').append('<p class="alert alert-danger ">' + error.response + '</p>');
                });
        });
    </script>
@stop
