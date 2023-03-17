<?php
include_once(__DIR__ . "/../../backheader.php");

if (
    !isset($_POST["coach_first_name"]) ||
    !isset($_POST["coach_second_name"]) ||
    !isset($_POST["coach_country"]) ||
    !isset($_POST["coach_division"]) ||
    !isset($_POST["coach_region"]) ||
    !isset($_POST["coach_gender"]) ||
    !isset($_POST["coach_birth_year"]) ||
    !isset($_POST["coach_birth_month"]) ||
    !isset($_POST["coach_birth_day"]) ||
    !isset($_POST["coach_age"]) ||
    !isset($_POST["coach_duty"]) ||
    !isset($_POST["coach_sector"])
    // !isset($_POST["coach_schedules"]) ||
    // !isset($_POST["attendance_sports"])
) {
    echo "<script>alert('기입하지 않은 정보가 있습니다.');window.close();</script>";
    exit;
}
// 데이터베이스 연결 :: auth 내부에서 auth 확인 후 db 연결
include_once(__DIR__ . "/../../includes/auth/config.php");
require_once "./imgUpload.php"; //B:데이터베이스 연결
require_once "./dictionary.php"; //B:서치 select 태크 사용하기 위한 자료구조

// $schedule = implode(',', $_POST["coach_schedules"]);
// $attendance_id = implode(',', $_POST["attendance_sports"]);
$sector = implode(',', $_POST["coach_sector"]);
$birth_day = $_POST["coach_birth_year"] . "-" . $_POST["coach_birth_month"] . "-" . $_POST["coach_birth_day"];
$name = strtolower($_POST["coach_second_name"]) . " " . strtoupper($_POST["coach_first_name"]);
$profile = strtolower($_POST["coach_second_name"]) . $birth_day . "_profile";

$coach_id = trim($_POST["coach_id"]);
$coach_name = trim($name);
$coach_country = trim($_POST["coach_country"]);
$coach_region = trim($_POST["coach_region"]);
$coach_division = trim($_POST["coach_division"]);
$coach_duty = trim($_POST["coach_duty"]);
$coach_gender = trim($_POST["coach_gender"]);
$coach_birth = trim($birth_day);
$coach_age = trim($_POST["coach_age"]);
$coach_sector = trim($sector);
$coach_profile = trim($profile);
// $coach_schedule = trim($schedule);
// $coach_attendance = trim($attendance_id);

if ($_POST["coach_birth_month"] > 12 || $_POST["coach_birth_month"] < 0) {
    echo "<script>alert('생일 항목을 입력을 잘못 입력하셨습니다.');window.close();</script>";
    exit;
}
if ($month_dic[$_POST["coach_birth_month"]] < $_POST["coach_birth_day"]) {
    echo "<script>alert('생일 항목을 입력을 잘못 입력하셨습니다.');window.close();</script>";
    exit;
}

if ($_FILES['coach_imgFile']["size"] == 0) {

    $sql = "UPDATE list_coach SET 
        coach_name=?,
        coach_country=?,
        coach_region=?,
        coach_division=?,
        coach_duty=?,
        coach_gender=?,
        coach_birth=?,
        coach_age=?,
        coach_sector=?
        -- coach_schedule=?,
        -- coach_attendance=?
        WHERE coach_id=?";
    $stmt = $db->prepare($sql);

    $stmt->bind_param(
        "ssssssssss",
        $coach_name,
        $coach_country,
        $coach_region,
        $coach_division,
        $coach_duty,
        $coach_gender,
        $coach_birth,
        $coach_age,
        $coach_sector,
        // $coach_schedule,
        // $coach_attendance,
        $coach_id
    );
    $stmt->execute();
} else { //이미지를 수정했을 경우

    $coach_profile = str_replace(' ', '', $coach_profile);
    $coach_profile = Img_Upload($_FILES['coach_imgFile'], "coach_img", $profile);

    $sql = "UPDATE list_coach SET 
        coach_name=?,
        coach_country=?,
        coach_region=?,
        coach_division=?,
        coach_duty=?,
        coach_gender=?,
        coach_birth=?,
        coach_age=?,
        coach_sector=?,
        -- coach_schedule=?,
        -- coach_attendance=?,
        coach_profile=?
        WHERE coach_id=?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param(
        "sssssssssss",
        $coach_name,
        $coach_country,
        $coach_region,
        $coach_division,
        $coach_duty,
        $coach_gender,
        $coach_birth,
        $coach_age,
        $coach_sector,
        // $coach_schedule,
        // $coach_attendance,
        $coach_profile,
        $coach_id
    );
    $stmt->execute();
}

logInsert($db, $_SESSION['Id'], '코치 생성', $coach_name . "-" . $coach_country . "-" . $coach_duty);

echo "<script>alert('수정되었습니다.');window.close();</script>";