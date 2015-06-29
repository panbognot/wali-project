<?php
session_start();
if (!isset($_SESSION['loggedin'])||($_SESSION['loggedin']==false))
	header("location:./");

$host="localhost"; // Host name 
$username="pi"; // Mysql username 
$password="raspberry"; // Mysql password 
$db_name="ilaw"; // Database name 
$tbl_name="bulb"; // Table name 

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
	<?php
		$sql="SELECT a.name, b.clusterid, c.scheduleid, c.start_time, c.end_time, c.brightness, c.start_date, c.end_date FROM cluster a, sched_cluster b, schedule c  WHERE b.clusterid=a.clusterid AND c.scheduleid=b.scheduleid";
		$result=mysql_query($sql);
		$countSchedules = 0;
		while($row=mysql_fetch_array($result))
		{
			$schedulesArray[$countSchedules]['scheduleid'] = $row['scheduleid'];
			$schedulesArray[$countSchedules]['clusterid'] = $row['clusterid'];
			$schedulesArray[$countSchedules]['name'] = $row['name'];
			$schedulesArray[$countSchedules]['brightness'] = $row['brightness'];
			$schedulesArray[$countSchedules]['start_time'] = $row['start_time'];
			$schedulesArray[$countSchedules]['end_time'] = $row['end_time'];
			$schedulesArray[$countSchedules]['start_date'] = $row['start_date'];
			$schedulesArray[$countSchedules]['end_date'] = $row['end_date'];
			$countSchedules++;
		}
		mysql_free_result($result);
		$events = "";
		for ($i=0; $i < $countSchedules; $i++){
			$dateNow = date("Y-m-d");
			if($schedulesArray[$i]['end_date'] >= $dateNow)
				$color = "#FF9900";
			else
				$color = "#CECECE";
			$event = "{ id:'".$schedulesArray[$i]['scheduleid']."', title:'".$schedulesArray[$i]['name']." \\nBrightness: ".$schedulesArray[$i]['brightness']."', allDay: false, start:'".$schedulesArray[$i]['start_date']."T".$schedulesArray[$i]['start_time']."Z', end:'".$schedulesArray[$i]['end_date']."T".$schedulesArray[$i]['end_time']."Z', url:'./schedule.php?clusterid=".$schedulesArray[$i]['clusterid']."&scheduleid=".$schedulesArray[$i]['scheduleid']."', editable: false, color:'".$color."'}";
			if ($i != ($countSchedules - 1))
				$event = $event.",";
			$events = $events . $event;
		}
	?>
	<script>
		var scheduleid = <?php echo $scheduleid; ?>;
		var eventsArray = [<?php echo $events;?>];

		/*
		$(document).ready(function() {

			//initialize the external events

			$('#external-events div.external-event').each(function() {
	
				// create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
				// it doesn't need to have a start or end
				var eventObject = {
					title: $.trim($(this).text()) // use the element's text as the event title
				};
		
				// store the Event Object in the DOM element so we can get to it later
				$(this).data('eventObject', eventObject);
		
				// make the event draggable using jQuery UI
				$(this).draggable({
					zIndex: 999,
					revert: true,      // will cause the event to go back to its
					revertDuration: 0  //  original position after the drag
				});
		
			});


			//initialize the calendar
	
			$('#calendar').fullCalendar({
				header: {
					left: 'prev,next today',
					center: 'title',
					right: 'month,agendaWeek,agendaDay'
				},
				defaultView: 'agendaDay',
				editable: false,
				droppable: false, 
				events: eventsArray
			});
	
	
		});
		*/

	</script>
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
	<?php
		/*
		$sql="SELECT start_date FROM schedule WHERE scheduleid=".$_GET['scheduleid'];
		$result=mysql_query($sql);
		$row=mysql_fetch_array($result);
			$start_date = $row['start_date'];
		mysql_free_result($result);
		$year = date('Y', strtotime($start_date));
		$month = date('n', strtotime($start_date));
		$day = date('j', strtotime($start_date));
		*/
	?>
	<script>
		function changeView(){
			/*
			var year = <?php echo $year;?>;
			var month = <?php echo $month;?>;
			month = month - 1;
			var day = <?php echo $day;?>;
			$('#calendar').fullCalendar('gotoDate', year, month, day);
			*/
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
<div id="float">
		<ul class="list-group list-unstyled">
			<li id="maps" class="dropdown">
			<?php
				$sql="SELECT clusterid, name FROM cluster";
				$result=mysql_query($sql);
				$clustersArray = array();
				$countClusters = 0;
				while($row=mysql_fetch_array($result))
				{
					$clustersArray[$countClusters]['clusterid'] = $row['clusterid'];
					$clustersArray[$countClusters]['name'] = $row['name'];
					$countClusters++;
				}
				mysql_free_result($result);
			?>
				<a href="#" class="list-group-item list-group-item-warning dropdown-toggle" data-toggle="dropdown">
				  <span class="glyphicon glyphicon-map-marker"></span>
				  Maps
				  <span class="badge pull-right badge-warning"><?php echo $countClusters; ?></span>
				</a>
				<ul class="dropdown-menu">
					<li role="presentation" class="dropdown-header">Map Clusters</li>
					<?php
						for($i = 0; $i < $countClusters; $i++) {
							echo "<li role=\"presentation\"><a role=\"menuitem\" tabindex=\"-1\" href=\"./cluster.php?clusterid=".$clustersArray[$i]['clusterid']."\">".$clustersArray[$i]['name']."</a></li>";
						}
					?>
					<li role="presentation" class="divider"></li>
					<li role="presentation"><a role="menuitem" tabindex="-1" href="./addcluster.php">Add a Cluster</a></li>
			  </ul>
			</li>
			<li id="lights" class="dropdown">
			<?php
				$sql="SELECT bulbid, name FROM bulb";
				$result=mysql_query($sql);
				$bulbsArray = array();
				$countBulbs = 0;
				while($row=mysql_fetch_array($result))
				{
					$bulbsArray[$countBulbs]['bulbid'] = $row['bulbid'];
					$bulbsArray[$countBulbs]['name'] = $row['name'];
					$countBulbs++;
				}
				mysql_free_result($result);
			?>
				<a href="#" class="list-group-item dropdown-toggle" data-toggle="dropdown">
				  <span class="glyphicon glyphicon-adjust"></span>
				  Lights
				  <span class="badge pull-right"><?php echo $countBulbs; ?></span>
				</a>
				<ul class="dropdown-menu">
					<li role="presentation" class="dropdown-header">Light Bulbs</li>
					<?php
						for($i = 0; $i < $countBulbs; $i++) {
							echo "<li role=\"presentation\"><a role=\"menuitem\" tabindex=\"-1\" href=\"./view.php?bulbid=".$bulbsArray[$i]['bulbid']."\">".$bulbsArray[$i]['name']."</a></li>";
						}
					?>
					<li role="presentation" class="divider"></li>
					<li role="presentation"><a role="menuitem" tabindex="-1" href="./addlight.php">Add a Light</a></li>
			  </ul>
			</li>
			<li id="readings" class="dropdown">
			<?php
				$sql="SELECT bulbid, name FROM bulb WHERE bulbid IN (SELECT DISTINCT bulbid FROM poweranalyzer ORDER BY bulbid)";
				$result=mysql_query($sql);
				$readingsArray = array();
				$countReadings = 0;
				while($row=mysql_fetch_array($result))
				{
					$readingsArray[$countReadings]['bulbid'] = $row['bulbid'];
					$readingsArray[$countReadings]['name'] = $row['name'];
					$countReadings++;
				}
				mysql_free_result($result);
			?>
				<a href="#" class="list-group-item dropdown-toggle" data-toggle="dropdown">
				  <span class="glyphicon glyphicon-signal"></span>
				  Reports
				  <span class="badge pull-right"><?php echo $countReadings; ?></span>
				</a>
				<ul class="dropdown-menu">
					<li role="presentation" class="dropdown-header">Consumption Reports</li>
					<?php
						for($i = 0; $i < $countReadings; $i++) {
							echo "<li role=\"presentation\"><a role=\"menuitem\" tabindex=\"-1\" href=\"./readings.php?bulbid=".$readingsArray[$i]['bulbid']."\">".$readingsArray[$i]['name']."</a></li>";
						}
					?>
					<li role="presentation" class="divider"></li>
					<li role="presentation"><a role="menuitem" tabindex="-1" href="#">Customize a Report</a></li>
			  </ul>
			</li>
			<li id="schedules" class="dropdown">
			<?php
				$sql="SELECT scheduleid, start_date, end_date, start_time, end_time FROM schedule";
				$result=mysql_query($sql);
				$schedulesArray = array();
				$countSchedules = 0;
				while($row=mysql_fetch_array($result))
				{
					$schedulesArray[$countSchedules]['scheduleid'] = $row['scheduleid'];
					$schedulesArray[$countSchedules]['start_date'] = $row['start_date'];
					$schedulesArray[$countSchedules]['start_time'] = $row['start_time'];
					$schedulesArray[$countSchedules]['end_date'] = $row['end_date'];
					$schedulesArray[$countSchedules]['end_time'] = $row['end_time'];
					$countSchedules++;
				}
				mysql_free_result($result);
			?>
				<a href="#" class="list-group-item dropdown-toggle" data-toggle="dropdown">
				  <span class="glyphicon glyphicon-calendar"></span>
				  Schedules
				  <span class="badge pull-right"><?php echo $countSchedules; ?></span>
				</a>
				<ul class="dropdown-menu">
					<li role="presentation" class="dropdown-header">Events</li>
					<?php
						$dateNow = date("Y-m-d");
						for($i = 0; $i < $countSchedules; $i++) {
							if ($schedulesArray[$i]['end_date'] > $dateNow)
								echo "<li role=\"presentation\"><a role=\"menuitem\" tabindex=\"-1\" href=\"./viewschedule.php?scheduleid=".$schedulesArray[$i]['scheduleid']."\">On ".$schedulesArray[$i]['start_date']." to ".$schedulesArray[$i]['end_date']." from ".$schedulesArray[$i]['start_time']." to ".$schedulesArray[$i]['end_time']."</a></li>";
						}
					?>
					<li role="presentation" class="divider"></li>
					<li role="presentation"><a role="menuitem" tabindex="-1" href="./addschedule.php">Schedule an Event</a></li>
			  </ul>
			</li>		
		</ul>
	</div>
	
<div id="wrapContent">
	<div id="content">
		<div class="container-fluid">
			<div id='wrap'>
				<!--
				<div id='calendar'></div>
				<div style='clear:both'></div>
				-->
				<?php
					$sql="SELECT name FROM cluster WHERE clusterid=".$_GET['clusterid'];
					$result=mysql_query($sql);
					$row=mysql_fetch_array($result);

						$cluster['name'] = $row['name'];
					
					mysql_free_result($result);
				?>
				<h2><?php echo $cluster['name'];?></h2>       
				<table class="table table-hover">
					<?php
						//Get the schedules from alarm_schedule
						$sql="SELECT 
								alarm_schedule.scheduleid, 
								alarm_schedule.clusterid, 
								alarm_schedule.activate_time, 
								alarm_schedule.brightness, 
								day_of_week.day AS day_of_week, 
								day_of_week.dow_id 
							FROM 
								alarm_schedule 
							INNER JOIN 
								day_of_week 
							ON 
								alarm_schedule.day_of_week=day_of_week.dow_id 
							WHERE 
								alarm_schedule.clusterid=".$_GET['clusterid'].
							" ORDER BY day_of_week.dow_id ASC, alarm_schedule.activate_time ASC";
						$result=mysql_query($sql);
						//$row=mysql_fetch_array($result);

						$countAlarmSched = 0;
						while($row=mysql_fetch_array($result))
						{
							$schedule[$countAlarmSched]['scheduleid'] = $row['scheduleid'];
							$schedule[$countAlarmSched]['activate_time'] = $row['activate_time'];
							$schedule[$countAlarmSched]['day_of_week'] = $row['day_of_week'];
							$schedule[$countAlarmSched]['brightness'] = $row['brightness'];

							$countAlarmSched++;
						}

						mysql_free_result($result);

						$daysofweek = array("ALL","MON","TUE","WED","THU","FRI","SAT","SUN");
					?>				  	
				    <thead>
				      <tr>
				        <th>Day of the Week</th>
				        <th>Activation Time</th>
				        <th>Brightness</th>
				        <th>Action</th>
				      </tr>
				    </thead>
				    <tbody>
				      
						<?php 	
							function createDayOfWeek ($dow) {
								$daysofweekArr = array("ALL","MON","TUE","WED","THU","FRI","SAT","SUN");

								$strDayOfWeek = "<td><select class=\"dayOfWeek form-control\">";

									foreach ($daysofweekArr as $day) {
										$selectStringPre = "<option value=\"$day\"";
										
										if ($dow == $day) {
											$selectStringPre = $selectStringPre . "selected=\"selected\"";
										}

										$selectStringPost = ">$day</option>";
										$selectString = $selectStringPre.$selectStringPost;

										$strDayOfWeek = $strDayOfWeek.$selectString;
									}

								$strDayOfWeek = $strDayOfWeek."</select></td>";

								return $strDayOfWeek;
							}

							foreach ($schedule as $timeslot) {
								$strTsId = $timeslot['scheduleid'];
								$strDayOfWeek = createDayOfWeek($timeslot['day_of_week']);

								$strActivate = "<td><input type=\"text\" class=\"form-control time activateTime\" value=\"".$timeslot['activate_time']."\"></td>";
								$strBrightness = "<td><input type=\"text\" class=\"form-control brightness\" value=\"".$timeslot['brightness']."\"></td>";
								$strActions = "<td><a href=\"#\" onclick=\"updateEntry()\">Update</a>&nbsp|&nbsp<a href=\"#\" onclick=\"deleteEntry()\">Delete</a></td>";
								
								$selectString = $strDayOfWeek.$strActivate.$strBrightness.$strActions."</tr>";
								echo "$selectString";
							}
						?>

				    </tbody>
				</table>
				<a href="#" class="btn btn-warning" onclick="newEntry()"><span class="glyphicon glyphicon-plus"></span> New Entry</a>	

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

<script type="text/javascript">
$(function(){
	$('.activateTime').timepicker({ 'timeFormat': 'H:i:s' });
});

var elems = [
    text( document.createElement("td"), "ALL" ),
    text( document.createElement("td"), "00:00:00" ),
    text( document.createElement("td"), "0" ),
    document.createElement("td")
];

var actions = [
	text( document.createElement("a"), "Add" ),
	document.createTextNode(" | "),
	text( document.createElement("a"), "Cancel" )
];

function text(node, txt){
    node.appendChild( document.createTextNode(txt) );
    return node;
}

function newEntry() {
	var div = document.getElementsByTagName("tbody");
	 
	for ( var i = 0; i < div.length; i++ ) {
		var node = document.createElement("tr");
		div[i].appendChild(node);

		var tdNode, selectNode, optionNode, inputNode, anchorNode;

		// Create Dropdown List for Day of Week
		node.appendChild(tdNode = document.createElement("td"));
		tdNode.appendChild(selectNode = document.createElement("select"));

		selectNode.setAttribute('class', 'dayOfWeek form-control');
		var daysofweeklist = ["ALL","MON","TUE","WED","THU","FRI","SAT","SUN"];

		for (var i = 0; i < daysofweeklist.length; i++) {
			optionNode = document.createElement("option");
			optionNode.value = daysofweeklist[i];
			optionNode.text = daysofweeklist[i];
			selectNode.add(optionNode);    		    
		}

		// Create Input for Activate Time
		node.appendChild(tdNode = document.createElement("td"));
		tdNode.appendChild(inputNode = document.createElement("input"));

		inputNode.setAttribute('type', 'text');
		inputNode.setAttribute('class', 'form-control time activateTime');
		inputNode.setAttribute('value', '00:00:00');

		$('.activateTime').timepicker({ 'timeFormat': 'H:i:s' });

		// Create Input for Brightness
		node.appendChild(tdNode = document.createElement("td"));
		tdNode.appendChild(inputNode = document.createElement("input"));

		inputNode.setAttribute('type', 'text');
		inputNode.setAttribute('class', 'form-control brightness');
		inputNode.setAttribute('value', '0');

		// Create Buttons for Add and Cancel Actions
		node.appendChild(tdNode = document.createElement("td"));
		// Add
		tdNode.appendChild(anchorNode = text( document.createElement("a"), "Add" ));
		anchorNode.setAttribute('href', '#');
		anchorNode.setAttribute('onclick', 'addNewEntry(this)');
		// Separator
		tdNode.appendChild(document.createTextNode(" | "));
		// Cancel
		tdNode.appendChild(anchorNode = text( document.createElement("a"), "Cancel" ));
		anchorNode.setAttribute('href', '#');
		anchorNode.setAttribute('onclick', 'cancelNewEntry(this)');
	}
}

function newEntryOld() {
	var div = document.getElementsByTagName("tbody");
	 
	for ( var i = 0; i < div.length; i++ ) {
		var node = document.createElement("tr");
		div[i].appendChild(node);
	    for ( var e = 0; e < elems.length; e++ ) {
	        node.appendChild( elems[e].cloneNode(true) );

	        if (e == elems.length - 1) {
	        	for ( var j = 0; j < actions.length; j++ ) {
	        		node.lastChild.appendChild( actions[j].cloneNode(true) );

	        		var childElem = node.lastChild.lastChild;
	        		if (childElem.nodeName.toLowerCase() === "a") {
	        			childElem.setAttribute('href', '#');

	        			if (childElem.textContent === "Add") {
	        				childElem.setAttribute('onclick', 'addNewEntry(this)');
	        			}
	        			if (childElem.textContent === "Cancel") {
	        				childElem.setAttribute('onclick', 'cancelNewEntry(this)');
	        			}
	        		}
	        	}
	        };
	    }
	}
}

function loadXMLDoc() {
	var xmlhttp;
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}
	else {// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			document.getElementById("myDiv").innerHTML=xmlhttp.responseText;
		}
	}
	//xmlhttp.open("GET","demo_get2.asp?fname=Henry&lname=Ford",true);
	//xmlhttp.send();

	xmlhttp.open("POST","demo_post2.asp",true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send("fname=Henry&lname=Ford");	
}

var clusterId, activateTime, brightness, dayOfWeekStr;
function addNewEntry (elemid) {
	var parent = elemid.parentNode;
	var rowElem = parent.parentNode.childNodes;
	var kids = parent.childNodes;

	clusterId = <?php echo $_GET['clusterid'] ?>;
	activateTime = rowElem[1].textContent;
	brightness = rowElem[2].textContent;
	dayOfWeekStr = rowElem[0].textContent;

	var xmlhttp;
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}
	else {// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			for (var i = 0; i < kids.length; i++) {
				if (kids[i].textContent === "Add") {
					kids[i].innerHTML = "Update";
					kids[i].setAttribute('onclick', 'updateEntry(this)');
				}
				if (kids[i].textContent === "Cancel") {
					kids[i].innerHTML = "Delete";
					kids[i].setAttribute('onclick', 'deleteEntry(this)');
				}	        
		    }
		}
	}

	xmlhttp.open("POST","processaddschedulealarm.php",true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send("clusterId="+clusterId+"&activateTime="+activateTime+"&brightness="+brightness+"&dayOfWeek="+dayOfWeekStr);	    
}

function cancelNewEntry (elemid) {
	var row = elemid.parentNode.parentNode;
	var tbody = row.parentNode;

	tbody.removeChild(row);
}

function updateEntry () {

}

function deleteEntry () {

}

</script>

</html>