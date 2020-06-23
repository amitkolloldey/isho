<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        .lds-dual-ring {
            display: inline-block;
            width: 80px;
            height: 80px;
        }

        .lds-dual-ring:after {
            content: " ";
            display: block;
            width: 64px;
            height: 64px;
            margin: 8px;
            border-radius: 50%;
            border: 6px solid #fff;
            border-color: #fff transparent #fff transparent;
            animation: lds-dual-ring 1.2s linear infinite;
        }

        @keyframes lds-dual-ring {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        div#loader {
            position: fixed;
            z-index: 999;
            height: 100%;
            width: 100%;
            background-color: rgba(0, 0, 0, .5);
            text-align: center;
        }

        .lds-dual-ring {
            position: absolute;
            left: 0;
            right: 0;
            margin: 0 auto;
            top: 50%;
        }

        #search button.btn-dark.btn.w-100 {
            position: absolute;
            right: -79px;
            top: 0;
            width: 100px !important;
            border-radius: 0;
        }

        div#search {
            position: relative;
        }

        div#search {
            margin-top: 10px;
            margin-left: 10px;
        }
    </style>
    <style>
        .search_wrapper {
            position: relative;
        }

        ul#product_list {
            position: absolute;
            width: 100%;
            list-style: none;
            padding: 0;
            z-index: 999;
        }

        ul#product_list li {
            background: #fff;
            padding: 10px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
        }
    </style>
    @yield('styles')
</head>
<body>
<div id="loader">
    <div class="lds-dual-ring"></div>
</div>
<div id="app">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('app.name', 'Laravel') }}
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">

                </ul>
                <form action="{{route('product_search')}}" method="get">
                    @csrf
                    <div id="search">
                        <div class="search_wrapper">
                            <input type="text" name="product_name" id="product_name" class="form-control input-lg"
                                   placeholder="Enter Product Name"/>
                            <ul id="product_list">
                            </ul>
                        </div>
                        <button class="btn-dark btn w-100" >Search
                        </button>
                    </div>
                </form>
                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto mr-2">
                    <!-- Authentication Links -->
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('showLogin') }}">{{ __('Login') }}</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('showRegister') }}">{{ __('Register') }}</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                               data-toggle="dropdown"
                               aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                      style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
                <div class="mr-2">
                    <label for="currency_convert">Convert To</label>
                    <select name="currency_convert" id="currency_convert" onchange="convert()">
                        <option value="">Select</option>
                        <option value="BDT">BDT</option>
                        <option value="USD">USD</option>
                        <option value="EUR">EUR</option>
                        <option value="AUD">AUD</option>
                    </select>
                </div>

            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="card p-2">
            @yield('content')
        </div>

    </main>
</div>

<script src="{{asset('js/app.js')}}"></script>
<script>
    window.onload = (event) => {
        $('#loader').hide();
    };
</script>
@yield('scripts')
<script>
    function convert(){
        $('#loader').show();
        let currency_key = $('#currency_convert').children("option:selected").val()
        axios.get('/currency/switch/?currency_key='+currency_key)
            .then(function (response) {
                console.log(response)
                location.reload()
                $('#loader').hide()
            })
            .catch(function (error) {
                $('#loader').hide();
                $('#error').append('<p class="alert alert-danger ">' + error.response.data.message + '</p>');
            });
    }
    $(document).ready(function () {
        $('#product_name').keyup(function () {
            $('#product_list').show();
            var query = $(this).val();
            if (query != '') {
                axios.get('/product/query/fetch/?query=' + query, {
                    headers: {
                        'Accept': 'application/json',
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
</body>
</html>
