<?php
if (!isset($_SESSION)) {
    session_start();
}
include_once("Database.php");
include_once("loginStatus.php");
$con = Database::open();

// get regular TA schedules
$schSql = 'SELECT day, start_time, end_time, first_name, last_name as title from schedule
    		join person on person.id = schedule.ta_id';
pg_prepare($con, "", $schSql);
$schRs = pg_execute($con, "", [])
    or die("Query failed: " . pg_last_error());
$semSql = 'SELECT * FROM semester WHERE now() > start_date AND now() < end_date';
pg_prepare($con, "", $semSql);
$semRs = pg_execute($con, "", [])
    or die("Query failed: " . pg_last_error());
$curSem = pg_fetch_row($semRs);

$res = array();
while ($row = pg_fetch_row($schRs)) {
    $begin  = strtotime($curSem[2]);
    $endOfSem = strtotime($curSem[3]);
    while ($begin < $endOfSem) // Loop will work begin to the end date 
    {
        $start = gmdate("Y-m-d H:i",  strtotime($row[0], $begin) + ($row[1] + 1) * 60 * 60);
        $end = gmdate("Y-m-d H:i", strtotime($row[0], $begin) + ($row[2] + 1) * 60 * 60);
        $arr = array(
            "start" => $start,
            "end" => $end,
            "title" => $row[3] . " " . $row[4],
            "class" => 'lab'
        );
        $res[] = $arr;

        $begin = strtotime('+7 day', $begin);
    }
}

// get all of the assigned covers for the week
$assignedCoverSql = 'SELECT DISTINCT date, start_time, end_time, ta.first_name as ta_fname,
cover_person.first_name as cover_name, ta.last_name as ta_lname, cover_person.last_name
from cover 
join person as cover_person on cover.cover_ta_id = cover_person.id 
join (
  select first_name, person.id as ta_id, last_name
  from person 
  join cover on unavailable_ta_id = person.id
) as ta on ta.ta_id = cover.unavailable_ta_id
where cover_ta_id is not null
';
pg_prepare($con, "", $assignedCoverSql);
$coverRs = pg_execute($con, "", [])
    or die("Query failed: " . pg_last_error());

while ($row = pg_fetch_row($coverRs)) {

    $start = gmdate("Y-m-d H:i",  strtotime($row[0]) + ($row[1] + 1) * 60 * 60);
    $end = gmdate("Y-m-d H:i", strtotime($row[0]) + ($row[2] + 1) * 60 * 60);
    $arr = array(
        "start" => $start,
        "end" => $end,
        "title" => $row[4] ." " . $row[6],
        "class" => 'cover',
        "content" => 'Cover for ' . $row[3] . " " . $row[5]
    );
    $res[] = $arr;
    $old_ta = array(
        "start" => $start,
        "end" => $end,
        "title" => $row[3] . " " . $row[5],
        "class" => 'lab',

    );
    if (($key = array_search($old_ta, $res)) !== false) {
        array_splice($res, $key, 1);
    }
}

// get all unassigned covers
$unassignedCoverSql = "SELECT DISTINCT date, start_time, end_time, person.first_name, person.last_name from cover
join person on unavailable_ta_id = person.id where cover_ta_id is null";
pg_prepare($con, "", $unassignedCoverSql);
$coverRs = pg_execute($con, "", [])
    or die("Query failed: " . pg_last_error());

while ($row = pg_fetch_row($coverRs)) {

    $start = gmdate("Y-m-d H:i",  strtotime($row[0]) + ($row[1] + 1) * 60 * 60);
    $end = gmdate("Y-m-d H:i", strtotime($row[0]) + ($row[2] + 1) * 60 * 60);
    $arr = array(
        "start" => $start,
        "end" => $end,
        "class" => 'unassigned-cover',
        "content" => 'Cover request for ' . $row[3] . " " . $row[4]
    );
    $res[] = $arr;
    $old_ta = array(
        "start" => $start,
        "end" => $end,
        "title" => $row[3] . " " . $row[4],
        "class" => 'lab',

    );
    if (($key = array_search($old_ta, $res)) !== false) {
        array_splice($res, $key, 1);
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
        "allDay"=> true,
        "content"=> '<a target="_blank" href=\'' . $row[2] . '\'> link to assignment </a>'
    );
    $res[] = $arr;
}
// echo things out
echo json_encode($res);

pg_close($con);
