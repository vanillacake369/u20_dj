<?php
session_start();

require_once __DIR__ . "/../database/dbconnect.php";
global $db;
$id = $_GET['scheduleid'];
$type = $_POST['scheduleType'];
$name = $_POST['scheduleName'];
$gender = $_POST['scheduleGender'];
$round = $_POST['scheduleRound'];
$location = $_POST['scheduleLocation'];
$start = $_POST['scheduleStart'];
$finish = $_POST['scheduleFinish'];
$status = $_POST['scheduleStatus'];
$date = $_POST['scheduleDate'];

$sql = "SELECT * from list_sports where sports_code='" . $type . "';";
$key = $db->query($sql);

if (mysqli_fetch_array($key)) {

    $sql = " UPDATE list_schedule SET schedule_sports='" . $type . "', schedule_name='" . $name . "', schedule_gender='" . $gender . "', schedule_round='" . $round . "', schedule_location='" . $location . "', schedule_start='" . $start . "', schedule_finish='" . $finish . "', schedule_status='" . $status . "', schedule_date='" . $date . "'  WHERE schedule_id= '" . $id . "';";
    $row = $db->query($sql);
    echo "<script>alert('경기 수정되었습니다.'); location.href='../sport.php';</script>";
    exit;
} else {
    echo "<script>alert('해당 경기 종목은 존재하지 않습니다.'); history.back();</script>";
}
