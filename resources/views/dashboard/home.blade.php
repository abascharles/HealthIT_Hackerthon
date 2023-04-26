@extends('layouts.master')

@section('css_scripts')

@endsection

@section('custom_css')
<style type="text/css">
	body.light-skin #wrapper .content {
		padding-top: 0px;
	    padding-right: 10px;
	    padding-left: 10px;
	}
	.hpanel {
		margin-bottom: 10px;
	}
	.key {
		font-size: 11px;
		margin-top: 0.5em;
	}
	.cr {
		background-color: rgba(255, 0, 0, 0.498039);
	}
	.rp {
		background-color: rgba(255, 255, 0, 0.498039);
	}
	.pd {
		background-color: rgba(0, 255, 0, 0.498039);
	}
	.cd {
		width: 0px;
		height: 0px;
		border-left: 8px solid transparent;
		border-right: 8px solid transparent;
		border-top: 8px solid black;
	}
</style>
@endsection

@section('content')
<div class="p-lg">
    <div class="content animate-panel" data-child="hpanel">
        <div class="row" style="margin-bottom: 1em;">
            <!-- Year -->
            <div class="col-md-6">
                <center><h5>Year Filter</h5></center>
                @for ($i = 0; $i <= 9; $i++)
                    @php
                        $year=Date('Y')-$i
                    @endphp
                    <a href='{{ url("dashboard/$year") }}'>{{ Date('Y')-$i }}</a> |
                @endfor
            </div>
            <!-- Year -->
            <!-- Month -->
            <div class="col-md-6">
                <center><h5>Month Filter</h5></center>
                @for ($i = 1; $i <= 12; $i++)
                    <a href='{{ url("dashboard/null/$i") }}'>{{ date("F", strtotime(date("Y") ."-". $i ."-01")) }}</a> |
                @endfor
            </div>
            <!-- Month -->
        </div>
        <div class="row">
            <div class="col-lg-7">
                <div class="hpanel">
                    <div class="alert alert-success">
                        <center><i class="fa fa-bolt"></i> Monthly Test Summary</center>
                    </div>
                    <div class="panel-body">
                        <div id="highchartsnya"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="hpanel">
		            <div class="alert alert-success">
		                <center><i class="fa fa-bolt"></i> Lab Statistics</center>
		            </div>
		            <div class="panel-body no-padding">
		            	<div class="table-responsive" style="padding-left: 15px;padding-top: 2px;padding-bottom: 2px;padding-right: 15px;">
                		<table cellpadding="1" cellspacing="1" class="table table-condensed">
		                	<tbody>
                            @if(session('testingSystem') == 'Viralload')
                                <tr>
                                    <td>Received samples in {{ $data->year . $data->month }}</td>
                                    <td>{{ number_format($lab_stats->receivedSamples) }}</td>
                                </tr>
                                <tr>
                                    <td>Rejected Samples</td>
                                    <td>{{ $lab_stats->rejectedSamples }}
                                     [ {{ round(@(($lab_stats->rejectedSamples/$lab_stats->receivedSamples)*100),1) }}% ]</td>
                                </tr>
                                <tr>
                                    <td>Tested Samples</td>
                                    <td>{{ $lab_stats->testedSamples }}</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Redraws ( After testing )</td>
                                    <td>{{ $lab_stats->redraws }}</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;&nbsp;&nbsp;Tests with Valid results ( > 1000 or < 1000 cp/ml)</td>
                                    <td>{{ $lab_stats->nonsuppressed+$lab_stats->suppressed }}</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Non Suppression ( > 1000 cp/ml )</td>
                                    <td>{{ $lab_stats->nonsuppressed }}</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Suppression ( < 1000 cp/ml )</td>
                                    <td>{{ $lab_stats->suppressed }}</td>
                                </tr>
                                <tr>
                                    <td>Total Tests Done ( Including Repeats )</td>
                                    <td>{{ $lab_stats->totaltestsinlab }}</td>
                                </tr>
                            @else
		                		<tr>
		                			<td>No. of received samples in {{ $data->year . $data->month }}</td>
		                			<td>{{ number_format($lab_stats->receivedSamples) }}</td>
		                		</tr>
		                		<tr>
		                			<td>No. of Rejected Samples</td>
		                			<td>{{ $lab_stats->rejectedSamples }}
		                			 [ {{ round(@(($lab_stats->rejectedSamples/$lab_stats->receivedSamples)*100),1) }}% ]</td>
		                		</tr>
		                		<tr>
		                			<td>No. of Tested Samples ( + or - or Redraws )</td>
		                			<td>{{ number_format($lab_stats->positives + $lab_stats->negatives + $lab_stats->redraws) }}</td>
		                		</tr>
		                		<tr>
		                			<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Redraws (after testing)</td>
		                			<td>{{ number_format($lab_stats->redraws) }}
		                			 [ {{ round(@(($lab_stats->redraws/($lab_stats->positives + $lab_stats->negatives + $lab_stats->redraws))*100),1) }}% ]</td>
		                		</tr>

		                		<!-- {{ round(@(($lab_stats->redraws/($lab_stats->positives + $lab_stats->negatives + $lab_stats->redraws)) * 100),1) }} -->
		                		<tr>
		                			<td>&nbsp;&nbsp;&nbsp;No. of Tests with Valid Results ( + OR - )</td>
		                			<td>{{ number_format($lab_stats->positives + $lab_stats->negatives) }}</td>
		                		</tr>
		                		<tr>
		                			<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Positives</td>
		                			<td>{{ number_format($lab_stats->positives) }}
		                			 [ {{ round(@(($lab_stats->positives/($lab_stats->positives + $lab_stats->negatives))*100),1) }}% ]</td>
		                		</tr>
		                		<tr>
		                			<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Negatives</td>
		                			<td>{{ number_format($lab_stats->negatives) }}
		                			 [ {{ round(@(($lab_stats->negatives/($lab_stats->positives + $lab_stats->negatives))*100),1) }}% ]</td>
		                		</tr>
		                		<tr>
		                			<td>Total Tests Done In Lab (Including Reruns)</td>
		                			<td>{{ number_format($lab_stats->testedSamples) }}</td>
		                		</tr>
                                @if(session('testingSystem') != 'HPV')
		                		<tr>
		                			<td><strong>No of SMS Printers Served by Lab</strong></td>
		                			<td><strong>{{ $lab_stats->smsPrinters}}</strong></td>
		                		</tr>
                                @endif
                            @endif
		                	</tbody>
		                </table>
		            </div>
		        </div>
            </div>
            @if(session('testingSystem') == 'Viralload')
                <div class="hpanel">
                    <div class="alert alert-success">
                        <center>Tests Done</center>
                    </div>
                    <div class="panel-body no-padding">
                        <div class="table-responsive" style="padding-left: 15px;padding-top: 2px;padding-bottom: 2px;padding-right: 15px;">
                        <table cellpadding="1" cellspacing="1" class="table table-condensed">
                            <thead>
                                <tr>
                                    <th>Sample Types</th>
                                    <th># Received</th>
                                    <th># Tests</th>
                                    <th># Rejected</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1. Frozen Plasma</td>
                                    <td>{{ (isset($lab_stats->sampletypes->receivedplasma)) ? number_format($lab_stats->sampletypes->receivedplasma) : 0 }}</td>
                                    <td>{{ (isset($lab_stats->sampletypes->testedplasma)) ? number_format($lab_stats->sampletypes->testedplasma) : 0 }}</td>
                                    <td>{{ (isset($lab_stats->sampletypes->rejectedplasma)) ? number_format($lab_stats->sampletypes->rejectedplasma) : 0 }}</td>
                                </tr>
                                <tr>
                                    <td>2. Venous Blood (EDTA)</td>
                                    <td>{{ (isset($lab_stats->sampletypes->receivededta)) ? number_format($lab_stats->sampletypes->receivededta) : 0 }}</td>
                                    <td>{{ (isset($lab_stats->sampletypes->testededta)) ? number_format($lab_stats->sampletypes->testededta) : 0 }}</td>
                                    <td>{{ (isset($lab_stats->sampletypes->rejectededta)) ? number_format($lab_stats->sampletypes->rejectededta) : 0 }}</td>
                                </tr>
                                <tr>
                                    <td>3. DBS Venous</td>
                                    <td>{{ (isset($lab_stats->sampletypes->receiveddbsv)) ? number_format($lab_stats->sampletypes->receiveddbsv) : 0 }}</td>
                                    <td>{{ (isset($lab_stats->sampletypes->testeddbsv)) ? number_format($lab_stats->sampletypes->testeddbsv) : 0 }}</td>
                                    <td>{{ (isset($lab_stats->sampletypes->rejecteddbsv)) ? number_format($lab_stats->sampletypes->rejecteddbsv) : 0 }}</td>
                                </tr>
                                <tr>
                                    <td>4. DBS Capillary (Infants)</td>
                                    <td>{{ (isset($lab_stats->sampletypes->receiveddbsc)) ? number_format($lab_stats->sampletypes->receiveddbsc) : 0 }}</td>
                                    <td>{{ (isset($lab_stats->sampletypes->testeddbsc)) ? number_format($lab_stats->sampletypes->testeddbsc) : 0 }}</td>
                                    <td>{{ (isset($lab_stats->sampletypes->rejecteddbsc)) ? number_format($lab_stats->sampletypes->rejecteddbsc) : 0 }}</td>
                                </tr>
                                <tr>
                                    <td>* Not Specified</td>
                                    <td>{{ (isset($lab_stats->sampletypes->receivednone)) ? number_format($lab_stats->sampletypes->receivednone) : 0 }}</td>
                                    <td>{{ (isset($lab_stats->sampletypes->testednone)) ? number_format($lab_stats->sampletypes->testednone) : 0 }}</td>
                                    <td>{{ (isset($lab_stats->sampletypes->rejectednone)) ? number_format($lab_stats->sampletypes->rejectednone) : 0 }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
        <div class="row">
            <div class="col-lg-8">
                <div class="hpanel">
                    <div class="alert alert-success">
                        <center>Lab TAT</center>
                    </div>
                    <div class="panel-body">
                        <div id="lab_tat" style="height:70px;"></div>
                        <div class="alert alert-success">
			                <center><i class="fa fa-key"></i>&nbsp;&nbsp;<strong>KEY:</strong></center>
			            </div>
							<div class="row">
								<div class="col-md-6">
									<div class="key cr"><center>Collection Receipt (C-R)</center></div>
									<div class="key rp"><center>Receipt to Processing (R-P)</center></div>
								</div>
								<div class="col-md-6">
									<div class="key pd"><center>Processing Dispatch (P-D)</center></div>
									<div class="key"><center><div class="cd"></div>Collection Dispatch (C-D)</center></div>
								</div>
							</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="hpanel">
		            <div class="alert alert-success">
		                <center><i class="fa fa-bolt"></i> Lab TAT Statistics</center>
		            </div>
		            <div class="panel-body no-padding">
		            	<div class="table-responsive" style="padding-left: 15px;padding-top: 2px;padding-bottom: 2px;padding-right: 15px;">
                		<table cellpadding="1" cellspacing="1" class="table table-condensed">
		                	<tbody>
		                		<tr>
		                			<td>Collection at Facility - Receipt at Lab</td>
		                			<td>{{ $lab_tat_stats->tat1 }} days</td>
		                		</tr>
		                		<tr>
		                			<td>Receipt at Lab - Processing</td>
		                			<td>{{ $lab_tat_stats->tat2 }} days</td>
		                		</tr>
		                		<tr>
		                			<td>Processing - Dispatch</td>
		                			<td>{{ $lab_tat_stats->tat3 }} days</td>
		                		</tr>
                                @if(session('testingSystem') != 'Covid')
    		                		<tr>
    		                			<td>Receipt at Lab - Dispatch</td>
    		                			<td>{{ $lab_tat_stats->tat5 }} days</td>
    		                		</tr>
                                @endif
		                		<tr>
		                			<td><strong>Collection at Facility - Dispatch</strong></td>
		                			<td><strong>{{ $lab_tat_stats->tat4 }} days</strong></td>
		                		</tr>
		                	</tbody>
		                </table>
		            </div>
		        </div>
            </div>    
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('vendor/highcharts/highcharts.js' )}}"></script>
<script src="{{ asset('vendor/highcharts/modules/data.js' )}}"></script>
<script src="{{ asset('vendor/highcharts/modules/series-label.js' )}}"></script>
<script src="{{ asset('vendor/highcharts/modules/exporting.js' )}}"></script>

<script type="text/javascript">
	$(function () {
        $('#highchartsnya').highcharts({
            plotOptions: {
                column: {
                    stacking: 'normal'
                }
            },
            chart: {
                zoomType: 'xy'
            },
            title: {
                text: ''
            },
            xAxis: [{
                	categories: @php
                                    echo json_encode($chart->categories)
                                @endphp
            	}],
            yAxis: [{ // Primary yAxis
      //           labels: {
      //               formatter: function() {
      //                   return this.value +'%';
      //               },
      //               style: {
      //                   color: '#89A54E'
      //               }
      //           },
      //           title: {
      //               text: 'Percentage',
      //               style: {
      //                   color: '#89A54E'
      //               }
      //           },
      //          opposite: true
    		// }, { // Secondary yAxis
                // gridLineWidth: 0,
                title: {
                    text: 'Tests',
                    style: {
                        color: '#4572A7'
                    }
                },
                labels: {
                    formatter: function() {
                        return this.value +'';
                    },
                    style: {
                        color: '#4572A7'
                    }
                }
                // min: 0, 
                // max: 70000,
                // tickInterval: 1
            }],
            tooltip: {
                shared: true
            },
            legend: {
                layout: 'horizontal',
                align: 'right',
                x: -70,
                verticalAlign: 'bottom',
                y: 5,
                floating: false,
                backgroundColor: '#FFFFFF'
            },colors: [
                        '#1BA39C',
                        '#F2784B',
                        '#257766',
                        '#257766'
                    ],
            series: @php
                        echo json_encode($chart->testtrends)
                    @endphp
             //    [{
            	// 	"name":"Not Suppressed",
            	// 	"type":"column",
            	// 	// "yAxis":1,
            	// 	"tooltip":{"valueSuffix":" "},
            	// 	"data":[17997,11953,8610,10797,7329,7721,5230,8737,4491,6496,3465,3261]
            	// },{
            	// 	"name":"Suppressed",
            	// 	"type":"column",
            	// 	// "yAxis":1,
            	// 	"tooltip":{"valueSuffix":" "},
            	// 	"data":[65540,49993,48984,44841,41172,37745,37228,34052,31418,27906,26946,23973]
            	// },{
            	// 	"name":"Something",
            	// 	"type":"column",
            	// 	// "yAxis":1,
            	// 	"tooltip":{"valueSuffix":" "},
            	// 	"data":[65540,49993,48984,44841,41172,37745,37228,34052,31418,27906,26946,23973]
            	// },{
            	// 	"name":"Suppression",
            	// 	"type":"spline",
            	// 	"tooltip":{"valueSuffix":" %"},
            	// 	"data":[7850,80.7,85000,80.6,84.9,83000,87.7,79.6,87500,81.1,88.6,88]
            	// }]
                // },{
            	// 	"name":"90% Target",
            	// 	"type":"spline",
            	// 	"tooltip":{"valueSuffix":" %"},
            	// 	"data":[90,90,90,90,90,90,90,90,90,90,90,90]
            	// }]
            });
    });


$(function () {

    /**
     * Highcharts Linear-Gauge series plugin
     */
    (function (H) {
        var defaultPlotOptions = H.getOptions().plotOptions,
            columnType = H.seriesTypes.column,
            wrap = H.wrap,
            each = H.each;

        defaultPlotOptions.lineargauge = H.merge(defaultPlotOptions.column, {});
        H.seriesTypes.lineargauge = H.extendClass(columnType, {
            type: 'lineargauge',
            //inverted: true,
            setVisible: function () {
                columnType.prototype.setVisible.apply(this, arguments);
                if (this.markLine) {
                    this.markLine[this.visible ? 'show' : 'hide']();
                }
            },
            drawPoints: function () {
                // Draw the Column like always
                columnType.prototype.drawPoints.apply(this, arguments);

                // Add a Marker
                var series = this,
                    chart = this.chart,
                    inverted = chart.inverted,
                    xAxis = this.xAxis,
                    yAxis = this.yAxis,
                    point = this.points[0], // we know there is only 1 point
                    markLine = this.markLine,
                    ani = markLine ? 'animate' : 'attr';

                // Hide column
                point.graphic.hide();

                if (!markLine) {
                    var path = inverted ? ['M', 0, 0, 'L', -3, -3, 'L', 3, -3, 'L', 0, 0, 'L', 0, 0 + xAxis.len] : ['M', 0, 0, 'L', -3, -3, 'L', -3, 3,'L', 0, 0, 'L', xAxis.len, 0];
                    markLine = this.markLine = chart.renderer.path(path)
                        .attr({
                            'fill': series.color,
                            'stroke': series.color,
                            'stroke-width': 1
                        }).add();
                }
                markLine[ani]({
                    translateX: inverted ? xAxis.left + yAxis.translate(point.y) : xAxis.left,
                    translateY: inverted ? xAxis.top : yAxis.top + yAxis.len -  yAxis.translate(point.y)
                });
            }
        });
    }(Highcharts));

    $('#lab_tat').highcharts({
        chart: {
            type: 'lineargauge',
            inverted: true
        },
        title: {
            text: null
        },
        xAxis: {
            lineColor: '#C0C0C0',
            labels: {
                enabled: false
            },
            tickLength: 0
        },
        yAxis: {
            min: 0,
            max: {{ $lab_tat_stats->tat4 }},
            tickLength: 3,
            tickWidth: 1,
            tickColor: '#C0C0C0',
            gridLineColor: '#C0C0C0',
            gridLineWidth: 1,
            minorTickInterval: 3,
            minorTickWidth: 1,
            minorTickLength: 3,
            minorGridLineWidth: 0,

            title: null,
            labels: {
                format: '{value}'
            },
            plotBands: [{
                from: 0,
                to: {{ $lab_tat_stats->tat1 }},
                color: 'rgba(255,0,0,0.5)'
            }, {
                from: {{ $lab_tat_stats->tat1 }},
                to: {{ $lab_tat_stats->tat2+$lab_tat_stats->tat1 }},
                color: 'rgba(255,255,0,0.5)'
            }, {
                from: {{ $lab_tat_stats->tat2+$lab_tat_stats->tat1 }},
                to: {{ $lab_tat_stats->tat3+$lab_tat_stats->tat2+$lab_tat_stats->tat1 }},
                color: 'rgba(0,255,0,0.5)'
            }]
        },
        legend: {
            enabled: false
        },
        exporting: {
            enabled: false
        },colors: [
            '#F64747',
            '#F9BF3B',
            '#26C281'
        ],
        series: [{
            data: [60],
            color: '#000000',
            dataLabels: {
                enabled: true,
                align: 'center',
                format: '{point.y}',
                y: 10
            }
        }]

    },
     // Add some life
    function (chart) {
        Highcharts.each(chart.series, function (serie) {
            var point = serie.points[0];
            point.update({{ $lab_tat_stats->tat4 }});
        });

    });
});
</script>
@endsection