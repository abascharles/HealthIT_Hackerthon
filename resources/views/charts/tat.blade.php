

@if(isset($for_labs))
    <div class='col-md-6' id="{{ $div }}" style='height:100px;'></div>

@else
<div id="{{ $div }}" style="height:70px;">
    
</div>
@endif
<script type="text/javascript">
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

    $('#{{ $div }}').highcharts({
        chart: {
            type: 'lineargauge',
            inverted: true
        },
        title: {
            text: "{{ $title ?? null }}"
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
            max: {{ $tat4 }},
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
                to: {{ $tat1 }},
                color: 'rgba(255,0,0,0.5)'
            }, {
                from: {{ $tat1 }},
                to: {{ $tat2 }},
                color: 'rgba(255,255,0,0.5)'
            }, {
                from: {{ $tat2 }},
                to: {{ $tat3 }},
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
            point.update({{ $tat4 }});
        });

    });
});

</script>