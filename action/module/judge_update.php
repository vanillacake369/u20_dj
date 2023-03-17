<?php
include_once(__DIR__ . "/../../backheader.php");

if (
    !isset($_POST["judge_first_name"]) ||
    !isset($_POST["judge_second_name"]) ||
    !isset($_POST["judge_country"]) ||
    !isset($_POST["judge_division"]) ||
    !isset($_POST["judge_gender"]) ||
    !isset($_POST["judge_birth_year"]) ||
    !isset($_POST["judge_birth_month"]) ||
    !isset($_POST["judge_birth_day"]) ||
    !isset($_POST["judge_age"]) ||
    !isset($_POST["judge_duty"]) ||
    !isset($_POST["judge_sector"]) ||
    !isset($_POST["judge_schedules"]) ||
    !isset($_POST["judge_password"]) ||
    !isset($_POST["attendance_sports"])
) {
    echo "<script>alert('기입하지 않은 정보가 있습니다.');window.close();</script>";
    exit;
}
// include_once(__DIR__ . "/../../auth/config.php"); // 데이터베이스 연결 :: auth 내부에서 auth 확인 후 db 연결
require_once __DIR__ . "/imgUpload.php"; //B:데이터베이스 연결
require_once __DIR__ . "/dictionary.php"; //B:서치 select 태크 사용하기 위한 자료구조

$sector = implode(',', $_POST["judge_sector"]);
$schedule = implode(',', $_POST["judge_schedules"]);
$attendance_id = implode(',', $_POST["attendance_sports"]);
$birth_day = $_POST["judge_birth_year"] . "-" . $_POST["judge_birth_month"] . "-" . $_POST["judge_birth_day"];
$name = strtolower($_POST["judge_second_name"]) . " " . strtoupper($_POST["judge_first_name"]);
$profile = strtolower($_POST["judge_second_name"]) . $birth_day . "_profile";

$judge_id = trim($_POST["judge_id"]);
$judge_name = trim($name);
$judge_country = trim($_POST["judge_country"]);
$judge_division = trim($_POST["judge_division"]);
$judge_duty = trim($_POST["judge_duty"]);
$judge_gender = trim($_POST["judge_gender"]);
$judge_birth = trim($birth_day);
$judge_age = trim($_POST["judge_age"]);
$judge_sector = trim($sector);
$judge_schedule = trim($schedule);
$judge_attendance = trim($attendance_id);
$judge_profile = trim($profile);
$judge_password = trim($_POST["judge_password"]);
$judge_password_hash = hash("sha256", $judge_password);


if ($_POST["judge_birth_month"] > 12 || $_POST["judge_birth_month"] < 0) {
    echo "<script>alert('생일 항목을 입력을 잘못 입력하셨습니다.');window.close();</script>";
    exit;
}
if ($month_dic[$_POST["judge_birth_month"]] < $_POST["judge_birth_day"]) {
    echo "<script>alert('생일 항목을 입력을 잘못 입력하셨습니다.');window.close();</script>";
    exit;
}

if ($_FILES['judge_imgFile']["size"] == 0) {

    $sql = "UPDATE list_judge SET 
        judge_name=?,
        judge_country=?,
        judge_division=?,
        judge_duty=?,
        judge_gender=?,
        judge_birth=?,
        judge_age=?,
        judge_sector=?,
        judge_schedule=?,
        judge_attendance=?,
        judge_password=?
        WHERE judge_id=?";
    $stmt = $db->prepare($sql);

    $stmt->bind_param(
        "ssssssssssss",
        $judge_name,
        $judge_country,
        $judge_division,
        $judge_duty,
        $judge_gender,
        $judge_birth,
        $judge_age,
        $judge_sector,
        $judge_schedule,
        $judge_attendance,
        $judge_password_hash, //충격 실화! 이거 순서 안 맞으면 DB에 저장 안되는 에러 발생
        $judge_id
    );
    $stmt->execute();
} else { //이미지를 수정했을 경우

    $judge_profile = str_replace(' ', '', $judge_profile);
    $judge_profile = Img_Upload($_FILES['judge_imgFile'], "judge_img", $profile);

    $sql = "UPDATE list_judge SET 
        judge_name=?,
        judge_country=?,
        judge_division=?,
        judge_duty=?,
        judge_gender=?,
        judge_birth=?,
        judge_age=?,
        judge_sector=?,
        judge_schedule=?,
        judge_attendance=?,
        judge_profile=?,
        judge_password=?
        WHERE judge_id=?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param(
        "sssssssssssss",
        $judge_name,
        $judge_country,
        $judge_division,
        $judge_duty,
        $judge_gender,
        $judge_birth,
        $judge_age,
        $judge_sector,
        $judge_schedule,
        $judge_attendance,
        $judge_profile,
        $judge_password_hash, //충격 실화! 이거 순서 안 맞으면 DB에 저장 안되는 에러 발생
        $judge_id
    );
    $stmt->execute();
}

// 로그 생성
logInsert($db, $_SESSION['Id'], '심판 수정', $judge_name . "-" . $judge_country . "-" . $judge_schedule);

echo "<script>alert('수정되었습니다.');window.close();</script>";