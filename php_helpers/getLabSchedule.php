<?php
if(!isset($_SESSION)) 
{ 
    session_start(); 
}
include_once("Database.php");
include_once("loginStatus.php");
$con = Database::open();
$schSql = 'SELECT day, min(start_time), max(end_time) FROM schedule GROUP BY day;';
pg_prepare($con, "", $schSql);
$schRs = pg_execute($con, "", [])
    or die("Query failed: " . pg_last_error());
$semSql = 'SELECT * FROM semester WHERE now() > start_date AND now() < end_date';
pg_prepare($con, "", $semSql);
$semRs = pg_execute($con, "", [])
    or die("Query failed: " . pg_last_error());


$curSem = pg_fetch_row($semRs);

$res = array();
while($row = pg_fetch_row($schRs)){
    $begin  = strtotime($curSem[2]);
    $endOfSem = strtotime($curSem[3]);
    while ($begin < $endOfSem) // Loop will work begin to the end date
    {
        $start = gmdate("Y-m-d H:i",  strtotime($row[0], $begin) + $row[1] * 60 * 60);
        $end = gmdate("Y-m-d H:i", strtotime($row[0], $begin) + $row[2] * 60 * 60);
        if (strtotime($start) > $endOfSem) {
        	break;
        }
        $arr = array(
            "start" => $start,
            "end" => $end,
            "title" => "Lab hours",
            "class" => 'lab'
        );
        $res[] = $arr;

        $begin = strtotime('+7 day', $begin);
    }

}
// get assignments
$assignmentSql = 'SELECT due_date, assignment_name, assignment_link, subject, number 
from assignment 
join course on course_id = course.id';
pg_prepare($con, "", $assignmentSql);
$assRs = pg_execute($con, "", [])
    or die("Query failed: " . pg_last_error());

while ($row = pg_fetch_row($assRs)) {

    $start = gmdate("Y-m-d",  strtotime($row[0]));
    $end = gmdate("Y-m-d", strtotime($row[0]));
    $arr = array(
        "start" => $start,
        "end" => $end,
        "title" => $row[3] . " " . $row[4] . " - " . $row[1],
        "class" => 'assignment',
        "allDay" => true,
        "content" => '<a target="_blank" href=\'' . $row[2] . '\'> link to assignment </a>'
    );
    $res[] = $arr;
}
echo json_encode($res);
pg_close($con);
