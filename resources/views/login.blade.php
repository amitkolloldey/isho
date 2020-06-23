@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 card p-3">
            <h4>Login</h4>
            <hr>
            <div id="error" >

            </div>
                <div class="form-group">
                    <label for="email">Email address:</label>
                    <input type="email" class="form-control" placeholder="Enter email" id="email" name="email">
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" placeholder="Enter password" id="password" name="password">
                </div>
                <button onclick="login()" type="submit" class="btn btn-primary">Submit</button>

        </div>
    </div>
</div>
@endsection
@section('scripts')
    <script>
        const user = ''
        function login( ) {
            $('#loader').show();
            var data = {
                email: document.getElementById('email').value,
                password: document.getElementById('password').value
            }
            axios
                .post("/api/admin/login", data)
                .then(resp => {
                    if(resp.data.validation){
                        $('#loader').hide();
                        if(resp.data.validation.email){
                            $('#error').append("<p class='alert alert-danger'>"+resp.data.validation.email+"</p>");
                        }
                        else if(resp.data.validation.password){
                            $('#error').append("<p class='alert alert-danger'>"+resp.data.validation.password+"</p>");
                        }else{
                            $('#error').append("<p class='alert alert-danger'>Un Authorized!</p>");
                        }
                    }else{
                        this.user = resp.data.user;
                        this.token = resp.data.access_token;
                        localStorage.setItem("isho_token", this.token);
                        axios.defaults.headers.common["Authorization"] =
                            "Bearer " + resp.data.token;
                        location.replace("/admin/dashboard")
                    }
                })
                .catch(err => {
                    $('#loader').hide();
                    $('#error').append("<p class='alert alert-danger'>"+err+"</p>");
                    localStorage.removeItem('isho_token');
                });
        }
    </script>
@endsection
