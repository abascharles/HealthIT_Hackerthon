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
        body.light-skin #menu {
            width: 240px;
        }
        #wrapper {
            margin: 0px 0px 0px 230px;
        }
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
@include('layouts.sidenav')

<!-- Main Wrapper -->
<div id="wrapper">
    <div class="small-header">
        <div class="hpanel">
            <div class="panel-body" style="padding-top:.5em;padding-bottom:.1em;">
                <h6 class="font-light pull-right" style="margin: 0px;">
                    <strong>
                        Welcome, 
                        @if(Auth()->user()->user_type_id == 5)
                            {{-- $user->name  --}}
                            {{ session('logged_facility')->name ?? ''  }}
                        @else
                            {{ Auth()->user()->surname }} {{ Auth()->user()->oname }}
                        @endif
                    </strong>
                    <p style="margin-top: .5em;margin-bottom: 0px;">{{ @Date("l, d F Y") }}</p>
                </h6>
                <div class="row">
                    @if (!Auth()->user()->lab_user)
                        <div class="col-md-3" style="margin-top: .7em;margin-bottom: .7em;">
                            <h2 class="font-light m-b-xs">
                                {{ $pageTitle ?? '' }}
                            </h2>
                        </div>
                        <div class="col-md-6">
                            
                        </div>
                    @else
                    <div class="col-md-2" style="margin-top: .7em;">
                        <h2 class="font-light m-b-xs">
                            {{ $pageTitle ?? '' }}
                        </h2>
                    </div>
                    <div class="col-md-5">
                        <div class="col-md-8" style="padding-top: 8px;">
                            <center>
                                <p style="margin-top:6px;font-size: 14px;display: inline;">
                                    <div id="systxt" style="display: inline;"><strong>EARLY INFANT DIAGNOSIS</strong></div> <strong>TESTING SYSTEM</strong>
                                </p>
                            </center>
                        </div>
                            <div class="col-md-4">
                                <button class="btn btn-success" id="sysSwitch" value="Viralload" style="margin-top:.5em;">
                                    Switch to Viralload
                                </button>
                            </div>
                    </div>
                        @if(Auth()->user()->user_type_id < 2)
                            @if(in_array(env('APP_LAB'), [1]))
                                @if(session('testingSystem') != 'DR')
                                <div class="col-md-1">
                                    <button class="btn btn-success" id="drswitch" style="margin-top:.5em;">
                                        Switch to DR
                                    </button>
                                </div>
                                @endif
                            @endif
                            @if(session('testingSystem') != 'HPV')
                            <div class="col-md-1">
                                <button class="btn btn-success" id="hpvswitch" style="margin-top:.5em;">
                                    Switch to HPV
                                </button>
                            </div>
                            @endif
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

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
            var message = $(this).attr("confirm_message");
            if(message){
                return confirm(message);
            }
            return confirm('Are you sure you would like to submit?');
        });

        current = "<?= @session('testingSystem')?>";
        if(current != ''){
            if(current == 'DR') { test = 'Viralload';text = '<strong>DRUG RESISTANCE</strong>'; } 
            else if(current == 'EID'){ test = 'Viralload'; text = '<strong>EARLY INFANT DIAGNOSIS</strong>'; } 
            @if(in_array(env('APP_LAB'), [7])) 
                else if (current == 'Viralload'){ test = 'Covid'; text = '<strong>VIRAL LOAD</strong>'; }    
                else if (current == 'Covid'){ test = 'DR'; text = '<strong>Covid-19</strong>'; }         
            @elseif(!in_array(env('APP_LAB'), [8])) 
                else if (current == 'Viralload'){ test = 'Covid'; text = '<strong>VIRAL LOAD</strong>'; } 
                else if (current == 'Covid'){ test = 'EID'; text = '<strong>Covid-19</strong>'; }
            @else
                else if (current == 'Viralload'){ test = 'EID'; text = '<strong>VIRAL LOAD</strong>'; } 
            @endif
            else if (current == 'CD4'){ test = 'EID'; text = '<strong>CD4</strong>'; }
            else if (current == 'HPV'){ test = 'EID'; text = '<strong>Cervical Cancer</strong>'; }

            /*if(current == 'DR'){
                $("#drswitch").hide();
            }*/
            // else {test = 'Viralload';text = '<strong>EARLY INFANT DIAGNOSIS</strong>';}
            $("#sysSwitch").html("Switch to "+test);
            $("#sysSwitch").val(test);
            $("#systxt").html(text);
        }

        // sysswitch
        
        $("#sysSwitch").click(function(){
            sys = $(this).val();
            $.get("<?= url('system_switch/"+sys+"'); ?>", function(data){
                location.replace("<?= url('home'); ?>");
            });
        });

        $("#drswitch").click(function(){
            $.get("<?= url('system_switch/DR'); ?>", function(data){
                location.replace("<?= url('home'); ?>");
            });
        });

        $("#cd4Switch").click(function(){
            $.get("<?= url('system_switch/CD4'); ?>", function(data){
                location.replace("<?= url('home'); ?>");
            });
        });

        $("#hpvswitch").click(function(){
            $.get("<?= url('system_switch/HPV'); ?>", function(data){
                location.replace("<?= url('home'); ?>");
            });
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

    function set_message(message)
    {
        setTimeout(function(){
            toastr.options = {
                closeButton: true,
                progressBar: true,
                showMethod: 'slideDown',
                timeOut: 15000,
                preventDuplicates: true
            };
            toastr.warning(message); 
        });
    }

</script>

@include('layouts.searches')

@yield('scripts')

</body>
</html>
