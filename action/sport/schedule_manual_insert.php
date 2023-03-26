<?php
require_once __DIR__ . "/../../console_log.php";
require_once __DIR__ . "/../../includes/auth/config.php";
require_once __DIR__ . "/../../security/security.php";
global $db;

// $id = cleanInput($_POST["id"]);
$athlete = $_POST["player_id"] ?? null;             // array: 참가하는 선수들 id
$lane = $_POST["lane"] ?? null;                     // array: 선수별 순서 or 레인
$group = $_POST["group"] ?? null;                   // array: 선수 별 그룹 배정
$sports = $_POST["sport_code"] ?? null;             // string: 스포츠 종목 명 (cleanInput 사용 시 4mr만 남음)
$round = cleanInput($_POST["round"]);               // string: 라운드(영어)
$gender = cleanInput($_POST["gender"]);             // string: 경기 성별
$count = cleanInput($_POST["count"]);               // string: 조 개수
$category = cleanInput($_POST["sport_category"]);   // string: 스포츠 카테고리

if (isset($_POST["order"])) {
    // 릴레이 경기 일때 해당
    $order = $_POST["order"];
}
if (!isset($athlete, $lane, $group, $round, $gender, $count, $sports, $category)) {
    // 필수 적인 값들이 안들어오면 창 종료
    mysqli_close($db);
    exit('<script>alert("모두 입력하세요.");history.back();</script>');
}

// 중복 생성 확인 코드
$schedule_result = $db->query("select * from list_record where record_sports='$sports' and record_round='$round' and record_gender='$gender'");
if (mysqli_num_rows($schedule_result) != 0) {
    mysqli_close($db);
    exit('<script>alert("해당 종목과 라운드에 대한 조가 이미 생성되어있습니다."); window.close();</script>');
}

// 선수 입력 값 제거하는 코드
foreach ($athlete as $index => $value) {
    if ($value === "") {
        // 만약 선수 id 값이 "" 이면 group, athlete, lane에 해당하는 배열 전부 삭제
        unset($athlete[$index]);
        unset($group[$index]);
        unset($lane[$index]);
    }
}

// 인덱스 가 순차적이지 않으므로 reindexing
$athlete = array_values($athlete);
$group = array_values($group);
$lane = array_values($lane);

// $start = $date = date("Y-m-d");
//$id = array();
//$lane = array();
//$group = array();
//$order = array();
$round_count = 0;

//@Potatoeunbi
//32~46line
//입력한 선수들 모두 배열에 넣어서 중복 있는지 확인
for ($athlete_count = 0; $athlete_count < count($athlete); $athlete_count++) {
    $athlete_ids[] = $athlete[$athlete_count];
}
// print_r($athlete_ids);
$ac = array_replace($athlete_ids, array_fill_keys(array_keys($athlete_ids, null), ''));
$arr = array_count_values($ac);
$filter_result = array_filter($arr, "isOne");

if (!empty($filter_result)) {
    echo "<script>alert('중복된 선수가 있습니다.'); history.back();  </script>";
    exit;
}
function isOne($val)
{
    return $val != 1;
}

function isfour($val)
{
    return $val != 4;
}

//@Potatoeunbi
//schedule_sports, category 알기 위한 sql문
//$sql = "SELECT *,(SELECT sports_category FROM list_sports WHERE schedule_sports=sports_code)AS category FROM list_schedule WHERE schedule_id='" . $id . "';";
//$result = $db->query($sql);
//$row = mysqli_fetch_array($result);
//$sports = $row['schedule_sports'];
//$category = $row['category'];

//@Potatoeunbi
//64~88line
//릴레이 경기일 때, 해당 팀의 국가가 모두 같지 않을 경우 확인
if ($sports == '4x400mR' || $sports == '4x100mR') {
    for ($idx = 0; $idx < count($athlete); $idx++) {  //선수 수만큼
        $relay_id[] = $athlete[$idx];
        if (((int)$order[$idx]) == 4) {
            for ($relay_count = 0; $relay_count < 4; $relay_count++) {

                $countrysql = "SELECT athlete_country from list_athlete WHERE athlete_id='" . $relay_id[$relay_count] . "'";
                $countryresult = $db->query($countrysql);
                $countryrow = mysqli_fetch_array($countryresult);
                $athlete_countrys[] = $countryrow['athlete_country'];
            }
            $cc = array_replace($athlete_countrys, array_fill_keys(array_keys($athlete_countrys, null), ''));
            $carr = array_count_values($cc);
            $filter_result = array_filter($carr, "isfour");
            $athlete_countrys = array();
            $relay_id = array();
            if (!empty($filter_result)) {
                echo "<script>alert('팀 구성원들의 국가가 올바르지 않습니다.');  history.back(); </script>";
                exit;
            }
        }
    }
}

//@Potatoeunbi
//93~150line
//schedule 생성
// "소분류 일정 생성"
$insert_sub_query = "INSERT INTO `list_schedule` (`schedule_sports`, `schedule_name`, `schedule_gender`, `schedule_round`, `schedule_group`, `schedule_division`, `schedule_location`, `schedule_start`, `schedule_date`) VALUES (?,?,?,?,?,?,?,?,?)";
$round_ten = array('100m', '400m', '110mh', '1500m', 'highjump', 'polevault', 'final', 'longjump', 'shotput', 'discusthrow', 'javelinthrow');
$round_seven = array('100mh', '200m', '800m', 'final', 'highjump', 'shotput', 'longjump', 'javelinthrow');
try {
    //@Potatoeunbi
    //154 ~ 251 line
    //record 생성
    if (($category == '트랙경기' && ($sports != '4x400mR' && $sports != '4x100mR')) || $sports == 'highjump' || $sports == 'polevault') {
        //릴레이 경기가 아닌 트랙경기와 높이뛰기, 장대높이뛰기인 경우
        for ($idx = 0; $idx < count($athlete); $idx++) {  //선수 수만큼
            $athlete_data = [
                $athlete[$idx], // 0: record_athlete_id
                $lane[$idx],    // 1: record_order
                $group[$idx],   // 2: record_group
                $sports,        // 3: record_sports
                $round,         // 4: record_round
                $gender,        // 5: record_gender
                0               // 6: record_medal
                // $schedule_ids[$group[$idx] - 1]['schedule_id'], // 1: record_schedule_id
            ];
            $insert_record_query = "INSERT INTO `list_record` (`record_athlete_id`, `record_order`, `record_group`, `record_sports`, `record_round`, `record_gender`, `record_medal`) VALUES (?,?,?,?,?,?,?)";
            $stmt = $db->prepare($insert_record_query);
            $stmt->bind_param("iiisssi", ...$athlete_data);
            $stmt->execute();
            $stmt->close();
        }
    } else if ($category == '필드경기' && ($sports != 'highjump' || $sports != 'polevault')) {
        //높이뛰기, 장대높이뛰기가 아닌 필드경기인 경우
        $MAX_TRIAL = in_array($sports, ["decathlon", "heptathlon"]) ? 3 : 6;
        for ($idx = 0; $idx < count($athlete); $idx++) {
            for ($trial = 1; $trial <= $MAX_TRIAL; $trial++) {  //선수 수만큼
                $athlete_data = [
                    $athlete[$idx], // 0: record_athlete_id
                    $lane[$idx],    // 1: record_order
                    $group[$idx],   // 2: record_group
                    $sports,        // 3: record_sports
                    $round,         // 4: record_round
                    $gender,        // 5: record_gender
                    0,              // 6: record_medal
                    $trial         // 7: record_trial
                    // $schedule_ids[$group[$idx] - 1]['schedule_id'], // 1: record_schedule_id
                ];
                $insert_record_query = "INSERT INTO `list_record` (`record_athlete_id`, `record_order`, `record_group`, `record_sports`, `record_round`, `record_gender`, `record_medal`,`record_trial`) VALUES (?,?,?,?,?,?,?,?)";
                $stmt = $db->prepare($insert_record_query);
                $stmt->bind_param("iiisssii", ...$athlete_data);
                $stmt->execute();
                $stmt->close();
            }
        }
    } else if ($sports == '4x400mR' || $sports == '4x100mR') {
        //릴레이 경기일 경우
        $relay_lane = 0;
        for ($idx = 0; $idx < count($athlete); $idx++) {  //선수 수만큼
            if (((int)$order[$idx]) % 4 == 1) {
                $relay_lane = $lane[$idx];
            }
            $athlete_data = [
                $athlete[$idx],             // 0: record_athlete_id
                $relay_lane,                // 1: record_order
                $idx % 4 + 1,               // 2: record_team_order
                $group[$idx],               // 3: record_group
                $sports,                    // 4: record_sports
                $round,                     // 5: record_round
                $gender,                    // 6: record_gender
                0                           // 7: record_medal
                // $schedule_ids[$group[$idx] - 1]['schedule_id'], // 1: record_schedule_id
            ];
            $insert_record_query = "INSERT INTO `list_record` (`record_athlete_id`, `record_order`, `record_team_order`, `record_group`, `record_sports`, `record_round`, `record_gender`, `record_medal`) VALUES (?,?,?,?,?,?,?,?)";
            $stmt = $db->prepare($insert_record_query);
            $stmt->bind_param("iiiisssi", ...$athlete_data);
            $stmt->execute();
            $stmt->close();
        }
    }

    if (in_array($sports, ["decathlon", "heptathlon"])) {
        // 종합경기 (7종, 10종)은 선수들의 종합 점수 넣는 record 칸이 존재해야한다.
        $query = 'SELECT COUNT(*) AS count FROM list_record WHERE record_round = "final" AND record_sports = ? AND record_gender = ?';
        $stmt = $db->prepare($query);
        $stmt->bind_param("ss", $sports, $gender);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        print_r($result);
        if ($result["count"] === 0) {
            // 점수 넣는 칸이 생성되어 있지 않으면 생성
            $insert_query = 'INSERT INTO `list_record` (`record_athlete_id`, `record_round`, `record_sports`, `record_gender`) VALUES (?,?,?,?)';
            for ($i = 0; $i < count($athlete); $i++) {
                $insert_data = [
                    $athlete[$i],   // 0: record_athlete_id
                    "final",        // 1: record_round
                    $sports,        // 2: record_sports
                    $gender         // 3: record_gender
                ];
                $stmt = $db->prepare($insert_query);
                $stmt->bind_param("isss", ...$insert_data);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
} catch (Exception $e) {
    //@Potatoeunbi
    //오류 발생시 rollback 하기 위해서 try~catch문 사용
    echo $e->getMessage();
    $db->rollback();
    echo "<script>alert('이미 생성된 경기 또는 기록이 있습니다.'); history.back();</script>";
    exit();
}

echo "<script>alert('생성되었습니다.'); window.close(); </script>";


/** Archive */
//@Potatoeunbi
//종합경기일 경우와 아닌 경우 구분해서 schedule 생성해야 함.
//    if ($sports == 'decathlon' || $sports == 'heptathlon') {
//        ($sports == 'decathlon') ? $round_count = 11 : $round_count = 8;
//        for ($group_num = 0; $group_num < $round_count; $group_num++) {
//            $sub_division_data = [
//                $row['schedule_sports'],
//                $row['schedule_name'],
//                $row['schedule_gender'],
//                ($sports == 'decathlon') ? $round_ten[$group_num] : $round_seven[$group_num],
//                1,
//                's',
//                $row['schedule_location'],
//                $start,
//                $date
//            ];
//            $stmt = $db->prepare($insert_sub_query);
//            $stmt->bind_param('ssssissss', ...$sub_division_data);
//            $stmt->execute();
//            $stmt->close();
//            // 소분류 schedule_id 생성 후 저장
//            $sub_division_query = "SELECT schedule_id FROM list_schedule WHERE schedule_sports = ? AND schedule_name = ? AND schedule_gender = ? AND schedule_round = ? AND schedule_group = ? AND schedule_division = ? AND schedule_location = ? AND schedule_start = ? AND schedule_date = ?";
//            $stmt = $db->prepare($sub_division_query);
//            $stmt->bind_param("ssssissss", ...$sub_division_data);
//            $stmt->execute();
//            $data = $stmt->get_result()->fetch_array(MYSQLI_ASSOC);
//            $schedule_ids[] = $data;
//        }
//    } else {
//        for ($group_num = 1; $group_num <= $count; $group_num++) {
//            $sub_division_data = [
//                $row['schedule_sports'],
//                $row['schedule_name'],
//                $row['schedule_gender'],
//                $row['schedule_round'],
//                $group_num,
//                's',
//                $row['schedule_location'],
//                $start,
//                $date
//            ];
//            $stmt = $db->prepare($insert_sub_query);
//            $stmt->bind_param('ssssissss', ...$sub_division_data);
//            $stmt->execute();
//            $stmt->close();
//            // 소분류 schedule_id 생성 후 저장
//            $sub_division_query = "SELECT schedule_id FROM list_schedule WHERE schedule_sports = ? AND schedule_name = ? AND schedule_gender = ? AND schedule_round = ? AND schedule_group = ? AND schedule_division = ? AND schedule_location = ? AND schedule_start = ? AND schedule_date = ?";
//            $stmt = $db->prepare($sub_division_query);
//            $stmt->bind_param("ssssissss", ...$sub_division_data);
//            $stmt->execute();
//            $data = $stmt->get_result()->fetch_array(MYSQLI_ASSOC);
//            $schedule_ids[] = $data;
//        }
//    }

//} else if ($sports == 'decathlon' || $sports == 'heptathlon') {
    //종합 경기일 경우 (현재 구현한걸로는 위에있는 필드 트랙만 사용해도 됨
    //        ($sports == 'decathlon') ? $overall_require = 6 : $overall_require = 4; // 필드 경기 개수 (필드 경기는 3회의 시도가 주어지기 때문에, 3번 insert 해야함
    //        ($sports == 'decathlon') ? $round_count = 11 : $round_count = 8;
    //        $all_round_sports = ["decathlon" => ["100m", "longjump", "shotput", "highjump", "400m", "110mh", "discusthrow", "polevault", "javelinthrow", "1500m"],
    //                             "heptathlon"=> ["100mh", "highjump", "shotput", "200m", "longjump", "javelinthrow", "800m"]];
    //        for ($idx = 0; $idx < count($athlete); $idx++) { //선수 수만큼
    //            for ($overall = 0; $overall < $round_count; $overall++) {
    //                if ($overall > $overall_require) {
    //                    for ($trial = 1; $trial <= 3; $trial++) {
    //                        // 필드 경기 INSERT
    //                        // 선수 수만큼*10종 경기*3회 시도하는 것들은 3개
    //                        $athlete_data = [
    //                            $athlete[$idx],                         // 0: record_athlete_id
    //                            // $schedule_ids[$group[$idx] - 1]['schedule_id'], // 1: record_schedule_id
    //                            $idx + 1,                               // 1: record_order
    //                            $group[$idx],                           // 2: record_group
    //                            $sports,                                // 3: record_sports
    //                            $all_round_sports[$sports][$overall],   // 4: record_round
    //                            $gender,                                // 5: record_gender
    //                            0,                                      // 6: record_medal
    //                            $trial,                                 // 7: record_trial
    //                        ];
    //                        $insert_record_query = "INSERT INTO `list_record` (`record_athlete_id`, `record_schedule_id`, `record_medal`, `record_order`, `record_trial`, `record_judge`) VALUES (?,?,?,?,?,?)";
    //                        $stmt = $db->prepare($insert_record_query);
    //                        $stmt->bind_param("iiiiii", ...$athlete_data);
    //                        $stmt->execute();
    //                        $stmt->close();
    //                    }
    //                } else {
    //                    // 트랙 경기 INSERT
    //                    $athlete_data = [
    //                        $athlete[$idx],            // 0: record_athlete_id
    //                        $schedule_ids[$overall]['schedule_id'], // 1: record_schedule_id
    //                        0,                                      // 2: record_medal
    //                        $lane[$idx],               // 3: record_order
    //                        1
    //                    ];
    //                    $insert_record_query = "INSERT INTO `list_record` (`record_athlete_id`, `record_schedule_id`, `record_medal`, `record_order`, `record_judge`) VALUES (?,?,?,?,?)";
    //                    $stmt = $db->prepare($insert_record_query);
    //                    $stmt->bind_param("iiiii", ...$athlete_data);
    //                    $stmt->execute();
    //                    $stmt->close();
    //                }
    //            }
    //        }
//}