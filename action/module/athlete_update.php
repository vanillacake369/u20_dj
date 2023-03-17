<?php
include_once(__DIR__ . "/../../backheader.php");

if (
    !isset($_POST["athlete_first_name"]) ||
    !isset($_POST["athlete_second_name"]) ||
    !isset($_POST["athlete_country"]) ||
    !isset($_POST["athlete_division"]) ||
    !isset($_POST["athlete_region"]) ||
    !isset($_POST["athlete_gender"]) ||
    !isset($_POST["athlete_birth_year"]) ||
    !isset($_POST["athlete_birth_month"]) ||
    !isset($_POST["athlete_birth_day"]) ||
    !isset($_POST["athlete_sector"]) ||
    !isset($_POST["athlete_schedules"]) ||
    !isset($_POST["attendance_sports"])
) {
    echo "<script>alert('기입하지 않은 정보가 있습니다.');window.close();</script>";
    exit;
}
// 데이터베이스 연결 :: auth 내부에서 auth 확인 후 db 연결
include_once(__DIR__ . "/../../includes/auth/config.php");
require_once __DIR__ . "/imgUpload.php"; //B:데이터베이스 연결
require_once __DIR__ . "/dictionary.php"; //B:서치 select 태크 사용하기 위한 자료구조

$sector = implode(',', $_POST["athlete_sector"]);
$schedule = implode(',', $_POST["athlete_schedules"]);
$attendance_id = implode(',', $_POST["attendance_sports"]);
$birth_day = $_POST["athlete_birth_year"] . "-" . $_POST["athlete_birth_month"] . "-" . $_POST["athlete_birth_day"];
$name = strtolower($_POST["athlete_second_name"]) . " " . strtoupper($_POST["athlete_first_name"]);
$profile = strtolower($_POST["athlete_second_name"]) . $birth_day . "_profile";

$athlete_id = trim($_POST["athlete_id"]);
$athlete_name = trim($name);
$athlete_country = trim($_POST["athlete_country"]);
$athlete_region = trim($_POST["athlete_region"]);
$athlete_division = trim($_POST["athlete_division"]);
$athlete_gender = trim($_POST["athlete_gender"]);
$athlete_birth = trim($birth_day);
$athlete_age = trim($_POST["athlete_age"]);
$athlete_sector = trim($sector);
$athlete_schedule = trim($schedule);
$athlete_attendance = trim($attendance_id);
$athlete_profile = trim($profile);

if ($_POST["athlete_birth_month"] > 12 || $_POST["athlete_birth_month"] < 0) {
    echo "<script>alert('생일 항목을 입력을 잘못 입력하셨습니다.');window.close();</script>";
    exit;
}
if ($month_dic[$_POST["athlete_birth_month"]] < $_POST["athlete_birth_day"]) {
    echo "<script>alert('생일 항목을 입력을 잘못 입력하셨습니다.');window.close();</script>";
    exit;
}

if ($_FILES['athlete_imgFile']["size"] == 0) {

    $sql = "UPDATE list_athlete SET 
        athlete_name=?,
        athlete_country=?,
        athlete_region=?,
        athlete_division=?,
        athlete_gender=?,
        athlete_birth=?,
        athlete_age=?,
        athlete_sector=?,
        athlete_schedule=?,
        athlete_attendance=?
        WHERE athlete_id=?";
    $stmt = $db->prepare($sql);

    $stmt->bind_param(
        "sssssssssss",
        $athlete_name,
        $athlete_country,
        $athlete_region,
        $athlete_division,
        $athlete_gender,
        $athlete_birth,
        $athlete_age,
        $athlete_sector,
        $athlete_schedule,
        $athlete_attendance,
        $athlete_id
    );
    $stmt->execute();
} else { //이미지를 수정했을 경우

    $athlete_profile = str_replace(' ', '', $athlete_profile);
    $athlete_profile = Img_Upload($_FILES['athlete_imgFile'], "athlete_img", $profile);

    $sql = "UPDATE list_athlete SET 
        athlete_name=?,
        athlete_country=?,
        athlete_region=?,
        athlete_division=?,
        athlete_gender=?,
        athlete_birth=?,
        athlete_age=?,
        athlete_sector=?,
        athlete_schedule=?,
        athlete_attendance=?,
        athlete_profile=?
        WHERE athlete_id=?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param(
        "ssssssssssss",
        $athlete_name,
        $athlete_country,
        $athlete_region,
        $athlete_division,
        $athlete_gender,
        $athlete_birth,
        $athlete_age,
        $athlete_sector,
        $athlete_schedule,
        $athlete_attendance,
        $athlete_profile,
        $athlete_id
    );
    $stmt->execute();
}

// 로그 생성
logInsert($db, $_SESSION['Id'], '선수 수정', $athlete_name . "-" . $athlete_country . "-" . $athlete_schedule);

echo "<script>alert('수정되었습니다.');window.close();</script>";