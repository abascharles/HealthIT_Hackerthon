<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'EID/VL') }}</title>

    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/font-awesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/metisMenu/dist/metisMenu.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/animate.css/animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/dist/css/bootstrap.css') }}" />


    <!-- App styles -->
    <link rel="stylesheet" href="{{ asset('fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css') }}" />
    <link rel="stylesheet" href="{{ asset('fonts/pe-icon-7-stroke/css/helper.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style type="text/css">
        .back-link {
            margin: 4px;
        }
    </style>
    @yield('css_scripts')
    @yield('custom_css')

</head>
<body class="light-skin blank">

<div class="back-link">
    @switch(env('APP_LAB'))
        @case(1)
            <img src="{{ asset('img/kemri_nascoplogo.png') }}">
            @break
        @case(2)
            <img src="{{ asset('img/kisumunascoplogo.png') }}">
            @break
        @case(3)
            <img src="{{ asset('img/alupenascoplogo.png') }}">
            @break
        @case(4)
            <img src="{{ asset('img/wrplogo.png') }}">
            @break
        @case(5)
            <img src="{{ asset('img/ampathnascoplogo.png') }}">
            @break
        @case(6)
            <img src="{{ asset('img/cpghlogo.png') }}">
            @break
        @case(7)
            <img src="{{ asset('img/nhrllogo.png') }}">
            @break
        @case(8)
            <img src="{{ asset('img/nyumbaninascoplogo.png') }}">
            @break
        @case(9)
            <img src="{{ asset('img/knhnascoplogo.png') }}">
            @break
        @case(23)
            <img src="{{ asset('img/kulogo.png') }}">
            @break
        @case(25)
            <img src="{{ asset('img/amrefnascoplogo.png') }}">
            @break
        @default
            <img src="{{ asset('img/nascoplogo.png') }}">
    @endswitch
    {{-- <img src="{{ asset(env('LOGO', 'img/nascoplogo.png') ) }}"> --}}
</div>
<div class="pull-right" style="margin: 1.5em;">
    <a href="#" class="btn btn-primary" style="background-color: rgba(0, 0, 0, 0); color: black; border: none;"><strong class="font-extra-bold font-uppercase">{{ @Date("l, d F Y") }}</strong></a>
</div>

<div class="hr-line-dashed"></div>
<div class="login-container">
    <div class="hr-line-dashed" style="margin-top: 40px;"></div>
    @yield('content')
    <div class="hr-line-dashed"></div>
    <div class="row">
        <div class="col-md-12 text-center">
            <strong>&copy; NASCOP</strong> - 1987 - {{ @Date('Y') }} | All Rights Reserved.
        </div>
    </div>
    <div class="hr-line-dashed"></div>
</div>

<script src="{{ asset('vendor/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('vendor/slimScroll/jquery.slimscroll.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('vendor/metisMenu/dist/metisMenu.min.js') }}"></script>
<script src="{{ asset('vendor/sparkline/index.js') }}"></script>
<script src="{{ asset('vendor/iCheck/icheck.min.js') }}"></script>
<script src="{{ asset('js/homer.js') }}"></script>

@yield('scripts')

</body>
</html>