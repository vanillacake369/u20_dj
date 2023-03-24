<?php
require_once __DIR__ . '/../../includes/auth/config.php';
require_once __DIR__ . '/algorithm/public_algo/split_by_group.php';
require_once __DIR__ . '/algorithm/public_algo/set_line.php';
require_once __DIR__ . '/algorithm/get_next_round_group_single.php';
require_once __DIR__ . '/algorithm/get_next_round_group_team.php';

global $db;
$sport_code = $_POST['sports'] ?? null;
$current_round = $_POST['current_round'] ?? null;
$next_round = $_POST['next_round'] ?? null;
$gender = $_POST['gender'] ?? null;
$athlete_count = $_POST['count'] ?? null;
$group_count = $_POST['group_count'] ?? null;
$reset = $_GET['reset'] ?? null;
//if ($round == "1") { // 라운드 직접 입력 시
//    $round = $_POST['direct_input'];
//}

if (isset($reset)) {
    // reset으로 페이지를 들어옴 (GET으로 들어옴)
    $sport_code = $_GET['sports'] ?? null;
    $gender = $_GET['gender'] ?? null;
    $current_round = $_GET['round'] ?? null;
    $next_round = $_GET['round'] ?? null;
    $data = get_record_round_information($sport_code, $gender, $current_round);
    $group_count = $data[0]["group_count"];
    $athlete_count = $data[0]["athlete_count"];
}

if (!isset($reset) && !isset($sport_code, $current_round, $gender, $athlete_count, $group_count)) { // 유효성 검사
    mysqli_close($db);
    echo '<script>alert("모두 입력하세요.");history.back();</script>';
    exit;
}
$record_information = get_record_round_information($sport_code, $gender, $current_round); // 버튼을 누른 페이지의 sub_schedule_id
if ($reset === null && in_array($next_round, array("결승", "final")) && $group_count !== '1') {
    // 다음 라운드 버튼을 눌렀을 때, 결승 생성시, 그룹이 1개가 아닐 시 애러 출력
    mysqli_close($db);
    echo '<script>alert("결승은 1개의 그룹만 생성할 수 있습니다.");</script>';
    echo '<script>history.back();</script>';
    exit;
} else if ($reset != null && !in_array($current_round, array("예선", "qualification"))) {
    // 예선 라운드를 제외한, 조 초기화 버튼 클릭 시
    $not_start_schedules = array_filter($record_information, function ($record) {
        // 시작된 경기가 아닌경우 저장
        return $record['record_status'] === 'n' && $record['record_state'] === 'n';
    });
    if (count($not_start_schedules) !== count($record_information)) {
        // 하나의 경기라도 시작된 경우 조 초기화를할 수 없음
        mysqli_close($db);
        echo '<script>alert("이미 시작된 경기가 있으므로 전체 조 재편성을 할 수 없습니다.");</script>';
        echo '<script>history.back();</script>';
        exit;
    } else {
        // 이전 schedule_data를 저장
        $current_round = get_previous_information($sport_code, $gender, $current_round);
        $record_information = get_record_round_information($sport_code, $gender, $current_round);
    }
} elseif ($reset == null && in_array($current_round, array("결승", "final"))) {
    // 결승 라운드에서 다음 라운드 조 편성 클릭 시, 경고문 출력
    mysqli_close($db);
    echo '<script>alert("결승 경기에서는 다음 라운드 조 편성을 할 수 없습니다.");</script>';
    echo '<script>history.back();</script>';
    exit;
} elseif ($reset != null) {
    // 예선 라운드에서 조 초기화 버튼 클릭 시, 경고문 출력
    mysqli_close($db);
    echo '<script>alert("예선 경기는 조 재편성을 할 수 없습니다.");</script>';
    echo '<script>history.back();</script>';
    exit;
}

if (!isset($reset)) {
    check_duplicate_create($sport_code, $gender, $next_round);   // 중복 생성 방지
}
if (!in_array($sport_code, ["4x100mR", "4x400mR"])) { // 개인 달리기 경기
    if (!in_array($sport_code, ["10000m", "5000m", "3000m", "3000mSC", "1500m", "800m"]) && $athlete_count > 8) {
        // 개인 달리기 경기에서 800m을 제외한 단거리 달리기는 최대 인원은 8명이 되어야한다.
        mysqli_close($db);
        echo '<script>alert("단거리 달리기(800m 제외)는 한 조에 8명을 넘을 수 없습니다.");</script>';
        echo '<script>history.back();</script>';
        exit;
    }
    $athletes_data = get_athletics_data($sport_code, $gender, $current_round);
    $athlete_by_group = split_by_group($athletes_data);
    $next_group = getNextRoundGroup($athlete_by_group, $athlete_count, $group_count);
    for ($i = 0; $i < count($next_group); $i++) {
        $next_group[$i] = set_line($next_group[$i], $sport_code);
    }
    update_qualify_record_single($next_group);  // Q, q 처리를 DB에 업데이트 (Q, q get_next_round_group_single 에서 처리)
    if (!isset($reset)) {   // 조 생성
        // $sub_schedule_ids = create_all_schedule($next_group, $record_information[0], $round);
        create_record_by_group_single($next_group, $sport_code, $gender, $next_round);
    } else {    // 조 재편성
        remove_qualify_record_single($next_group, $athlete_by_group);  // 기록이 수정되어 Q나 q의 자격이 박탈된 경우 qualify 제거
        update_record_by_group_single($next_group, $sport_code, $gender, $next_round);
    }
} else { // 팀 릴레이 경기
    if ($athlete_count > 8) {
        // 릴레이 경기는 8팀을 넘을 수 없다.
        mysqli_close($db);
        echo '<script>alert("릴레이 경기(팀 경기)는 8팀을 넘을 수 없습니다.");</script>';
        echo '<script>history.back();</script>';
        exit;
    }
    $athletes_data = get_athletics_data($sport_code, $gender, $current_round);
    $athlete_by_teams = create_ahtlete_data_by_teams($athletes_data);
    $group_by_record = create_group_by_record($athlete_by_teams);
    $team_by_groups = create_team_by_groups($group_by_record);
    $next_group = getNextRoundGroup($team_by_groups, $athlete_count, $group_count);
    for ($i = 0; $i < count($next_group); $i++) {
        $next_group[$i] = set_line($next_group[$i], $sport_code);
    }
    update_qualify_record_multi($next_group, $athlete_by_teams);  // Q, q 처리를 DB에 업데이트 (Q, q get_next_round_group_single 에서 처리)
    if (!isset($reset)) {   // 조 생성
        // $sub_schedule_ids = create_all_schedule($next_group, $record_information[0], $round);
        create_record_by_group_multi($next_group, $athlete_by_teams, $sport_code, $gender, $next_round);
    } else {    // 조 재생성
        remove_qualify_record_multi($next_group, $athlete_by_teams);  // 기록이 수정되어 Q나 q의 자격이 박탈된 경우 qualify 제거
        update_record_by_group_multi($next_group, $athlete_by_teams, $sport_code, $gender, $next_round);
    }
}

mysqli_close($db);
if (!isset($reset)) {   // 다음 라운드 조 편성 버튼
    echo '<script>alert("조 편성이 완료되었습니다.\n일정을 생성해 주세요.");</script>';
    echo '<script>window.close();</script>';
} else {    // 조 재편성 버튼
    echo '<script>alert("조 재편성이 완료되었습니다.");</script>';
    echo '<script>history.back();</script>';
}
exit();

/**
 * 다음 라운드 생성 전에 list_record에 동일한 일정이 등록되어있는지 확인하는 함수
 * schedule_sports, schedule_gender, schedule_round, schedule_division, schedule_loaction을 확인하여 있으면 경고문 출력 후 프로그램 종료
 * @param string $sports_code 스포츠 코드
 * @param string $gender 성별
 * @param string $next_round 라운드
 * @return void
 */
function check_duplicate_create(string $sports_code, string $gender, string $next_round)
{
    global $db;
    $duplicate_check_query = "SELECT COUNT(*) as num FROM list_record WHERE record_sports = ? AND record_gender = ? AND record_round = ?";
    $stmt = $db->prepare($duplicate_check_query);
    $stmt->bind_param("sss", $sports_code, $gender, $next_round);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();

    if ($data['num'] != 0) {
        mysqli_close($db);
        echo '<script>alert("중복 조 편성 입니다.");</script>';
        echo '<script>window.close();</script>';
        exit();
    }
}

/**
 * sports_code, gender, round를 통해 해당 경기의 state와 status를 가져오는 함수
 * 해당 경기의 정보가 없는경우 경고문 출력 후 종료
 * @param string $sports 스포츠 이름
 * @param string $gender 성별
 * @param string $round 라운드
 * @return array|void 해당 경기 상태 정보|없음
 */
function get_record_round_information(string $sports, string $gender, string $round)
{
    global $db;
    $main_division_query = "SELECT MAX(record_order) as athlete_count, MAX(record_group) as group_count, record_state, record_status FROM list_record 
                            WHERE record_sports = ? AND record_gender = ? AND record_round = ?
                            GROUP BY record_state, record_status";
    $stmt = $db->prepare($main_division_query);
    $stmt->bind_param('sss', $sports, $gender, $round);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    if (count($data) == 0) {
        mysqli_close($db);
        echo '<script>alert("잘못된 접근입니다.");</script>';
        echo '<script>history.back();</script>';
        exit();
    }

    return $data;
}

/**
 * 소분류 schedule_id들을 통하여 각 경기의 선수 정보를 한번에 불러오는 함수
 * get_result()가 false -> 경고문 출력 후 종료
 * 조 편성시 mysqli_result를 사용하기 때문에 mysqli_result return
 * @param string $sports_code 스포츠 코드
 * @param string $gender 성별
 * @param string $round 라운드
 * @return mysqli_result|void 성공|실패
 */
function get_athletics_data(string $sports_code, string $gender, string $round)
{
    global $db;
    $list_record_query = "SELECT * FROM list_record WHERE record_sports=? AND record_gender=? AND record_round=? ORDER BY record_official_result ASC";
    $stmt = $db->prepare($list_record_query);
    $stmt->bind_param("sss", $sports_code, $gender, $round);
    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result) {
        // query문 실패 시
        mysqli_close($db);
        echo '<script>alert("잘못된 접근입니다.");</script>';
        echo '<script>history.back();</script>';
        exit();
    }
    return $result;
}

/**
 * list_schedule에 schedule을 생성하는 함수
 * 이전 경기의 schedule_sports, schedule_name, schedule_gender, schedule_location을 clone하여 사용
 * @param array $next_group 다음 라운드 선수들
 * @param array $previous_schedule_data 이전 schedule 정보
 * @param string $round 생성할 라운드
 * @return array
 */
function create_all_schedule(array $next_group, $previous_schedule_data, $round)
{
    global $db;
    $schedule_ids = [];
    $start = $date = date("Y-m-d");
    // 대분류 생성
    $main_division_data = [
        $previous_schedule_data['schedule_sports'],     // 0: schedule_sports
        $previous_schedule_data['schedule_name'],       // 1: schedule_name
        $previous_schedule_data['schedule_gender'],     // 2: schedule_gender
        $round,                                         // 3: schedule_round
        $previous_schedule_data['schedule_location'],   // 4: schedule_location
        $start,                                         // 5: schedule_start
        $date                                           // 6: schedule_date
    ];
    $insert_main_query = "INSERT INTO `list_schedule` (`schedule_sports`, `schedule_name`, `schedule_gender`, `schedule_round`, `schedule_location`, `schedule_start`, `schedule_date`) VALUES (?,?,?,?,?,?,?)";
    $stmt = $db->prepare($insert_main_query);
    $stmt->bind_param('sssssss', ...$main_division_data);
    $stmt->execute();
    $stmt->close();
    // 소분류 생성
    $insert_sub_query = "INSERT INTO `list_schedule` (`schedule_sports`, `schedule_name`, `schedule_gender`, `schedule_round`, `schedule_group`, `schedule_division`, `schedule_location`, `schedule_start`, `schedule_date`) VALUES (?,?,?,?,?,?,?,?,?)";
    for ($group_num = 1; $group_num <= count($next_group); $group_num++) {
        $sub_division_data = [
            $previous_schedule_data['schedule_sports'],     // 0: schedule_sports
            $previous_schedule_data['schedule_name'],       // 1: schedule_name
            $previous_schedule_data['schedule_gender'],     // 2: schedule_gender
            $round,                                         // 3: schedule_round
            $group_num,                                     // 4: schedule_group
            's',                                            // 5: schedule_division
            $previous_schedule_data['schedule_location'],   // 6: schedule_location
            $start,                                         // 7: schedule_start
            $date                                           // 8: schedule_date
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
        $stmt->close();
    }
    return $schedule_ids;
}

/**
 * 트랙경기(개인) list_record에 record 생성하는 함수
 * @param array $next_group 다음 라운드 선수들
 * @param string $sports_code 스포츠 코드
 * @param string $gender 성별
 * @param string $round 라운드
 * @return void
 */
function create_record_by_group_single(array $next_group, string $sports_code, string $gender, string $round)
{
    global $db;
    for ($idx = 0; $idx < count($next_group); $idx++) {
        foreach ($next_group[$idx] as $athlete) {
            $athlete_data = [
                $athlete['record_athlete_id'], // 0: record_athlete_id
                $athlete['record_order'],    // 1: record_order
                $idx + 1,   // 2: record_group
                $sports_code,        // 3: record_sports
                $round,         // 4: record_round
                $gender,        // 5: record_gender
                0               // 6: record_medal
            ];
            $insert_record_query = "INSERT INTO `list_record` (`record_athlete_id`, `record_order`, `record_group`, `record_sports`, `record_round`, `record_gender`, `record_medal`) VALUES (?,?,?,?,?,?,?)";
            $stmt = $db->prepare($insert_record_query);
            $stmt->bind_param("iiisssi", ...$athlete_data);
            $stmt->execute();
            $stmt->close();
        }
    }
}

/**
 * 트랙경기(팀) list_record에 record를 생성하는 함수
 * next_group에 있는 팀 정보를 불러와 athlete_by_teams에 있는 선수 정보를 불러옴
 * @param array $next_group 다음 라운드 팀
 * @param array $athlete_by_teams 팀 별로 선수들의 정보가 정리된 배열
 * @param array $sub_schedule_ids 소분류 schedule_id
 * @return void
 */
function create_record_by_group_multi(array $next_group, array $athlete_by_teams, string $sports_code, string $gender, string $round)
{
    global $db;
    for ($idx = 0; $idx < count($next_group); $idx++) { // 전체 그룹
        for ($jdx = 0; $jdx < count($next_group[$idx]); $jdx++) { // 그룹 안의 팀
            $athlete = $next_group[$idx][$jdx];
            $group_id = $athlete['record_athlete_id'];
            $order = $athlete['record_order'];
            $athlete_by_team = $athlete_by_teams[$group_id];
            echo count($athlete_by_team).'<br>';
            foreach ($athlete_by_team as $athlete) { // 팀 안의 선수 정보
                $athlete_data = [
                    $athlete['record_athlete_id'],          // 0: record_athlete_id
                    0,                                      // 1: record_medal
                    $order,                                 // 2: record_order
                    $athlete['record_judge'],               // 3: record_judge
                    $idx + 1,                               // 4: record_group
                    $athlete['record_team_order'],          // 5: record_team_order
                    $sports_code,                           // 6: record_sports
                    $round,                                 // 7: record_round
                    $gender,                                // 8: record_gender
                ];
                $insert_record_query = "INSERT INTO `list_record` (`record_athlete_id`, `record_medal`, `record_order`, `record_judge`,`record_group`,`record_team_order`,`record_sports`,`record_round`,`record_gender`) VALUES (?,?,?,?,?,?,?,?,?)";
                $stmt = $db->prepare($insert_record_query);
                $stmt->bind_param("iiiiiisss", ...$athlete_data);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
}

/**
 * 현재 정보를 통해 전 라운드를 알아내는 함수
 * @param string $sports_code 스포츠 코드
 * @param string $gender 성별
 * @param string $current_round 현재 라운드
 * @return array|void 이전 라운드 정보|실패
 */
function get_previous_information(string $sports_code, string $gender, string $current_round)
{
    global $db;
    // 현재 경기 정보를 통해 이전 경기들의 그룹 갯수, 최대 그룹 인원 수를 가져옴
    $record_query = "SELECT record_round FROM list_record 
                     WHERE record_sports = ? AND record_gender = ? AND record_round != ? GROUP BY record_round ORDER BY record_id ASC;";
    $stmt = $db->prepare($record_query);
    $stmt->bind_param("sss", $sports_code, $gender, $current_round);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    if (count($data) == 0) {    // 현재 스케쥴 id로 이전 라운드 경기 정보를 끌고 오지 못할 때
        echo '<script>alert("잘못된 접근입니다.");</script>';
        echo '<script>history.back();</script>';
        exit();
    }
    return array_slice($data, -1)[0]["record_round"];
}

/**
 * 트랙경기(개인) 조 재편성 시 list_record의 기존 record를 update하는 함수
 * @param array $next_group 다음 라운드 선수들
 * @param string $sports_code 스포츠 코드
 * @param string $gender 성별 
 * @param string $round 현재 라운드
 * @return void
 */
function update_record_by_group_single(array $next_group, string $sports_code, string $gender, string $round)
{
    global $db;
    $record_ids = get_record_id($sports_code, $gender, $round);
    $record_id_idx = 0;
    for ($idx = 0; $idx < count($next_group); $idx++) {
        foreach ($next_group[$idx] as $athlete) {
            $athlete_data = [
                $athlete['record_athlete_id'],           // 0: record_athlete_id
                0,                                       // 1: record_medal
                $athlete['record_order'],                // 2: record_order
                $athlete['record_judge'],                // 3: record_judge
                $record_ids[$record_id_idx]["record_id"] // 4: record_id
            ];
            $update_query = "UPDATE list_record SET record_athlete_id=?, record_medal=?, record_order=?, record_judge=? WHERE record_id=?;";
            $stmt = $db->prepare($update_query);
            $stmt->bind_param("iiiii", ...$athlete_data);
            $stmt->execute();
            $stmt->close();
            $record_id_idx += 1;
        }
    }
}

/**
 * 트랙경기(팀) 조 재편성 시 list_record의 기존 record를 update하는 함수
 * @param array $next_group 다음 라운드 팀
 * @param array $athlete_by_teams 팀 별로 선수들의 정보가 정리된 배열
 * @param string $sports_code
 * @param string $gender
 * @param string $round
 * @return void
 */
function update_record_by_group_multi(array $next_group, array $athlete_by_teams, string $sports_code, string $gender, string $round)
{
    global $db;
    $record_ids = get_record_id($sports_code, $gender, $round);
    $record_id_idx = 0;
    for ($idx = 0; $idx < count($next_group); $idx++) { // 전체 그룹
        for ($jdx = 0; $jdx < count($next_group[$idx]); $jdx++) { // 그룹 안의 팀
            $athlete = $next_group[$idx][$jdx];
            $group_id = $athlete['record_athlete_id'];
            $order = $athlete['record_order'];
            $athlete_by_team = $athlete_by_teams[$group_id];
            foreach ($athlete_by_team as $athlete) { // 팀 안의 선수 정보
                $athlete_data = [
                    $athlete['record_athlete_id'],          // 0: record_athlete_id
                    0,                                      // 1: record_medal
                    $order,                                 // 2: record_order
                    $athlete['record_judge'],               // 3: record_judge
                    $idx + 1,                               // 4: record_group
                    $athlete['record_team_order'],          // 5: record_team_order
                    $record_ids[$record_id_idx]["record_id"]// 6: record_id
                ];

                $record_id_idx += 1;
                $insert_record_query = "UPDATE list_record SET record_athlete_id=?, record_medal=?, record_order=?, record_judge=?, record_group=?, record_team_order=? WHERE record_id=?;";
                $stmt = $db->prepare($insert_record_query);
                $stmt->bind_param("iiiiiii", ...$athlete_data);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
}

/**
 * 조 재편성 시 list_reocrd의 record update시 사용하는 함수
 * list_schedule의 소분류 schedule_id를 통해 list_record의 record_id를 불러온다
 * @param string $sport_code
 * @param string $gender
 * @param string $round
 * @return array
 */
function get_record_id(string $sport_code, string $gender, string $round)
{
    global $db;
    $record_id_query = "SELECT record_id, record_group FROM list_record WHERE record_sports = ? AND record_round = ? AND record_gender = ?";
    $stmt = $db->prepare($record_id_query);
    $stmt->bind_param("sss", $sport_code, $round, $gender);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * 트랙 경기(개인) Q, q 업데이트
 * @param array $next_group Q나 q를 받은 선수 (다음 라운드 진출 예정자)
 * @return void
 */
function update_qualify_record_single(array $next_group)
{
    global $db;
    for ($idx = 0; $idx < count($next_group); $idx++) {
        foreach ($next_group[$idx] as $athlete) {
            $athlete_data = [
                $athlete['record_memo'],        // 0: record_memo
                $athlete['record_id'],          // 1: record_id
            ];
            $update_query = "UPDATE list_record SET record_memo=? WHERE record_id=?;";
            $stmt = $db->prepare($update_query);
            $stmt->bind_param("si", ...$athlete_data);
            $stmt->execute();
            $stmt->close();
        }
    }
}

/**
 * 트랙 경기(개인) Q, q 제거
 * 기록이 변경되어 조를 재 편성하는 상황이 발생할 경우 Q, q 변동이 있을 것을 대비하여 만든 함수
 *
 * @param array $next_group Q나 q를 받은 선수 (다음 라운드 진출 예정자)
 * @param array $athlete_by_group 해당 라운드 조별로 정리된 선수 경기 결과
 * @return void
 */
function remove_qualify_record_single(array $next_group, array $athlete_by_group)
{
    global $db;
    $total_athlete = [];
    $total_next_round_athlete = [];
    $remove_qualify_athlete = [];

    foreach ($athlete_by_group as $group) {
        // 그룹으로 나눠져 있는 선수들을 1차원 배열로 합침
        $total_athlete = array_merge($total_athlete, $group);
    }
    foreach ($next_group as $group) {
        // 그룹으로 나눠져있는 다음 라운드 선수들을 1차원 배열로 합침
        $total_next_round_athlete = array_merge($total_next_round_athlete, $group);
    }
    // total_next_round_athlete에 있지 않은 선수가 Q나 q를 갖고 있을 경우 query문을 통해 제거
    foreach ($total_athlete as $athlete) {
        $find_athlete = null;
        foreach ($total_next_round_athlete as $next_round_athlete) {
            // 다음 라운드 선수에 본인이 있다면, find_athlete에 저장
            if ($athlete["record_id"] === $next_round_athlete["record_id"]) {
                $find_athlete = $next_round_athlete;
                break;
            }
        }
        if ($find_athlete === null && ((strpos($athlete["record_memo"], "Q") !== false) || (strpos($athlete["record_memo"], "q") !== false))) {
            // 다음라운드 진출자도 아니면서 Q나 q를 갖고 있는 선수를 remove_qualify_athlete에 추가
            $remove_qualify_athlete[] = $athlete;
        }
    }
    // remove_qualify_athlete의 선수의 memo 안에 있는 Q나 q를 제거후 query문으로 업데이트
    foreach ($remove_qualify_athlete as $athlete) {
        $athlete["record_memo"] = str_replace(array("Q, ", "q, ", "Q", "q"), '', $athlete["record_memo"]);
        $query_data = [
            $athlete["record_memo"], // 0: record_memo
            $athlete["record_id"]    // 1: record_id
        ];
        $qualify_remove_query = "UPDATE list_record SET record_memo=? WHERE record_id=?;";
        $stmt = $db->prepare($qualify_remove_query);
        $stmt->bind_param("si", ...$query_data);
        $stmt->execute();
        $stmt->close();
    }
}

/**
 * 트랙 경기(팀) Q, q 추가
 * @param array $next_group Q나 q를 받은 선수 (다음 라운드 진출 예정자)
 * @param array $athlete_by_teams 해당 라운드 팀별로 정리된 선수 경기 결과
 * @return void
 */
function update_qualify_record_multi(array $next_group, array $athlete_by_teams)
{
    global $db;
    for ($idx = 0; $idx < count($next_group); $idx++) { // 전체 그룹
        for ($jdx = 0; $jdx < count($next_group[$idx]); $jdx++) { // 그룹 안의 팀
            $athlete = $next_group[$idx][$jdx];
            $group_id = $athlete['record_athlete_id'];
            $memo = $athlete['record_memo'];
            $athlete_by_team = $athlete_by_teams[$group_id];
            foreach ($athlete_by_team as $athlete) { // 팀 안의 선수 정보
                $athlete_data = [
                    $memo,                                  // 0: record_memo
                    $athlete['record_id']                   // 1: record_id
                ];

                $insert_record_query = "UPDATE list_record SET record_memo=? WHERE record_id=?;";
                $stmt = $db->prepare($insert_record_query);
                $stmt->bind_param("si", ...$athlete_data);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
}

/**
 * 트랙 경기(팀) Q, q 제거
 * 기록이 변경되어 조를 재 편성하는 상황이 발생할 경우 Q, q 변동이 있을 것을 대비하여 만든 함수
 * @param array $next_group Q나 q를 받은 선수 (다음 라운드 진출 예정자)
 * @param array $athlete_by_teams 해당 라운드 팀별로 정리된 선수 경기 결과
 * @return void
 */
function remove_qualify_record_multi(array $next_group, array $athlete_by_teams)
{
    global $db;
    $total_athlete = [];
    $total_next_round_athlete = [];
    $remove_qualify_athlete = [];

    foreach ($athlete_by_teams as $team) {
        // 그룹으로 나눠져 있는 선수들을 1차원 배열로 합침
        $total_athlete = array_merge($total_athlete, $team);
    }
    foreach ($next_group as $group) {
        // 그룹으로 나눠져있는 다음 라운드 선수들을 1차원 배열로 합침
        foreach ($group as $team) {
            $group_id = $team['record_athlete_id'];
            $athlete_by_team = $athlete_by_teams[$group_id];
            $total_next_round_athlete = array_merge($total_next_round_athlete, $athlete_by_team);
        }
    }
    // total_next_round_athlete에 있지 않은 선수가 Q나 q를 갖고 있을 경우 query문을 통해 제거
    foreach ($total_athlete as $athlete) {
        $find_athlete = null;
        $memo = $athlete['record_memo'] ?? null;
        foreach ($total_next_round_athlete as $next_round_athlete) {
            // 다음 라운드 선수에 본인이 있다면, find_athlete에 저장
            if ($athlete["record_id"] === $next_round_athlete["record_id"]) {
                $find_athlete = $next_round_athlete;
                break;
            }
        }
        if ($find_athlete === null  && $memo !== null && ((strpos($athlete["record_memo"], "Q") !== false) || (strpos($athlete["record_memo"], "q") !== false))) {
            // 다음라운드 진출자도 아니면서 Q나 q를 갖고 있는 선수를 remove_qualify_athlete에 추가
            $remove_qualify_athlete[] = $athlete;
        }
    }
    // remove_qualify_athlete의 선수의 memo 안에 있는 Q나 q를 제거후 query문으로 업데이트
    foreach ($remove_qualify_athlete as $athlete) {
        $athlete["record_memo"] = str_replace(array("Q, ", "q, ", "Q", "q"), '', $athlete["record_memo"]);
        $query_data = [
            $athlete["record_memo"], // 0: record_memo
            $athlete["record_id"]    // 1: record_id
        ];
        $qualify_remove_query = "UPDATE list_record SET record_memo=? WHERE record_id=?;";
        $stmt = $db->prepare($qualify_remove_query);
        $stmt->bind_param("si", ...$query_data);
        $stmt->execute();
        $stmt->close();
    }
}