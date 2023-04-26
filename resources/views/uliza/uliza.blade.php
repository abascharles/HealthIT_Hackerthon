@extends('uliza.uliza_layout')

@section('content')
	@if($page->link == 'home')
		<div class="col-md-7">
			<div class="card mt-2">
				<div class="card-header bg-primary text-white text-center">
					<h4> {{ $page->title }} </h4>
				</div>
				<div class="card-body">
					{!! $page->get_raw() !!}
				</div>				
			</div>			
		</div>
		<div class="col-md-5">
			<div class="card mt-2">
				<div class="card-header bg-primary text-white text-center">
					<h4> {{ $page2->title }} </h4>
				</div>
				<div class="card-body">
					{!! $page2->get_raw() !!}
				</div>				
			</div>
		</div>
	@else
		<div class="col-md-12">
			<div class="card mt-2">
				<div class="card-header bg-primary text-white text-center">
					<h4> {{ $page->title }} </h4>
				</div>
				<div class="card-body">
					@if($page->link == 'contactus')
						<div class="row">
							<div class="col-md-3">
								{!! $page->get_raw() !!}
							</div>
							<div class="col-md-6">
								{!! $page->get_raw('map') !!}
							</div>
							<div class="col-md-3">
								<form autocomplete="off">
									<input class="form-control form-control-lg mb-2" type="text" name="name" placeholder="Your Name" required>
									<input class="form-control form-control-lg mb-2" type="email" name="email" placeholder="Your Email" required>
									<input class="form-control form-control-lg mb-2" type="text" name="subject" placeholder="Subject" required>
									<textarea class="form-control form-control-lg mb-2" name="message" placeholder="Your Message" required rows="5"></textarea>
									<button disabled class="btn btn-primary btn-lg  btn-block">
										Send
									</button>
								</form>								
							</div>
						</div>
					@elseif($page->link == 'uliza')
						<div class="row">
							<div class="col-md-9">
								{!! $page->get_raw() !!}
							</div>
							<div class="col-md-3">
								<img src="{{ asset('uliza_nascop/uliza-logo.png') }}">
								<h2 class="text-center">Log In</h2>
								<form autocomplete="off" method="POST" action="{{ url('uliza/login') }}">
									@csrf
									<input class="form-control mb-2" type="email" name="email" placeholder="Username" required>
									<input class="form-control mb-2" type="password" name="password" placeholder="Password" required>

									<button class="btn btn-primary btn-lg  btn-block">
										Login
									</button>
									<a href="#">Forgot Password?</a>
								</form>								
							</div>
						</div>
					@else
						{!! $page->get_raw() !!}
					@endif
				</div>				
			</div>
		</div>

	@endif

@endsection