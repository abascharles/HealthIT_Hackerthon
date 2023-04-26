<div @isset($div_class) class="{{ $div_class }}" @endisset id="{{$div}}"></div>

{!! $paragraph ?? '' !!}

<script type="text/javascript">
	
    $(function () {
        $('#{{$div}}').highcharts({
            plotOptions: {
                column: {
                    @if(isset($stacking_false))
                        stacking: false
                    @elseif(isset($stacking_percentage))
                        stacking: 'percent'
                    @else
                        stacking: 'normal'
                    @endif
                },
                bar: {
                    @if(isset($stacking_false))
                        stacking: false
                    @elseif(isset($stacking_percentage))
                        stacking: 'percent'
                    @else
                        stacking: 'normal'
                    @endif
                },
                @if(isset($data_labels))
                    series: {
                        dataLabels: {
                            enabled: true
                        }
                    },
                @endif
            },
            title: {
                text: "{{ $title ?? '' }}"
            },
            chart: {
                zoomType: 'xy',
                @isset($height)
                    height: {{ $height }},
                @endisset
            },
            xAxis: [{
                categories: {!! json_encode($categories ?? []) !!}
            }],
            yAxis: {
                title: {
                    text: "{{ $yAxis ?? 'Totals' }}"
                }
            },

            tooltip: {
                valueSuffix: "{{ $suffix ?? '' }}",
                valuePrefix: "{{ $prefix ?? '' }}",
                borderRadius: 2,
                borderWidth: 1,
                borderColor: '#999',
                shadow: false,
                shared: true,
                useHTML: true,
                yDecimals: 0,
                valueDecimale: 0,
                headerFormat: '<table class="tip"><caption>{point.key}</caption>'+'<tbody>',
                pointFormat: '<tr><th style="color:{series.color}">{series.name}:</th>'+'<td style="text-align:right">{point.y}' + 
                @if(isset($extra_tooltip))
                    '{point.z}'
                @else
                    ' '
                @endif
                 + '</td><td>&nbsp; Contribution <b>({point.percentage:.1f}%)</b> </td>  </tr>',
                footerFormat: '<tr><th>Total:</th>'+'<td style="text-align:right"><b>{point.total}</b> </td><th></th>  </tr>'+'</tbody></table>'
            },
            legend: {
                layout: 'horizontal',
                align: 'left',
                x: 5,
                verticalAlign: 'bottom',
                y: 5,
                floating: false,
                width: $(window).width() - 20,
                // width: 1000,
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
                '#80ff00',
                '#ff8000',
                '#00ffff',
                '#ff4000'
            ],     
            series: {!! json_encode($outcomes) !!}
        });
    });
</script>