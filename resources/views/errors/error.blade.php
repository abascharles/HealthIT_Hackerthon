<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>{{ $code }} | {{ env('APP_NAME') }}</title>
<link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/font-awesome.css') }}" />
<link rel="stylesheet" href="{{ asset('vendor/metisMenu/dist/metisMenu.css') }}" />
<link rel="stylesheet" href="{{ asset('vendor/animate.css/animate.css') }}" />
<link rel="stylesheet" href="{{ asset('vendor/bootstrap/dist/css/bootstrap.css') }}" />
<link rel="stylesheet" href="{{ asset('fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css') }}" />
<link rel="stylesheet" href="{{ asset('fonts/pe-icon-7-stroke/css/helper.css') }}" />
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="light-skin blank">
<div class="color-line"></div>
<div class="error-container">
<i class="pe-7s-way text-danger big-icon"></i>
<h1>{{ $code }}</h1>
<strong>{{ $title }}</strong>
<p>
{{ $description }}
</p>
<a href="{{ url('home') }}" class="btn btn-xs btn-danger">Go back to home</a>
</div>
<script src="{{ asset('vendor/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('vendor/slimScroll/jquery.slimscroll.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('vendor/metisMenu/dist/metisMenu.min.js') }}"></script>
<script src="{{ asset('vendor/iCheck/icheck.min.js') }}"></script>
<script src="{{ asset('vendor/sparkline/index.js') }}"></script>
<script src="{{ asset('js/homer.js') }}"></script>
</body>
</html>
