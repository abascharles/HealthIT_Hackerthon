<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Page title -->
    <title>EID/VL | LAB</title>

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
    <!--<link rel="shortcut icon" type="image/ico" href="favicon.ico" />-->

    <!-- Vendor styles -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/font-awesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/metisMenu/dist/metisMenu.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/animate.css/animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/dist/css/bootstrap.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/select2/select2.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/toastr/toastr.min.css') }}" type="text/css">

    <!-- App styles -->
    <link rel="stylesheet" href="{{ asset('fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css') }}" />
    <link rel="stylesheet" href="{{ asset('fonts/pe-icon-7-stroke/css/helper.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />

    @yield('css_scripts')
    @yield('custom_css')
    
    <style type="text/css">
        #toast-container > div {
            color: black;
        }
        .navbar-nav>li>a {
            padding: 15px 15px;
            font-size: 13px;
            color: black;
        }
        .btn {
            padding: 4px 8px;
            font-size: 12px;
        }
    </style>

</head>
<body class="light-skin fixed-navbar sidebar-scroll">

<!-- Header -->
@include('layouts.topnav')
<!-- Navigation -->

<!-- Main Wrapper -->
<div id="wrapper">
    
    @yield('content')

    <!-- Footer-->
    <footer class="footer">
        <center>&copy; NASCOP 2010 - {{ @Date('Y') }} | All Rights Reserved</center>
    </footer>

</div>

<script src="{{ asset('vendor/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('vendor/slimScroll/jquery.slimscroll.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>

<script src="{{ asset('vendor/metisMenu/dist/metisMenu.min.js') }}"></script>
<script src="{{ asset('vendor/iCheck/icheck.min.js') }}"></script>
<script src="{{ asset('vendor/peity/jquery.peity.min.js') }}"></script>
<script src="{{ asset('vendor/sparkline/index.js') }}"></script>
<script src="{{ asset('js/homer.js') }}"></script>

<script src="{{ asset('js/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('js/toastr/toastr.min.js') }}"></script>

<script type="text/javascript">
    
    $(document).ready(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        @php
            $toast_message = session()->pull('toast_message');
            $toast_error = session()->pull('toast_error');
        @endphp
        
        @if($toast_message)
            setTimeout(function(){
                toastr.options = {
                    closeButton: false,
                    progressBar: false,
                    showMethod: 'slideDown',
                    timeOut: 10000
                };
                @if($toast_error)
                    toastr.error("{!! $toast_message !!}", "Warning!");
                @else
                    toastr.warning("{!! $toast_message !!}");
                @endif
            });
        @endif

        $(".confirmAction").on('click', function(){
            return confirm('Are you sure?');
        });

        $(".confirmSubmit").on('submit', function(){
            return confirm('Are you sure you would like to submit?');
        });
    });

    function set_warning(message)
    {
        setTimeout(function(){
            toastr.options = {
                closeButton: true,
                progressBar: true,
                showMethod: 'slideDown',
                timeOut: 10000,
                preventDuplicates: true
            };
            toastr.error(message, "Warning!"); 
        });
    }

</script>

@yield('scripts')

</body>
</html>
