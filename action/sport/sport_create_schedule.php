<?php
require_once __DIR__ . "/../../includes/auth/config.php";
require_once __DIR__ . "/../../backheader.php";
// 로그 기능
if (!authCheck($db, "authSchedulesRead")) {
    exit("<script>
        alert('잘못된 접근입니다.');
        history.back();
    </script>");
}
if (!authCheck($db, "authSchedulesCreate")) {
    exit("<script>
            alert('잘못된 접근입니다.');
            history.back();
        </script>");
}
global $db;
const QUERY = "SELECT * FROM list_athlete; SELECT * FROM list_sports;";
$db->multi_query(QUERY);
$result = $db->store_result();
$athletes_data = $result->fetch_all(MYSQLI_ASSOC);
$db->next_result();
$schedule_dict = [];
$result = $db->store_result();
$sports_data = $result->fetch_all(MYSQLI_ASSOC);
$sports_dict = [];
$no_create_schedule = [];

/**
 * list_athlete의 athlete_schedule에 맞춰 schedule 생성하는 함수
 * 1. list_sports에 sports_named을 sports_name_kr로 변경해주는 배열을 생성한다.
 *                                 sports_name_kr => sports_name :: @vanillacake369(임지훈)
 * 2. list_ahtlete에 athelete_schedule을 참고하여 추가해야할 schedule을 생성한다.
 * 3. 생성한 schedule을 DB에 등록한다.
 */
if (count($athletes_data) != 0 && count($sports_data) != 0) {
    create_sports_dictionary($sports_data);
    create_schedule_dictionary($athletes_data);
    create_schedule();
    print_result();
    mysqli_close($db);
} else {
    echo '<script>alert("DB와의 연결이 원할하지 않습니다.");</script>';
}
/**
 * sports_code -> sports_name_kor로 변환해주는 dictionary를 생성하는 함수
 * @param array $sports_data list_sports에 있는 스포츠 정보
 * @return void
 */
function create_sports_dictionary(array $sports_data)
{
    global $sports_dict;
    foreach ($sports_data as $data) {
        $sports_dict[$data["sports_code"]] = $data["sports_name"];
        // $sports_dict[$data["sports_code"]] = $data["sports_name_kr"];
    }
}

/**
 * list_athlete의 athlete_schedule을 참고하여 list_schedule, list_record 생성을 도와주는 schedule_dict 생성 함수
 * 1. 선수의 athlete_schedule을 분리하여 배열에 저장한다.
 * 2. 분리한 schedule을 반목문을 돌려 schedule별로 선수의 데이터를 schedule_dict에 추가한다.
 * 3. 이때 400m 릴레이 경기는 혼성, 남, 여  3개가 있으므로 혼성은 따로 만들어 추가한다.
 * @param array $athletes_data list_athlete에 있는 선수 정보
 * @return void
 */
function create_schedule_dictionary(array $athletes_data)
{
    global $schedule_dict;
    foreach ($athletes_data as $data) {
        $schedule_name = explode(",", $data["athlete_schedule"]);
        foreach ($schedule_name as $schedule) {
            $schedule = trim($schedule) . '_' . $data['athlete_gender'];
            if (isset($schedule_dict[$schedule])) {
                $schedule_dict[$schedule][] = $data;
            } else {
                $schedule_dict[$schedule] = array($data);
            }
        }
    }
    $schedule_dict["4x400mR_c"][] = array_merge($schedule_dict["4x400mR_m"] ?? [], $schedule_dict["4x400mR_f"] ?? []);
}

/**
 * list_schedule에 대분류 schedule을 생성하는 함수
 * 1. 각 경기에 맞게 shcedule을 등록한다.
 * 2. 이때, 경기가 바로 결승인 경기가 있으므로 filter_round를 통해 라운드를 필터링한다.
 * @return void
 */
function create_schedule()
{
    global $schedule_dict;
    global $sports_dict;
    global $db;

    $insert_query = "INSERT INTO `list_schedule` (`schedule_sports`, `schedule_name`, `schedule_gender`, `schedule_round`, `schedule_location`, `schedule_start`, `schedule_date`) VALUES (?,?,?,?,?,?,?)";
    foreach ($schedule_dict as $name => $schedule) {
        list($sport, $gender) = explode('_', $name);
        $start = $date = date("Y-m-d");
        $insert_data = [
            $sport,                                 // 0: schedule_sport
            $sports_dict[$sport],                   // 1: schedule_name
            $gender,                                // 2: schedule_gender
            filter_round($sport, count($schedule)), // 3: schedule_round
            ' ',                                    // 4: schedule_location
            $start,                                 // 5: schedule_start
            $date                                   // 6: schedule_date
        ];
        if (!is_duplicate_schedule($insert_data)) {
            $stmt = $db->prepare($insert_query);
            $stmt->bind_param("sssssss", ...$insert_data);
            $stmt->execute();
            $stmt->close();
        }
    }
}

/**
 * 생성하려고 할 중복 스케줄이 있나 확인하는 함수
 * @param array $insert_data 추가할 스케줄 데이터
 * @return bool true: 중복 O, false: 중복 X
 */
function is_duplicate_schedule(array $insert_data)
{
    global $db, $no_create_schedule;
    $search_query = "SELECT COUNT(schedule_id) as num FROM list_schedule WHERE schedule_sports=? AND schedule_name=? AND schedule_gender=? AND schedule_round=? AND schedule_division=?";
    $edit_data = array_slice($insert_data, 0, -3);
    $edit_data[] = 'b';
    $stmt = $db->prepare($search_query);
    $stmt->bind_param("sssss", ...$edit_data);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    if ($data['num'] != 0) {    // 중복 스케줄 등록 시 no_create_schedule에 추가
        $gender = $insert_data[2] == 'm' ? "남" : "여";
        $no_create_schedule[] = $insert_data[0] . '(' . $insert_data[1] . ', ' . $gender . ')';
        return true;
    }
    return false;
}

/**
 * 경기에 따라 예선인지 결승인지 확인하는 함수
 * @param string $sport 경기 id
 * @param int $total_athlete 해당 경기에 출전하는 총 선수
 * @return string 예선 또는 결승
 */
function filter_round(string $sport, int $total_athlete)
{
    // 바로 결승인 sports_code 리스트
    $fix_final_sports = [
        "discusthrow", "hammerthrow", "highjump", "javelinthrow", "longjump", "polevault", "shotput", "triplejump",
        "3000m", "3000mSC", "5000m", "10000m", "decathlon", "heptathlon"
    ];
    if (in_array($sport, $fix_final_sports)) {
        return "final";
    } elseif ($sport === "800m" && $total_athlete <= 10) {
        // 800m는 10명 이내면 바로 결승
        return "final";
    } else {
        return "qualification";
    }
}

/**
 * 스케쥴 생성 결과 출력
 * @return void
 */
function print_result()
{
    global $no_create_schedule, $schedule_dict;
    $total_no_create_schedule = count($no_create_schedule);
    $total_schedule = count($schedule_dict);
    if ($total_no_create_schedule == 0) {   // 모든 일정이 등록 되었을 때
        echo '<script>alert("모든 예선(결승) 일정이 등록 완료되었습니다.");</script>';
    } else if ($total_schedule == $total_no_create_schedule) {  // 이미 모든 일정이 등록되어 있을 때
        echo '<script>alert("이미 모든 예선(결승) 일정이 등록되어 있습니다.");</script>';
    } else {    // 부분 일정이 등록되어 있을 때
        echo '<script>alert("일부 일정이 중복되어, 아래와 같은 일정이 생성되지 않았습니다.\n' . implode('\n', $no_create_schedule) . '");</script>';
    }
    echo '<script>history.back();</script>';
}
