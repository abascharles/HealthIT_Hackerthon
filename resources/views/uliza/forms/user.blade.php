@extends('uliza.main_layout')

@section('content')

<div class="col-md-9">

	<div class="card mr-2">
		<div class="card-body">
			<div class="d-flex align-items-center justify-content-center p-1 text-white bg-success rounded box-shadow">
				<div class="text-center">
					<h6 class="mb-0 text-white">User</h6>
				</div>
			</div>
			<div class="card mt-1">
				<div class="card-body">
			        @if(isset($uliza_user))
			            <form method="POST" class="val-form" action='{{ url("/uliza-user/{$uliza_user->id}") }}' >
			            @method('PUT')
			        @else
			            <form method="POST" class="val-form" action='{{ url("/uliza-user/") }}'>
			        @endif

			        @csrf
			        	<input name="lab_id" type="hidden" value="0">

			        	@if(auth()->user()->uliza_secretariat)
			        		<div class="alert alert-info">
			        			Uliza Reviewer <br />
			        			Twg: {{ auth()->user()->twg->twg ?? '' }} 			        			
			        		</div>

			        		<input type="hidden" name="user_type_id" value="104">
			        		<input type="hidden" name="twg_id" value="{{ auth()->user()->twg_id }}">

			        	@else

						<div class="form-row mb-3">
							<div class="col-md-3">
								<span class="input-group-text text-left"> User Type: </span>
							</div>
							<select class="form-control col-md-9 select2" name="user_type_id" required>
								<option></option>
								@foreach($user_types as $user_type)
									<option value="{{ $user_type->id }}" @if(isset($uliza_user) && $uliza_user->user_type_id == $user_type->id) selected  @endif > {{ $user_type->user_type }} </option>
								@endforeach
							</select>
						</div>

						<div class="form-row mb-3">
							<div class="col-md-3">
								<span class="input-group-text text-left"> TWG: </span>
							</div>
							<select class="form-control col-md-9 select2" name="twg_id" required>
								<option></option>
								@foreach($twgs as $twg)
									<option value="{{ $twg->id }}" @if(isset($uliza_user) && $uliza_user->twg_id == $twg->id) selected  @endif > {{ $twg->twg }} </option>
								@endforeach
							</select>
						</div>

						@endif

						<div class="form-row mb-3">
							<div class="col-md-3">
								<span class="input-group-text text-left"> Receive Emails: </span>
							</div>
							<select class="form-control col-md-9 select2" name="receive_emails" required>
								<option></option>
								<option value="0" @if(isset($uliza_user) && !$uliza_user->receive_emails) selected @endif> Should Not Receive Emails </option>
								<option value="1" @if(isset($uliza_user) && $uliza_user->receive_emails) selected @endif> Should Receive Emails </option>
							</select>
						</div>

						<div class="form-row mb-3">
							<div class="col-md-12 input-group required">
								<div class="input-group-prepend">
									<span class="input-group-text text-left">
										Email:
										<span style='color: #ff0000;'>*</span>
									</span>
								</div>
								<input class="form-control" name="email" required="required" type="email" value="{{ $uliza_user->email ?? '' }}">
							</div>
						</div>

						<div class="form-row mb-3">
							<div class="col-md-12 input-group required">
								<div class="input-group-prepend">
									<span class="input-group-text text-left"> Telephone: </span>
								</div>
								<input class="form-control" name="telephone" type="text" value="{{ $uliza_user->telephone ?? '' }}">
							</div>
						</div>

						<div class="form-row mb-3">
							<div class="col-md-12 input-group required">
								<div class="input-group-prepend">
									<span class="input-group-text text-left">
										Surname:
										<span style='color: #ff0000;'>*</span>
									</span>
								</div>
								<input class="form-control" name="surname" required="required" type="text" value="{{ $uliza_user->surname ?? '' }}">
							</div>
						</div>
						<div class="form-row mb-3">
							<div class="col-md-12 input-group required">
								<div class="input-group-prepend">
									<span class="input-group-text text-left">
										Other Name:
										<span style='color: #ff0000;'>*</span>
									</span>
								</div>
								<input class="form-control" name="oname" required="required" type="text" value="{{ $uliza_user->oname ?? '' }}">
							</div>
						</div>

					  
						<div class="mb-3 float-right">
							<button class="btn btn-warning" type="submit" >Submit</button>
						</div>
					</form>					
				</div>
			</div>
			<br>
		</div>
	</div>
</div>

@endsection

@section('scripts')

    @component('/uliza/forms/scripts')
	@endcomponent
@endsection