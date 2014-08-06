<?php
    $page = 'reporting_second';
    $pageTitle = 'Project Chart';
    $pageName = 'Project Chart';
    $pageDescription = 'Project Chart';
?>
<?php include('layout/header.php');?>

<?php include('layout/sidebar.php');?>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Year', 'Jhon', 'Joe'],
          ['2004',  1000,      400],
          ['2005',  1170,      460],
          ['2006',  660,       1120],
          ['2007',  1030,      540]
        ]);

        var options = {
          title: 'Total opens by sender (Yearly) [Line]'
        };

        var chart = new google.visualization.LineChart(document.getElementById('container'));
        chart.draw(data, options);
      }
	  
	  
 function drawLine() {
        var data = google.visualization.arrayToDataTable([
          ['Year', 'Sales', 'Expenses'],
          ['2004',  1000,      400],
          ['2005',  1170,      460],
          ['2006',  660,       1120],
          ['2007',  1030,      540]
        ]);

        var options = {
          title: 'Company Performance'
        };

        var chart = new google.visualization.LineChart(document.getElementById('container'));
        chart.draw(data, options);
      }
	  

	  
function drawTrend() {
  var data = google.visualization.arrayToDataTable([
    ['Diameter', 'Age'],
    [8, 37], [4, 19.5], [11, 52], [4, 22], [3, 16.5], [6.5, 32.8], [14, 72]]);

  var options = {
   title: 'Total opens by sender (Yearly) [Trend]',
    hAxis: {title: 'Diameter'},
    vAxis: {title: 'Age'},
    legend: 'none',
    trendlines: { 0: {} }    // Draw a trendline for data series 0.
  };

  var chart = new google.visualization.ScatterChart(document.getElementById('container'));
  chart.draw(data, options);
}
	  
    </script>
<script>
	$(function(){
		$('select').selectric();
	});
</script>
<script>
  $(function() {
    $( "#datepicker" ).datepicker();
  });
  
  
function change_s(val)
{
      if(val==1)
	  {
          drawLine();
	  }
	  else
	  {
          drawTrend();
	  }
}
  
  </script>
  
<div class="body_div" id="rightSide" style="font-family: Arial;border: 0 none;">
    <?php include('layout/top-navigation.php');?>
<div class="body_div"> 
<div class="body_left" style="float: left;">
<select class="sel1">
<option>Last 30 days: Nov5 2013 - Dec4 2013</option>
<option>Last 30 days: Nov5 2013 - Dec4 2013</option>
<option>Last 30 days: Nov5 2013 - Dec4 2013</option>
<option>Last 30 days: Nov5 2013 - Dec4 2013</option>
<option>Last 30 days: Nov5 2013 - Dec4 2013</option>
<option>Last 30 days: Nov5 2013 - Dec4 2013</option>
<option>Last 30 days: Nov5 2013 - Dec4 2013</option>
<option>Last 30 days: Nov5 2013 - Dec4 2013</option>
<option>Last 30 days: Nov5 2013 - Dec4 2013</option>
</select>


<select class="sel2">
<option>Data Interval(Yearly)</option>
<option>Data Interval(Yearly)</option>
<option>Data Interval(Yearly)</option>
<option>Data Interval(Yearly)</option>
<option>Data Interval(Yearly)</option>
<option>Data Interval(Yearly)</option>
</select>

<select class="sel2">
<option>Metric (Sender)</option>
<option>Metric (Sender)</option>
<option>Metric (Sender)</option>
<option>Metric (Sender)</option>
<option>Metric (Sender)</option>
<option>Metric (Sender)</option>
</select>

<select class="sel2">
<option>Type (Opens)</option>
<option>Type (Opens)</option>
<option>Type (Opens)</option>
<option>Type (Opens)</option>
<option>Type (Opens)</option>
<option>Type (Opens)</option>
</select>

<select class="sel2" onchange="change_s(this.value);">
<option value="1">Graph Style 1</option>
<option value="2">Graph Style 2</option>
</select>

<div class="date"><input type="text" id="datepicker"></div>
</div>
<div class="body_right" style="float: left;width: 100%">
<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
</div>

<div class="clear"></div>
<div class="tab_upper">
<table id="testing"  border="0" cellpadding="0" cellspacing="0">
			<thead>
				<th>header</th><th>header</th><th>header</th>
			</thead>
			<tbody><tr>
				<td>cell</td><td>cell</td><td>cell</td>
			</tr>
			<tr>
				<td>cell</td><td>cell</td><td>cell</td>
			</tr>
			<tr>
				<td>cell</td><td>cell</td><td>cell</td>
			</tr>
			<tr>
				<td>cell</td><td>cell</td><td>cell</td>
			</tr>
			<tr>
				<td>cell</td><td>cell</td><td>cell</td>
			</tr>
			<tr>
				<td>cell</td><td>cell</td><td>cell</td>
			</tr>
			<tr>
				<td>cell</td><td>cell</td><td>cell</td>
			</tr>
			<tr>
				<td>cell</td><td>cell</td><td>cell</td>
			</tr>
			<tr>
				<td>cell</td><td>cell</td><td>cell</td>
			</tr>
			<tr>
				<td>cell</td><td>cell</td><td>cell</td>
			</tr>
			<tr>
				<td>cell</td><td>cell</td><td>cell</td>
			</tr>
			<tr>
				<td>cell</td><td>cell</td><td>cell</td>
			</tr>
			<tr>
				<td>cell</td><td>cell</td><td>cell</td>
			</tr>
			<tr>
				<td>cell</td><td>cell</td><td>cell</td>
			</tr>
			<tr>
				<td>cell</td><td>cell</td><td>cell</td>
			</tr>	</tbody>																
		</table>
		<div class="clear"></div>
		</div>


</div>
</div>
<?php include('layout/footer.php');?>

		
	<link rel="stylesheet" type="text/css" href="js/jquery-ui/css/redmond/jquery-ui-1.8.4.custom.css"/>
		<link rel="stylesheet" type="text/css" href="js/jquery-ui/css/ui-lightness/jquery-ui-1.8.4.custom.css"/>
		<link rel="stylesheet" type="text/css" href="js/jquery-ui/css/smoothness/jquery-ui-1.8.4.custom.css"/>
		<link rel="stylesheet" type="text/css" href="js/jquery-ui/css/flick/jquery-ui-1.8.4.custom.css" id="link"/>
		<link rel="stylesheet" type="text/css" href="css/base.css" />
		
		<script type="text/javascript" src="js/highlighter/codehighlighter.js"></script>	
		<script type="text/javascript" src="js/highlighter/javascript.js"></script>			
		<script type="text/javascript" src="js/jquery.fixheadertable.min.js"></script>
		
		<script type="text/javascript">  
					
			$(document).ready(function() {
			$('#testing').fixheadertable({
     /**
      Set a title to your table (param : string, default : ' ')
     */
     //caption        : ' ',
     /**
      Enable/disable the toggle display button (param : boolean, default : true)
      (works only with the caption option)
     */
     showhide       : true,
     /**
      Class name for add/modify theme (param : string, default : 'ui')
     */
     theme          : 'ui',
     /**
      Height of the table (param : integer, default : null)
     */
     height         : 200,
     /**
      Width of the table (param : integer, default : null)
     */
     width          : "100%",
     /**
      The minimum width of the table before horizontal scrolling (param : integer, default : null)
      (override the minWidthAuto option)
     */
     minWidth       : null,
     /**
      The minimum width calculated by the plugin (param : integer, default : null)
     */
     minWidthAuto   : true,
     /**
       Set the width of each column in pixel. (param : Array, default : [])
       The array must contain as much element as columns in your table, and
       each value must be an integer
     */
     colratio       : [ ],
     /**
       The CSS whiteSpace property applied to the table's cells (param : string, default : 'nowrap')
       values : 'nowrap' | 'normal'
     */
     whiteSpace     : 'nowrap',
     /**
       Add title bubbles when hovering cells (param : boolean, default : false)
     */
     //addTitles      : true,
     /**
       Alternate row style (param : boolean, default : false)
     */
     //zebra          : true,
     /**
       Set the CSS class applied alternatively (param : string, default : 'ui-state-active')
       (works with the zebra option)
     */
     //zebraClass     : 'ui-state-active',
     /**
       Make your columns sortable (param : boolean, default : false)
       (all the columns will be sortable)
     */
     //sortable       : true,
     /**
       Sort a column by default (param : integer, default : null)
     */
     sortedColId    : null,
     /**
       Set the sort callback of each column (param : Array, default : [])
       (if the array is empty, the default callback will be string)
       The array must contain as much element as columns in your table, and
       each value must be an string
       Available callbacks : 'string' | 'integer' | 'float' | 'date'
     */
     sortType       : [],
     /**
       The date format for the sort callback (param : string, default : 'd-m-y')
     */
     dateFormat     : 'd-m-y',
     /**
       Enable/Disable the pager (param : boolean, default : false)
     */
     pager          : true,
     /**
        The number of row to display (param : integer, default : 10)
        (works with the pager) allowed values : 10 | 25 | 50 | 100
     */
     rowsPerPage    : 10,
     /**
       Enable/Disable the column resizing (param : boolean, default : false)
     */
     resizeCol      : true,
     /**
       The minimum width in pixel of a resizable column (param : integer, default : 100)
     */
     minColWidth    : 100,
     /**
       Just wrap the table with a container (param : boolean, default : true)
     */
     wrapper        : true
});});
		</script>
		
</html>
