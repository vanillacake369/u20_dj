<?php
require_once __DIR__ . "/../../includes/auth/config.php";
require_once __DIR__ . "/../../backheader.php";
require_once __DIR__ . "/../../class-image.php";

require_once __DIR__ . "/imgUpload.php"; //B:데이터베이스 연결
require_once __DIR__ . "/dictionary.php"; //B:서치 select 태크 사용하기 위한 자료구조

if (
    (!isset($_POST["athlete_first_name"]) || $_POST["athlete_first_name"] == "") ||
    (!isset($_POST["athlete_second_name"]) || $_POST["athlete_second_name"] == "")||
    (!isset($_POST["athlete_country"]) || $_POST["athlete_country"] == "")||
    (!isset($_POST["athlete_division"]) || $_POST["athlete_division"] == "")||
    (!isset($_POST["athlete_region"]) || $_POST["athlete_region"] == "")||
    (!isset($_POST["athlete_gender"]) || $_POST["athlete_gender"] == "")||
    (!isset($_POST["athlete_birth_year"]) || $_POST["athlete_birth_year"] == "")||
    (!isset($_POST["athlete_birth_month"]) || $_POST["athlete_birth_month"] == "")||
    (!isset($_POST["athlete_birth_day"]) || $_POST["athlete_birth_day"] == "")||
    (!isset($_POST["athlete_schedules"]) || $_POST["athlete_schedules"] == "")||
    (!isset($_POST["attendance_sports"]) || $_POST["attendance_sports"] == "")||
    (!isset($_POST["athlete_sector"]) || $_POST["athlete_sector"]== "")||
    (!isset($_POST["athlete_village"]) || $_POST["athlete_village"]== "")||
    (!isset($_POST["athlete_seats"]) || $_POST["athlete_seats"]== "")||
    (!isset($_POST["athlete_venue_access"]) || $_POST["athlete_venue_access"]== "")
) {
    echo "<script>alert('기입하지 않은 정보가 있습니다.');history.back();</script>";
    exit;
}
// 데이터베이스 연결 :: auth 내부에서 auth 확인 후 db 연결

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
$athlete_schedule = trim($schedule);
$athlete_attendance = trim($attendance_id);
$athlete_profile = trim($profile);
if (isset($_POST["athlete_eat"]) && $_POST["athlete_eat"] != "")
    $athlete_eat = "y";
else
    $athlete_eat = "n";
if (isset($_POST["athlete_transport"]) &&  $_POST["athlete_transport"] != "")
    $athlete_transport = trim($_POST["athlete_transport"]);
else
    $athlete_transport = "";
$athlete_seats = trim($_POST["athlete_seats"]);
$athlete_village = trim($_POST["athlete_village"]);
$athlete_sector = implode(',', $_POST["athlete_sector"]);
$athlete_venue_access = trim($_POST["athlete_venue_access"]);
$athlete_bib = trim($_POST["athlete_bib"] ?? NULL);
if (isset($_POST["athlete_sb_sports"]) &&  $_POST["athlete_sb_sports"] != "")
    $athlete_sb_sports = $_POST["athlete_sb_sports"];
else
    $athlete_sb_sports = "";
if (isset($_POST["athlete_sb"]) &&  $_POST["athlete_sb"] != "")
    $athlete_sb = $_POST["athlete_sb"];
else
    $athlete_sb = "";

$athlete_sb_json = array();
if (isset($_POST["athlete_pb_sports"]) &&  $_POST["athlete_pb_sports"] != "")
    $athlete_pb_sports = $_POST["athlete_pb_sports"];
else
    $athlete_pb_sports = "";
if (isset($_POST["athlete_pb"]) &&  $_POST["athlete_pb"] != "")
    $athlete_pb = $_POST["athlete_pb"];
else
    $athlete_pb = "";
$athlete_pb_json = array();

// athlete_sb_json {"sports_code"=>record}
if ($athlete_sb_sports != "")
    for ($i = 0; $i < count($athlete_sb_sports); $i++) {
        $athlete_sb_json[$athlete_sb_sports[$i]] = $athlete_sb[$i];
    }
$athlete_sb_json_str = json_encode($athlete_sb_json);
// athlete_pb_json {"sports_code"=>record}
for ($i = 0; $i < count($athlete_pb); $i++) {
    $athlete_pb_json[$athlete_pb_sports[$i]] = $athlete_pb[$i];
}
$athlete_pb_json_str = json_encode($athlete_pb_json);

if ($_POST["athlete_birth_month"] > 12 || $_POST["athlete_birth_month"] < 0) {
    echo "<script>alert('생일 항목을 입력을 잘못 입력하셨습니다.');window.close();</script>";
    exit;
}
if ($month_dic[$_POST["athlete_birth_month"]] < $_POST["athlete_birth_day"]) {
    echo "<script>alert('생일 항목을 입력을 잘못 입력하셨습니다.');window.close();</script>";
    exit;
}

if ($_FILES['main_photo']["size"] == 0) {

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
        athlete_sb=?,
        athlete_pb=?,
        athlete_eat=?,
        athlete_transport=?,
        athlete_venue_access=?,
        athlete_seats=?,
        athlete_village=?,
        athlete_bib=?
        WHERE athlete_id=?";
    $stmt = $db->prepare($sql);

    $stmt->bind_param(
        "sssssssssssssssssis",
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
        $athlete_sb_json_str,
        $athlete_pb_json_str,
        $athlete_eat,
        $athlete_transport,
        $athlete_venue_access,
        $athlete_seats,
        $athlete_village,
        $athlete_bib,
        $athlete_id
    );
    $stmt->execute();
} else { //이미지를 수정했을 경우

    $athlete_image = '';

    if ($_FILES['main_photo']['name']) {
        $upload_dir = '../../assets/img/athlete_img/';
        if (!is_dir($upload_dir))
            mkdir($upload_dir, 0777, true);
        // for ($i = 0; $i < count($_FILES['athlete_imgFile']['name']); $i++) {
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
            $athlete_photo = str_replace("../../assets/img/athlete_img/", "", $upload_dir) . $myFile;
            $athlete_photo = $upload_dir . $myFile;
            $athlete_image = $myFile;
        } else {
            AlertBox("[오류] 관리자에게 문의해주세요.", 'back', '');
            exit;
        }
        // }
    } else {
        $athlete_image = 'profile.jpg';
    }

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
        athlete_profile=?,
        athlete_sb=?,
        athlete_pb=?,
        athlete_eat=?,
        athlete_transport=?,
        athlete_venue_access=?,
        athlete_seats=?,
        athlete_village=?,
        athlete_bib=?,
        WHERE athlete_id=?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param(
        "sssssssssssssis",
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
        $athlete_sb_json_str,
        $athlete_pb_json_str,
        $athlete_eat,
        $athlete_transport,
        $athlete_venue_access,
        $athlete_seats,
        $athlete_village,
        $athlete_bib,
        $athlete_id
    );
    $stmt->execute();
}

// 로그 생성
logInsert($db, $_SESSION['Id'], '선수 수정', $athlete_name . "-" . $athlete_country . "-" . $athlete_schedule);

echo "<script>alert('수정되었습니다.');window.close();</script>";
