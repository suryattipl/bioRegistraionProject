<?php



$timezone = "Asia/Calcutta";
/* 	if (function_exists('date_default_timezone_set')) date_default_timezone_set($timezone);
	$t=time();
	$date_now1 = date("Y-m-d",$t);
	if($date_now1 <= '2025-11-08'){
	$db1="AppscCandidateBioReg_08_11_2025";
    $OperatorTable = "operatorassignment_08_11_2025";
	}else if ($date_now1 <= '2025-11-09'){
		$db1="AppscCandidateBioReg_09_11_2025";
    $OperatorTable = "operatorassignment_09_11_2025";

	}

    

$hostname="apssb072025.cz2omaw4y0fe.ap-south-1.rds.amazonaws.com";
$username="dashboard_user";
$password='Q7uK0cYsoFYdY09PZzHbXQk04D6fVkL7dY0';
 
  //connection to the database
$con1 = @mysqli_connect($hostname, $username, $password,$db1,'3316') 
 or die("Unable to connect to MySQL");



$hostname="apssb072025.cz2omaw4y0fe.ap-south-1.rds.amazonaws.com";
$db="Appsc112025Operators";
$username="dashboard_user";
$password='Q7uK0cYsoFYdY09PZzHbXQk04D6fVkL7dY0';
 
  //connection to the database
$con2 = @mysqli_connect($hostname, $username, $password,$db,'3316') 
 or die("Unable to connect to MySQL"); */

$servername = "localhost";  
$username = "root";  
$password = "Ttipl@surya24";
$dbname ="bio_registrations";
$port = 3306; 
$con1 = new mysqli($servername, $username, $password, $dbname,$port);
// Check connection
/* if ($con1->connect_error) {
    die("Connection failed: " . $con1->connect_error);
} 
echo "Connected successfully"; */



?>

