<?php
require_once(__DIR__ . '/../../includes/auth/config.php');
require_once(__DIR__ . '/algorithm/public_algo/set_line.php');
global $db;


/**
 * list_athlete -> list_schedule 안에 sport_name이 있는 선수들의 정보를 가져오는 함수
 * @param string $sport_name 경기 종목
 * @param string $gender 경기 성별
 * @param array $athlete_ids 참가하는 선수 id
 * @param bool $multi_mode 종합 경기 여부
 * @return void
 */
function get_athlete_season_best(string $sport_name, string $gender, array $athlete_ids, $multi_mode)
{
    global $db;
    if (!isset($multi_mode)) {
        $query = "SELECT athlete_id, athlete_name, athlete_country, athlete_division, athlete_schedule, athlete_sb FROM list_athlete WHERE athlete_gender = ? AND INSTR(athlete_schedule, ?)";
    } else {
        $query = "SELECT athlete_id, athlete_name, athlete_country, athlete_division, athlete_schedule, athlete_sb FROM list_athlete WHERE athlete_gender = ? AND INSTR(athlete_schedule, ?)";
        $sport_name = $multi_mode;
    }
    $stmt = $db->prepare($query);
    $stmt->bind_param('ss', $gender, $sport_name);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $return_data = [];
    foreach ($data as $datum) {
        // INSTR로 걸러내지 못한 데이터 걸러내기 (ex. 100m면 100mh도 같이 끌고와짐)
        $schedule_list = explode(',', $datum['athlete_schedule']);
        $json_data = json_decode($datum['athlete_sb'], true);
        $datum['athlete_sb'] = $json_data;  // 문자열 json을 배열 json으로 변경하여 삽입
        if (!isset($json_data[$sport_name]) && in_array($sport_name, $schedule_list) && in_array($datum['athlete_id'], $athlete_ids)) {
            // athlete_schedule에 sport_name이 있으면서 season best 기록이 없는 경우, json_data에 0 데이터를 삽입 후 return_data에 삽입
            $json_data[$sport_name] = '0';
            $datum["record_official_record"] = '0'; // 필드 경기의 set_line을 위한 data
            $return_data[] = $datum;
        } else if (isset($json_data[$sport_name]) && in_array($sport_name, $schedule_list) && in_array($datum['athlete_id'], $athlete_ids)) {
            // athlete_schedule에 sport_name이 있고 season best 기록도 있는 경우, return_data에 삽입
            $datum["record_official_record"] = $datum["athlete_sb"][$sport_name]; // 필드 경기의 set_line을 위한 data
            $return_data[] = $datum;
        }
    }

    if (count($return_data) === 0) {
        mysqli_close($db);
        echo '<script>alert("해당 종목을 참가하는 선수가 없습니다");</script>';
        echo '<script>window.close();</script>';
        exit();
    }
    return $return_data;
}

/**
 * list_athlete -> list_schedule 안에 sport_name이 있는 선수들의 정보를 가져오는 함수
 * @param string $sport_name    경기 종목
 * @param string $gender        경기 성별
 * @param array $country_code   참가 국가 코드
 * @return void
 */
function get_athlete_season_best_relay(string $sport_name, string $gender, array $country_code)
{
    global $db;
    $query = "SELECT athlete_id, athlete_name, athlete_country, athlete_division, athlete_schedule, athlete_sb FROM list_athlete WHERE INSTR(athlete_schedule, ?) AND (athlete_country = \"";
    $query = $query . implode('" OR athlete_country = "', $country_code) . '")';
    $type = 's';
    if ($sport_name !== '4x400mR(Mixed)') {
        // 경기 종목이 혼성경기가 아니라면 athlete_gender WHERE절 추가
        $query = $query . " AND athlete_gender = ?";
        $type = "ss";
    }
    $query = $query . ' ORDER BY athlete_country ASC, athlete_name ASC';
    $stmt = $db->prepare($query);
    if ($sport_name !== '4x400mR(Mixed)') {
        // 경기 종목이 혼성경기가 아니라면 bind_param 2개 (sport_name, gender)
        $stmt->bind_param($type, $sport_name, $gender);
    } else {
        // 경기 종목이 혼성경기라면 bind_param 1개 (sport_name)
        $stmt->bind_param($type, $sport_name);
    }
    $stmt->execute();
    $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $return_data = [];
    foreach ($data as $datum) {
        // INSTR로 걸러내지 못한 데이터 걸러내기 (ex. 100m면 100mh도 같이 끌고와짐)
        $schedule_list = explode(',', $datum['athlete_schedule']);
        $json_data = json_decode($datum['athlete_sb'], true);
        $datum['athlete_sb'] = $json_data;  // 문자열 json을 배열 json으로 변경하여 삽입
        if (!isset($json_data[$sport_name]) && in_array($sport_name, $schedule_list) && in_array($datum['athlete_country'], $country_code)) {
            // athlete_schedule에 sport_name이 있으면서 season best 기록이 없는 경우, json_data에 0 데이터를 삽입 후 return_data에 삽입
            $json_data[$sport_name] = '0';
            $datum["record_official_record"] = '0'; // 필드 경기의 set_line을 위한 data
            $return_data[] = $datum;
        } else if (isset($json_data[$sport_name]) && in_array($sport_name, $schedule_list) && in_array($datum['athlete_country'], $country_code)) {
            // athlete_schedule에 sport_name이 있고 season best 기록도 있는 경우, return_data에 삽입
            $datum["record_official_record"] = $datum["athlete_sb"][$sport_name]; // 필드 경기의 set_line을 위한 data
            $return_data[] = $datum;
        }
    }

    if (count($return_data) === 0) {
        mysqli_close($db);
        echo '<script>alert("해당 종목을 참가하는 선수가 없습니다");</script>';
        echo '<script>window.close();</script>';
        exit();
    }
    return $return_data;
}


/**
 * 조를 편성하는 함수
 * 지그재그를 사용하여 조를 편성함
 * @param array $athlete_data 선수들 정보
 * @param int $total_group 총 그룹 갯수
 * @param int $max_athlete_in_group 그룹 최대 인원
 * @param string $sport_category 경기 유형
 * @param string $sport_name 경기 이름
 * @return array[]
 */
function make_group(array $athlete_data, int $total_group, int $max_athlete_in_group, string $sport_category, string $sport_name): array
{
    $groups = array(array());
    $limit_player_count = count($athlete_data);
    $count = 0;
    for ($i = 0; $i < $max_athlete_in_group; $i += 2) {
        // 아래로 삽입
        for ($j = 0; $j < $total_group; ++$j) {
            $groups[$j][$i] = $athlete_data[$count++] ?? null;
            if (($count == $limit_player_count) || ($groups[$j][$i] == null)) break;
        }
        if ($count == $limit_player_count) break;
        // 위로 삽입
        for ($k = $total_group - 1; $k > -1; --$k) {
            $groups[$k][$i + 1] = $athlete_data[$count++] ?? null;
            if (($count == $limit_player_count) || ($groups[$k][$i + 1] == null)) break;
        }
        if ($count == $limit_player_count) break;
    }
    if ($sport_category === "트랙경기") {
        // 트랙경기 경우 -> 라인 또는 순서 설정
        for ($i = 0; $i < count($groups); $i++) {
            $groups[$i] = set_line($groups[$i], $sport_name);
        }
    } else {
        // 필드경기 경우 -> 차례대로 순서 설정
        for ($i = 0; $i < count($groups); $i++) {
            for ($j = 0; $j < count($groups[$i]); $j++) {
                $groups[$i][$j]['record_order'] = $j + 1;
            }
        }
    }
    // 각 조를 랜덤 배치 후 return
    shuffle($groups);
    return $groups;
}

/**
 * 그룹 최대 인원을 구하는 함수
 * 트랙경기는 경기 종목에 따라 최대 그룹 인원이 다름
 * @param string $schedule_name 경기 종목 명
 * @return int
 */
function select_max_athlete_in_group(string $schedule_name)
{
    if (in_array($schedule_name, ["1500m", "3000mSC"])) {
        // 1500m, 3000m 장애물 -> 최대 인원 17명
        return 17;
    } else if (in_array($schedule_name, ["3000m", "5000m"])) {
        // 3000m, 5000m -> 최대 인원 34명
        return 34;
    } else if ($schedule_name === "10000m") {
        // 10000m -> 최대 인원 38명
        return 38;
    } else {
        // 그 이외 경기 -> 8명
        return 8;
    }
}

/**
 * 트랙경기 (한 그룹당 최대 8명, 장거리는 규칙에 의거하여 조 편성)
 * 1. list_athlete에서 성별, 참가 스포츠로 해당 선수의 시즌 최고기록을 가져온다.
 * 2. 지그재그를 사용해서 조편성을 한다.
 * 3. list_record에 저장한다.
 * 필드경기 (한 그룹당 최대 15명)
 * 1. 랜덤으로 조를 편성
 * 2. list_record에 저장한다.
 * 종합경기
 * 1. 종합경기 (경기 갯수만큼 get_group 호출)
 * 2. 경기 category에 맞게 get_group 호출
 */
function get_group(string $sport_code, string $total_group_count, string $max_in_group, string $athlete_gender, string $sport_category, array $athlete_ids, $multi_mode = null)
{
    global $db;
    $athlete_data = get_athlete_season_best($sport_code, $athlete_gender, $athlete_ids, $multi_mode);
    if ($sport_category === "트랙경기") {
        // 트랙경기 경우 조 편성
        // 기록 순으로 오름차순 정렬
        usort($athlete_data, function ($data1, $data2) use ($sport_code) {
            $compare_record1 = intval(str_replace(array('.', ':', ';'), '', $data1["athlete_sb"][$sport_code] ?? 0));
            $compare_record2 = intval(str_replace(array('.', ':', ';'), '', $data2["athlete_sb"][$sport_code] ?? 0));
            if (($compare_record1 > $compare_record2) && $compare_record1 !== 0 && $compare_record2 !== 0) {
                return 1;
            } elseif (($compare_record1 < $compare_record2) && $compare_record1 !== 0 && $compare_record2 !== 0) {
                return -1;
            } else {
                if ($compare_record1 !== 0 && $compare_record2 === 0) {
                    return 1;
                } else if ($compare_record1 === 0 && $compare_record2 !== 0) {
                    return -1;
                }
                return 0;
            }
        });
        return make_group($athlete_data, intval($total_group_count), intval($max_in_group), $sport_category, $sport_code);
        // $sub_schedule_ids = create_sub_schedule($schedule_data, count($group));
        // create_record($group, $sub_schedule_ids);
    } else if ($sport_category == "필드경기") {
        // 필드경기 또는 종합경기 경우 조 편성
        // 랜덤으로 선수들을 섞음
        shuffle($athlete_data);
        return make_group($athlete_data, intval($total_group_count), intval($max_in_group), $sport_category, $sport_code);
        // $sub_schedule_ids = create_sub_schedule($schedule_data, count($group));
        // create_record($group, $sub_schedule_ids);
    } else {
        // 스포츠 종목을 못 불러왔을 경우 (못 불러올 일이 없음)
        mysqli_close($db);
        echo '<script>alert("종목 카테고리를 불러올 수 없습니다");</script>';
        echo '<script>window.close();</script>';
        exit();
    }
    mysqli_close($db);
    exit();
}


/**
 * 릴레이 경기 조 편성 함수
 * 경기 규칙엔 없어서 랜덤 배정으로 진행
 * @param string $sport_code 경기 종목 코드
 * @param string $gender 경기 성별
 * @param array $county_code 경기에 참가하는 국가
 * @return void
 */
function get_group_relay(string $sport_code, string $gender, array $county_code)
{
    require_once __DIR__ . "/algorithm/get_next_round_group_team.php";
    // query로 참가하는 선수들 가져오기
    $athletes_data = get_athlete_season_best_relay($sport_code, $gender, $county_code);
    $athlete_by_team = null;
    foreach ($athletes_data as $athlete) {
        $athlete_by_team[$athlete["athlete_country"]][] = $athlete;
    }
    shuffle($athlete_by_team);
    // 릴레이는 최대 인원 8명
    $max_athlete_in_group = 8;
    $total_group = count($athlete_by_team) / $max_athlete_in_group;
    $limit_player_count = count($athlete_by_team);
    $count = 0;
    $groups = array(array());
    for ($i = 0; $i < $max_athlete_in_group; $i += 2) {
        // 아래로 삽입
        for ($j = 0; $j < $total_group; ++$j) {
            $groups[$j][$i] = $athlete_by_team[$count++] ?? null;
            if (($count == $limit_player_count) || ($groups[$j][$i] == null)) break;
        }
        if ($count == $limit_player_count) break;
        // 위로 삽입
        for ($k = $total_group - 1; $k > -1; --$k) {
            $groups[$k][$i + 1] = $athlete_by_team[$count++] ?? null;
            if (($count == $limit_player_count) || ($groups[$k][$i + 1] == null)) break;
        }
        if ($count == $limit_player_count) break;
    }
    for ($i = 0; $i < count($groups); $i++) {
        $groups[$i] = set_line($groups[$i], $sport_code, false);
    }
    return $groups;
}

/** 안쓰지만 혹시 모르니 Archive */
///**
// * list_schedule에 schedule을 불러오는 함수
// * @param string $schedule_id 대분류 schedule_id
// * @return array 대분류 schedule information
// */
//function get_schedule_info(string $schedule_id): array
//{
//    global $db;
//    $query = "SELECT * FROM list_schedule WHERE schedule_id = ? ";
//    $stmt = $db->prepare($query);
//    $stmt->bind_param('i', $schedule_id);
//    $stmt->execute();
//    $data = $stmt->get_result()->fetch_assoc();
//    if ($data === null || $data === false) {
//        mysqli_close($db);
//        echo '<script>alert("잘못된 접근입니다.\nError: get_schedule_info");</script>';
//        echo '<script>window.close();</script>';
//        exit();
//    }
//    return $data;
//}
//
//
///**
// * 스포츠 종목 카테고리를 알아내는 함수
// * @param string $schedule_sports schedule_sports
// * @return void
// */
//function get_sport_category(string $schedule_sports)
//{
//    global $db;
//    $query = "SELECT sports_category FROM list_sports WHERE sports_code = ?";
//    $stmt = $db->prepare($query);
//    $stmt->bind_param('s', $schedule_sports);
//    $stmt->execute();
//    $data = $stmt->get_result()->fetch_assoc();
//    if ($data === null || $data === false) {
//        mysqli_close($db);
//        echo '<script>alert("잘못된 접근입니다.\nError: get_sport_category");</script>';
//        echo '<script>window.close();</script>';
//        exit();
//    }
//    return $data['sports_category'];
//}
//
///**
// * 그룹 갯수 만큼 sub_schedule 생성하는 함수
// * @param array $schedule_data 스케줄 정보
// * @param int $count 그룹 갯수
// * @return array sub_schedule id
// */
//function create_sub_schedule(array $schedule_data, int $count)
//{
//    global $db;
//    $start = $date = date('m/d/Y h:i:s a', time());
//    for ($i = 0; $i < $count; $i++) {
//        $data = [
//            $schedule_data["schedule_sports"],  // 0: schedule_sports
//            $schedule_data["schedule_name"],    // 1: schedule_name
//            $schedule_data["schedule_round"],   // 2: schedule_round
//            $i + 1,                             // 3: schedule_group
//            $schedule_data["schedule_gender"],  // 4: schedule_gender
//            's',                                // 5: schedule_division
//            $schedule_data["schedule_location"],// 6: schedule_location
//            $start,                             // 7: schedule_start
//            $date                               // 8: schedule_date
//        ];
//        $query = "INSERT INTO `list_schedule` (`schedule_sports`, `schedule_name`, `schedule_round`, `schedule_group`, `schedule_gender`, `schedule_division`, `schedule_location`, `schedule_start`, `schedule_date`) VALUES (?,?,?,?,?,?,?,?,?)";
//        $stmt = $db->prepare($query);
//        $stmt->bind_param("sssisssss", ...$data);
//        $stmt->execute();
//        $stmt->close();
//    }
//    return get_sub_schedule_id($schedule_data["schedule_id"]);
//}

///**
// * list_record에 데이터를 집어넣는 함수
// * @param array $groups 그룹
// * @return void`
// */
//function create_record(array $groups, array $sub_schedule_id)
//{
//    global $db;
//    $count = 0;
//    foreach ($groups as $group) {
//        foreach ($group as $athlete) {
//            $data = [
//                $athlete["athlete_id"],                     // 0: record_athlete_id
//                $sub_schedule_id[$count]["schedule_id"],  // 1: record_schedule_id
//                0,                                          // 2: record_medal
//                $athlete["record_order"],                   // 3: record_order
//                0                                           // 4: record_judge
//            ];
//            $insert_record_query = "INSERT INTO `list_record` (`record_athlete_id`, `record_schedule_id`, `record_medal`, `record_order`, `record_judge`) VALUES (?,?,?,?,?)";
//            $stmt = $db->prepare($insert_record_query);
//            $stmt->bind_param("iiiii", ...$data);
//            $stmt->execute();
//            $stmt->close();
//        }
//        $count = $count + 1;
//    }
//}

///**
// * 대분류 schedule_id를 통하여 소분류 schedule_id를 불러오는 함수
// * 소분류 schedule_id가 없는 경우 경고문 출력 후 프로그램 종료
// * @param string $schedule_id 대분류 schedule_id
// * @return array|void 소분류|없음
// */
//function get_sub_schedule_id($schedule_id)
//{
//    global $db;
//    $main_division_query = "SELECT * FROM list_schedule WHERE schedule_id = ?";
//    $stmt = $db->prepare($main_division_query);
//    $stmt->bind_param('i', $schedule_id);
//    $stmt->execute();
//    $data = $stmt->get_result()->fetch_assoc();
//    if (count($data) == 0) {
//        echo '<script>alert("잘못된 접근입니다.");</script>';
//        echo '<script>history.back();</script>';
//        exit();
//    }
//
//    $data['schedule_division'] = "s";
//    $sub_division_query = "SELECT * FROM list_schedule WHERE schedule_sports = ? AND schedule_gender = ? AND schedule_round = ? AND schedule_division = ? AND schedule_location = ?";
//    $stmt = $db->prepare($sub_division_query);
//    $stmt->bind_param("sssss", $data['schedule_sports'], $data['schedule_gender'], $data['schedule_round'], $data['schedule_division'], $data['schedule_location']);
//    $stmt->execute();
//    $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
//    if (count($data) == 0) {
//        echo '<script>alert("잘못된 접근입니다.");</script>';
//        echo '<script>history.back();</script>';
//        exit();
//    }
//    return $data;
//}