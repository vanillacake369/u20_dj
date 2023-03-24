<?php
session_start();

require_once __DIR__ . "/../database/dbconnect.php";
global $db;


$code = $_POST['countryCode'];

$name = $_POST['countryName'];
$namekr = $_POST['countryNameKr'];

$sql = "SELECT * from list_country where country_code='" . $code . "';";
$key = $db->query($sql);

if (!mysqli_fetch_array($key)) {

    $sql = " INSERT into list_country (country_code, country_name, country_name_kr)  values ('" . $code . "','" . $name . "','" . $namekr . "');";
    $row = $db->query($sql);
    echo "<script>alert('국가 생성되었습니다.'); location.href='../sport.php';</script>";
    exit;
} else {
    echo "<script>alert('해당 국가는 이미 존재합니다.'); history.back();</script>";
}
