<?php
session_start();
if (!isset($_SESSION['loggedin'])||($_SESSION['loggedin']==false))
	header("location:./");

$host="localhost"; // Host name 
$username="pi"; // Mysql username 
$password="raspberry"; // Mysql password 
$db_name="ilaw"; // Database name 
$tbl_name="bulb"; // Table name 

//Right Navigation Menu Highlights
$groupWarningMaps = "";
$badgeWarningMaps = "";
$groupWarningLights = "";
$badgeWarningLights = "";
$groupWarningReportsIndividual = "";
$badgeWarningReportsIndividual = "";
$groupWarningReportsCluster = "";
$badgeWarningReportsCluster = "";
$groupWarningSchedules = "";
$badgeWarningSchedules = "";

// Connect to server and select databse.
$con=mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
mysql_select_db("$db_name")or die("cannot select DB");

$sql="SELECT MAX(scheduleid) AS scheduleid FROM schedule";
$result=mysql_query($sql);
$row=mysql_fetch_array($result);
$scheduleid = $row['scheduleid'];
if (is_null($scheduleid))
	$scheduleid = 0;
mysql_free_result($result);
?>
<!DOCTYPE HTML>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
    	<meta name="viewport" content="width=device-width, initial-scale=1">
    	
    	<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">

		<!-- Optional theme -->
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">
		
		<!-- nav-bar with footer-->
		<link href="./css/schedule.css" rel="stylesheet">
	<title>View Schedule | iLaw</title>
	<style>
		html, body, #map-canvas {
        	height: 100%;
			margin: 0px;
			padding: 0px
		}
		#map-canvas {
			height: 100%;
			position: absolute; 
			top: 0; 
			bottom: -200px; 
			left: 0; 
			right: 0; 
			z-index: 0;
		}
		#float {
			z-index: 100;
			float: right;
			padding-top: 70px;
			padding-right: 20px;
			width: 25%;
		}
		#content {
			z-index: 100;
			float: left;
			padding-top: 70px;
			padding-left: 10px;
			width: 73%;
		}
		
    </style>
	<script src="//code.jquery.com/jquery-latest.js"></script>
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
	<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
	<script src="./js/jquery.ui.touch-punch.min.js"></script>
	<script>
		$(function () {
				var showPopover = function () {
					$(this).popover('show');
				}
				, hidePopover = function () {
					$(this).popover('hide');
				};
		$('#messages').popover({
					html: 'true',
					title: '<a href="#">See all messages</a>',
					content: '<table class="table table-hover table-condensed"><tr class="warning"><td><span class="glyphicon glyphicon-signal"></span></td><td><small>Mar 2014 Consumption Report</small></td></tr><tr class="warning"><td><span class="glyphicon glyphicon-wrench"></span></td><td><small>Mar 2014 Maintenance Report</small></td></tr><tr><td><span class="glyphicon glyphicon-stats"></span></td><td><small>Feb 28, 2014 Power Reading Stats</small></td></tr><tr><td><span class="glyphicon glyphicon-sort-by-attributes-alt"></span></td><td><small>Feb 2014 Savings Report</small></td></tr></table>',
					trigger: 'click',
					placement: 'auto'
				})
		$('#notifications').popover({
					html: 'true',
					title: '<a href="#">See all notifications</a>',
					content: '<table class="table table-hover table-condensed"><tr class="warning"><td><span class="glyphicon glyphicon-certificate"></span></td><td><small>New light added</small></td></tr><tr><td><span class="glyphicon glyphicon-warning-sign"></span></td><td><small>Pilferage detected</small></td></tr><tr><td><span class="glyphicon glyphicon-wrench"></span></td><td><small>Repair needed</small></td></tr><tr><td><span class="glyphicon glyphicon-flash"></span></td><td><small>Power surge detected</small></td></tr><tr><td><span class="glyphicon glyphicon-remove"></span></td><td><small>Light can not be reached</small></td></tr><tr><td><span class="glyphicon glyphicon-time"></span></td><td><small>Light not responding</small></td></tr></table>',
					trigger: 'click',
					placement: 'auto'
				})
		$('#messages').on('show.bs.popover', function () {
				  $('#notifications').popover('hide')
				})
		$('#notifications').on('show.bs.popover', function () {
				  $('#messages').popover('hide')
				})
		$('#settings').on('show.bs.dropdown', function () {
				  $('#notifications').popover('hide');
				  $('#messages').popover('hide');
				})
		$('#account').on('show.bs.dropdown', function () {
				  $('#notifications').popover('hide');
				  $('#messages').popover('hide');
				})
		});
	</script>
	<!--
	<link href='./fullcalendar/fullcalendar.css' rel='stylesheet' />
	<link href='./fullcalendar/fullcalendar.print.css' rel='stylesheet' media='print' />
	<script src='./lib/jquery.min.js'></script>
	<script src='./lib/jquery-ui.custom.min.js'></script>
	<script src='./fullcalendar/fullcalendar.min.js'></script>
	-->
	<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
	<script type="text/javascript" src="./js/jquery.timepicker.js"></script>
	<link rel="stylesheet" type="text/css" href="./css/jquery.timepicker.css" />	
	<style>
	
		#wrap {
			width: 100%;
			height: 100%;
			margin: 0 auto;
			}
	
		#external-events {
			float: left;
			width: 100%;
			padding: 0 10px;
			border: 1px solid #ccc;
			background: #eee;
			text-align: left;
			}
	
		#external-events h4 {
			font-size: 16px;
			margin-top: 0;
			padding-top: 1em;
			}
	
		.external-event { /* try to mimick the look of a real event */
			margin: 10px 0;
			padding: 2px 4px;
			background: #FF9900;
			color: #fff;
			font-size: .85em;
			cursor: pointer;
			}
	
		#external-events p {
			margin: 1.5em 0;
			font-size: 11px;
			color: #666;
			}
	
		#external-events p input {
			margin: 0;
			vertical-align: middle;
			}

		#calendar {
			float: left;
			width: 100%;
			height: 100%;
			}

	</style>
	<script>
		function changeView(){

		}
	</script>
	</head>
	<body onload="changeView()">
<?php
include './header.php';
?>
<div class="modal fade bs-example-modal-sm" id="myModal" tabindex="-1" role="dialog" arial-labelledby="myModalLabel" arial-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" arial-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Options</h4>
			</div>
			<div class="modal-body">
				<p>Modify the brightness or delete the event?</p>
			</div>
			<div class="modal-footer">
				<button type="button" id="edit" class="btn btn-default">Edit Brightness</button>
				<button type="button" id="delete" class="btn btn-warning">Delete Event</button>
			</div>
		</div>
	</div>
</div>
<?php
include './rightnavigationbar.php';
?>
<div id="wrapContent">
	<div id="content">
		<div class="container-fluid">
			<div id='wrap'>
			  <h3>Power Consumption (PC) Values:</h3>
			  <div class="input-group">
			  	<span class="input-group-addon" id="basic-addon1">Target Consumption</span>
			    <input id="targetPCid" type="text" class="form-control" placeholder="Enter your target Power Consumption" aria-label="Watts">
			    <span class="input-group-addon">Watts</span>
			  </div>

			  <div class="input-group">
			  	<span class="input-group-addon" id="basic-addon1">Current Consumption</span>
			    <input id="currentPCid" type="text" class="form-control" placeholder="Enter your Current Power Consumption" aria-label="Watts">
			    <span class="input-group-addon">Watts</span>
			  </div>

			  <div class="input-group">
			  	<span class="input-group-addon" id="basic-addon1">Predicted Consumption</span>
			    <input id="predictedPCid" type="text" class="form-control" placeholder="Enter your Predicted Power Consumption" aria-label="Watts">
			    <span class="input-group-addon">Watts</span>
			  </div>

			  <h2>Power Consumption for <?php echo date("M Y") ?></h2>
			  <a href="#" data-toggle="tooltip" title="Enabling the Auto Light Intesity Adjustment will give the user less worries in making sure that the power consumption for the month will not exceed the target consumption. This feature recalculates how much reduction in light intensity should be executed should the Predicted Consumption exceed the Target Consumption. Predicted Consumption is calculated based on the cluster schedules set by the user.">
			  	<label class="checkbox-inline">
			  		<input type="checkbox" value="">Enable Auto Light Intensity Adjustment (ALIA) [to be implemented]
			  	</label>
			  </a>
			  <Br><Br>
			  <div class="progress">
			    <div id="consumptionTarget" class="progress-bar progress-bar-warning" role="progressbar" style="width:40%">
			    </div>
			  </div>
			  <div class="progress">
			    <div id="consumptionCurrent" class="progress-bar progress-bar-info" role="progressbar" style="width:50%">
			    </div>
			  </div>
			  <div class="progress">
			    <div id="consumptionPredicted" class="progress-bar progress-bar-success" role="progressbar" style="width:60%">
			    </div>
			  </div>
			 
			  <div class="jumbotron">
			    <a href="">
			      <h2 id="intensityID"></h2> 
			    </a>
			  </div>  
			</div>
		</div>
		<div id="push" class="container"><h1>&nbsp;</h1></div>
	</div>
</div>

<div id="footer" class="footer navbar-fixed-bottom">
      <div class="container-fluid">
        <p class="text-muted">&copy; 2014 Solatronics <small class="pull-right"><a href="#">about</a> &#8226; <a href="#">contact</a> &#8226; <a href="#">help</a></small></p>
      </div>
</div>

</body>
<?php
	$date = new DateTime();
	$RealPowerReadingArray = array();
	$interval = new DateInterval('P1D');
	
	$dateString = date_format($date, 'Y-m-d');
	$yearString = date_format($date, 'Y');
	$monthString = date_format($date, 'm');
	$dayString = date_format($date, 'd');	
	
	$MonthlyAveragePower;
	for ($i=0; $i<12; $i++) {
		$MonthlyAveragePower[$i] = 0;
	}

	$sql = "SELECT bulbid FROM bulb;";

	$result=mysql_query($sql);

	$bulbCtr = 0;
	$tempBulbAll;
	while($row = mysql_fetch_array($result)) {
		$tempBulbAll[$bulbCtr]['bulbid'] = $row['bulbid'];
		$bulbCtr++;		
	}	

	foreach ($tempBulbAll as $bulbAll) {
		$sql="SELECT
				Year(timeinterval) as year, 
				Month(timeinterval) - 1 as month, 
				sum(abs(ave_va * ave_pf)) as total_watts
			FROM
				(
				select 
					avg(va) as ave_va, 
					avg(pf) as ave_pf, 
					convert((min(timestamp) div 6000)*6000, datetime) as timeinterval
				from poweranalyzer
				where bulbid = ".$bulbAll['bulbid']." AND 
					timestamp > ".date("Y-m")."
				group by timestamp div 6000
				) as newdb
			GROUP BY
				Year(timeinterval),
				Month(timeinterval);";

		$result=mysql_query($sql);

		while($row = mysql_fetch_array($result)) {
			$tempMonth = $row['month'];

			if ($tempMonth != NULL) {
				$ctr = $row['month'];
				$MonthlyAveragePower[$ctr] += (float) $row['total_watts'];
			}
		}	

		//echo json_encode($MonthlyAveragePower);
	}
?>
<script type="text/javascript">
var pcTarget = 5000;
var pcCurrent = 2000;
var pcPredicted = 4500;
var testConsumption = <?php echo json_encode($MonthlyAveragePower); ?>;

function refreshProgressBars() {
  var percentageTarget = 0,
      percentageCurrent = 0,
      percentagePredicted = 0,
      maxValue = 0,
      ratioPredOverTarg = 0,
      intensityReduction = 0;

  //Predicted will always be greater than Current because
  //predicted = current + predicted consumption * remaining hours

  //Predicted should be approximately equal to Target
  if (parseInt(pcTarget) >= parseInt(pcPredicted)) {
    maxValue = pcTarget;
    $("#consumptionPredicted").attr("class", "progress-bar progress-bar-success");
    $("#consumptionPredicted").text("Predicted Consumption: " + pcPredicted + " Watts");
    $(".jumbotron").hide();
  }
  else {
    maxValue = pcPredicted;
    $("#consumptionPredicted").attr("class", "progress-bar progress-bar-danger");
    $("#consumptionPredicted").text("Predicted Consumption: " + pcPredicted + " Watts (already Exceeds Target!)");

    ratioPredOverTarg = (((pcPredicted/pcTarget)-1)*100);

    //Intensity reduction is 5% more of calculated for the sake of margins
    intensityReduction = (1 - ((pcTarget-pcCurrent)/(pcPredicted-pcCurrent))) * 100;

    $("#intensityID").text("Predicted Consumption is " + parseFloat(ratioPredOverTarg.toFixed(2)) + 
    	"% more of Target Consumption. Click this to reduce the intensity of lights by " + 
    	parseFloat(intensityReduction.toFixed(2)) + "% and conform with the Target Consumption.");
    $(".jumbotron").show();
  };

  if (parseInt(pcTarget) < parseInt(pcCurrent)) {
    $("#consumptionTarget").attr("class", "progress-bar progress-bar-danger");
    $("#consumptionTarget").text("Target Consumption: " + pcTarget + " Watts (impossible)");

    $("#intensityID").text("Not Possible to set Target Consumption to Less than your Current Consumption.");
    $(".jumbotron").show();
  }
  else {
    $("#consumptionTarget").attr("class", "progress-bar progress-bar-warning");
    $("#consumptionTarget").text("Target Consumption: " + pcTarget + " Watts");
  };

  percentageTarget = (pcTarget / maxValue) * 100;
  percentageCurrent = (pcCurrent / maxValue) * 100;
  percentagePredicted = (pcPredicted / maxValue) * 100;

  //Texts
  //$("#consumptionTarget").text("Target Consumption: " + pcTarget + " Watts");
  $("#consumptionCurrent").text("Current Consumption: " + pcCurrent + " Watts");

  //Widths
  $("#consumptionTarget").css("width", percentageTarget + "%");
  $("#consumptionCurrent").css("width", percentageCurrent + "%");
  $("#consumptionPredicted").css("width", percentagePredicted + "%");
}

function getTargetPowerConsumption() {
  var tempPC = $("#targetPCid").val();

  if (tempPC == "") {
  	return;
  };

  if (tempPC >= 0) {
    pcTarget = tempPC;
  }
}
$("#targetPCid").keypress(function(e) {
    if(e.which == 13) {
      getTargetPowerConsumption();
      refreshProgressBars();
    }
});
$("#targetPCid").blur(function(){
  getTargetPowerConsumption();
  refreshProgressBars();
});

function getCurrentPowerConsumption() {
  var tempPC = $("#currentPCid").val();

  if (tempPC == "") {
  	return;
  };

  if (tempPC >= 0) {
    pcCurrent = tempPC;
  }
}
$("#currentPCid").keypress(function(e) {
    if(e.which == 13) {
      getCurrentPowerConsumption();
      refreshProgressBars();
    }
});
$("#currentPCid").blur(function(){
  getCurrentPowerConsumption();
  refreshProgressBars();
});

function getPredictedPowerConsumption() {
  var tempPC = $("#predictedPCid").val();

  if (tempPC == "") {
  	return;
  };

  if (tempPC >= 0) {
    pcPredicted = tempPC;
  }
}
$("#predictedPCid").keypress(function(e) {
    if(e.which == 13) {
      getPredictedPowerConsumption();
      refreshProgressBars();
    }
});
$("#predictedPCid").blur(function(){
  getPredictedPowerConsumption();
  refreshProgressBars();
});

$(document).ready(function(){
  $(".jumbotron").hide();
  refreshProgressBars();
});
</script>

</html>