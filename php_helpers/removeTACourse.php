<?php
if(!isset($_SESSION)) 
{ 
    session_start(); 
} 
include_once("Database.php");
include_once("loginStatus.php");
$con = Database::open();

$dataString = file_get_contents('php://input');
$data = json_decode($dataString);

$sql = 'DELETE FROM ta_course_experience WHERE ta_id=$1 AND course_id=$2;';
pg_prepare($con, "", $sql);
$rs = pg_execute($con, "", array($_POST["ta_id"], $_POST["course_id"]))
    or die("Query failed: " . pg_last_error());
pg_close($con);
$_POST = array();