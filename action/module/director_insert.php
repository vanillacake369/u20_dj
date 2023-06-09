<?php
include_once(__DIR__ . "/../../backheader.php");
require_once __DIR__ . "/../class-image.php";
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
    // !isset($_POST["director_schedules"]) ||
    // !isset($_POST["attendance_sports"])
) {
    echo "<script>alert('기입하지 않은 정보가 있습니다.');window.close();</script>";
    exit;
}
// 데이터베이스 연결 :: auth 내부에서 auth 확인 후 db 연결
include_once(__DIR__ . "/../../includes/auth/config.php");
require_once "./imgUpload.php"; //B:데이터베이스 연결
require_once "./dictionary.php"; //B:서치 select 태크 사용하기 위한 자료구조

// $schedule = implode(',', $_POST["director_schedules"]);
// $attendance_id = implode(',', $_POST["attendance_sports"]);
$sector = implode(',', $_POST["director_sector"]);
$birth_day = $_POST["director_birth_year"] . "-" . $_POST["director_birth_month"] . "-" . $_POST["director_birth_day"];
$name = strtolower($_POST["director_second_name"]) . " " . strtoupper($_POST["director_first_name"]);
$profile = strtolower($_POST["director_second_name"]) . $birth_day . "_profile";

// month 0 이하 12 초과 필터링
if ($_POST["director_birth_month"] > 12 || $_POST["director_birth_month"] < 0) {
    echo "<script>alert('생일을 입력을 잘못 입력하셨습니다.');window.close();</script>";
    exit;
}
// month의 최대일수를 넘긴 경우 필터링
if ($month_dic[$_POST["director_birth_month"]] < $_POST["director_birth_day"]) {
    echo "<script>alert('생일을 입력을 잘못 입력하셨습니다.');window.close();</script>";
    exit;
}

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

$director_image = "";

if($_FILES['main_photo']['name'][0])
		{
			$upload_dir = "../upload/editors/";

			if(!is_dir($upload_dir))
				mkdir($upload_dir, 0777);

			for($i=0; $i<count($_FILES['main_photo']['name']); $i++)
			{
				$FileExt = substr(strrchr($_FILES['main_photo']['name'][$i], "."), 1); // 확장자 추출
				$myFile = str_replace(" ","",microtime()).'.'.$FileExt;
				
				if( $FileExt != "jpg" && $FileExt != "gif" && $FileExt != "jpeg" && $FileExt != "png" && $FileExt != "JPG" && $FileExt != "GIF" && $FileExt != "JPEG" && $FileExt != "PNG")
				{
					AlertBox ("[오류] 올바른 이미지 확장자가 아닙니다.",'back','');
					Exit;
				}
				if( move_uploaded_file($_FILES['main_photo']['tmp_name'][$i], $upload_dir.$myFile) )
				{
					$image_photo = new Image($upload_dir.$myFile);

					if($image_photo->getWidth() < 10 || $image_photo->getHeight() < 10)
					{
						AlertBox ("[오류] 올바른 이미지가 아닙니다.",'back','');
						Exit;
					}
					
					if($image_photo->getWidth() > 2000)
						$image_photo->resizeToWidth(2000);

					$image_photo->save($upload_dir.$myFile);
					$director_photo = str_replace("../upload/editors/","",$upload_dir).$myFile;

					$director_profile = $director_photo;
				}
				else
				{
					AlertBox ("[오류] 관리자에게 문의해주세요.",'back','');
					Exit;
				}
			}
		}
		else
		{
			$director_image = 'profile.jpg';
		}

$sql = "INSERT INTO list_director
            (director_name, director_country, director_division, director_duty, director_gender, director_birth, director_age, director_sector, director_profile)
            VALUES(?,?,?,?,?,?,?,?,?)";
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
    $director_profile
);

$stmt->execute();

// 로그 생성
logInsert($db, $_SESSION['Id'], '임원 생성', $director_name . "-" . $director_country . "-" . $director_schedule);

echo "<script>alert('등록되었습니다.');window.close();</script>";