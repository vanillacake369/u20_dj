<?php
session_start();

require_once __DIR__ . "/../database/dbconnect.php";
global $db;


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

    $sql = " INSERT into list_schedule (schedule_sports, schedule_name,schedule_gender,schedule_round,schedule_location,schedule_start,schedule_finish,schedule_status,schedule_date)  values ('" . $type . "','" . $name . "','" . $gender . "','" . $round . "','" . $location . "','" . $start . "','" . $finish . "','" . $status . "','" . $date . "');";
    $row = $db->query($sql);
    echo "<script>alert('경기 생성되었습니다.'); location.href='../sport.php';</script>";
    exit;
} else {
    echo "<script>alert('해당 경기 종목은 존재하지 않습니다.'); history.back();</script>";
}
