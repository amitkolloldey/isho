@extends('admin.layouts.app')
@section('title') Attributes @endsection
@section('content')
    <hr>
    <h1>Attributes</h1>
    <hr>
    <div class="content">
        <div id="error">

        </div>
        <div class="action_buttons mb-2">
            <!-- Button trigger modal -->
            <a href="{{route('attribute_create')}}" class="btn btn-primary"  >
                Add New
            </a>
        </div>
        <table class="table">
            <thead class="thead-dark">
            <tr>
                <th scope="col">Name</th>
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
            axios.delete('/api/attribute/delete/'+item_id, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer '+'{{session()->get('access_token')}}'
                }
            })
                .then(function (response) {
                    $('#loader').hide();
                    console.log(response);
                    if(response.data.success){
                        location.replace('/admin/attributes')
                    }
                })
                .catch(function (error) {
                    $('#loader').hide();
                    $('#error').append('<p class="alert alert-danger ">'+error.response.data.message+'</p>');
                });
        }
        window.onload = (event) => {
            $('#loader').show();
            axios.get('/api/attributes', {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer '+'{{session()->get('access_token')}}'
                }
            })
                .then(function (response) {
                    $.each(response.data.attributes, function( index, value ) {
                        $('#table_data').append('<tr>' +
                            '<td>'+ value.name +'</td>' +
                            '<td>'+ value.created_at +'</td>' +
                            '<td><a href="/admin/attribute/edit/'+value.id+'">Edit</a>/<a href="#" onclick="event.preventDefault(); destroy('+value.id+')">Delete</a></td>' +
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
