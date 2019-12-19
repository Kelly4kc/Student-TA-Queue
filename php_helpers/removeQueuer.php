<?php
if (!isset($_SESSION)) {
	session_start();
}
if (isset($_GET['queueId'])) {
	include_once("Database.php");
	include_once("loginStatus.php");
	$con = Database::open();
	
	$id = $_GET['queueId'];
	
	$query = 'DELETE FROM queue
		      WHERE id = $1';
	pg_prepare($con, "", $query);
	pg_execute($con, "", array($id))
	or die("Query failed: " . pg_last_error());
	pg_close($con);
	$_POST = array();
	header("Location:../student.php");
}