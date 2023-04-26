
<!DOCTYPE html>
<html>
<head>

	<style type="text/css">
		body {
			font-weight: 1px;
		}

		table {
			border-collapse: collapse;
			margin-bottom: .5em;
		}

		table, th, td {
			border: 1px solid black;
			border-style: solid;
     		font-size: 8px;
		}

		h5 {
			margin-top: 6px;
		    margin-bottom: 6px;
		}

		p {
			margin-top: 2px;
     		font-size: 8px;
		}
		* {
			font-size: 8px;
		}
	</style>
</head>
<body>    
    @php
        $globaltesttype = $testtype;
        $replace = 'Quantitative';
        if($globaltesttype == 'EID')
            $replace = 'Quanlitative';
        $globaltesttypevalue = 1;
        if($globaltesttype == 'VL')
            $globaltesttypevalue = 2;

        $counter = 0;
    @endphp
	<table class="table" border="0" style="width: 100%; border:none;">
		<tr>
			<td colspan="7" align="center" style="border: none;">
                <img src="{{ public_path('img/naslogo.jpg') }}" alt="NASCOP">
			</td>
		</tr>
		<tr>
			<td colspan="7" align="center" style="border: none;">
				<h5>{{-- $lab->name --}} MONTHLY LAB ALLOCATION {{-- $year --}} {{-- date("F", mktime(null, null, null, $month)) --}}</h5>
			</td>
		</tr>
	</table>

	<br />
    @foreach ($allocations as $allocation)
        <table class="table" style="width: 100%;">
            <tr><th><center>Allocation for {{ $allocation->machine->machine ?? ''}} @if ($allocation->machine) , @endif {{ ucfirst(strtolower($globaltesttype)) }}</center></th></tr>
        </table>
        <table class="table" style="width: 100%;">
            <thead>
                <tr>
                    <th>Name of Commodity</th>
                    @if ($globaltesttype != 'CONSUMABLES')
                    <th>Average Monthly Consumption(AMC)</th>
                    <th>Months of Stock(MOS)</th>
                    <th>Ending Balance</th>
                    <th>Recommended Quantity to Allocate (by System)</th>
                    @else
                    <th>Unit</th>
                    @endif
                    <th>Quantity Allocated by Lab</th>
                </tr>
            </thead>
            <tbody>
            @php
                if ($globaltesttype != 'CONSUMABLES') {
                    $tests = $allocation->machine->testsforLast3Months()->$globaltesttype;
                    $qualamc = 0;
                }
            @endphp
            @foreach($allocation->breakdowns as $detail)
                @php
                    if ($globaltesttype != 'CONSUMABLES') {
                        $test_factor = json_decode($detail->breakdown->testFactor);
                        $factor = json_decode($detail->breakdown->factor);
                        if ($detail->breakdown->alias == 'qualkit')
                            $qualamc = (($tests / $test_factor->$globaltesttype) / 3);

                        if ($allocation->machine->id == 2)
                            $amc = $qualamc * $factor->$globaltesttype;
                        else
                            $amc = $qualamc * $factor;

                        $ending = 0;
                        $consumption = $detail->breakdown->consumption
                                            ->where('month', $last_month)->where('year', $last_year)
                                            ->where('testtype', $globaltesttypevalue)->pluck('ending');
                        foreach($consumption as $value) {
                            $ending += $value;
                        }
                        $mos = @($ending / $amc);
                    }
                @endphp
                <tr>
                    <td>{{ str_replace("REPLACE", $replace, $detail->breakdown->name) }}</td>
                    @if ($globaltesttype != 'CONSUMABLES')
                    <td>{{ round($amc, 1) }}</td>
                    <td>
                    @if(is_nan($mos))
                        {{ 0 }}
                    @else
                        {{ round($mos) }}
                    @endif
                    </td>
                    <td>{{ $ending }}</td>
                    <td>{{ round(($amc * 2) - $ending, 1) }}</td>
                    @else
                    <td>{{ $detail->breakdown->unit }}</td>
                    @endif
                    <td>{{ $detail->allocated }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <table class="table" style="width: 100%;">
            <tr>
                <td>Lab Comments</td>
                <td>{{ $allocation->allocationcomments ?? '' }}</td>
            </tr>
            <tr>
                <td>Allocation Committee Feedback</td>
                <td>{{ $allocation->issuedcomments ?? '' }}</td>
            </tr>
        </table>
        @if(!$loop->last)
            {{-- <pagebreak sheet-size='A4'> --}}
            <br />
        @endif
    @endforeach
</body>
</html>