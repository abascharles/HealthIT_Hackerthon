@extends('uliza.main_layout')

@section('content')

<div class="col-md-12">
	<div class="card mr-2">
		<div class="card-body">
			<div class="d-flex align-items-center justify-content-center p-1 text-white bg-success rounded box-shadow">
				<div class="text-center">
					<h6 class="mb-0 text-white">Users</h6>
				</div>
			</div>
			<div class="card mt-1">
				<div class="card-body">
					<table class="table table-striped table-bordered table-hover data-table">
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>Telephone</th>
                                <th>Name</th>
                                <th>User Type</th>
                                <th>TWG</th>
                                <th>Receive Emails</th>
                                <th>Edit</th>
                                <th>Resend Email</th>
                                <th>Deactivate / Restore</th>
                            </tr>
                        </thead>
                        <tbody> 
                        	@foreach($users as $user)
                        		<tr>
                        			<td> {{ $user->email }} </td>
                                    <td> {{ $user->telephone }} </td>
                                    <td> {{ $user->full_name }} </td>
                                    <td> {{ $user->user_type->user_type }} </td>
                                    <td> {{ $user->twg->twg ?? '' }} </td>
                                    <td> {{ $user->receive_emails ? 'Receiving' : 'Not Receiving' }} </td>
                        			<td> <a href="{{ url('uliza-user/' . $user->id . '/edit') }} "> Edit</a> </td>
                                    <td> <a href="{{ url('uliza-user/resend_email/' . $user->id) }} "> Resend</a> </td>
                                    <td>        
                                        @if($user->deleted_at)                              
                                            <form action="{{ url('/uliza-user/restore/' . $user->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-xs btn-success">Restore</button> 
                                            </form>
                                        @else                                
                                            <form action="{{ url('/uliza-user/' . $user->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-warning">Deactivate</button> 
                                            </form>
                                        @endif
                                    </td>
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