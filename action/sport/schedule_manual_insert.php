<?php
include_once(__DIR__ . "/../../includes/auth/config.php");
include_once(__DIR__ . "/../../security/security.php");

if (!isset($_POST['id']) || $_POST['id'] == "" || !isset($_POST['lane']) || $_POST['lane'] == "" || !isset($_POST['playerid']) || $_POST['playerid'] == "" || !isset($_POST['group']) || $_POST['group'] == "" || !isset($_POST['count']) || $_POST['count'] == "") {
    mysqli_close($db);
    echo '<script>alert("모두 입력하세요.");history.back();</script>';
    exit;
}
$id = array();
$lane = array();
$athlete = array();
$group = array();
$order = array();

$id = cleanInput($_POST["id"]);
$lane = $_POST["lane"];
$athlete = $_POST["playerid"];
$group = $_POST["group"]; //조
$round_count = 0;

$count = cleanInput($_POST["count"]);
$start = $date = date("Y-m-d");

if (isset($_POST["order"])) {
    $order = $_POST["order"];
}

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
$sql = "SELECT *,(SELECT sports_category FROM list_sports WHERE schedule_sports=sports_code)AS category FROM list_schedule WHERE schedule_id='" . $id . "';";
$result = $db->query($sql);
$row = mysqli_fetch_array($result);
$sports = $row['schedule_sports'];
$category = $row['category'];


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
$insert_sub_query = "INSERT INTO `list_schedule` (`schedule_sports`, `schedule_name`, `schedule_gender`, `schedule_round`, `schedule_group`, `schedule_division`, `schedule_location`, `schedule_start`, `schedule_date`) VALUES (?,?,?,?,?,?,?,?,?)";
$round_ten = array('100m', '400m', '110mh', '1500m', 'highjump', 'polevault', 'final', 'longjump', 'shotput', 'discusthrow', 'javelinthrow');
$round_seven = array('100mh', '200m', '800m', 'final', 'highjump', 'shotput', 'longjump', 'javelinthrow');
try {
    //@Potatoeunbi
    //종합경기일 경우와 아닌 경우 구분해서 schedule 생성해야 함.
    if ($sports == 'decathlon' || $sports == 'heptathlon') {
        ($sports == 'decathlon') ? $round_count = 11 : $round_count = 8;
        for ($group_num = 0; $group_num < $round_count; $group_num++) {
            $sub_division_data = [
                $row['schedule_sports'],
                $row['schedule_name'],
                $row['schedule_gender'],
                ($sports == 'decathlon') ? $round_ten[$group_num] : $round_seven[$group_num],
                1,
                's',
                $row['schedule_location'],
                $start,
                $date
            ];
            $stmt = $db->prepare($insert_sub_query);
            $stmt->bind_param('ssssissss', ...$sub_division_data);
            $stmt->execute();
            $stmt->close();
            // 소분류 schedule_id 생성 후 저장
            $sub_division_query = "SELECT schedule_id FROM list_schedule WHERE schedule_sports = ? AND schedule_name = ? AND schedule_gender = ? AND schedule_round = ? AND schedule_group = ? AND schedule_division = ? AND schedule_location = ? AND schedule_start = ? AND schedule_date = ?";
            $stmt = $db->prepare($sub_division_query);
            $stmt->bind_param("ssssissss", ...$sub_division_data);
            $stmt->execute();
            $data = $stmt->get_result()->fetch_array(MYSQLI_ASSOC);
            $schedule_ids[] = $data;
        }
    } else {
        for ($group_num = 1; $group_num <= $count; $group_num++) {
            $sub_division_data = [
                $row['schedule_sports'],
                $row['schedule_name'],
                $row['schedule_gender'],
                $row['schedule_round'],
                $group_num,
                's',
                $row['schedule_location'],
                $start,
                $date
            ];
            $stmt = $db->prepare($insert_sub_query);
            $stmt->bind_param('ssssissss', ...$sub_division_data);
            $stmt->execute();
            $stmt->close();
            // 소분류 schedule_id 생성 후 저장
            $sub_division_query = "SELECT schedule_id FROM list_schedule WHERE schedule_sports = ? AND schedule_name = ? AND schedule_gender = ? AND schedule_round = ? AND schedule_group = ? AND schedule_division = ? AND schedule_location = ? AND schedule_start = ? AND schedule_date = ?";
            $stmt = $db->prepare($sub_division_query);
            $stmt->bind_param("ssssissss", ...$sub_division_data);
            $stmt->execute();
            $data = $stmt->get_result()->fetch_array(MYSQLI_ASSOC);
            $schedule_ids[] = $data;
        }
    }

    //@Potatoeunbi
    //154 ~ 251 line
    //record 생성

    //릴레이 경기가 아닌 트랙경기와 높이뛰기, 장대높이뛰기인 경우
    if (($category == '트랙경기' && ($sports != '4x400mR' && $sports != '4x100mR')) || $sports == 'highjump' || $sports == 'polevault') {
        for ($idx = 0; $idx < count($athlete); $idx++) {  //선수 수만큼
            $athlete_data = [
                $athlete[$idx],          // 0: record_athlete_id
                $schedule_ids[$group[$idx] - 1]['schedule_id'], // 1: record_schedule_id
                0,                                      // 2: record_medal
                $lane[$idx],               // 3: record_order
                1
            ];
            $insert_record_query = "INSERT INTO `list_record` (`record_athlete_id`, `record_schedule_id`, `record_medal`, `record_order`, `record_judge`) VALUES (?,?,?,?,?)";
            $stmt = $db->prepare($insert_record_query);
            $stmt->bind_param("iiiii", ...$athlete_data);
            $stmt->execute();
            $stmt->close();
        }
    }
    //높이뛰기, 장대높이뛰기가 아닌 필드경기인 경우
    else if ($category == '필드경기' && ($sports != 'highjump' || $sports != 'polevault')) {

        for ($idx = 0; $idx < count($athlete); $idx++) {
            for ($trial = 1; $trial <= 6; $trial++) {  //선수 수만큼
                $athlete_data = [
                    $athlete[$idx],            // 0: record_athlete_id
                    $schedule_ids[$group[$idx] - 1]['schedule_id'], // 1: record_schedule_id
                    0,                                      // 2: record_medal
                    $lane[$idx],               // 3: record_order
                    $trial,            // 4: record_trial
                    1
                ];
                $insert_record_query = "INSERT INTO `list_record` (`record_athlete_id`, `record_schedule_id`, `record_medal`, `record_order`, `record_trial`, `record_judge`) VALUES (?,?,?,?,?,?)";
                $stmt = $db->prepare($insert_record_query);
                $stmt->bind_param("iiiiii", ...$athlete_data);
                $stmt->execute();
                $stmt->close();
            }
        }

        //종합 경기일 경우
    } else if ($sports == 'decathlon' || $sports == 'heptathlon') {
        ($sports == 'decathlon') ? $overall_require = 6 : $overall_require = 4;
        for ($idx = 0; $idx < count($athlete); $idx++) { //선수 수만큼
            for ($overall = 0; $overall < $round_count; $overall++) {
                if ($overall > $overall_require) {
                    for ($trial = 1; $trial <= 3; $trial++) {  // 선수 수만큼*10종 경기*3회 시도하는 것들은 3개
                        $athlete_data = [
                            $athlete[$idx],           // 0: record_athlete_id
                            $schedule_ids[$overall]['schedule_id'], // 1: record_schedule_id
                            0,                                      // 2: record_medal
                            $lane[$idx],               // 3: record_order
                            $trial,            // 4: record_judge
                            1
                        ];
                        $insert_record_query = "INSERT INTO `list_record` (`record_athlete_id`, `record_schedule_id`, `record_medal`, `record_order`, `record_trial`, `record_judge`) VALUES (?,?,?,?,?,?)";
                        $stmt = $db->prepare($insert_record_query);
                        $stmt->bind_param("iiiiii", ...$athlete_data);
                        $stmt->execute();
                        $stmt->close();
                    }
                } else {
                    $athlete_data = [
                        $athlete[$idx],            // 0: record_athlete_id
                        $schedule_ids[$overall]['schedule_id'], // 1: record_schedule_id
                        0,                                      // 2: record_medal
                        $lane[$idx],               // 3: record_order
                        1
                    ];
                    $insert_record_query = "INSERT INTO `list_record` (`record_athlete_id`, `record_schedule_id`, `record_medal`, `record_order`, `record_judge`) VALUES (?,?,?,?,?)";
                    $stmt = $db->prepare($insert_record_query);
                    $stmt->bind_param("iiiii", ...$athlete_data);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }

        //릴레이 경기일 경우
    } else if ($sports == '4x400mR' || $sports == '4x100mR') {
        $relay_lane = 0;
        for ($idx = 0; $idx < count($athlete); $idx++) {  //선수 수만큼
            if (((int)$order[$idx]) % 4 == 1) {
                $relay_lane = $lane[$idx];
            }
            $athlete_data = [
                $athlete[$idx],          // 0: record_athlete_id
                $schedule_ids[$group[$idx] - 1]['schedule_id'], // 1: record_schedule_id
                0,                                      // 2: record_medal
                $relay_lane,               // 3: record_order
                $order[$idx],               // 4: record_judge
                1
            ];
            $insert_record_query = "INSERT INTO `list_record` (`record_athlete_id`, `record_schedule_id`, `record_medal`, `record_order`, `record_team_order`, `record_judge`) VALUES (?,?,?,?,?,?)";
            $stmt = $db->prepare($insert_record_query);
            $stmt->bind_param("iiiiii", ...$athlete_data);
            $stmt->execute();
        }
    }

    //@Potatoeunbi
    //오류 발생시 rollback 하기 위해서 try~catch문 사용
} catch (Exception $e) {
    echo $e->getMessage();
    $db->rollback();
    echo "<script>alert('이미 생성된 경기 또는 기록이 있습니다.'); history.back();</script>";
    exit();
}

echo "<script>alert('생성되었습니다.'); window.close(); </script>";