<div class="box box-default">
    <div class="box-header"> <i class="fa fa-clock-o"></i>
        <h3 class="box-title">In-Time Statistics</h3>
        <div class="pull-right box-tools">
            <button class="btn btn-default btn-xs" data-widget='collapse' data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
        </div><!-- /. tools -->
    </div>
    <div class="box-body">
        <div id="in_time_statistics_chart" style="height: 320px;"></div>
    </div>
    <!-- /.box-body-->
</div>

<script type="text/javascript">


    var data_for_chart = [
        ['Before 8:30', {{ $in_time_statistics_data['Before 8:30'] }} ],
        ['8:30 - 9:00', {{ $in_time_statistics_data['8:30 - 9:00'] }} ],
        ['9:00 - 9:30', {{ $in_time_statistics_data['9:00 - 9:30'] }} ],
        ['9:30 - 10:00', {{ $in_time_statistics_data['9:30 - 10:00'] }} ],
        ['10:00 - 10:30', {{ $in_time_statistics_data['10:00 - 10:30'] }} ],
        ['10:30 - 11:00', {{ $in_time_statistics_data['10:30 - 11:00'] }} ],
        ['After 11:00', {{ $in_time_statistics_data['After 11:00'] }} ]
    ];

    var chart = {
        renderTo: 'in_time_statistics_chart',
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false
    };

    var colors = [ '#a1c436','#5fb4ef', '#f2bc02', '#f23a02', '#FFF263',  '#50B432', '#ED561B', '#6AF9C4'];

    var title = {
        text: ''
    };

    var tooltip = {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    };

    var plotOptions = {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true
            },
            showInLegend: true
        }
    };

    var series = [{
        type: 'pie',
        name: 'Attendance In-Time Statistics',
        data: data_for_chart
    }];

    var json = {};
    json.chart = chart;
    json.colors = colors;
    json.title = title;
    json.tooltip = tooltip;
    json.plotOptions = plotOptions;
    json.series = series;

    chart = new Highcharts.Chart(json);

    //$('#in_time_statistics_chart').highcharts(json);



</script>
