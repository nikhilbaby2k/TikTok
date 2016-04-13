@extends('Layouts.MainCommonElements.app')

@section('content')
<!DOCTYPE html>
<html>

<!-- Mirrored from attendance.gov.in/ by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 28 Dec 2015 16:36:59 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->
<head>
    <meta charset="UTF-8">
    <title>TikTok.org | Dashboard</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css"/>
    <link href="assets/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="assets/css/select2.css">
    <link rel="stylesheet" href="assets/css/select2-bootstrap.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
</head>
<body class="skin-black"  onload="">
<!-- header logo: style can be found in header.less -->
<header class="header" >
    <a href="/" class="logo">
        <!-- Add the class icon to your logo image or logo icon to add the margining -->
        Tik - Tok</a>


    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>
        <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button" style="font-size:20px; font-weight:bold; color:#000; margin-top:0px;">
                       <span>
                        </span></a>
        <!-- /Sidebar toggle button-->
    </nav>
</header>


<div class="wrapper row-offcanvas row-offcanvas-left">
    <aside class="left-side sidebar-offcanvas">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">

            <ul class="sidebar-menu">
                <li class="active"> <a href="index.html"> <i class="fa fa-dashboard"></i> <span>Dashboard</span> </a></li>

                <li><a href="register/organization.html"><i class="fa fa-building-o"></i> Organization Registration</a></li>


                <li class="treeview"> <a href="#"> <i class="fa fa-tablet"></i> <span>Attendance Reports</span> <i class="fa fa-angle-left pull-right"></i> </a>
                    <ul class="treeview-menu">
                        <li><a href="reports/regemp.html"> <i class="fa fa-angle-double-right"></i> <span> Registered Employee</span> </a> </li>
                        <li><a href="reports/activeuser.html"><i class="fa fa-angle-double-right"></i> Present Today</a></li>
                        <li><a href="present_today_org_hr/hierarchy_org.html"><i class="fa fa-angle-double-right"></i> Present Today(Org.HRC)</a></li>
                        <li><a href="reports/device.html"><i class="fa fa-angle-double-right"></i> Device</a></li>

                    </ul>
                </li>

                <li>
                    <a href="login.html">
                        <i class="fa fa-unlock"></i> <span>Login </span>
                    </a>
                </li>


                <li class="treeview"> <a href="faq.html"> <i class="fa fa-question"></i> <span>FAQ</span> <i class="fa fa-angle-left pull-right"></i> </a>
                    <ul class="treeview-menu">
                        <li><a href="faq/public_faq.html"><i class="fa fa-angle-double-right"></i> General Questions</a></li>
                        <li><a href="faq/attendance_faq.html"><i class="fa fa-angle-double-right"></i> How to Mark Attendance</a></li>
                        <li><a href="assets/doc/Phase-II_of_BAS.pdf"><i class="fa fa-angle-double-right"></i> Guideline for Phase-II</a></li>
                        <li><a href="assets/doc/OfficeMemo.pdf"><i class="fa fa-angle-double-right"></i> Office Memorandum- BAS</a></li>
                        <li><a href="http://www.dgserver.dgsnd.gov.in/reports/rwservlet?KEY1&amp;report=webdescription_hindi.rdf&amp;destype=cache&amp;desformat=pdf&amp;paramform=no&amp;pmajor=711D0000"><i class="fa fa-angle-double-right"></i> DGS&D RCs for Devices </a></li>
                        <li><a href="assets/doc/Dashboard.pdf"><i class="fa fa-angle-double-right"></i> Nodal Officer Manual </a></li>
                        <li><a href="assets/doc/employee_um.pdf"><i class="fa fa-angle-double-right"></i> Employee User Manual </a></li>
                        <li><a href="assets/doc/Regarding-Biometric-AttendanceSystem.pdf"><i class="fa fa-angle-double-right"></i> Biometric Attendance System </a></li>
                        <li><a href="assets/doc/Dopt-order%20Dated%2022.06.2015.pdf"><i class="fa fa-angle-double-right"></i> DoPT Order Dated 22.06.2015 </a></li>

                    </ul>
                </li>

            </ul>


            <!-- search form -->
            <form action="#" method="get" class="sidebar-form">
                 <div class="input-group">
                     <input type="text" name="q" class="form-control" placeholder="Find your Organization..."/>
                     <span class="input-group-btn">
                         <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
                     </span>
                 </div>
             </form>
            <!-- /.search form -->

        </section>
        <!-- /.sidebar -->
    </aside>



    <aside class="right-side">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1> Dashboard <small> Biometric Attendance System</small> </h1>
            <ol class="breadcrumb">
                <li><a href="/admin"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <script src="assets/js/jquery.min.js"></script>
            <script src="assets/js/highcharts.js"></script>
            <!-- Small boxes (Stat box) top statistics shorts -->
            <div class="row">
                @include('Layouts.Models.OrganizationModel')
                <!-- ./col -->
                @include('Layouts.Models.RegisteredEmployeeModel')
                <!-- ./col -->
                @include('Layouts.Models.EmployeePresentTodayModel')
                <!-- ./col -->
                @include('Layouts.Models.ActiveBioMetricDevices')
                <!-- ./col -->
            </div>
            <!-- /.row -->

            <!-- top row -->
            <div class="row">
                <!-- Left col -->
                <!-- /.Left col -->

                <!-- live graph-->
                <section class="col-lg-6 connectedSortable">
                    <!-- interactive chart -->
                    @include('Layouts.Charts.AttendanceActivityChart')
                    <!-- /.box -->
                </section>


                <!--  detailed analysis  / -->
                <section class="col-lg-6 connectedSortable">
                    <!-- Box (with bar chart) -->
                    @include('Layouts.Charts.AttendanceStatisticsChartWithSummery')
                    <!-- /.box -->
                </section><!-- /.Left col -->
                <!-- / detailed analysis -->


                <section class="col-lg-6 connectedSortable left">
                    <!-- employee registration bar graph -->
                    @include('Layouts.Charts.EmployeeInTimeStatisticsPerDayChart')
                    <!-- /.box -->
                </section>
                <!-- right col -->


                <section class="col-lg-6 connectedSortable">
                    <!-- auth response line  -->
                    @include('Layouts.Charts.TodaysAttendanceTrends')
                    <!-- /.box -->
                </section>

                <center><h6> Beta Version | ♦♦ ñi ♦♦ <br/>
                        &copy; 2015 mx.tiktok.org. All rights !reserved.</h6></center>

            </div>
            <!-- /.row -->
            <!-- graph section -->

        </section>
        <!-- /.content -->
    </aside>
    <!-- /.right-side -->
</div>
<!-- ./wrapper -->

<!-- add new calendar event modal -->
<script type="text/javascript">
    // This identifies your website in the createToken call below

    jQuery(document).ready(function($){

        window.setInterval(function(){

            $.ajax({
                type: "POST",
                url : "{{ route('update_in_out_time_status')  }}",
                data : { '_token': "{{ csrf_token()  }}" },
                success : function(data){
                    console.log("returned 1");
                }
            });

            $.ajax({
                type: "POST",
                url : "{{ route('update_attendance')  }}",
                data : { '_token': "{{ csrf_token()  }}" },
                success : function(data){
                    console.log("returned 2");
                }
            });

        }, 10000);





    });




</script>


</body>
<!-- Mirrored from attendance.gov.in/ by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 28 Dec 2015 16:39:07 GMT -->
</html>
@endsection