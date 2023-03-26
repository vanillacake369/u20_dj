<?php
require_once __DIR__ . "/../../backheader.php";
require_once __DIR__ . "/../../class-image.php";
require_once __DIR__ . "/../../includes/auth/config.php";
require_once "./imgUpload.php"; //B:데이터베이스 연결
require_once "./dictionary.php"; //B:서치 select 태크 사용하기 위한 자료구조

if (
    (!isset($_POST["director_first_name"]) || $_POST["director_first_name"] == "") ||
    (!isset($_POST["director_second_name"]) || $_POST["director_second_name"] == "") ||
    (!isset($_POST["director_country"]) || $_POST["director_country"] == "") ||
    (!isset($_POST["director_division"]) || $_POST["director_division"] == "") ||
    (!isset($_POST["director_gender"]) || $_POST["director_gender"] == "") ||
    (!isset($_POST["director_birth_year"]) || $_POST["director_birth_year"] == "") ||
    (!isset($_POST["director_birth_month"]) || $_POST["director_birth_month"] == "") ||
    (!isset($_POST["director_birth_day"]) || $_POST["director_birth_day"] == "") ||
    (!isset($_POST["director_age"]) || $_POST["director_age"] == "") ||
    (!isset($_POST["director_duty"]) || $_POST["director_duty"] == "") ||
	(!isset($_POST["director_village"]) || $_POST["director_village"] == "") ||
	(!isset($_POST["director_seats"]) || $_POST["director_seats"] == "") ||
	(!isset($_POST["director_sector"]) || $_POST["director_sector"] == "") ||
	(!isset($_POST["director_venue_access"]) ||  $_POST["director_venue_access"] == "")
    // !isset($_POST["director_sector"])
    // !isset($_POST["director_schedules"]) ||
    // !isset($_POST["attendance_sports"])
) {
    echo "<script>alert('기입하지 않은 정보가 있습니다.');history.back();</script>";
    exit;
}
// 데이터베이스 연결 :: auth 내부에서 auth 확인 후 db 연결

// $schedule = implode(',', $_POST["director_schedules"]);
// $attendance_id = implode(',', $_POST["attendance_sports"]);
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
if (isset($_POST["director_eat"]) && $_POST["director_eat"] != "")
	$director_eat = "y";
else
	$director_eat = "n";
if (isset($_POST["director_transport"]) &&  $_POST["director_transport"] != "")
	$director_transport = trim($_POST["director_transport"]);
else
	$director_transport = "";
$director_seats = trim($_POST["director_seats"]);
$director_village = trim($_POST["director_village"]);
$director_sector = implode(',', $_POST["director_sector"]);
$director_venue_access = trim($_POST["director_venue_access"]);
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

if ($_FILES['main_photo']["size"] == 0) {

    $sql = "UPDATE list_director SET 
        director_name=?,
        director_country=?,
        director_division=?,
        director_duty=?,
        director_gender=?,
        director_birth=?,
        director_age=?,
        director_sector=?,
        director_eat=?,
        director_transport=?,
        director_venue_access=?,
        director_seats=?,
        director_village=?
        -- director_schedule=?,
        -- director_attendance=?
        WHERE director_id=?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param(
        "ssssssssssssss",
        $director_name,
        $director_country,
        $director_division,
        $director_duty,
        $director_gender,
        $director_birth,
        $director_age,
        $director_sector,
        $director_eat,
        $director_transport,
        $director_venue_access,
        $director_seats,
        $director_village,
        // $director_schedule,
        // $director_attendance,
        $director_id
    );
    $stmt->execute();
} else { //이미지를 수정했을 경우

    $director_image = '';

    if ($_FILES['main_photo']['name']) {
        $upload_dir = "../../assets/img/director_img/";
    
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
                $director_photo = str_replace("../../assets/img/director_img/", "", $upload_dir) . $myFile;
    
                $director_image = $director_photo;
            } else {
                AlertBox("[오류] 관리자에게 문의해주세요.", 'back', '');
                exit;
        }
    } else {
        $director_image = 'profile.jpg';
    }

    $sql = "UPDATE list_director SET 
        director_name=?,
        director_country=?,
        director_division=?,
        director_duty=?,
        director_gender=?,
        director_birth=?,
        director_age=?,
        director_sector=?,
        director_profile=?,
        director_eat=?,
        director_transport=?,
        director_venue_access=?,
        director_seats=?,
        director_village=?
        WHERE director_id=?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param(
        "sssssssssssssss",
        $director_name,
        $director_country,
        $director_division,
        $director_duty,
        $director_gender,
        $director_birth,
        $director_age,
        $director_sector,
        $director_image,
        $director_eat,
        $director_transport,
        $director_venue_access,
        $director_seats,
        $director_village,
        $director_id
    );
    $stmt->execute();
}

// 로그 생성
logInsert($db, $_SESSION['Id'], '임원 수정', $director_name . "-" . $director_country . "-" . $director_id);

echo "<script>alert('수정되었습니다.');window.close();</script>";