<?php
require_once __DIR__ . "/../../backheader.php";
require_once __DIR__ . "/../../class-image.php";
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
    !isset($_POST["userid"]) ||
    !isset($_POST["judge_password"])
    // || !isset($_POST["attendance_sports"]))
) {
    echo "<script>alert('기입하지 않은 정보가 있습니다.');window.close(); </script>";
    exit;
}
require_once __DIR__ . "/../../database/dbconnect.php"; //B:데이터베이스 연결
require_once __DIR__ . "/judge_filter.php"; //B:데이터베이스 연결
require_once __DIR__ . "/imgUpload.php"; //B:데이터베이스 연결
require_once __DIR__ . "/dictionary.php"; //B:서치 select 태크 사용하기 위한 자료구조

$userid = $_POST['userid'];
$sector = implode(',', $_POST["judge_sector"]);
$schedule = implode(',', $_POST["judge_schedules"]);
// $attendance_id = implode(',', $_POST["attendance_sports"]);
$birth_day = $_POST["judge_birth_year"] . "-" . $_POST["judge_birth_month"] . "-" . $_POST["judge_birth_day"];
$name = strtolower($_POST["judge_second_name"]) . " " . strtoupper($_POST["judge_first_name"]);
$profile = strtolower($_POST["judge_second_name"]) . $birth_day . "_profile";
$id_check = mq("select * from list_judge where judge_account='$userid'");


// id가 중복될 경우
$id_check = $id_check->fetch_array();
if ($id_check >= 1) {
    echo "<script>alert('아이디가 중복됩니다.'); history.back();</script>";
    exit;
}

// month 0 이하 12 초과 필터링
if ($_POST["judge_birth_month"] > 12 || $_POST["judge_birth_month"] < 0) {
    echo "<script>alert('생일을 잘못 입력하셨습니다.');window.close();</script>";
    exit;
}
// month의 최대일수를 넘긴 경우 필터링
if ($month_dic[$_POST["judge_birth_month"]] < $_POST["judge_birth_day"]) {
    echo "<script>alert('생일을 잘못 입력하셨습니다.');window.close();</script>";
    exit;
}

$judge_name = trim($name);
$judge_country = trim($_POST["judge_country"]);
$judge_division = trim($_POST["judge_division"]);
$judge_duty = trim($_POST["judge_duty"]);
$judge_gender = trim($_POST["judge_gender"]);
$judge_birth = trim($birth_day);
$judge_age = trim($_POST["judge_age"]);
$judge_sector = trim($sector);
$judge_schedule = trim($schedule);
// $judge_attendance = trim($attendance_id);
$judge_attendance = '';
$judge_profile = trim($profile);
$userid = trim($_POST["userid"]);
$judge_password = trim($_POST["judge_password"]);
$judge_password_hash = hash("sha256", $judge_password);
$judge_image = "";

if ($_FILES['main_photo']['name']) {
    $upload_dir = "../../assets/img/judge_img/";

    if (!is_dir($upload_dir))
        mkdir($upload_dir, 0777);

        $FileExt = substr(strrchr($_FILES['main_photo']['name'], "."), 1); // 확장자 추출
        $myFile = str_replace(" ", "", microtime()) . '.' . $FileExt;

        if ($FileExt != "jpg" && $FileExt != "gif" && $FileExt != "jpeg" && $FileExt != "png" && $FileExt != "JPG" && $FileExt != "GIF" && $FileExt != "JPEG" && $FileExt != "PNG") {
            AlertBox("[오류] 올바른 이미지 확장자가 아닙니다.", 'back', '');
            exit;
        }
        if (move_uploaded_file($_FILES['main_photo']['tmp_name'], $upload_dir . $myFile)) {
            $image_photo = new Image($upload_dir . $myFile);

            if ($image_photo->getWidth() < 10 || $image_photo->getHeight() < 10) {
                AlertBox("[오류] 올바른 이미지가 아닙니다.", 'back', '');
                exit;
            }

            if ($image_photo->getWidth() > 2000)
                $image_photo->resizeToWidth(2000);

            $image_photo->save($upload_dir . $myFile);
            $judge_photo = str_replace("../../assets/img/judge_img/", "", $upload_dir) . $myFile;

            $judge_image = $judge_photo;
        } else {
            AlertBox("[오류] 관리자에게 문의해주세요.", 'back', '');
            exit;
    }
} else {
    $judge_image = 'profile.jpg';
}


$sql = "INSERT INTO list_judge
            (judge_name, judge_country, judge_division, judge_duty, judge_gender, judge_birth, judge_age, judge_sector, judge_schedule, judge_profile, judge_attendance, judge_account, judge_password)
            VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)";
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
    $judge_profile,
    $judge_attendance,
    $userid,
    $judge_password_hash
);


$stmt->execute();

// 로그 생성
logInsert($db, $_SESSION['Id'], '심판 생성', $judge_name . "-" . $judge_country . "-" . $judge_schedule);

echo "<script>alert('등록되었습니다.');window.close();</script>";