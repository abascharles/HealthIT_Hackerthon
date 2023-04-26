<div id="{{$div}}"></div>

<script type="text/javascript">

  
    $("#{{$div}}").highcharts({
        title: {
            text: "{{ $chart_title ?? '' }}",
            x: -20
        },
        chart: {
            zoomType: 'xy'
        },
        @isset($stacking)
            plotOptions: {
                column: {
                    // stacking: 'normal'
                    stacking: $stacking
                }
            },
        @endisset
        xAxis: {
            categories: {!! json_encode($categories ?? []) !!}
        },
        yAxis: {
            title: {
                text: "{{ $yAxis ?? '' }}"
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }],
            labels: {
                formatter: function() {
                    return this.value + "{{ $suffix ?? '%' }}";
                },
                style: {
                    
                }
            },
        },
        tooltip: {
            valueSuffix: "{{ $suffix ?? '%' }}",
            valuePrefix: "{{ $prefix ?? '' }}",
            shared: true,
            useHTML: true,
            yDecimals: 0,
            valueDecimale: 0,
            headerFormat: '<table class="tip"><caption>{point.key}</caption>'+'<tbody>',
            pointFormat: '<tr><th style="color:{series.color}">{series.name}:</th>'+'<td style="text-align:right">{point.y}' 
                @if(isset($extra_tooltip))
                    + '</td><td>{point.z}'
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
            // valueDecimals: 2
        },
        legend: {
            /*layout: 'vertical',
            align: 'right',
            verticalAlign: 'bottom',
            floating: false,
            borderWidth: 0*/
            
            layout: 'horizontal',
            align: 'left',
            x: 5,
            verticalAlign: 'bottom',
            y: 5,
            floating: false,
            width: $(window).width() - 20,
            backgroundColor: '#FFFFFF'
        },
        colors: [
            '#F2784B',
            '#1BA39C',
            '#913D88',
            '#4d79ff',
            '#80ff00',
            '#ff8000',
            '#00ffff',
            '#ff4000',
            '#000000',
            '#003300',
            '#993333',
            '#669999',
            '#cc0000',
            '#cc0099',
            '#ffff00',
            '#663300',
            '#ff6600',
        ],   
        series: {!! json_encode($outcomes) !!}
            
    });
  

 
</script>