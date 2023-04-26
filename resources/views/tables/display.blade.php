@extends('layouts.master')

    @component('/tables/css')
    @endcomponent

@section('content')
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-body">
            	    <table class="table table-striped table-bordered table-hover data-table" style="/*font-size: 10px;" >
                        <thead>
                            <tr class="colhead">
                                @php
                                    echo $columns 
                                @endphp
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


@endsection

@section('scripts') 

    @component('/tables/scripts')

    @endcomponent
@endsection