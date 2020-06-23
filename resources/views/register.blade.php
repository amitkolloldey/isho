@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 card p-3">
                <h4>Register</h4>
                <hr>
                <div id="error" >

                </div>
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" class="form-control" placeholder="Enter Name" id="name">
                </div>
                <div class="form-group">
                    <label for="email">Email address:</label>
                    <input type="email" class="form-control" placeholder="Enter email" id="email">
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" placeholder="Enter password" id="password">
                </div>
                <button onclick="register()" type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        const user = ''
        function register( ) {
            $('#loader').show();
            var data = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                password: document.getElementById('password').value
            }
            axios
                .post("/api/admin/register", data)
                .then(resp => {
                    $('#loader').hide();
                    if(resp.data.validation){
                        if(resp.data.validation.email){
                            $('#error').append("<p class='alert alert-danger'>"+resp.data.validation.email+"</p>");
                        }
                        else{
                            $('#error').append("<p class='alert alert-danger'>"+resp.data.validation.password+"</p>");
                        }
                    }else{
                        location.replace('/email/verify')
                    }
                })
                .catch(err => {
                    $('#loader').hide();
                    $('#error').append("<p class='alert alert-danger'>"+err+"</p>");
                });
        }
    </script>
@endsection
