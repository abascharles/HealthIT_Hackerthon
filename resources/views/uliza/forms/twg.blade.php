@extends('uliza.main_layout')

@section('content')


<div class="col-md-9">

	<div class="card mr-2">
		<div class="card-body">
			<div class="d-flex align-items-center justify-content-center p-1 text-white bg-success rounded box-shadow">
				<div class="text-center">
					<h6 class="mb-0 text-white">Technical Working Group</h6>
				</div>
			</div>
			<div class="card mt-1">
				<div class="card-body">
			        @if(isset($ulizaTwg))
			            <form method="POST" class="val-form" action='{{ url("/uliza-twg/{$ulizaTwg->id}") }}' >
			            @method('PUT')
			        @else
			            <form method="POST" class="val-form" action='{{ url("/uliza-twg/") }}'>
			        @endif
			        
			        @csrf
						<div class="form-row mb-3">
							<div class="col-md-12 input-group required">
								<div class="input-group-prepend">
									<span class="input-group-text text-left">
										TWG:
										<div style='color: #ff0000; display: inline;'>*</div>
									</span>
								</div>
								<input class="form-control" name="twg" required="required" type="text" value="{{ $ulizaTwg->twg ?? '' }}">
							</div>
						</div>

						<div class="form-row mb-3">
							<div class="col-md-3">
								<span class="input-group-text text-left">
									Counties:
								</span>
							</div>
							<select class="form-control col-md-9 select2" multiple name="counties[]">
								<option></option>
								@foreach($counties as $county)
									<option value="{{ $county->id }}" @if(isset($ulizaTwg) && $county->twg_id == $ulizaTwg->id) selected  @endif > {{ $county->name }} </option>
								@endforeach
							</select>
						</div>

						<div class="form-row mb-3">
							<label class="col-md-4">
								Default TWG
							</label>
							<div class="form-group col-md-4">
								<input class="form-check-input ml-1" name="default_twg"  required="required" type="radio" value="1"  @if(isset($ulizaTwg) && $ulizaTwg->default_twg == 1) checked  @endif>
								<label class="form-check-label ml-5" >Default</label>
							</div>
							<div class="form-group col-md-4">
								<input class="form-check-input ml-1" name="default_twg" required="required" type="radio" value="0"  @if(isset($ulizaTwg) && $ulizaTwg->default_twg == 0) checked  @endif>
								<label class="form-check-label ml-5" >Not Default</label>
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