<?php
require_once __DIR__ . "/../../backheader.php";

if (
    !isset($_POST["director_first_name"]) ||
    !isset($_POST["director_second_name"]) ||
    !isset($_POST["director_country"]) ||
    !isset($_POST["director_division"]) ||
    !isset($_POST["director_gender"]) ||
    !isset($_POST["director_birth_year"]) ||
    !isset($_POST["director_birth_month"]) ||
    !isset($_POST["director_birth_day"]) ||
    !isset($_POST["director_age"]) ||
    !isset($_POST["director_duty"])
    // !isset($_POST["director_sector"])
    // !isset($_POST["director_schedules"]) ||
    // !isset($_POST["attendance_sports"])
) {
    echo "<script>alert('기입하지 않은 정보가 있습니다.');window.close();</script>";
    exit;
}
// 데이터베이스 연결 :: auth 내부에서 auth 확인 후 db 연결
require_once __DIR__ . "/../../includes/auth/config.php";
require_once "./imgUpload.php"; //B:데이터베이스 연결
require_once "./dictionary.php"; //B:서치 select 태크 사용하기 위한 자료구조

// $schedule = implode(',', $_POST["director_schedules"]);
// $attendance_id = implode(',', $_POST["attendance_sports"]);
$sector = implode(',', $_POST["director_sector"]);
$birth_day = $_POST["director_birth_year"] . "-" . $_POST["director_birth_month"] . "-" . $_POST["director_birth_day"];
$name = strtolower($_POST["director_second_name"]) . " " . strtoupper($_POST["director_first_name"]);
$profile = strtolower($_POST["director_second_name"]) . $birth_day . "_profile";

$director_id = trim($_POST["director_id"]);
$director_name = trim($name);
$director_country = trim($_POST["director_country"]);
$director_division = trim($_POST["director_division"]);
$director_duty = trim($_POST["director_duty"]);
$director_gender = trim($_POST["director_gender"]);
$director_birth = trim($birth_day);
$director_age = trim($_POST["director_age"]);
$director_sector = trim($sector);
$director_profile = trim($profile);
// $director_schedule = trim($schedule);
// $director_attendance = trim($attendance_id);

if ($_POST["director_birth_month"] > 12 || $_POST["director_birth_month"] < 0) {
    echo "<script>alert('생일 항목을 입력을 잘못 입력하셨습니다.');window.close();</script>";
    exit;
}
if ($month_dic[$_POST["director_birth_month"]] < $_POST["director_birth_day"]) {
    echo "<script>alert('생일 항목을 입력을 잘못 입력하셨습니다.');window.close();</script>";
    exit;
}

if ($_FILES['director_imgFile']["size"] == 0) {

    $sql = "UPDATE list_director SET 
        director_name=?,
        director_country=?,
        director_division=?,
        director_duty=?,
        director_gender=?,
        director_birth=?,
        director_age=?,
        director_sector=?
        -- director_schedule=?,
        -- director_attendance=?
        WHERE director_id=?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param(
        "sssssssss",
        $director_name,
        $director_country,
        $director_division,
        $director_duty,
        $director_gender,
        $director_birth,
        $director_age,
        $director_sector,
        // $director_schedule,
        // $director_attendance,
        $director_id
    );
    $stmt->execute();
} else { //이미지를 수정했을 경우

    $director_profile = str_replace(' ', '', $director_profile);
    $director_profile = Img_Upload($_FILES['director_imgFile'], "director_img", $profile);

    $sql = "UPDATE list_director SET 
        director_name=?,
        director_country=?,
        director_division=?,
        director_duty=?,
        director_gender=?,
        director_birth=?,
        director_age=?,
        director_sector=?,
        director_profile=?
        WHERE director_id=?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param(
        "ssssssssss",
        $director_name,
        $director_country,
        $director_division,
        $director_duty,
        $director_gender,
        $director_birth,
        $director_age,
        $director_sector,
        $director_profile,
        $director_id
    );
    $stmt->execute();
}

// 로그 생성
logInsert($db, $_SESSION['Id'], '임원 수정', $director_name . "-" . $director_country . "-" . $director_id);

echo "<script>alert('수정되었습니다.');window.close();</script>";