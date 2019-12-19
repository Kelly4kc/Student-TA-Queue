<?php
if(!isset($_SESSION)) 
{ 
    session_start(); 
} 
if (isset($_GET['taId']) && isset($_GET['semesterId']) && isset($_GET['day']) && isset($_GET['startTime']) && isset($_GET['endTime'])) {
    include_once("Database.php");
    include_once("loginStatus.php");
    $con = Database::open();
    $taId = $_GET['taId'];
    $semesterId = $_GET['semesterId'];
    $day =  $_GET['day'];
    $startTime = $_GET['startTime'];
    $endTime = $_GET['endTime'];

    $sql = 'INSERT INTO schedule VALUES($1, $2, $3, $4, $5);';
    pg_prepare($con, "", $sql);
    $rs = pg_execute($con, "", array($taId, $day, $startTime, $endTime, $semesterId))
        or die("Query failed: " . pg_last_error());
    echo 'OI';
    pg_close($con);
    $_POST = array();
    header("Location:../calendar.php");
}
