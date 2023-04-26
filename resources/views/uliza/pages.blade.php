@extends('uliza.uliza_layout')

@section('content')

	<br />
	<br />
	<div class="col-md-12">
		<div class="card mt-2">
			<!-- <div class="card-header bg-primary text-white text-center">
				<h4> Pages </h4>
			</div> -->
			<div class="card-body">
				<table class="table table-bordered">
					<thead class="thead-dark">
						<tr>
							<th> Title </th>
							<th> Edit </th>
						</tr>			
					</thead>
					<tbody>
						@foreach($pages as $page)
							<tr>
								<td> {{ $page->title }} </td>
								<td> <a href="{{ url('uliza/edit/' . $page->id) }} ">Edit</a></td>
							</tr>
						@endforeach
					</tbody>
					
				</table>
			</div>	
		</div>	

	</div>

@endsection