@extends('layouts.master')

@component('/forms/css')
    <link href="{{ asset('css/datapicker/datepicker3.css') }}" rel="stylesheet" type="text/css">
@endcomponent

@section('content')
   <div class="content">
        <div>
            
        @if($create)

            @if (isset($worksheet))
                <form method="POST" action="{{ $worksheet->view_url }}" class="form-horizontal" target="_blank">
                    @csrf
                    @method('PUT')
            @else

                @if($machine_type == 0)
                <form method="POST" action="/cancerworksheet" class="form-horizontal">
                @endif

                <div class="row">
                    <div class="col-lg-12">
                        <div class="hpanel">
                            <div class="panel-heading">
                                <center>Samples</center>
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
	<table  class="table table-striped table-bordered table-hover data-table">
		<thead>
			<tr>
				<th>#</th>
				<th>Lab ID</th>
				<th>Patient</th>
				<th>Facility</th>
				<th>Entry Type</th>
				<th>Run</th>
				<th>Previous Runs</th>
				<th>Original ID</th>
				<th>Date Collected</th>
				<th>Entered By</th>
				<th>Release as Redraw</th>
				<th>Update</th>
				<th>Delete</th>
			</tr>
		</thead>
		<tbody>
			@foreach($samples as $key => $sample)
				<tr>
					<td> {{ ($key+1) }} </td>
					<td> {{ $sample->id }} </td>
					<td> {{ $sample->patient->patient }} </td>
					<td> {{ $sample->facility->name ?? '' }} </td>
					@if($sample->site_entry == 0)
						<td> Lab Entry </td>
					@elseif($sample->site_entry == 1)
						<td> Site Entry </td>
					@endif
					<td> {{ $sample->run }} </td>
					<td><a href="{{ url('sample/runs/' . $sample->id) }}" target="_blank"> Runs</a> </td>
					@if($sample->parentid)
						<td> {{ $sample->parentid ?? null }} </td>
					@else
						<td></td>
					@endif
					
					<td> {{ $sample->datecollected }} </td>

					@if($sample->site_entry == 0)
						<td> {{ $sample->surname . ' ' . $sample->oname }} </td>
					@elseif($sample->site_entry == 1)
						<td>  </td>
					@endif


	                <td> <a href="{{ url('sample/release/' . $sample->id) }}" class="confirmAction"> Release</a> </td>
	                <td> <a href="{{ url('sample/' . $sample->id . '/edit') }}"> Edit</a> </td>
	                <td> 
                        <form action="{{ url('sample/' . $sample->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete the following sample?');">
                            @csrf
                            @method('DELETE')
	                        <button type="submit" class="btn btn-xs btn-primary">Delete</button>
	                    </form>
	                </td>
				</tr>
			@endforeach

		</tbody>

	</table>
</div>
                            </div>
                        </div>
                    </div>                
                </div>

                @if($machine_type != 0)
                    @if(isset($covid))            
                    <form method="POST" action="/covid_worksheet" class="form-horizontal">
                    @else
                    <form method="POST" action="{{ url('cancerworksheet') }}" class="form-horizontal" target="_blank">
                    @endif
                @endif

                @isset($combined)
                    <input type="hidden" value="{{ $combined }}" name="combined" >
                @endisset

                @isset($sampletype)
                    <input type="hidden" value="{{ $sampletype }}" name="sampletype" >
                @endisset

                @isset($entered_by)
                    @if(is_array($entered_by))
                        @foreach($entered_by as $value)
                            <input type="hidden" value="{{ $value }}" name="entered_by[]" >
                        @endforeach
                    @else
                        <input type="hidden" value="{{ $entered_by }}" name="entered_by" >
                    @endif
                @endisset

                <input type="hidden" value="{{ $machine_type }}" name="machine_type" >

                @if($limit)
                    <input type="hidden" value="{{ $limit }}" name="limit" >
                @endif
            @endif

            @csrf

            <div class="row">
                <div class="col-lg-12">
                    <div class="hpanel">
                        <div class="panel-heading">
                            <center>Worksheet Information</center>
                        </div>
                        <div class="panel-body">

                            @if($machine_type == 1)

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Lot No</label>
                                    <div class="col-sm-8">
                                        <input class="form-control"  name="lot_no" type="text" value="{{ $worksheet->lot_no ?? '' }}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Date Cut</label>
                                    <div class="col-sm-8">
                                        <div class="input-group date date_cut">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text"  class="form-control" value="{{ $worksheet->datecut ?? '' }}" name="datecut">
                                        </div>
                                    </div>                            
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">HIQCAP Kit No</label>
                                    <div class="col-sm-8">
                                        <input class="form-control"  name="hiqcap_no" type="text" value="{{ $worksheet->hiqcap_no ?? '' }}">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Rack No</label>
                                    <div class="col-sm-8">
                                        <input class="form-control"  name="rack_no" type="text" value="{{ $worksheet->rack_no ?? '' }}">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Spek Kit No</label>
                                    <div class="col-sm-8">
                                        <input class="form-control"  name="spekkit_no" type="text" value="{{ $worksheet->spekkit_no ?? '' }}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">KIT EXP</label>
                                    <div class="col-sm-8">
                                        <div class="input-group date date_exp">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text"  class="form-control" value="{{ $worksheet->kitexpirydate ?? '' }}" name="kitexpirydate">
                                        </div>
                                    </div>                            
                                </div>

                            @else

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Sample Prep</label>
                                    <div class="col-sm-3">
                                        <input class="form-control"  name="sample_prep_lot_no" type="text" value="{{ $worksheet->sample_prep_lot_no ?? '' }}">
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="input-group date date_exp">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text"  class="form-control" value="{{ $worksheet->sampleprepexpirydate ?? '' }}" name="sampleprepexpirydate" placeholder="Expiry Date">
                                        </div>
                                    </div>                            
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Bulk Lysis Buffer</label>
                                    <div class="col-sm-3">
                                        <input class="form-control"  name="bulklysis_lot_no" type="text" value="{{ $worksheet->bulklysis_lot_no ?? '' }}">
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="input-group date date_exp">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text"  class="form-control" value="{{ $worksheet->bulklysisexpirydate ?? '' }}" name="bulklysisexpirydate" placeholder="Expiry Date">
                                        </div>
                                    </div>                            
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Control</label>
                                    <div class="col-sm-3">
                                        <input class="form-control"  name="control_lot_no" type="text" value="{{ $worksheet->control_lot_no ?? '' }}">
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="input-group date date_exp">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text"  class="form-control" value="{{ $worksheet->controlexpirydate ?? '' }}" name="controlexpirydate" placeholder="Expiry Date">
                                        </div>
                                    </div>                            
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Calibrator</label>
                                    <div class="col-sm-3">
                                        <input class="form-control"  name="calibrator_lot_no" type="text" value="{{ $worksheet->calibrator_lot_no ?? '' }}">
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="input-group date date_exp">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text"  class="form-control" value="{{ $worksheet->calibratorexpirydate ?? '' }}" name="calibratorexpirydate" placeholder="Expiry Date">
                                        </div>
                                    </div>                            
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Amplification Kit</label>
                                    <div class="col-sm-3">
                                        <input class="form-control"  name="amplification_kit_lot_no" type="text" value="{{ $worksheet->amplification_kit_lot_no ?? '' }}">
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="input-group date date_exp">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text"  class="form-control" value="{{ $worksheet->amplificationexpirydate ?? '' }}" name="amplificationexpirydate" placeholder="Expiry Date">
                                        </div>
                                    </div>                            
                                </div>

                            @endif

                            <div class="form-group cdc-only">
                                <label class="col-sm-4 control-label">CDC Worksheet No (Lab Defined)</label>
                                <div class="col-sm-8">
                                    <input class="form-control" name="cdcworksheetno" type="text" value="{{ $worksheet->cdcworksheetno ?? '' }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-8 col-sm-offset-4">
                                    <button class="btn btn-success" type="submit">Save & Print Worksheet</button>
                                </div>
                            </div>




                        </div>
                    </div>
                </div>
            </div>
            
            </form>
            
        @else

            <div class="row">
                <div class="col-lg-12">
                    <div class="hpanel">
                        <div class="panel-body"> 
                            <div class="alert alert-warning">
                                <center>
                                    There are only {{ $count }} samples that qualify to be in a worksheet.
                                </center>
                            </div>
                        <br />
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="hpanel">
                        <div class="panel-heading">
                            <center>Samples</center>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
								<table  class="table table-striped table-bordered table-hover data-table">
									<thead>
										<tr>
											<th>#</th>
											<th>Lab ID</th>
											<th>Patient</th>
											<th>Facility</th>
											<th>Entry Type</th>
											<th>Spots</th>
											<th>Run</th>
											<th>Previous Runs</th>
											<th>Original ID</th>
											<th>Date Collected</th>
											<th>Entered By</th>
											<th>Release as Redraw</th>
											<th>Update</th>
											<th>Delete</th>
										</tr>
									</thead>
									<tbody>
										@foreach($samples as $key => $sample)
											{{-- <tr>
												<td> {{ ($key+1) }} </td>
												<td> {{ $sample->id }} </td>
												<td> {{ $sample->patient->patient ?? '' }} </td>
												<td> {{ $sample->name }} </td>
												@if($sample->site_entry == 0)
													<td> Lab Entry </td>
												@elseif($sample->site_entry == 1)
													<td> Site Entry </td>
												@endif
												<td> {{ $sample->spots }} </td>
												<td> {{ $sample->run }} </td>
												<td><a href="{{ url('sample/runs/' . $sample->id) }}" target="_blank"> Runs</a> </td>
												@if($sample->parentid)
													<td> {{ $sample->parentid ?? null }} </td>
												@else
													<td></td>
												@endif
												
												<td> {{ $sample->datecollected }} </td>

												@if($sample->site_entry == 0)
													<td> {{ $sample->surname . ' ' . $sample->oname }} </td>
												@elseif($sample->site_entry == 1)
													<td>  </td>
												@endif


								                <td> <a href="{{ url('sample/release/' . $sample->id) }}" class="confirmAction"> Release</a> </td>
								                <td> <a href="{{ url('sample/' . $sample->id . '/edit') }}"> Edit</a> </td>
								                <td> 
							                        <form action="{{ url('sample/' . $sample->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete the following sample?');">
							                            @csrf
							                            @method('DELETE')
								                        <button type="submit" class="btn btn-xs btn-primary">Delete</button>
								                    </form>
								                </td>
											</tr> --}}
										@endforeach

									</tbody>

								</table>
							</div>
                        </div>
                    </div>
                </div>                
            </div>

        @endif

        

      </div>
    </div>

@endsection

@section('scripts')

    @component('/forms/scripts')
        @slot('js_scripts')
            <script src="{{ asset('js/datapicker/bootstrap-datepicker.js') }}"></script>
        @endslot



        $(".date_cut").datepicker({
            startView: 0,
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: true,
            autoclose: true,
            startDate: "-7d",
            endDate: "+7d",
            format: "yyyy-mm-dd"
        });

        $(".date_exp").datepicker({
            startView: 0,
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: true,
            autoclose: true,
            startDate: "-5d",
            endDate: "+5y",
            format: "yyyy-mm-dd"
        });

        @if(env('APP_LAB') != 2)
            $(".cdc-only").hide();
        @endif

    @endcomponent


@endsection
