<?php
session_start();
if (!isset($_SESSION['loggedin'])||($_SESSION['loggedin']==false))
	header("location:./");

if (isset($_SESSION['user'])) {
	//echo "username: " . $_SESSION['user'];
} else {
	echo "Not logged in...";
	return;
}

$host="localhost"; // Host name 
$username="pi"; // Mysql username 
$password="raspberry"; // Mysql password 
$db_name="ilaw"; // Database name 

// Connect to server and select databse.
$con=mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
mysql_select_db("$db_name")or die("cannot select DB");

if (isset($_GET['get'])) {
	$sql="SELECT * FROM settings WHERE username='" . strtolower($_SESSION['user']) . "';";

	$result=mysql_query($sql, $con);
	$row=mysql_fetch_array($result);
		$latestSettings['targetmonthlyconsumption'] = $row['targetmonthlyconsumption'];
		$latestSettings['en_alia'] = $row['en_alia'];
	mysql_free_result($result);
	mysql_close($con);
	echo json_encode($latestSettings);
}
else if (isset($_GET['update']) && isset($_GET['en_alia'])) {
	$en_alia = $_GET['en_alia'];
	echo "en_alia: $en_alia";

	$sql="UPDATE settings 
		  SET en_alia=$en_alia
		  WHERE username='" . strtolower($_SESSION['user']) . "';";

	$result=mysql_query($sql, $con);
	mysql_close($con);
}
else if (isset($_GET['update']) && isset($_GET['targetmonthlyconsumption'])) {
	$targetmonthlyconsumption = $_GET['targetmonthlyconsumption'];
	echo "targetmonthlyconsumption: $targetmonthlyconsumption";

	$sql="UPDATE settings 
		  SET targetmonthlyconsumption=$targetmonthlyconsumption
		  WHERE username='" . strtolower($_SESSION['user']) . "';";

	$result=mysql_query($sql, $con);
	mysql_close($con);
}

?>