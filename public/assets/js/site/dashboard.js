
		//console.log(window.location);
            $(function () {
    $(document).ready(function () {
        Highcharts.setOptions({
            global: {
                useUTC: false
            }
        });
        $('#container').highcharts({
            chart: {
                type: 'spline',
                animation: Highcharts.svg, // don't animate in old IE
                marginRight: 10,
                events: {
                    load: function () {
						 var series = this.series[0];
						
						if(typeof(EventSource) !== "undefined") {
							var source = new EventSource("app/sse_livechart");
							source.onmessage = function(event) {
								//console.log(event.data);
								var x = (new Date()).getTime();
								series.addPoint([x, parseInt(event.data)], true, true);
								
							};
							// Yes! Server-sent events support!
							// Some code.....
						} else {
							// Sorry! No server-sent events support..
							
							setInterval(function () {
													$.ajax({   
													url: "app/livechart", 
													success: function(y) {
														var x = (new Date()).getTime();
													series.addPoint([x, parseInt(y)], true, true);
													
														//console.log(a);
													}
												});
								}, 8000);
						}


						
                    }
                }
            },
            title: {
                text: ''
            },
            xAxis: {
                type: 'datetime',
                tickPixelInterval: 300
            },
            yAxis: {
				minTickInterval: 1,
                title: {
                   text: 'Employees Present in Office'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            tooltip: {
                formatter: function () {
                    return '<b>' + this.series.name + '</b><br/>Time : ' +
                        Highcharts.dateFormat('%H:%M:%S', this.x) + '<br/>' +
                        'No.:'+Highcharts.numberFormat(this.y, 0)+'';
                }
            },
            legend: {
                enabled: false
            },
            exporting: {
                enabled: false
            },
            series: [{
                name: 'Presence Graph',
                data: (function () {
                    // generate an array of random data
                    var data = [],
                        time = (new Date()).getTime(),
                        i;

                    for (i = -19; i <= 0; i += 1) {
						data.push({
                            x: time + i * 1000,
                            y: parseInt($('#count_start').html())
                        });
                    }
                    return data;
                }())
            }]
        });

	loadbar();
	if(typeof(EventSource) !== "undefined") {
							var source = new EventSource("app/sse_employeechart");
							source.onmessage = function(event) {
							var results=event.data;
							var res= results.split(",");
							animate_count('count_reg',$('#count_reg').html(),parseInt(res[0]));
							animate_count('count_present',$('#count_present').html(),parseInt(res[1]));
							animate_count('count_device',$('#count_device').html(),parseInt(res[2]));								
							};
						} else {
	setInterval(function () {
		$.ajax({
							url: "app/employeechart", 
							async: false,
							dataType: "html", 
							success: function(result) {
								//console.log(result);
							var obj = JSON.parse(result);
							animate_count('count_reg',$('#count_reg').html(),parseInt(obj.tot_reg));
							animate_count('count_present',$('#count_present').html(),parseInt(obj.t_in));
							animate_count('count_device',$('#count_device').html(),parseInt(obj.t_device_active));
							}
		})
		
		
		
		}, 10000);

						}
	function loadbar(){	
	var xmlhttp = new XMLHttpRequest();
var url = "app/dashboard_pie";

xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        var myArr = JSON.parse(xmlhttp.responseText);
		var data= [];
       for(var i=0;i<myArr.length;i++)
	   {
		  data.push([myArr[i].time_cat,parseInt(myArr[i].emp_num)]);
	   }
		
		 $('#employeechart').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
			colors: [ '#a1c436','#5fb4ef', '#f2bc02', '#f23a02', '#FFF263',  '#50B432', '#ED561B', '#6AF9C4'],
            title: {
                text: ''
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true
                    },
                    showInLegend: true
                }
            },
            series: [{
                type: 'pie',
                name: 'Attendance In-Time Statistics',
                data: data
            }]
        });
		
    }
}
xmlhttp.open("GET", url, true);
xmlhttp.send();

	}					
//loadlinebar();
	//setInterval(function () {loadlinebar();}, 20000);					
	function loadlinebar(){							
			$.ajax({
							url: "app/networkchart", 
							async: false,
							dataType: "html", 
							success: function(result) {
								//console.log(result);
							var data = JSON.parse(result);
								


    var series = [{
        name: 'Lan',
        data: []
    }, {
        name: 'GPRS',
        data: []
    }, {
        name: 'Wi-Fi',
        data: []
    }],
        categories = [];
    
    
    for(var category in data) {
        var points = data[category];
        categories.push(category);
        for(var i in points) {
             series[i].data.push(points[i]);  
        }
    }

    var i = 1.0;
    $('#networkchart').highcharts({
        title: {
            text: ' ',
            x: -20 //center
        },
        subtitle: {
            text: ' ',
            x: -20
        },
        xAxis: {
            categories: categories
        },
        yAxis: {
            title: {
                text: 'Avg. Response in Sec'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            valueSuffix: 'sec'
        },
       
        series: series
    });
	
	}
	})
	}

});
});
 var options = {
        useEasing : true, // toggle easing
        useGrouping : true, // 1,000,000 vs 1000000
        separator : '', // character to use as a separator
        decimal : '.', // character to use as a decimal
    }
    var useOnComplete = false;
    var useEasing = true;
    var useGrouping = true;

    var demo;

    // create instance
    function animate_count(id,start,end) {
        // fire animation
        // var element = document.querySelector('.jumbo');
        demo = new countUp(id, parseInt(start), parseInt(end), 0, 8, options);
		demo.start();

    }

	/* knobs */
	$(function() {
    "use strict";


    /* jQueryKnob */
    $(".knob").knob();
 });
	

	

 $(function () {
                     var chart;
                     $(document).ready(function () {
						 $.ajax({
							url: "app/dashboard_trend", 
							success: function(result) {
							var obj = JSON.parse(result);
							var data=[];
							for(var i=0;i<obj.length;i++)
						   {
							   var res = obj[i].att_time.split(":");
							   var d = new Date();
							   var time=new Date(d.getFullYear(),d.getMonth(),d.getDate(),res[0],res[1]).getTime();
							   
							data.push([time,parseInt(obj[i].attendance)]);
						   }
							
                         chart = new Highcharts.Chart({
                             chart: {
                                 renderTo: 'dashboard_trend',
								 zoomType: 'x',
                                 defaultSeriesType: 'areaspline'
								
								 

                             },
							 colors: ['#8fb313'],
							
                             title: {
                                 text: ''
                             },
                             xAxis: {
                                 type: 'datetime',
                                 tickInterval: 3600 * 1000 * 2,
								 
                             },
                             yAxis: {
								minTickInterval: 1,
								title: {
										text: 'Attendance Trend'
									},
									min:0
                             },
							  plotOptions: {
									area: {
										marker: {
											radius: 2,
											lineColor: '#666666',
											lineWidth: 1
										}
									}
								},
							 legend: {
								enabled: true
							},
							exporting: {
								enabled: false
							},
                             series: [{
								 name: 'Todays Trend',
								 lineWidth: 2,
                                 data: data
                             }]
                         });
						 
						}
						});
                     });
					 
		

       });
	   
	   
	   /*  attendance marked  */
	   
	   
	   $(document).ready(function () {
		loadbar();	
		//setInterval(function () {loadbar();}, 300);	

	function loadbar(){	
	var empId=$('#emp_id').val();
	//alert(empId);
	$.ajax({
		url: "app/at_mark", 

		dataType: "json", 
		success: function(xdata) {
		
			var data_att_tab=[];
			var data_att_desk=[];
			var data_log_date=[];
			for(var i=0;i<xdata.length;i++)
			  {
				  
				data_att_desk.push([parseInt(xdata[i].desktop)]);
				data_att_tab.push([parseInt(xdata[i].tablet)]);
				data_log_date.push([xdata[i].day_month]);
 
			  }

			// Preconfigure
					$('#combination').highcharts({
					chart: {
						type: 'areaspline'
					},
					title: {
						text: ''
					},
					legend: {
						enabled: false,
						align: 'right',
						verticalAlign: 'top',
						floating: true,
						backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
					},
					xAxis: {
						categories: data_log_date
						
					},
					yAxis: {
						title: {
							text: ''
						}
					},
					tooltip: {
						shared: false
					},
					credits: {
						enabled: false
					},
					plotOptions: {
						areaspline: {
							fillOpacity: 0.5
						}
					},
					series: [{
						name: 'Tablet',
						data: data_att_tab
						}, 
						{
						name: 'Desktop',
						data: data_att_desk
					}]
				});
		
	}
	});
	}
});

