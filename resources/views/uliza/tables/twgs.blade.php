@extends('uliza.main_layout')

@section('content')

<div class="col-md-12">
	<div class="card mr-2">
		<div class="card-body">
			<div class="d-flex align-items-center justify-content-center p-1 text-white bg-success rounded box-shadow">
				<div class="text-center">
					<h6 class="mb-0 text-white">Technical Working Group</h6>
				</div>
			</div>
			<div class="card mt-1">
				<div class="card-body">
					<table class="table table-striped table-bordered table-hover data-table">
                        <thead>
                            <tr>
                                <th>TWG</th>
                                <th>Counties</th>
                                <th>Email List</th>
                                <th>Edit</th>
                            </tr>
                        </thead>
                        <tbody> 
                        	@foreach($twgs as $twg)
                        		<tr>
                        			<td> {{ $twg->twg }} </td>
                        			<td>
                        				@foreach($twg->county as $county)
                        					{{ $county->name }}, 
                        				@endforeach
                        			</td>
                                    <td>
                                        @foreach($twg->email_array as $email)
                                            {{ $email }}, 
                                        @endforeach
                                    </td>
                        			<td> <a href="{{ url('uliza-twg/' . $twg->id . '/edit') }} "> Edit</a> </td>
                        		</tr>
                        	@endforeach
                        </tbody>						
					</table>

				</div>
			</div>
			<br>
		</div>
	</div>
</div>

@endsection