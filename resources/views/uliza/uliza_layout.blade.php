<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Page title -->
    <title>NHCSC</title>

    <!-- Vendor styles -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/font-awesome.css') }}" />
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <link rel="stylesheet" href="{{ asset('css/select2/select2.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/toastr/toastr.min.css') }}" type="text/css">
    <link href="{{ asset('css/datapicker/datepicker3.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/summernote/summernote.css') }}" rel="stylesheet" type="text/css">

    <style type="text/css">
    	label.error {
    		color: #e74c3c;
		    margin: 5px 0 0 0;
		    font-weight: 400;
    	}
    </style>

</head>
<body>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-2"> <img src="{{ asset('uliza_nascop/moh-logo-2.png') }}" /> </div>
			<div class="col-md-7 align-self-center"> 
				<div class="my-auto">
					<h1> National HIV Clinical Support Center </h1> 
				</div>
			</div>
			<div class="col-md-3"> <img src="{{ asset('uliza_nascop/nascop-logo.png') }}" /> </div>

		</div>

		<nav class="nav navbar sticky-top navbar-expand-lg navbar-dark bg-dark">
			<!-- <a class="navbar-brand" href="#">Home</a> -->
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			    <span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav mr-auto justify-content-center">					
					<li class="nav-item active"> <a class="nav-link" href="{{ url('uliza/home') }}">Home</a> </li>
					<li class="nav-item"> <a class="nav-link" href="{{ url('uliza/uliza') }}">Uliza</a> </li>
					<li class="nav-item"> <a class="nav-link" href="{{ url('uliza/ushauri') }}">Ushauri</a> </li>
					<li class="nav-item"> <a class="nav-link" href="{{ url('uliza/trainsmart') }}">Trainsmart</a> </li>
					<li class="nav-item"> <a class="nav-link" href="{{ url('uliza/echo') }}">ECHO</a> </li>
					<li class="nav-item"> <a class="nav-link" href="{{ url('uliza/faqs') }}">FAQS</a> </li>
					<li class="nav-item"> <a class="nav-link" href="{{ url('uliza/contactus') }}">Contact Us</a> </li>
				</ul>

			</div>
			
		</nav>

		<div class="row">
			@yield('content')
		</div>
		
	</div>

	<!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script> -->
	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>


	<script src="{{ asset('vendor/iCheck/icheck.min.js') }}"></script>
	<script src="{{ asset('js/select2/select2.full.min.js') }}"></script>
	<script src="{{ asset('js/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('js/datapicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('js/summernote/summernote.js') }}"></script>
	<script src="{{ asset('js/validate/jquery.validate.min.js') }}"></script>


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
	                    toastr.success("{!! $toast_message !!}");
	                @endif
	            });
	        @endif

        });
        
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