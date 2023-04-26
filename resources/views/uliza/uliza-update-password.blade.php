@extends('uliza.main_layout')

@section('content')

<div class="col-md-6">
	<div class="card mr-2">
		<div class="card-body">
			<div class="d-flex align-items-center justify-content-center p-1 text-white bg-primary rounded box-shadow">
				<div class="text-center">
					<h6 class="mb-0 text-white">Update Password Form</h6>
				</div>
			</div>
			<div class="p-3 " >
				<form autocomplete="off" class="val-form" method="POST" action="{{ url('uliza/update-password') }} ">
					@csrf

					<div class="form-row mb-3">
						<div class="col-md-12 input-group">
							<div class="input-group-prepend">
								<span class="input-group-text text-left" id="password_label">Password :</span>
							</div>
							<input aria-describedby="password_label" class="form-control" name="password" type="password" required id='password_field'>
						</div>
					</div>

					<div class="form-row mb-3">
						<div class="col-md-12 input-group">
							<div class="input-group-prepend">
								<span class="input-group-text text-left" id="confirm_password_label">Confirm Password :</span>
							</div>
							<input aria-describedby="confirm_password_label" class="form-control" name="confirm_password" type="password" required id='confirm_password'>
						</div>
					</div>
					  
					<div class="mb-3 float-right">
						<button class="btn btn-warning" type="submit" >Submit</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>



@endsection


@section('scripts')
    @component('/uliza/forms/scripts')
        @slot('val_rules')
           ,
            rules: {
                confirm_password: {
                    equalTo: "#password_field"
                }                                
            }
        @endslot
	@endcomponent
@endsection