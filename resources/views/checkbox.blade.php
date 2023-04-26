@extends('layouts.master')

@section('content')
    <div class="small-header">
        <div class="hpanel">
            <div class="panel-body">
                <h2 class="font-light m-b-xs">
                    CheckBoxes
                </h2>
            </div>
        </div>
    </div>
   <div class="content">
        <div>
            
                <div class="row">
                    <div class="col-lg-7 col-lg-offset-2">
                        <div class="hpanel">
                            <div class="panel-body">
                                <form method="get" class="form-horizontal">
                       
                        <div class="form-group"><label class="col-sm-2 control-label">Checkboxes &amp; radios <br/>
                            <small class="text-navy">Custom elements</small>
                        </label>

                            <div class="col-sm-10">
                                <div><label> <input type="checkbox" class="i-checks"> Option one </label></div>
                                <div><label> <input type="checkbox" class="i-checks" checked> Option two checked </label></div>
                                <div><label> <input type="checkbox" class="i-checks" checked disabled> Option three checked and disabled </label></div>
                                <div><label> <input type="checkbox" class="i-checks" disabled> Option four disabled </label></div>
                                <div><label> <input type="radio" class="i-checks"> Option one </label></div>
                                <div><label> <input type="radio" class="i-checks" checked> Option two checked </label></div>
                                <div><label> <input type="radio" class="i-checks" disabled> Option four disabled </label></div>
                                <div><label> <input type="radio" class="i-checks" checked disabled> Option three checked and disabled</label></div>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group"><label class="col-sm-2 control-label">Inline checkboxes</label>

                            <div class="col-sm-10">
                                <label> <input type="checkbox" class="i-checks" checked> a </label>
                                <label> <input type="checkbox" class="i-checks"> b </label>
                                <label> <input type="checkbox" class="i-checks"> c </label></div>
                        </div>
                    </form>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
@endsection()