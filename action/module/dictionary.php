<?php

/**
 * @category 모든 테이블 공통 dictionary
 * country_dic          : 국가한글명(key) : 국가id(value)       // 삭제 
 * country_code_dic     : 국가한글명(key) : 국가코드(value)
 * sport_dic            : 경기id(key) : 경기한글명(value)       // id(key) => 경기코드(key)
 * categoryOfSports_dic : 경기id(key) : 종목한글명(value)       // id(key) => 경기코드(key)
 * dateOfSports_dic     : 경기id(key) : 경기날짜(value)         // id(key) => 경기코드(key)
 * => @depends sport_dic: array_keys(종목이름)를 통해 종목id(x) 경기코드(o)를 가져옴
 * month_dic            : 1~12(key) : 최대일수(value)
 * level_dic            : 권한id(key) : 권한 상세 정의(한국어)(value)
 * level_dic_en            : 권한id(key) : 권한 상세 정의(영어)(value)
 */
$country_dic = [];
$country_code_dic = [];
$sport_dic = [];
$sector_dic = [];
$categoryOfSports_dic = [];
$dateOfSports_dic = [];
$month_dic = [];
$level_dic = [];
$level_dic_en = [];
$schedule_dic = [];

// "ALL" schedule_dic
$sql = "SELECT DISTINCT schedule_name, record_schedule_id FROM list_record LEFT JOIN list_schedule ON (schedule_id = record_schedule_id)";
$result = $db->query($sql);
while ($row = mysqli_fetch_array($result)) {
    $schedule_dic[$row["record_schedule_id"]] = $row["schedule_name"];
}
// "ALL" country_code_dic
$sql = "SELECT DISTINCT country_code,country_name_kr FROM list_country ORDER BY country_name_kr ASC";
$result = $db->query($sql);
while ($row = mysqli_fetch_array($result)) {
    $country_code_dic[$row["country_name_kr"]] = $row["country_code"];
}

// "ALL" sports_dic
/** 기존(code=>name_kr)을 새로운 요구사항(code=>name)으로 변경함 */
$sql = "SELECT DISTINCT sports_code,sports_name FROM list_sports ORDER BY FIELD(sports_name,'100m','100m Hurdles','110m Hurdles','200m','400m','400m Hurdles','800m','1500m','3000m','3000m Hurdles','3000m Steeplechase','5000m','10000m','4x100 Relay','4x400 Relay','Race Walk',
'Discus Throw','Javelin Throw','Shot Put','Hammer Throw','Long Jump','Triple Jump','Long Jump','High Jump','Pole Vault','Heptathlon','Decathlon')";
$result = $db->query($sql);
while ($row = mysqli_fetch_array($result)) {
    $sport_dic[$row["sports_code"]] = $row["sports_name"];
}

// "ALL" sector_dic
$sector_dic = ["track-area" => "track-area", "field-area" => "field-area", "warm-up-area" => "warm-up-area"];

// "ALL" categorty of sports dic
$sql = "SELECT DISTINCT sports_code,sports_category FROM list_sports ORDER BY FIELD(sports_name,
'100m','100m Hurdles','110m Hurdles','200m','400m','400m Hurdles',
'800m','1500m','3000m','3000m Hurdles','3000m Steeplechase','5000m','10000m',
'4x100 Relay','4x400 Relay','Race Walk','Discus Throw','Javelin Throw',
'Shot Put','Hammer Throw','Long Jump','Triple Jump','Long Jump',
'High Jump','Pole Vault');";
// $sql = "SELECT DISTINCT sports_code,sports_category FROM list_sports ORDER BY FIELD(sports_name_kr,'100m','100m 허들','110m 허들','200m','400m','400m 허들','800m','1500m','3000m','3000m 허들','3000m 장애물','5000m','10000m','4x100 릴레이','4x400 릴레이','경보',
// '원반던지기','창던지기','투포환','해머던지기','멀리뛰기','세단뛰기','멀리뛰기','높이뛰기','장대 높이뛰기','7종경기(여)','10종경기(남)')";
$result = $db->query($sql);
while ($row = mysqli_fetch_array($result)) {
    $categoryOfSports_dic[$row["sports_code"]] = $row["sports_category"];
}

// "ALL" date of sports dic
$sql = "SELECT DISTINCT schedule_sports,schedule_date FROM list_schedule";
$result = $db->query($sql);
while ($row = mysqli_fetch_array($result)) {
    $dateOfSports_dic[$row["schedule_sports"]] = $row["schedule_date"];
}
// "ALL" month(key) => Maximum day(value)
$month_dic = [
    "1" => 31,
    "01" => 31,
    "2" => 28,
    "02" => 28,
    "3" => 31,
    "03" => 31,
    "4" => 30,
    "04" => 30,
    "5" => 31,
    "05" => 31,
    "6" => 30,
    "06" => 30,
    "7" => 31,
    "07" => 31,
    "8" => 31,
    "08" => 31,
    "9" => 30,
    "09" => 30,
    "10" => 31,
    "11" => 30,
    "12" => 31
];
// level_dic
$level_dic = [
    "authEntrysRead" => "참가자 읽기",
    "authEntrysUpdate" => "참가자 수정",
    "authEntrysDelete" => "참가자 삭제",
    "authEntrysCreate" => "참가자 등록",
    "authSchedulesRead" => "경기 읽기",
    "authSchedulesUpdate" => "경기 수정",
    "authSchedulesDelete" => "경기 삭제",
    "authSchedulesCreate" => "경기 생성",
    "authRecordsRead" => "기록 읽기",
    "authRecordsUpdate" => "기록 수정",
    "authRecordsDelete" => "기록 삭제",
    "authRecordsCreate" => "기록 등록",
    "authStaticsRead" => "통계 읽기",
    "authStaticsUpdate" => "통계 수정",
    "authStaticsDelete" => "통계 삭제",
    "authStaticsCreate" => "통계 등록",
    "authAccountsRead" => "계정 읽기",
    "authAccountsUpdate" => "계정 수정",
    "authAccountsDelete" => "계정 삭제",
    "authAccountsCreate" => "계정 등록"
];

// level_dic_eng
$level_dic_en = [
    "authEntrysRead" => "Entry Read",
    "authEntrysUpdate" => "Entry Update",
    "authEntrysDelete" => "Entry Delete",
    "authEntrysCreate" => "Entry Create",
    "authSchedulesRead" => "Schedule Read",
    "authSchedulesUpdate" => "Schedule Update",
    "authSchedulesDelete" => "Schedule Delete",
    "authSchedulesCreate" => "Schedule Create",
    "authRecordsRead" => "Record Read",
    "authRecordsUpdate" => "Record Update",
    "authRecordsDelete" => "Record Delete",
    "authRecordsCreate" => "Record Create",
    "authStaticsRead" => "Statistics Read",
    "authStaticsUpdate" => "Statistics Update",
    "authStaticsDelete" => "Statistics Delete",
    "authStaticsCreate" => "Statistics Create",
    "authAccountsRead" => "Account Read",
    "authAccountsUpdate" => "Account Update",
    "authAccountsDelete" => "Account Delete",
    "authAccountsCreate" => "Account Create"
];



/**
 * @category 참가자 > 코치진에 관한 dictionary
 * coach_country_dic    : 국가한글명(key) : 국가코드(value)
 * => used as? :: 국가한글명 입력값에 대한 DB 처리
 * coach_region_dic     : num++(key) : 지역명(value)
 * coach_division_dic   : num++(key) : 소속명(value)
 * coach_gender_dic     : num++(key) : 성별(value)
 * coach_duty_dic       : num++(key) : 직무명(value)            
 * @category 참가자 > 선수진에 관한 dictionary
 * athlete_country_dic    : 국가한글명(key) : 국가코드(value)
 * => used as? :: 국가한글명 입력값에 대한 DB 처리
 * athlete_region_dic     : num++(key) : 지역명(value)
 * athlete_division_dic   : num++(key) : 소속명(value)
 * athlete_gender_dic     : num++(key) : 성별(value)           
 * @category 참가자 > 심판진에 관한 dictionary
 * judge_country_dic    : 국가한글명(key) : 국가코드(value)
 * => used as? :: 국가한글명 입력값에 대한 DB 처리
 * judge_division_dic   : num++(key) : 소속명(value)
 * judge_gender_dic     : num++(key) : 성별(value)
 * judge_duty_dic       : num++(key) : 직무명(value)            
 * @category 참가자 > 임원진에 관한 dictionary
 * director_country_dic    : 국가한글명(key) : 국가코드(value)
 * => used as? :: 국가한글명 입력값에 대한 DB 처리
 * director_division_dic   : num++(key) : 소속명(value)
 * director_gender_dic     : num++(key) : 성별(value)
 * director_duty_dic       : num++(key) : 직무명(value) 
 * worldrecord_gender_dic  : num++(key) : 성별(value) + 혼성    
 * worldrecord_athletics_dic  : 기록 약자(key) : 기록 풀네임(value)
 */
$coach_country_dic = [];
$coach_region_dic = [];
$coach_division_dic = [];
$coach_gender_dic = [];
$coach_duty_dic = [];

$athlete_country_dic = [];
$athlete_region_dic = [];
$athlete_division_dic = [];
$athlete_gender_dic = [];

$judge_country_dic = [];
$judge_region_dic = [];
$judge_division_dic = [];
$judge_gender_dic = [];
$judge_duty_dic = [];

$director_country_dic = [];
$director_region_dic = [];
$director_division_dic = [];
$director_gender_dic = [];
$director_duty_dic = [];

$worldrecord_gender_dic = [];
$worldrecord_athletics_dic = [];

$schedule_gender_dic = [];

// "COACH" country_dic
$sql = "SELECT DISTINCT coach_country,country_name_kr FROM list_coach INNER JOIN list_country ON coach_country=country_code ORDER BY country_name_kr ASC";
$result = $db->query($sql);
while ($row = mysqli_fetch_array($result)) {
    $coach_country_dic[$row["country_name_kr"]] = $row["coach_country"];
}
// "COACH" region_dic
$sql = "SELECT DISTINCT coach_region FROM list_coach";
$result = $db->query($sql);
$num = 0;
while ($row = mysqli_fetch_array($result)) {
    $num++;
    $coach_region_dic[$num] = $row["coach_region"];
}
// "COACH" division_dic
$sql = "SELECT DISTINCT coach_division FROM list_coach";
$result = $db->query($sql);
$num = 0;
while ($row = mysqli_fetch_array($result)) {
    $num++;
    $coach_division_dic[$num] = $row["coach_division"];
}
// "COACH" gender_dic
$sql = "SELECT DISTINCT coach_gender FROM list_coach";
$result = $db->query($sql);
$num = 0;
while ($row = mysqli_fetch_array($result)) {
    $num++;
    $coach_gender_dic[$num] = $row["coach_gender"];
}

// "COACH" duty_dic
// 연맹 측에서 coach 직무 던져주면 배열에 삽입
$coach_duty_dic = ["track" => "track", "field" => "field"];

// "ATHLETE" country_dic
$sql = "SELECT DISTINCT athlete_country,country_name_kr FROM list_athlete INNER JOIN list_country ON athlete_country=country_code ORDER BY country_name_kr ASC";
$result = $db->query($sql);
while ($row = mysqli_fetch_array($result)) {
    $athlete_country_dic[$row["country_name_kr"]] = $row["athlete_country"];
}
// "ATHLETE" region_dic
$sql = "SELECT DISTINCT athlete_region FROM list_athlete";
$result = $db->query($sql);
$num = 0;
while ($row = mysqli_fetch_array($result)) {
    $num++;
    $athlete_region_dic[$num] = $row["athlete_region"];
}
// "ATHLETE" division_dic
$sql = "SELECT DISTINCT athlete_division FROM list_athlete";
$result = $db->query($sql);
$num = 0;
while ($row = mysqli_fetch_array($result)) {
    $num++;
    $athlete_division_dic[$num] = $row["athlete_division"];
}
// "ATHLETE" gender_dic
$sql = "SELECT DISTINCT athlete_gender FROM list_athlete";
$result = $db->query($sql);
$num = 0;
while ($row = mysqli_fetch_array($result)) {
    $num++;
    $athlete_gender_dic[$num] = $row["athlete_gender"];
}

// "JUDGE" country_dic
$sql = "SELECT DISTINCT judge_country,country_name_kr FROM list_judge INNER JOIN list_country ON judge_country=country_code ORDER BY country_name_kr ASC";
$result = $db->query($sql);
while ($row = mysqli_fetch_array($result)) {
    $judge_country_dic[$row["country_name_kr"]] = $row["judge_country"];
}
// "JUDGE" division_dic
$sql = "SELECT DISTINCT judge_division FROM list_judge";
$result = $db->query($sql);
$num = 0;
while ($row = mysqli_fetch_array($result)) {
    $num++;
    $judge_division_dic[$num] = $row["judge_division"];
}
// "JUDGE" sports_dic
$sql = "SELECT DISTINCT sports_code,sports_name FROM list_sports ORDER BY FIELD(sports_name,
'100m','100m Hurdles','110m Hurdles','200m','400m','400m Hurdles',
'800m','1500m','3000m','3000m Hurdles','3000m Steeplechase','5000m','10000m',
'4x100 Relay','4x400 Relay','Race Walk','Discus Throw','Javelin Throw',
'Shot Put','Hammer Throw','Long Jump','Triple Jump','Long Jump',
'High Jump','Pole Vault');";
$result = $db->query($sql);
while ($row = mysqli_fetch_array($result)) {
    if (($row["sports_code"] == "heptathlon") || ($row["sports_code"] == "decathlon")) {
        continue;
    }
    $judge_sport_dic[$row["sports_code"]] = $row["sports_name"];
}
$judge_sport_dic["heptathlon-100mh"] = "Heptathlon-100m Hurdles";
$judge_sport_dic["heptathlon-highjump"] = "Heptathlon-High Jump";
$judge_sport_dic["heptathlon-shotput"] = "Heptathlon-Shotput";
$judge_sport_dic["heptathlon-200m"] = "Heptathlon-200m";
$judge_sport_dic["heptathlon-longjump"] = "Heptathlon-Long Jump";
$judge_sport_dic["heptathlon-javelinthrow"] = "Heptathlon-Jevelin Throw";
$judge_sport_dic["heptathlon-800m"] = "Heptathlon-800m";
$judge_sport_dic["decathlon-100m"] = "Decathlon-100m";
$judge_sport_dic["decathlon-highjump"] = "Decathlon-High Jump";
$judge_sport_dic["decathlon-shotput"] = "Decathlon-Shotput";
$judge_sport_dic["decathlon-400m"] = "Decathlon-400m";
$judge_sport_dic["decathlon-110mh"] = "Decathlon-110m Hurdles";
$judge_sport_dic["decathlon-discusthrow"] = "Decathlon-Discus Throw";
$judge_sport_dic["decathlon-polevault"] = "Decathlon-Pole Vault";
$judge_sport_dic["decathlon-javelinthrow"] = "Decathlon-Javelin Throw";
$judge_sport_dic["decathlon-1500m"] = "Decathlon-1500m";

// "JUDGE" gender_dic
$sql = "SELECT DISTINCT judge_gender FROM list_judge";
$result = $db->query($sql);
$num = 0;
while ($row = mysqli_fetch_array($result)) {
    $num++;
    $judge_gender_dic[$num] = $row["judge_gender"];
}
// "JUDGE" duty_dic
$sql = "SELECT DISTINCT judge_duty FROM list_judge";
$result = $db->query($sql);
$num = 0;
while ($row = mysqli_fetch_array($result)) {
    $num++;
    $judge_duty_dic[$num] = $row["judge_duty"];
}

// "DIRECTOR" country_dic
$sql = "SELECT DISTINCT director_country,country_name_kr FROM list_director INNER JOIN list_country ON director_country=country_code ORDER BY country_name_kr ASC";
$result = $db->query($sql);
while ($row = mysqli_fetch_array($result)) {
    $director_country_dic[$row["country_name_kr"]] = $row["director_country"];
}
// "DIRECTOR" division_dic
$sql = "SELECT DISTINCT director_division FROM list_director";
$result = $db->query($sql);
$num = 0;
while ($row = mysqli_fetch_array($result)) {
    $num++;
    $director_division_dic[$num] = $row["director_division"];
}
// "DIRECTOR" gender_dic
$sql = "SELECT DISTINCT director_gender FROM list_director";
$result = $db->query($sql);
$num = 0;
while ($row = mysqli_fetch_array($result)) {
    $num++;
    $director_gender_dic[$num] = $row["director_gender"];
}

// "DIRECTOR" duty_dic
// 연맹 측에서 director 직무 던져주면 배열에 삽입
$director_duty_dic = ["chairman" => "chairman", "president" => "president"];

// "worldrecord" gender_dic
$sql = "SELECT DISTINCT worldrecord_gender FROM list_worldrecord";
$result = $db->query($sql);
$num = 0;
while ($row = mysqli_fetch_array($result)) {
    $num++;
    $worldrecord_gender_dic[$num] = $row["worldrecord_gender"];
}
// "worldrecord" athletics_dic
$sql = "SELECT DISTINCT worldrecord_athletics FROM list_worldrecord";
$result = $db->query($sql);
$num = 0;
while ($row = mysqli_fetch_array($result)) {
    $num++;
    $worldrecord_athletics_dic[$num] = $row["worldrecord_athletics"];
}

// "schedule" gender_dic
$sql = "SELECT DISTINCT schedule_gender FROM list_schedule";
$result = $db->query($sql);
$num = 0;
while ($row = mysqli_fetch_array($result)) {
    $num++;
    $schedule_gender_dic[$num] = $row["schedule_gender"];
}