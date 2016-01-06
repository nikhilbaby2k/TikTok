<div class="box box-info">
    <div class="box-header"> <i class="fa fa-signal"></i>
        <div style="display: none;" id="count_start">8529</div>
        <h3 class="box-title">Attendance Activity</h3>
        <div class="pull-right box-tools">
            <button class="btn btn-info btn-xs" data-widget='collapse' data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
        </div><!-- /. tools -->
    </div>
    <div class="box-body">
        <div id="container" style="height: 304px;"></div>
    </div>
    <!-- /.box-body-->
</div>

<script language="JavaScript">
    $(document).ready(function() {

        var chart = {
            type: 'spline',
            animation: Highcharts.svg, // don't animate in IE < IE 10.
            marginRight: 10,
            events: {
                load: function () {

                    var data_for_chart = 0;

                    // set up the updating of the chart each second
                    var series = this.series[0];
                    setInterval(function () {
                        var x = (new Date()).getTime(); // current time
                        var  y =  Math.abs(data_for_chart);

                        series.addPoint([x, y], true, true);

                        $.ajax({
                            type: "POST",
                            url : "{{ route('live_attendance_data_ajax')  }}",
                            data : { '_token': "{{ csrf_token()  }}" },
                            success : function(data){

                                data_for_chart = data;
                            }
                        });

                    }, 5000);
                }
            }
        };
        var title = {
            text: ''
        };
        var xAxis = {
            type: 'datetime',
            tickPixelInterval: 150
        };
        var yAxis = {
            title: {
                text: 'Employees Present in Office'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        };
        var tooltip = {
            formatter: function () {
                return '<b>' + this.series.name + '</b><br/>' +
                        Highcharts.dateFormat('%A %H:%M:%S', this.x) + '<br/>' +
                        'Present: ' + Highcharts.numberFormat(this.y, 0);
            }
        };
        var plotOptions = {
            area: {
                pointStart: 1940,
                marker: {
                    enabled: false,
                    symbol: 'circle',
                    radius: 2,
                    states: {
                        hover: {
                            enabled: true
                        }
                    }
                }
            }
        };
        var legend = {
            enabled: false
        };
        var exporting = {
            enabled: false
        };
        var series= [{
            name: 'Live Attendance Data',
            data: (function () {

                // generate an array of random data
                var data = [],time = (new Date()).getTime(),i;
                for (i = 0; i < 10; i++ ) {
                    data.push({
                        x: time + i * 500,
                        y: 0
                    });
                }
                return data;
            }())
        }];

        var json = {};
        json.chart = chart;
        json.title = title;
        json.tooltip = tooltip;
        json.xAxis = xAxis;
        json.yAxis = yAxis;
        json.legend = legend;
        json.exporting = exporting;
        json.series = series;
        json.plotOptions = plotOptions;


        Highcharts.setOptions({
            global: {
                useUTC: false
            }
        });
        $('#container').highcharts(json);

    });



</script>
