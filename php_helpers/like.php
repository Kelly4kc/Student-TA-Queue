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
echo $data;
echo $_POST["id"];
echo $_POST['likes'];

$sql = 'UPDATE question SET likes = $2 WHERE id=$1; ';
pg_prepare($con, "", $sql);
$rs = pg_execute($con, "", array($_POST["id"], $_POST["likes"]+1))
    or die("Query failed: " . pg_last_error());

pg_close($con);
while($row = pg_fetch_row($rs)){
    echo $row[0];
}
$_POST = array();