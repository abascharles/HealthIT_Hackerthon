@extends('layouts.master')

@component('/forms/css')
    <link href="{{ asset('css/datapicker/datepicker3.css') }}" rel="stylesheet" type="text/css">
@endcomponent

@section('custom_css')
    <style type="text/css">
        .hpanel {
            margin-bottom: 4px;
        }
        .hpanel.panel-heading {
            padding-bottom: 2px;
            padding-top: 4px;
        }
    </style>
@endsection

@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="hpanel">
                <div class="form-group">
                    <label class="col-sm-4 control-label">Facilities</label>
                    <div class="col-sm-8">
                        <select class="form-control" id="facility_search">

                        </select>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <br />

    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="hpanel">
                <div class="form-group">
                    <label class="col-sm-4 control-label">Batches</label>
                    <div class="col-sm-8">
                        <select class="form-control" id="batch_search">

                        </select>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="hpanel">
                <div class="form-group">
                    <label class="col-sm-4 control-label">Patients</label>
                    <div class="col-sm-8">
                        <select class="form-control" id="patient_search">

                        </select>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="hpanel">
                <div class="form-group">
                    <label class="col-sm-4 control-label">Worksheets</label>
                    <div class="col-sm-8">
                        <select class="form-control" id="worksheet_search">

                        </select>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="hpanel">
                <div class="form-group">
                    <label class="col-sm-4 control-label">Viralload Batches</label>
                    <div class="col-sm-8">
                        <select class="form-control" id="viralbatch_search">

                        </select>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="hpanel">
                <div class="form-group">
                    <label class="col-sm-4 control-label">Viralload Patients</label>
                    <div class="col-sm-8">
                        <select class="form-control" id="viralpatient_search">

                        </select>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="hpanel">
                <div class="form-group">
                    <label class="col-sm-4 control-label">Viralload Worksheets</label>
                    <div class="col-sm-8">
                        <select class="form-control" id="viralworksheet_search">

                        </select>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

@endsection

@section('scripts')

    

@endsection
