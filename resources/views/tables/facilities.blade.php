@extends('layouts.master')

    @component('/tables/css')
    @endcomponent

@section('content')
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
            	<div class="panel-heading">
                    @if ($message = Session::get('success'))

                        <div class="alert alert-success" id="message">

                            <center><p>{{ $message }}</p></center>

                        </div>

                    @endif
                    @if ($message = Session::get('failed'))

                        <div class="alert alert-danger" id="message">

                            <center><p>{{ $message }}</p></center>

                        </div>

                    @endif
            		<div class="alert alert-success" style="/*padding-top: 4px;padding-bottom: 4px;">
		                <p>
		                    <center>FACILITIES</center>
		                </p>
		            </div>
            	</div>
                <div class="panel-body">
                    <div class="table-responsive">

                	    <table class="table table-striped table-bordered table-hover data-table" style="font-size: 10px;" >
                            <thead>
                                <tr class="colhead">
                                    @php
                                        echo $columns 
                                    @endphp
                                    <!-- <th>MFL</th>
                                    <th>Facility Name</th>
                                    <th>County</th>
                                    <th>District</th>
                                    <th>Facility Phone 1</th>
                                    <th>Facility Phone 2</th>
                                    <th>Facility Email</th>
                                    <th>Facility SMS Printer #</th>
                                    <th>Contact Person Names</th>
                                    <th>Contact Phone 1</th>
                                    <th>Contact Phone 2</th>
                                    <th>Contact Email</th>
                                    <th>G4S Branch</th>
                                    <th>Task</th> -->
                                </tr>
                            </thead>
                            <tbody>
                            	@php 
                            		echo $row 
                            	@endphp
                            </tbody>
                        </table>
                    </div>
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
        setTimeout(function(){
            $("#message").hide();
        }, 2000);
    </script>
@endsection