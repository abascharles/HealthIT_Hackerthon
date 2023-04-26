<div class="panel-body">
    <div class="alert alert-warning">
        Please select the parameters from the options below to generate the Submitted Kits Consumption query.
    </div>
    <div style="margin-top: 2em;">
    <form action="{{ url('/reports/kitsconsumption') }}" class="form-horizontal" method="POST" id='reports_consumption'>
        @csrf
        <div class="form-group spacing-div-form">
            <label class="col-sm-3 control-label">Select Test Type</label>
            <div class="col-sm-9">
            @foreach($data['testtypes'] as $type)
                <label> <input type="radio" name="types" value="{{ $type->id }}" class="i-checks" required> {{ $type->name }} </label>
            @endforeach
            </div>
        </div>
        <hr />
        <div class="form-group spacing-div-form">
            <label class="col-sm-3 control-label">Platform</label>
            <div class="col-sm-9">
            @foreach($data['platforms'] as $platform)
                <label><input type="radio" name="platform" value="{{ $platform->id }}" class="i-checks" required> {{ strtoupper($platform->machine) }} </label>
            @endforeach
            </div>
        </div>
        <hr />
        <div class="form-group spacing-div-form">
            <label class="col-sm-3 control-label">Month of Consumption</label>
            <div class="col-sm-9">
                <div class="col-sm-6">
                    <select class="form-control" id="month" name="month">
                        <option selected="true" disabled="true">Select a Month</option>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}">{{ date("F", mktime(0, 0, 0, $i, 1, Date('Y'))) }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-sm-6">
                    <select class="form-control" id="year" name="year">
                        <option selected="true" disabled="true">Select a Year</option>
                        @for ($i = 0; $i <= 6; $i++)
                            @php
                                $year=Date('Y')-$i
                            @endphp
                        <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>
        <hr />

            <div class="form-group">
            <center>
                <button type="submit" class="btn btn-default" id="generate_report">Generate Report</button>
                <button type="reset" class="btn btn-default">Reset Options</button>
            </center>
        </div>  
    </form>
    </div>
</div>