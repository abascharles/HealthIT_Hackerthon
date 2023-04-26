@extends('layouts.master')

    @component('/tables/css')
    @endcomponent

@section('content')
<style type="text/css">
    .spacing-div-form {
        margin-top: 15px;
    }
</style>
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-body" style="padding: 20px;box-shadow: none; border-radius: 0px;">
                	<table class="table table-striped table-bordered table-hover" style="font-size: 10px;margin-top: 1em;">
                		<thead>
                			<tr>
                				<th>#</th>
                                <th>Start of Week</th>
                                <th>End of Week</th>
                                <th>Lab</th>
                                <th>All Tests</th>
                                <th>Action</th>
                			</tr>
                		</thead>
                		<tbody>
                        @forelse($consumptions as $key => $consumption)
                            <tr>
                				<td>{{ $key+1 }}</td>
                                <td>{{ $consumption->start_of_week ?? '' }}</td>
                                <td>{{ $consumption->end_of_week ?? '' }}</td>
                                <td>{{ $consumption->lab->name ?? '' }}</td>
                                <td>{{ $consumption->tests_done }}</td>
                                <td><a class="btn btn-primary" href="{{ url('covidkits/reports/' . $consumption->id) }}" >View Details</a></td>
                			</tr>
                        @empty
                            <tr>
                                <td colspan="5">No COVID consumption data available</td>
                            </tr>
            			@endforelse
                		</tbody>
                	</table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts') 

    @component('/tables/scripts')

    @endcomponent
    <script type="text/javascript">
        $(document).ready(function(){
            
        });
    </script>
@endsection