<?php
if(!isset($_SESSION)) 
{ 
    session_start(); 
} 
if (isset($_GET['assignmentId'])) {
    include_once("Database.php");
    include_once("loginStatus.php");
    $con = Database::open();
    
    $id = $_GET['assignmentId'];

    $sqlUpdate = 'UPDATE question SET assignment_id = NULL WHERE assignment_id=$1;';
    pg_prepare($con, "", $sqlUpdate);
    $rs = pg_execute($con, "", array($id))
        or die("Query failed: " . pg_last_error());
        
    $sql = 'DELETE FROM assignment WHERE id=$1;';
    pg_prepare($con, "", $sql);
    $rs = pg_execute($con, "", array($id))
        or die("Query failed: " . pg_last_error());
    pg_close($con);
    $_POST = array();
    header("Location:../assignments.php");
}
