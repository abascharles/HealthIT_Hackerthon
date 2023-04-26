<div class="panel-body">
    <div class="alert alert-warning">
        Please select the parameters from the options below to generate the Kits Deliveries query.
    </div>
    <div style="margin-top: 2em;">
    <form action="{{ url('/reports/kitdeliveries') }}" class="form-horizontal" method="POST" id='reports_kits'>
        @csrf
        {{--<div class="form-group spacing-div-form">
            <label class="col-sm-3 control-label">Select Test Type</label>
            <div class="col-sm-9">
            @foreach($data['testtypes'] as $type)
                <label> <input type="radio" name="types" value="{{ $type->id }}" class="i-checks" required> {{ $type->name }} </label>
            @endforeach
            </div>
        </div>
        <hr />
        <div class="form-group spacing-div-form">
            <label class="col-sm-3 control-label">Kit Source</label>
            <div class="col-sm-9">
                <label> <input type="radio" name="source" value="scms" class="i-checks" required> SCMS </label>
                <label> <input type="radio" name="source" value="kemsa" class="i-checks" checked required> KEMSA </label>
                <label> <input type="radio" name="source" value="lab" class="i-checks" required> Other Lab </label>
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
        <hr />--}}
        <div class="form-group spacing-div-form">
            <label class="col-sm-3 control-label">
                Received in the <br/>
                NB: Quarter & Year Options show cummulative values ���
            </label>
            <div class="col-sm-9">
                <label> <input type="radio" name="period" value="monthly" required> Month </label>
                <label> <input type="radio" name="period" value="quarterly" required> Quarter </label>
                <label> <input type="radio" name="period" value="yearly" required> Year </label>
            </div>
            <div class="row" id="periodSelection" style="display: none;">
                <div class="col-md-9 col-md-offset-3" id="monthSelection">
                    <table cellpadding="1" cellspacing="1" class="table table-condensed">
                        <tbody>
                            <tr>
                                <th>Select Year and Month </th>
                                <td>
                                    <select class="form-control" id="year" name="year" required>
                                        <option selected="true" disabled="true">Select a Year</option>
                                        @for ($i = 6; $i >= 0; $i--)
                                            @php
                                                $year=Date('Y')-$i
                                            @endphp
                                        <option value="{{ $year }}">{{ $year }}</option>
                                        @endfor
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control" id="month" name="month" required>
                                        <option selected="true" disabled="true">Select a Month</option>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}">{{ date("F", mktime(0, 0, 0, $i, 1, Date('Y'))) }} </option>
                                        @endfor
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>    
                </div>
                <div class="col-md-9  col-md-offset-3" id="quarterSelection">
                    <table cellpadding="1" cellspacing="1" class="table table-condensed">
                        <tbody>
                            <tr>
                                <th>Select Year and Quarter </th>
                                <td>
                                    <select class="form-control" id="year" name="year" required>
                                        <option selected="true" disabled="true">Select a Year</option>
                                        @for ($i = 6; $i >= 0; $i--)
                                            @php
                                                $year=Date('Y')-$i
                                            @endphp
                                        <option value="{{ $year }}">{{ $year }}</option>
                                        @endfor
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control" id="quarter" name="quarter" required>
                                        <option selected="true" disabled="true">Select a Quarter</option>
                                        @for ($i = 1; $i <= 4; $i++)
                                            <option value="{{ $i }}">Q{{ $i }}</option>
                                        @endfor
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>    
                </div>
                <div class="col-md-9  col-md-offset-3" id="yearSelection">
                    <table cellpadding="1" cellspacing="1" class="table table-condensed">
                        <tbody>
                            <tr>
                                <th>Select Year </th>
                                <td>
                                    <select class="form-control" id="year" name="year" required>
                                        <option selected="true" disabled="true">Select a Year</option>
                                        @for ($i = 6; $i >= 0; $i--)
                                            @php
                                                $year=Date('Y')-$i
                                            @endphp
                                        <option value="{{ $year }}">{{ $year }}</option>
                                        @endfor
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>    
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