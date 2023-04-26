<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-heading">
                CIF Covid-19 Samples
            </div>
            <div class="panel-body">
                <div class="alert alert-success">
                    Select samples that have been entered in CIF and have arrived at the lab. Select those samples, then submit and those samples will be sent to the LIMS.
                </div>
                <div class="table-responsive">
                    <form  method="post" action="{{ url('covid_sample/cif/') }}" onsubmit="return confirm('Are you sure you want to import the selected samples?');">
                        @csrf

                        <table class="table table-striped table-bordered table-hover" id="{{ $div }}" >
                            <thead>
                                <tr class="colhead">
                                    <th> CIF ID </th>
                                    <th> Identifier </th>
                                    <th> National ID </th>
                                    <th> County </th>
                                    <th> Name </th>
                                    <th> DOB </th>
                                    <th> Age </th>
                                    <th> Date Collected </th>
                                    <th> Select Sample </th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach($samples as $sample)
                                    <tr>
                                        <td> {{ $sample->cif_sample_id ?? '' }} </td>
                                        <td> {{ $sample->patient->identifier ?? '' }} </td>
                                        <td> {{ $sample->patient->national_id ?? '' }} </td>
                                        <td> {{ $sample->patient->county ?? '' }} </td>
                                        <td> {{ $sample->patient->patient_name ?? '' }} </td>
                                        <td> {{ $sample->patient->dob ?? '' }} </td>
                                        <td> {{ $sample->age ?? '' }} </td>
                                        <td> {{ $sample->datecollected ?? '' }} </td>
                                        <td> 
                                            <div align="center">
                                                <input name="sample_ids[]" type="checkbox" class="checks" value="{{ $sample->id }}"  />
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <button type="submit" class="btn btn-primary">Pull Samples</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        
        $('#{{ $div }}').dataTable({
            // dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>tp",
            responsive: true,
            "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
            buttons: [
                {extend: 'copy',className: 'btn-sm'},
                {extend: 'csv',title: 'Download', className: 'btn-sm'},
                {extend: 'pdf', title: 'Download', className: 'btn-sm'},
                {extend: 'print',className: 'btn-sm'}
            ]
        });

    });
    
</script>