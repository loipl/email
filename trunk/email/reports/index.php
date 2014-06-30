<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title>
     Project Chart
    </title>
<link rel="stylesheet" type="text/css" href="css/style.css" />
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.css" />
<link rel="stylesheet" href="css/selectric.css">

<script type="text/javascript" src="//www.google.com/jsapi"></script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<!--<script type="text/javascript"  src="js/colResizable-1.3.min.js"></script>-->
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script src="js/jquery.selectric.js"></script>

    <script type="text/javascript">

      google.load('visualization', '1', {packages: ['table']});
	  google.load("visualization", "1", {packages:["corechart"]});
	  

    var visualization;
    var data;

    var options = {'showRowNumber': true};

    function drawVisualization() {
      // Create and populate the data table.


      var dataAsJson =
      {cols:[
        {id:'A',label:'Sender',type:'string'},
        {id:'B',label:'Year',type:'number'},
        {id:'C',label:'Opens',type:'number'},
        {id:'D',label:'Currently Enabled',type:'boolean'}],
      rows:[
        {c:[{v:'Dave'},{v:159.0,f:'159'},{v:true,f:'TRUE'},{v:[12,25,0,0],f:'12:25:00'}]},
      {c:[{v:'Peter'},{v:185.0,f:'185'},{v:false,f:'FALSE'},{v:[13,15,0,0],f:'13:15:00'}]},
      {c:[{v:'John'},{v:145.0,f:'145'},{v:true,f:'TRUE'},{v:[15,45,0,0],f:'15:45:00'}]},
      {c:[{v:'Moshes'},{v:198.0,f:'198'},{v:true,f:'TRUE'},{v:[16,32,0,0],f:'16:32:00'}]},
      {c:[{v:'Karen'},{v:169.0,f:'169'},{v:true,f:'TRUE'},{v:[19,50,0,0],f:'19:50:00'}]},
      {c:[{v:'Phil'},{v:169.0,f:'169'},{v:false,f:'FALSE'},{v:[18,10,0,0],f:'18:10:00'}]},
      {c:[{v:'Gori'},{v:159.0,f:'159'},{v:true,f:'TRUE'},{v:[13,15,0,0],f:'13:15:00'}]},
      {c:[{v:'Bill'},{v:155.0,f:'155'},{v:false,f:'FALSE'},{v:[7,40,48,0],f:'7:40:48'}]},
      {c:[{v:'Valery'},{v:199.0,f:'199'},{v:true,f:'TRUE'},{v:[6,0,0,0],f:'6:00:00'}]},
      {c:[{v:'Joey'},{v:187.0,f:'187'},{v:true,f:'TRUE'},{v:[5,2,24,0],f:'5:02:24'}]},
      {c:[{v:'Karen'},{v:190.0,f:'190'},{v:true,f:'TRUE'},{v:[6,14,24,0],f:'6:14:24'}]},
      {c:[{v:'Jin'},{v:169.0,f:'169'},{v:false,f:'FALSE'},{v:[5,45,36,0],f:'5:45:36'}]},
      {c:[{v:'Gili'},{v:178.0,f:'178'},{v:true,f:'TRUE'},{v:[6,43,12,0],f:'6:43:12'}]},
      {c:[{v:'Harry'},{v:172.0,f:'172'},{v:false,f:'FALSE'},{v:[6,14,24,0],f:'6:14:24'}]},
      {c:[{v:'Handerson'},{v:175.0,f:'175'},{v:true,f:'TRUE'},{v:[6,57,36,0],f:'6:57:36'}]},
      {c:[{v:'Vornoy'},{v:170.0,f:'170'},{v:true,f:'TRUE'},{v:[13,12,0,0],f:'13:12:00'}]}]};
      data = new google.visualization.DataTable(dataAsJson);
    
      // Set paging configuration options
      // Note: these options are changed by the UI controls in the example.
      options['page'] = 'enable';
      options['pageSize'] = 10;
      options['pagingSymbols'] = {prev: 'prev', next: 'next'};
      options['pagingButtonsConfiguration'] = 'auto';
    
      // Create and draw the visualization.
      visualization = new google.visualization.Table(document.getElementById('table'));
      draw();
    }
    
    function draw() {
      visualization.draw(data, options);
    }
    

   // google.setOnLoadCallback(drawVisualization);

    // sets the number of pages according to the user selection.
    function setNumberOfPages(value) {
      if (value) {
        options['pageSize'] = parseInt(value, 10);
        options['page'] = 'enable';
      } else {
        options['pageSize'] = null;
        options['page'] = null;  
      }
      draw();
    }

    // Sets custom paging symbols "Prev"/"Next"
    function setCustomPagingButtons(toSet) {
      options['pagingSymbols'] = toSet ? {next: 'next', prev: 'prev'} : null;
      draw();  
    }

    function setPagingButtonsConfiguration(value) {
      options['pagingButtonsConfiguration'] = value;
      draw();
    }


 function drawLine() {
//var lineData = google.visualization.arrayToDataTable(LineData);


var lineData = new google.visualization.DataTable();
	  for (var i = 0, len = LineData[0].length; i < len; i++) {
	  if(i==0)
lineData.addColumn('string', LineData[0][i]);
else{
lineData.addColumn('number', LineData[0][i]);
lineData.addColumn({type: 'string', role: 'tooltip', p: {html:true}});
}
}
	  for (var i = 1, len = LineData.length; i < len; i++) {
 lineData.addRow(LineData[i]);
}
 



        var optionsLine = {
		tooltip: {isHtml: true},
          title: ChartTitle+'[Line]',
width: '100%',
  height: ($(window).height()*60)/100,
		  pointSize: 5,
		  vAxis: {maxValue: 200},
		  chartArea: {left:"5%", width:"75%"}
        };

        var chart = new google.visualization.LineChart(document.getElementById('container'));
        chart.draw(lineData, optionsLine);
		


		
		
      }
	  
	   $( window ).resize(function() {
drawTable();
$(".body_div").css('height' , ($(window).height()*70)/100);
$(".body_left_upper").css('width' , ($(window).width()*23)/100);
//$(".body_left_upper li").css('height' , ($(window).height()*5)/100);
//$(".body_left_upper li").css('line-height' , ($(window).height()*5)/100);
//$(".selectric p").css('height' , ($(window).height()*5)/100);
//$(".selectric p").css('line-height' , ($(window).height()*5)/100);
//$(".selectric .button").css('height' , ($(window).height()*5)/100);

//$(".selectric .button").css('line-height' , ($(window).height()*5)/100);
//$(".li_button").css('height' , ($(window).height()*5)/100);
//$(".li_button").css('line-height' , ($(window).height()*5)/100);


 if(chartType=='Line')
 {
 drawLine();
}
else
{
drawTrend();
}
    });
	  
function drawTrend() {
//var trendData = google.visualization.arrayToDataTable(TrendData);
var trendData = new google.visualization.DataTable();

	  for (var i = 0, len = TrendData[0].length; i < len; i++) {
	  if(i==0)
trendData.addColumn('number', TrendData[0][i]);
else{
trendData.addColumn('number', TrendData[0][i]);
trendData.addColumn({type: 'string', role: 'tooltip', p: {html:true}});
}
}
	  for (var i = 1, len = TrendData.length; i < len; i++) {
 trendData.addRow(TrendData[i]);
}
 


  var optionsTrend = {
  tooltip: {isHtml: true},
   title: ChartTitle+'[Trend]',
width: '100%',
  height: ($(window).height()*60)/100,
		  vAxis: {maxValue: 200},
		  chartArea: {left:"5%", width:"75%"},
    trendlines: { 0: {} } ,   // Draw a trendline for data series 0.
	hAxis: { textPosition: 'none', baselineColor: '#000' , textColor:'#FFFFFF'}
  };

  var chart = new google.visualization.ScatterChart(document.getElementById('container'));
  chart.draw(trendData, optionsTrend);
}
function change_s(val)
{
      if(val==1)
	  {
          drawLine();
		  chartType = 'Line';
	  }
	  else
	  {
          drawTrend();
		  chartType = 'Trend';
	  }
}


var interval_types = new Array();
/*interval_types[]='year';
interval_types[]='month';
interval_types[]='day';
interval_types[]='hour';
interval_types[]='minute';*/
var metric = 'sender';
var type = 'open';
var interval = 'day';
var intervalSet = 'minute';
var time = 'last30';
var ChartTitle = '';
var LineData;
var TrendData;
var TableData;
var chartType = 'Line'; 


function drawTable()
{

 data = new google.visualization.DataTable();
	  for (var i = 0, len = TableData.cols.length; i < len; i++) {
data.addColumn(TableData.cols[i][0], TableData.cols[i][1]);

}
data.addRows(TableData.rows);
      // Set paging configuration options
      // Note: these options are changed by the UI controls in the example.

  options['width'] = '100%';

      options['page'] = 'enable';
      options['pageSize'] = 10;
      options['pagingSymbols'] = {prev: 'prev', next: 'next'};
      options['pagingButtonsConfiguration'] = 'auto';
    
      // Create and draw the visualization.
      visualization = new google.visualization.Table(document.getElementById('table'));
      visualization.draw(data, options);

}
function draw_page(ref , key)
{
    if(ref=='metric')metric=key;
	else if(ref=='type')type=key;
	else if(ref=='interval')interval=key;
	else if(ref=='time')time=key;
	$("#loading").show();
	$('#loading').css('opacity', 0.8);
    var URL = 'api.php';
    $.ajax({
	type: "POST",
	data:'metric='+metric+'&type='+type+'&time='+time+'&interval='+interval,
	url: encodeURI(URL),
	dataType: "json",
	success:function(data3){
    
      // Create and populate the data table.
      var dataAsJson = data3;
      if(dataAsJson.error==1)
	  {
	      alert(dataAsJson.msg);
		  return;
	  }
		 LineData = dataAsJson.lineData;
		 TrendData = dataAsJson.trendData;
		 TableData = dataAsJson.tableData;
		 ChartTitle = dataAsJson.chart_title;
		 interval = dataAsJson.interval;
		 intervalSet = dataAsJson.intervalSet;

       var i;
	   $("#interval_input_box > li").hide();
		$("#interval_input_box > li").each(function()
		{
		  
		 if( $(this).attr('title') == interval )
		 {
		 
		  $("#interval_place").html($(this).html());

		  }
		   
		  

for (i = 0, len = intervalSet.length; i < len; i++) {
 if($(this).attr('title') == intervalSet[i])
 $(this).show();
}
 


		});
drawTable();

     	 $("#loading").hide();
	 $('#loading').css('opacity', 0);
	 
/*var sampleData = [], results = dataAsJson.lineData;
for (var i = 0, len = dataAsJson.tableData.length; i < len; i++) {
    var result = results[i];
	for (var k = 0, len = dataAsJson.tableData.length; k < len;k++) {
        sampleData[i].push({k: result[k]});
	}
}*/
 if(chartType=='Line')
 {
 drawLine();
}
else
{
drawTrend();
}

setNumberOfPages($("#num_rows").val());


	}});




}
function fff()
{
    $("#test").show();
}

$(function(){
draw_page('metric','sender');
	$('.sel2').selectric();
$('.sel3').selectric();
	$("#select_with_input").on("click" , function(){
	    $("#test").show();
		$("#interval_input_box").hide();
	});
	$("#interval_input").on("click" , function(){
	    $("#test").hide();
		$("#interval_input_box").show();
	});

	$("#interval_input_box > li").on("click" , function(event){
	    //event.preventDefault();

		setTimeout('$("#interval_input_box").hide()',50);
			$("#interval_place").html($(this).html());
			draw_page('interval' , $(this).attr('title'));

		
	});


$(document).mouseup(function (e)
{
    var container = $("#select_with_input");
	var date_container = $(".ui-datepicker");

    if (!container.is(e.target)&&container.has(e.target).length === 0&&!date_container.is(e.target)&&date_container.has(e.target).length === 0)
    {
        $("#test").hide();
    }
	
    var container_interval = $("#interval_input");


    if (!container_interval.is(e.target)&&container_interval.has(e.target).length === 0)
    {
        $("#interval_input_box").hide();
    }
	
});



$("#datepicker1").datepicker({
        numberOfMonths: 1,
        onSelect: function(selected) {
          $("#datepicker2").datepicker("option","minDate", selected);
		  $("#current_time").html($("#datepicker1").val()+" - "+$("#datepicker2").val());
        },
		dateFormat: "yy-mm-dd"
    });
    $("#datepicker2").datepicker({ 
        numberOfMonths: 1,
        onSelect: function(selected) {
           $("#datepicker1").datepicker("option","maxDate", selected);
		   $("#current_time").html($("#datepicker1").val()+" - "+$("#datepicker2").val());
		   if($("#datepicker1").val()!='')
		   {
		       $("#test").hide();
			   draw_page('time' , $("#current_time").html());
		   }
        },
		dateFormat: "yy-mm-dd"
    });  
	$("#test > li").on("click" , function(event){
	    //event.preventDefault();
	    if($(this).attr('title')!='date'){
		setTimeout('$("#test").hide()',50);
			$("#current_time").html($(this).html());
			draw_page('time' , $(this).attr('title'));
	    }
		
	});
	$("#clear_dates").on("click" , function(event){
		$("#current_time").html('All Time');
		$("#datepicker1").val('');
		$("#datepicker2").val('');
		time = 'all';
	});

});
    </script>
  </head>
  <body style="font-family: Arial;border: 0 none;">
  

  <div class="body_div">  
<div class="body_left_upper">  
<div class="body_left">
<div id="select_with_input" class="select_with_input">
<li><div class="l" id="current_time">Last 30 days</div><div class="li_button">&#9660;</div></li>
<div id="test" class="test">
<li title='date'><div class="l">Custom&nbsp;:&nbsp;</div><input type="text" id="datepicker1" class="date_sel"><div class="l">&nbsp;-&nbsp;</div><input type="text" id="datepicker2" class="date_sel">&nbsp;&nbsp;&nbsp;&nbsp;<a id="clear_dates" href="javascript:void(0);"><img src="images/cross.png" border="0" title="Clear" /></a></li>
<li title="today">Today</li>
<li title="yesterday">Yesterday</li>
<li title="thisweek">This week</li>
<li title="last7">Last 7 days</li>
<li title="lastw">Last week</li>
<li title="lastbw">Last business week (Mon-Fir)</li>
<li title="last14">Last 14 days</li>
<li title="thismonth">This month</li>
<li title="last30">Last 30 days</li>
<li title="lastm">Last month</li>
<li title="all">All time</li>
</div>
</div>



<div class="clear"></div>


<div id="interval_input" class="select_with_input1">
<li><div class="l" id="interval_place">Data Interval (Yearly)</div><div class="li_button">&#9660;</div></li>
<div id="interval_input_box" class="test1">
<li title="year">Data Interval (Yearly)</li>
<li title="month">Data Interval (Monthly)</li>
<li title="day">Data Interval (Daily)</li>
<li title="hour">Data Interval (Hourly)</li>
<li title="minute">Data Interval (Minute)</li>
</div>
</div>




<div class="clear"></div>


<select class="sel2" name="metric" onchange="draw_page(this.name,this.value);">
<option value="sender">Metric (Sender)</option>
<option value="campaign">Metric (Campaign)</option>
<option value="creative">Metric (Creative)</option>
<option value="channel">Metric (Channel)</option>
<option value="recipientdomain">Metric (Recipientdomain)</option>

</select>





<select class="sel2" name="type" onchange="draw_page(this.name,this.value);">
<option value="open">Type (Opens)</option>
<option value="total">Type (Total)</option>
<option value="click">Type (Clicks)</option>
<option value="unsubscribe">Type (Unsubscribes)</option>
<option value="softbounce">Type (Softbounces)</option>
<option value="complaint">Type (Complaints)</option>
<option value="hardbounce">Type (Hardbounces)</option>
</select>


<select class="sel2" onchange="change_s(this.value);">
<option value="1">Graph Style (Line)</option>
<option value="2">Graph Style (Trend)</option>
</select>



</div><div class="clear"></div>
</div>
<div class="body_right">
<div id="container"></div>
</div>

<div class="clear"></div>






<!--<div class="tab_upper">
<div id="testing"></div>
<table id="testing" width="100%" border="0" cellpadding="0" cellspacing="0">
			<thead>
				<th>&nbsp;</th><th><strong>Sender</strong></th><th><strong>Year</strong></th><th><strong>Opens</strong></th><th><strong>Currently Enabled</strong></th>
			</thead>
			<tbody><tr>
				<td>1</td><td>John</td><td>2004</td><td>100</td><td>&#10003;</td>
			</tr>
			<tr>
				<td>2</td><td>Joe</td><td>2004</td><td>1080</td><td>X</td>
			</tr>
			<tr>
				<td>3</td><td>John</td><td>2005</td><td>0</td><td>X</td>
			</tr>
			<tr>
				<td>4</td><td>Joe</td><td>2005</td><td>1355</td><td>&#10003;</td>
			</tr>
			<tr>
				<td>5</td><td>John</td><td>2006</td><td>209</td><td>&#10003;</td>
			</tr>
			<tr>
				<td>6</td><td>Joe</td><td>2006</td><td>1006</td><td>&#10003;</td>
			</tr>
				</tbody>																
		</table>
		<div class="clear"></div>
		</div>-->



  
  
  
  
  
  
  
  
  
  
  
  
  
    <div style=" margin:auto; width:80%;">

      <form action="">
        <span style="font-size: 12px;">Number of rows:</span>
        <select style="font-size: 12px" id="num_rows" class="sel3" onchange="setNumberOfPages(this.value)">
          <option value="">No paging</option>
          <option value="3">3</option>
          <option value="5">5</option>
          <option value="9">9</option>
          <option selected="selected" value="10">10</option>
          <option value="100">100</option>
        </select>
       
      </form>
      </div>
    <div id="table"></div>
	

	
	
	<div id="loading"></div>
	<div class="clear"></div>
	</div>
	
	<script type="text/javascript">
$(document).ready(function() {
var H = $(window).height();


//$(".body_left").css("height",((H*60)/100)-20);

});
</script>
  </body>
</html>
