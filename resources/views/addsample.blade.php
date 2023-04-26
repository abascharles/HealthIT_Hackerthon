@extends('layouts.master')

@section('content')
    <div class="small-header">
        <div class="hpanel">
            <div class="panel-body">
                <h2 class="font-light m-b-xs">
                    ADD SAMPLE
                </h2>
            </div>
        </div>
    </div>
   <div class="content">
        <div>
            <form method="get" class="form-horizontal">
                <div class="row">
                    <div class="col-lg-7 col-lg-offset-2">
                        <div class="hpanel">
                            <div class="panel-body">
                                <div class="form-group"><label class="col-sm-4 control-label input-sm">Facility</label>
                                    <div class="col-sm-8"><select class="form-control" name="account">
                                        <option>option 1</option>
                                        <option>option 2</option>
                                        <option>option 3</option>
                                        <option>option 4</option>
                                    </select>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                                <div class="form-group"><label class="col-sm-4 control-label input-sm">AMRS Location</label>
                                    <div class="col-sm-8"><select class="form-control m-b input-sm" name="account">
                                        <option>option 1</option>
                                        <option>option 2</option>
                                        <option>option 3</option>
                                        <option>option 4</option>
                                    </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-7 col-lg-offset-2">
                        <div class="hpanel">
                            <div class="panel-heading">
                                <center>Infant Information</center>
                            </div>
                            <div class="panel-body">
                                <div class="form-group"><label class="col-sm-4 control-label">Patient/Sample ID</label>
                                        <div class="col-sm-8"><input type="text" class="form-control"></div>
                                </div>
                                <div class="form-group"><label class="col-sm-4 control-label input-sm">AMRS Provider Identifier</label>
                                    <div class="col-sm-8"><input type="text" class="form-control input-sm"></div>
                                </div>
                                <div class="form-group"><label class="col-sm-4 control-label input-sm">Patient Names</label>
                                    <div class="col-sm-8"><input type="text" class="form-control input-sm"></div>
                                </div>
                                <div class="form-group"><label class="col-sm-4 control-label input-sm">Sex</label>
                                    <div class="col-sm-8"><select class="form-control m-b input-sm" name="account">
                                        <option>option 1</option>
                                        <option>option 2</option>
                                        <option>option 3</option>
                                        <option>option 4</option>
                                    </select>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                                <div class="form-group"><label class="col-sm-4 control-label input-sm">Age</label>
                                    <div class="col-sm-8"><input type="text" class="form-control input-sm" placeholder="Months"></div>
                                    <div class="col-sm-8 col-sm-offset-4 input-sm" style="margin-top: 1em;"><input type="text" class="form-control" placeholder="Weeks"></div>
                                </div>
                                <div class="hr-line-dashed"></div>
                                <div class="form-group"><label class="col-sm-4 control-label input-sm">Infant Prophylaxis</label>
                                    <div class="col-sm-8">
                                        <select class="form-control m-b input-sm" name="account">
                                            <option>option 1</option>
                                            <option>option 2</option>
                                            <option>option 3</option>
                                            <option>option 4</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-7 col-lg-offset-2">
                        <div class="hpanel">
                            <div class="panel-heading">
                                <center>Mother Information</center>
                            </div>
                            <div class="panel-body">
                                <div class="form-group"><label class="col-sm-4 control-label input-sm">PMTCT Intervention</label>
                                    <div class="col-sm-8"><select class="form-control m-b input-sm" name="account">
                                        <option>option 1</option>
                                        <option>option 2</option>
                                        <option>option 3</option>
                                        <option>option 4</option>
                                    </select>
                                    </div>
                                </div>
                                <div class="form-group"><label class="col-sm-4 control-label input-sm">Feeding Types</label>
                                    <div class="col-sm-8">
                                        <select class="form-control m-b input-sm" name="account">
                                            <option>option 1</option>
                                            <option>option 2</option>
                                            <option>option 3</option>
                                            <option>option 4</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group"><label class="col-sm-4 control-label input-sm">Entry Point</label>
                                    <div class="col-sm-8"><select class="form-control m-b input-sm" name="account">
                                        <option>option 1</option>
                                        <option>option 2</option>
                                        <option>option 3</option>
                                        <option>option 4</option>
                                    </select>
                                    </div>
                                </div>
                                <div class="form-group"><label class="col-sm-4 control-label input-sm">HIV Status</label>
                                    <div class="col-sm-8">
                                        <select class="form-control m-b input-sm" name="account">
                                            <option>option 1</option>
                                            <option>option 2</option>
                                            <option>option 3</option>
                                            <option>option 4</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group"><label class="col-sm-4 control-label input-sm">Caregiver Phone Number</label>
                                    <div class="col-sm-8"><input type="text" class="form-control input-sm"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-7 col-lg-offset-2">
                        <div class="hpanel">
                            <div class="panel-heading">
                                <center>Sample Information</center>
                            </div>
                            <div class="panel-body">
                                <div class="form-group"><label class="col-sm-4 control-label input-sm">No. of spots</label>
                                    <div class="col-sm-8"><select class="form-control m-b input-sm" name="account">
                                        <option>option 1</option>
                                        <option>option 2</option>
                                        <option>option 3</option>
                                        <option>option 4</option>
                                    </select>
                                    </div>
                                </div>
                                <div class="form-group"><label class="col-sm-4 control-label input-sm">Date of collection</label>
                                    <div class="col-sm-8"><input type="date" class="form-control input-sm"></div>
                                </div>
                                <div class="form-group"><label class="col-sm-4 control-label input-sm">Date Dispatched from facility</label>
                                    <div class="col-sm-8"><input type="date" class="form-control input-sm"></div>
                                </div>
                                <div class="form-group"><label class="col-sm-4 control-label input-sm">Date Received</label>
                                    <div class="col-sm-8"><input type="date" class="form-control input-sm"></div>
                                </div>
                                <div class="form-group"><label class="col-sm-4 control-label input-sm">PCR Type</label>
                                    <div class="col-sm-8"><select class="form-control m-b input-sm" name="account">
                                        <option>option 1</option>
                                        <option>option 2</option>
                                        <option>option 3</option>
                                        <option>option 4</option>
                                    </select>
                                    </div>
                                </div>
                                <div class="form-group"><label class="col-sm-4 control-label input-sm">Received Status</label>
                                    <div class="col-sm-8">
                                        <select class="form-control m-b input-sm" name="account">
                                            <option>option 1</option>
                                            <option>option 2</option>
                                            <option>option 3</option>
                                            <option>option 4</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-7 col-lg-offset-2">
                        <div class="hpanel">
                            <div class="panel-body">
                                <div class="form-group"><label class="col-sm-4 control-label input-sm">Comments (from facility)</label>
                                    <div class="col-sm-8"><textarea  class="form-control input-sm"></textarea></div>
                                </div>
                                <div class="form-group"><label class="col-sm-4 control-label input-sm">Lab Comments</label>
                                    <div class="col-sm-8"><textarea  class="form-control input-sm"></textarea></div>
                                </div>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <div class="col-sm-8 col-sm-offset-2">
                                <button class="btn btn-success" type="submit">Save & Release sample</button>
                                <button class="btn btn-primary" type="submit">Save & Add sample</button>
                                <button class="btn btn-danger" type="submit">Clear</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection()