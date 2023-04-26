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
    @foreach($covidkits as $machinekey => $kits)
        @php
            $machine = \App\Machine::find($machinekey);
            if ($machine) {
                $machinename = $machine->machine . ' Kits';
            } else {            
                $machinename = $machinekey;
                if ($machinekey == '')
                    $machinename = 'Consumables';
            }
        @endphp
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-body table-responsive" style="padding: 20px;box-shadow: none; border-radius: 0px;">
                    <div class="alert alert-info">
                        <center><i class="fa fa-bolt"></i> COVID-19 <strong>{{ ucfirst($machinename) }}</strong> consumption report for the week starting {{ $consumption->start_of_week }} and ending {{ $consumption->end_of_week }}.
                        @if($machine)
                            <strong>(Week`s Tests:{{ number_format($machine->getCovidTestsDone($consumption->start_of_week, $consumption->end_of_week)) }})</strong>
                            <input type="hidden" name="machine[]" value="{{ $machine->id }}">   
                        @endif
                        </center>
                    </div>
                	<table class="table table-striped table-bordered table-hover data-table-modified" style="font-size: 10px;margin-top: 1em;">
                		<thead>
                			<tr>
                				<th>#</th>
                                <th>Material No</th>
                                <th>Product Description</th>
                                <th>Begining Balance</th>
                                <th>Received</th>
                                <th>Used</th>
                                <th>Positive Adjustment</th>
                                <th>Negative Adjustment</th>
                                <th>Losses/Wastage</th>
                                <th>Ending Balance</th>
                                <th>Requested</th>
                			</tr>
                		</thead>
                		<tbody>
                        @foreach($kits as $kitkey => $kit)
                            <tr>
                				<td>{{ $kitkey+1 }}</td>
                                <td>{{ $kit->material_no ?? '' }}</td>
                                <td>{{ $kit->product_description ?? '' }}</td>
                                <td>{{ $kit->specific_details($consumption->id)->begining_balance ?? '' }}</td>
                                <td>{{ $kit->specific_details($consumption->id)->received ?? '' }}</td>
                                <td>{{ $kit->specific_details($consumption->id)->kits_used ?? '' }}</td>
                                <td>{{ $kit->specific_details($consumption->id)->positive ?? '' }}</td>
                                <td>{{ $kit->specific_details($consumption->id)->negative ?? '' }}</td>
                                <td>{{ $kit->specific_details($consumption->id)->wastage ?? '' }}</td>
                                <td>{{ $kit->specific_details($consumption->id)->ending ?? '' }}</td>
                                <td>{{ $kit->specific_details($consumption->id)->requested ?? '' }}</td>
                			</tr>
            			@endforeach
                		</tbody>
                	</table>
                </div>
            </div>
        </div>
    @endforeach
    </div>
</div>

@endsection

@section('scripts') 

    @component('/tables/scripts')
        
    @endcomponent
    <script type="text/javascript">
        $(document).ready(function(){
            $('.data-table-modified').dataTable({
                dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>tp",
                lengthMenu: [ [50, -1], [50, "All"] ],                
                buttons : [
                {
                  text:  'Export to CSV',
                  extend: 'csvHtml5',
                  title: "{{ $consumption->lab->labdesc }} COVID Consumption {{date('Ymd', strtotime($consumption->start_of_week))}} to {{date('Ymd', strtotime($consumption->end_of_week))}}"
                },
                {
                  text:  'Export to Excel',
                  extend: 'excelHtml5',
                  title: "{{ $consumption->lab->labdesc }} COVID Consumption {{date('Ymd', strtotime($consumption->start_of_week))}} to {{date('Ymd', strtotime($consumption->end_of_week))}}"
                },
                {
                  text:  'Export to PDF',
                  extend: 'pdfHtml5',
                  title: "{{ $consumption->lab->labdesc }} COVID Consumption {{date('Ymd', strtotime($consumption->start_of_week))}} to {{date('Ymd', strtotime($consumption->end_of_week))}}"
                }
              ]
                
            });
        });
    </script>
@endsection