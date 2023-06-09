<?php
include_once(__DIR__ . "/../../backheader.php");
require_once __DIR__ . "/../class-image.php";

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
require_once __DIR__ . "/imgUpload.php"; //B:데이터베이스 연결
require_once __DIR__ . "/dictionary.php"; //B:서치 select 태크 사용하기 위한 자료구조

// $schedule = implode(',', $_POST["coach_schedules"]);
// $attendance_id = implode(',', $_POST["attendance_sports"]);
$sector = implode(',', $_POST["coach_sector"]);
$birth_day = $_POST["coach_birth_year"] . "-" . $_POST["coach_birth_month"] . "-" . $_POST["coach_birth_day"];
$name = strtolower($_POST["coach_second_name"]) . " " . strtoupper($_POST["coach_first_name"]);
$profile = strtolower($_POST["coach_second_name"]) . $birth_day . "_profile";

// month 0 이하 12 초과 필터링
if ($_POST["coach_birth_month"] > 12 || $_POST["coach_birth_month"] < 0) {
    echo "<script>alert('생일을 입력을 잘못 입력하셨습니다.');window.close();</script>";
    exit;
}
// month의 최대일수를 넘긴 경우 필터링
if ($month_dic[$_POST["coach_birth_month"]] < $_POST["coach_birth_day"]) {
    echo "<script>alert('생일을 입력을 잘못 입력하셨습니다.');window.close();</script>";
    exit;
}

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

$coach_image = "";

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
					$coach_photo = str_replace("../upload/editors/","",$upload_dir).$myFile;

					$coach_image = $coach_photo;
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
			$coach_image = 'profile.jpg';
		}
        
$sql = "INSERT INTO list_coach
            (coach_name, coach_country, coach_region, coach_division, coach_duty, coach_gender, coach_birth, coach_age, coach_sector, coach_profile)
            VALUES(?,?,?,?,?,?,?,?,?,?)";
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
    $coach_profile
);

$stmt->execute();

// 로그 생성
logInsert($db, $_SESSION['Id'], '코치 생성', $coach_name . "-" . $coach_country . "-" . $coach_duty);


echo "<script>alert('등록되었습니다.');window.close();</script>";