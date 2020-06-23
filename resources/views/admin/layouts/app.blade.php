<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title> @yield('title') </title>

    <!-- Bootstrap core CSS -->
    <link href="{{asset('/css/app.css')}}" rel="stylesheet">
    <link href="{{asset('/css/dashboard.css')}}" rel="stylesheet">

    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
    </style>
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
            background-color: rgba(0,0,0,.5);
            text-align: center;
        }

        .lds-dual-ring {
            position: absolute;
            left: 0;
            right: 0;
            margin: 0 auto;
            top: 50%;
        }
    </style>
    @yield('styles')
</head>
<body>
<div id="loader">
    <div class="lds-dual-ring"></div>
</div>
<nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 mr-0 px-3" href="{{config('app.url')}}">{{config('app.name')}}</a>
    <ul class="navbar-nav px-3">
        <li class="nav-item text-nowrap">
            <a class="nav-link" href="{{ route('logout') }}"
               onclick="event.preventDefault();  document.getElementById('logout-form').submit();">
                {{ __('Logout') }}
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </li>
    </ul>
</nav>

<div class="container-fluid">
    <div class="row">
        @include('admin.partials.sidebar')
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
             <div class="row">
                 <div class="col-md-12">
                     @yield('content')
                 </div>
             </div>
        </main>
    </div>
</div>
<script src="{{asset('js/app.js')}}"></script>
<script src="{{asset('js/dashboard.js')}}"></script>
<script>
    window.onload = (event) => {
        $('#loader').hide();
    };
</script>
@yield('scripts')
</body>
</html>
