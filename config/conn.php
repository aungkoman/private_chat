<?php
/* local development database server */
$servername = $_SERVER['HTTP_DB_HOST'];
$username = $_SERVER['HTTP_DB_USERNAME'];
$password = $_SERVER['HTTP_DB_PASSWORD'];
$db_name = $_SERVER['HTTP_DB_DBNAME'];
$conn = new mysqli($servername, $username, $password, $db_name);
if($conn->connect_error){
	//echo "error in connection ". $conn->connect_error;
}
else {
	//echo "<br> Database Connected to Medical Checkup Database  ";
}
mysqli_set_charset($conn,"utf8");
?>
