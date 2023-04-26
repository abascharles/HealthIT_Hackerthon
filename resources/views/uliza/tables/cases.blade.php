@extends('uliza.main_layout')

@section('content')


<div class="col-md-12">
    <form action="{{ url('/uliza-form/index') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-md-4"> 
                <div class="form-group">
                    <label class="col-sm-3 control-label">Select Status</label>
                    <div class="col-sm-9">
                        <select class="form-control" name="status_id">
                            <option></option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->id }}"

                                @if (isset($status_id) && $status_id == $status->id)
                                    selected
                                @endif

                                > {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>                        
                </div> 
            </div>
            <div class="col-md-4"> 
                <div class="form-group">
                    <label class="col-sm-3 control-label">Select Subcounty</label>
                    <div class="col-sm-9">
                        <select class="form-control" name="subcounty_id" id="subcounty_id">
                            <option></option>
                            @foreach ($subcounties as $subcounty)
                                <option value="{{ $subcounty->id }}"

                                @if (isset($subcounty_id) && $subcounty_id == $subcounty->id)
                                    selected
                                @endif

                                > {{ $subcounty->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>                        
                </div> 
            </div>
            <div class="col-md-4"> 
                <div class="form-group">
                    <label class="col-sm-3 control-label">Select County</label>
                    <div class="col-sm-9">
                        <select class="form-control" name="county_id" id="county_id">
                            <option></option>
                            @foreach ($counties as $county)
                                <option value="{{ $county->id }}"

                                @if (isset($county_id) && $county_id == $county->id)
                                    selected
                                @endif

                                > {{ $county->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>                        
                </div> 
            </div>
            <div class="col-md-4"> 
                <div class="form-group">
                    <label class="col-sm-3 control-label">Select Twg</label>
                    <div class="col-sm-9">
                        <select class="form-control" name="twg_id" id="twg_id">
                            <option></option>
                            @foreach ($twgs as $twg)
                                <option value="{{ $twg->id }}"

                                @if (isset($twg_id) && $twg_id == $twg->id)
                                    selected
                                @endif

                                > {{ $twg->twg }}
                                </option>
                            @endforeach
                        </select>
                    </div>                        
                </div> 
            </div>
            <div class="col-md-4"> 
                <div class="form-group">
                    <label class="col-sm-3 control-label">Start Date</label>
                    <div class="col-sm-9">
                        <input type="text" name="start_date" class="form-control date" />
                    </div>                        
                </div> 
            </div>
            <div class="col-md-4"> 
                <div class="form-group">
                    <label class="col-sm-3 control-label">End Date</label>
                    <div class="col-sm-9">
                        <input type="text" name="end_date" class="form-control date" />
                    </div>                        
                </div> 
            </div>

            <div class="col-sm-2">                
                <button class="btn btn-primary" id="date_range">Filter</button>  
            </div> 
        </div> 

        
    </form>
    
</div>

<div class="col-md-12">
	<div class="card mr-2">
		<div class="card-body">
			<div class="d-flex align-items-center justify-content-center p-1 text-white bg-success rounded box-shadow">
				<div class="text-center">
					<h6 class="mb-0 text-white">Clinical Summary Cases</h6>
				</div>
			</div>
			<div class="card mt-1">
				<div class="card-body">
					<table class="table table-striped table-bordered table-hover data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Uliza No</th>
                                <th>RTWG</th>
                                <th>County</th>
                                <th>Subcounty</th>
                                <th>Facility</th>
                                <th>Status</th>
                                <th>Reporting Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody> 
                        	@foreach($forms as $key => $form)
                        		<tr>
                        			<td> {{ $key }} </td>
                                    <td> {{ $form->nat_number }} </td>
                                    <td> {{ $form->twg->twg }} </td>
                                    <td> {{ $form->view_facility->county }} </td>
                                    <td> {{ $form->view_facility->subcounty }} </td>
                                    <td> {{ $form->view_facility->name }} </td>
                                    <td> {{ $form->get_prop_name($statuses, 'status_id') }} </td>
                        			<td> {{ $form->created_at }} </td>
                        			<td> 
                                        @if($form->status_id == 4)
                                            <a href="{{ url('uliza-review/view/' . $form->id) }} ">
                                                <button class="btn btn-success"> View Feedback </button>
                                            </a> 
                                        @else
                                            <a href="{{ url('uliza-review/create/' . $form->id) }} ">
                                                <button class="btn btn-primary"> Process Feedback </button>
                                            </a> 
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