<?php
if(!isset($_SESSION)) 
{ 
    session_start(); 
} 
if (isset($_GET['tacoverId']) && isset($_GET['coverId'])) {
    include_once("Database.php");
    include_once("loginStatus.php");
    $con = Database::open();
    
    $id = $_GET['tacoverId'];
    $coverid = $_GET['coverId'];
    echo $id;
    echo $coverid;

    $sqlUpdate = 'UPDATE cover SET cover_ta_id = $1 WHERE id=$2;';
    pg_prepare($con, "", $sqlUpdate);
    $rs = pg_execute($con, "", array($id, $coverid))
        or die("Query failed: " . pg_last_error());
        
    
    $_POST = array();
    header("Location:../calendar.php");
}
