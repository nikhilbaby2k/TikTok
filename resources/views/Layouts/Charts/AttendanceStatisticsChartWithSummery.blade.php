<div class="box box-default" >
    <div class="box-header">
        <!-- tools box -->
        <div class="pull-right box-tools">
            <button class="btn btn-default btn-xs" data-widget='collapse' data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
        </div><!-- /. tools -->
        <i class="fa fa-cloud"></i>

        <h3 class="box-title">Attendance Statistics </h3>
    </div><!-- /.box-header -->
    <div class="box-body no-padding">
        <div class="row">
            <div class="col-sm-7">
                <!-- bar chart -->
                <div class="chart" id="combination" style="height: 220px;"></div>
            </div>
            <div class="col-sm-5">
                <div class="pad box-lbl-txt">

                    <div class="clearfix">
                        <span class="pull-left">Active Employees</span>
                        <small class="pull-right">{{$registered_employee_count_active}} / {{$registered_employee_count}}</small>
                    </div>
                    <div class="progress xs progress-striped active">
                        <div class="progress-bar progress-bar-light-blue" style="width: {{intval($registered_employee_count_active*100/$registered_employee_count)}}%;"></div>
                    </div>

                    <div class="clearfix">
                        <span class="pull-left">Biometric Terminals</span>
                        <small class="pull-right">{{$active_devices}}/{{$active_devices}}</small>
                    </div>
                    <div class="progress xs progress-striped active">
                        <div class="progress-bar progress-bar-yellow" style="width: {{intval($active_devices*100/$active_devices)}}%;"></div>
                    </div>

                    <div class="clearfix">
                        <span class="pull-left">Desktop Device</span>
                        <small class="pull-right">3710/4979</small>
                    </div>
                    <div class="progress xs progress-striped active">
                        <div class="progress-bar progress-bar-aqua" style="width: 74%;"></div>
                    </div>

                    <div class="clearfix">
                        <span class="pull-left">Auth Request (Desktop)</span>
                        <small class="pull-right"><span class="text-green">41479</span> | <span class="text-black">225540</span></small>
                    </div>
                    <div class="progress xs progress-striped active xs">
                        <div class="progress-bar progress-bar-red" style="width: 18%;"></div>
                    </div>
                    <!-- Buttons -->

                </div><!-- /.pad -->
            </div><!-- /.col -->
        </div><!-- /.row - inside box -->
    </div><!-- /.box-body -->

    <div class="box-footer">
        <div class="row">
            <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                <h2><span class="text-orange"> {{$avg['in_time']}} </span></h2>
                <div class="knob-label">Average In-Time</div>
            </div>
            <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                <h2><span class="text-maroon"> {{$avg['out_time']}}  </span></h2>
                <div class="knob-label">Average Out Time</div>
            </div>
            <div class="col-xs-4 text-center">
                <h2><span class="text-teal"> {{$avg['response_time']}} <small>sec</small> </span></h2>
                <div class="knob-label">Average Response</div>
            </div><!-- ./col -->

        </div><!-- /.row -->
    </div><!-- /.box-footer -->
</div>