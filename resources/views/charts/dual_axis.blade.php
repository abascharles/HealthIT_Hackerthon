<div id="{{$div}}"></div>

<script type="text/javascript">
	
    $(function () {
        @isset($dd)
            var dump_data = {!! $dd !!};
            console.log(dump_data);
        @endisset

        $('#{{$div}}').highcharts({
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
                categories: {!! json_encode($categories ?? []) !!}
            }],
            yAxis: [{ // Primary yAxis
                labels: {
                    formatter: function() {
                        return this.value +'<?= (isset($tat) ? @"": @"%"); ?>';
                    },
                    style: {
                        
                    }
                },
                title: {
                    text: "{{ $yAxis2 ?? 'Percentage' }} ",
                    style: {
                        color: '#89A54E'
                    }
                },
                opposite: true
    
            }, { // Secondary yAxis
                gridLineWidth: 0,
                title: {
                    text: "{{ $yAxis ?? 'Tests' }} ",
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
                borderRadius: 2,
                borderWidth: 1,
                borderColor: '#999',
                shadow: false,
                shared: true,
                useHTML: true,
                yDecimals: 0,
                valueDecimale: 0,
                headerFormat: '<table class="tip"><caption>{point.key}</caption>'+'<tbody>',
                pointFormat: '<tr><th style="color:{series.color}">{series.name}:</th>'+'<td style="text-align:right">{point.y}' 
                    @if(isset($extra_tooltip))
                        + '</td><td> {point.z}'
                    @endif
                    @if(isset($point_percentage))
                        + '</td><td> Contribution <b>({point.percentage:.1f}%)</b>'
                    @endif

                + '</td></tr>',
                footerFormat: '<tr><th>Total:</th>'+'<td style="text-align:right"><b>{point.total}</b>' 
                    @if(isset($extra_tooltip) || isset($point_percentage))
                        + '</td><td>'
                    @endif
                +'</td></tr>'+'</tbody></table>'
            },
            legend: {
                layout: 'horizontal',
                align: 'left',
                x: 5,
                verticalAlign: 'bottom',
                y: 5,
                floating: false,
                width: $(window).width() - 20,
                backgroundColor: '#FFFFFF'
            },
            navigation: {
                buttonOptions: {
                    verticalAlign: 'bottom',
                    y: -20
                }
            },
            colors: [
                '#F2784B',
                '#1BA39C',
                '#913D88',
                '#4d79ff',
                '#ff1a1a'
            ],     
            series: {!! json_encode($outcomes) !!}
        });
    });
</script>