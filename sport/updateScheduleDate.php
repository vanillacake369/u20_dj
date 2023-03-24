<?php
session_start();

require_once __DIR__ . "/../database/dbconnect.php";
global $db;
$id = $_GET['scheduleid'];
$start = $_POST['scheduleStart'];
$finish = $_POST['scheduleFinish'];


$sql = " UPDATE list_schedule SET schedule_start='" . $start . "', schedule_finish='" . $finish . "'  WHERE schedule_id= '" . $id . "';";
$row = $db->query($sql);
echo "<script>alert('경기 일정 수정되었습니다.'); location.href='../sport.php';</script>";
exit;
