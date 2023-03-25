<?php
include_once(__DIR__ . "/../../backheader.php");
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
	!isset($_POST["judge_village"]) ||
	!isset($_POST["judge_seats"]) ||
	!isset($_POST["judge_venue_access"])
) {
    echo "<script>alert('기입하지 않은 정보가 있습니다.');window.close();</script>";
    exit;
}

$judge_password_hash = "";

if (isset($_POST["judge_password"]) && $_POST["judge_password"] != "")
{
    if ($_POST["judge_password"] != $_POST["cpassword"]){
        echo "<script>alert('비밀번호가 다릅니다.');window.close();</script>";
        exit;
    }
    $judge_password = trim($_POST["judge_password"]);
    $judge_password_hash = hash("sha256", $judge_password);
}
// include_once(__DIR__ . "/../../auth/config.php"); // 데이터베이스 연결 :: auth 내부에서 auth 확인 후 db 연결
require_once __DIR__ . "/imgUpload.php"; //B:데이터베이스 연결
require_once __DIR__ . "/dictionary.php"; //B:서치 select 태크 사용하기 위한 자료구조

$schedule = implode(',', $_POST["judge_schedules"]);
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
$judge_schedule = trim($schedule);
$judge_profile = trim($profile);
if (isset($_POST["judge_eat"]) && $_POST["judge_eat"] != "")
	$judge_eat = "y";
else
	$judge_eat = "n";
if (isset($_POST["judge_transport"]) &&  $_POST["judge_transport"] != "")
	$judge_transport = trim($_POST["judge_transport"]);
else
	$judge_transport = "";
$judge_seats = trim($_POST["judge_seats"]);
$judge_village = trim($_POST["judge_village"]);
$judge_sector = implode(',', $_POST["judge_sector"]);
$judge_venue_access = trim($_POST["judge_venue_access"]);

if ($_POST["judge_birth_month"] > 12 || $_POST["judge_birth_month"] < 0) {
    echo "<script>alert('생일 항목을 입력을 잘못 입력하셨습니다.');window.close();</script>";
    exit;
}
if ($month_dic[$_POST["judge_birth_month"]] < $_POST["judge_birth_day"]) {
    echo "<script>alert('생일 항목을 입력을 잘못 입력하셨습니다.');window.close();</script>";
    exit;
}

if ($_FILES['main_photo']["size"] == 0) {

    if (isset($_POST["judge_password"]) && $_POST["judge_password"] != "")
    {
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
            judge_password=?,
            judge_eat=?,
            judge_transport=?,
            judge_venue_access=?,
            judge_seats=?,
            judge_village=?
            WHERE judge_id=?";
        $stmt = $db->prepare($sql);

        $stmt->bind_param(
            "ssssssssssssssss",
            $judge_name,
            $judge_country,
            $judge_division,
            $judge_duty,
            $judge_gender,
            $judge_birth,
            $judge_age,
            $judge_sector,
            $judge_schedule,
            $judge_password_hash, //충격 실화! 이거 순서 안 맞으면 DB에 저장 안되는 에러 발생
            $judge_eat,
            $judge_transport,
            $judge_venue_access,
            $judge_seats,
            $judge_village,
            $judge_id);
    }
    else
    {
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
            judge_eat=?,
            judge_transport=?,
            judge_venue_access=?,
            judge_seats=?,
            judge_village=?
        WHERE judge_id=?";
    $stmt = $db->prepare($sql);

    $stmt->bind_param(
        "sssssssssssssss",
        $judge_name,
        $judge_country,
        $judge_division,
        $judge_duty,
        $judge_gender,
        $judge_birth,
        $judge_age,
        $judge_sector,
        $judge_schedule,
        $judge_eat,
            $judge_transport,
            $judge_venue_access,
            $judge_seats,
            $judge_village,
        $judge_id);
    }
    $stmt->execute();
} else { //이미지를 수정했을 경우

    $judge_image = '';

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

    if (isset($_POST["judge_password"]) && $_POST["judge_password"] != "")
    {

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
            judge_profile=?,
            judge_password=?,
            judge_eat=?,
            judge_transport=?,
            judge_venue_access=?,
            judge_seats=?,
            judge_village=?
            WHERE judge_id=?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param(
            "sssssssssssssssss",
            $judge_name,
            $judge_country,
            $judge_division,
            $judge_duty,
            $judge_gender,
            $judge_birth,
            $judge_age,
            $judge_sector,
            $judge_schedule,
            $judge_image,
            $judge_password_hash, //충격 실화! 이거 순서 안 맞으면 DB에 저장 안되는 에러 발생
            $judge_eat,
            $judge_transport,
            $judge_venue_access,
            $judge_seats,
            $judge_village,
            $judge_id
        );
        $stmt->execute();
    }
    else
    {
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
        judge_profile=?,
            judge_eat=?,
            judge_transport=?,
            judge_venue_access=?,
            judge_seats=?,
            judge_village=?
        WHERE judge_id=?";
        $stmt = $db->prepare($sql);

        $stmt->bind_param(
            "ssssssssssssssss",
            $judge_name,
            $judge_country,
            $judge_division,
            $judge_duty,
            $judge_gender,
            $judge_birth,
            $judge_age,
            $judge_sector,
            $judge_schedule,
            $judge_image,
            $judge_eat,
            $judge_transport,
            $judge_venue_access,
            $judge_seats,
            $judge_village,
            $judge_id
        );
        $stmt->execute();
    }
}

// 로그 생성
logInsert($db, $_SESSION['Id'], '심판 수정', $judge_name . "-" . $judge_country . "-" . $judge_schedule);

echo "<script>alert('수정되었습니다.');window.close();</script>";
