@extends('layouts.master')

    @component('/tables/css')
    @endcomponent

@section('content')
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-heading">
                    <div class="panel-tools">
                        <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                        <!-- <a class="closebox"><i class="fa fa-times"></i></a> -->
                    </div>
                    @if($pre == 'viral')
                        VL
                    @else
                        EID
                    @endif
                    POC Samples List
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped data-table table-bordered table-hover" >
                            <thead>
                                <tr>
                                    <th>Lab #</th>
                                    <th>Batch #</th>
                                    <th>Facility</th>
                                    <th>Patient #</th>
                                    <th>Age </th>
                                    <th>Gender</th>
                                    <th>Date Drawn</th>
                                    <th>Received Status</th>
                                    <th>Date Tested</th>
                                    <th>Date Dispatched</th>
                                    <th>Result</th>
                                    <th>Action</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach($samples as $key => $sample)
                                    <tr>
                                        <td> {{ $sample->id }} </td>
                                        <td> {{ $sample->batch_id }} </td>
                                        <td> {{ $sample->facility->name ?? '' }} </td>
                                        <td> {{ $sample->patient }} </td>
                                        <td> {{ $sample->age }} </td>
                                        <td> {{ $sample->gender ?? '' }} </td>
                                        <td> {{ $sample->my_date_format('datecollected') }} </td>
                                        <td> {{ $sample->received }} </td>
                                        <td> {{ $sample->my_date_format('datetested') }} </td>
                                        <td> {{ $sample->my_date_format('datedispatched') }} </td>
                                        <td> 
                                            @if($pre == '')
                                                {{ $results->where('id', $sample->result)->first()->name ?? '' }}
                                            @else
                                                {{ $sample->result }}
                                            @endif 
                                        </td>
                                        <td> 
                                            @if($sample->datedispatched)
                                                <a href="{{ url($pre . 'batch/summary/' . $sample->batch_id) }}" target="_blank" class="btn btn-success btn-xs" style="margin-bottom: 0.2em;"><i class='fa fa-print'></i> Summary</a>
                                                <br />
                                                <a href="{{ url($pre . 'sample/print/' . $sample->id) }}" target="_blank" class="btn btn-primary btn-xs" style="margin-bottom: 0.2em;"><i class='fa fa-print'></i> Print</a>
                                                <br />
                                            @endif

                                            @if(auth()->user()->user_type_id != 7)
                                                @if($sample->age_in_days < 30)
                                                    <a href="{{ url($pre . 'sample/' . $sample->id . '/edit/') }}" target="_blank" class="btn btn-warning btn-xs" style="margin-bottom: 0.2em;">Edit</a>
                                                    <br/>
                                                @endif
                                                    <a href="{{ url($pre . 'sample/' . $sample->id . '/edit_result/') }}" target="_blank" class="btn btn-success btn-xs" style="margin-bottom: 0.2em;">Update Result</a>
                                                    <br />
                                                @if(!$sample->result)
                                                    <form action="{{ url($pre . 'sample/' . $sample->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete the following sample?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-xs btn-danger">Delete</button>
                                                    </form>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $samples->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('scripts') 

    {{-- 
    @component('/tables/scripts')

    @endcomponent
    --}}

@endsection