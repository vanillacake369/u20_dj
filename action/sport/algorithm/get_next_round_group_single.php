<?php
error_reporting(E_ALL ^ E_WARNING);
include_once(__DIR__ . '/public_algo/sort_by_time.php');

/** 필드경기 다음 라운드 조 편성 함수 (p.179)
 * 1. 경기 결과가 통과인 선수만 배열에 저장
 * 2. 제외할 인원 수 보다 통과하지 못한선수가 적을 시, 통과한 인원 수 만큼 재배치
 * 3. 각 조 시간이 빠른 순으로 정렬 후 1, 2, 그외 등수를 따로 배열에 저장
 * 4. 따로 저장한 배열을 다시 시간 순으로 정렬
 * 5. 따로 저장한 배열을 하나로 합쳐 지그재그로 선수 배치
 * 6. 각 조를 추첨(랜덤)후 리턴
 * @param array $result_player_groups 경기 결과 각 조
 * @param int $total_player_count 경기에 참여한 총 인원
 * @param int $exclude_player_count 제외 할 선수들의 수
 * @return array 다음 라운드 조 편성
 */
function getNextRoundGroup(array $result_player_groups, int $total_by_group, int $total_group_count): array
{
    $pass_player_count = 0;
    // 경기 결과가 '통과(p)인 선수만 재배치'
    foreach ($result_player_groups as $group) {
        $pass_player_group = array_filter($group, 'isPass');
        $pass_player_groups[] = $pass_player_group;
        $pass_player_count = $pass_player_count + count($pass_player_group);
    }
    // 통과한 선수가 한명도 없을 때
    if ($pass_player_count == 0) {
        echo '<script>alert("경기가 시작되지 않았거나, 통과한 선수가 없습니다.");</script>';
        echo '<script>window.close();</script>';
        exit();
    }
    // 제외할 인원 수 보다 통과하지 못한 선수가 적을 시, 통과한 인원 수 만큼 재배치
    if ($pass_player_count > ($total_group_count * $total_by_group)) {
        $limit_player_count = ($total_group_count * $total_by_group);
    } else {
        $limit_player_count = $pass_player_count;
    }
    $depth = $total_group_count;
    $first_group = array();
    $second_group = array();
    $other_group = array();
    $next_round_group = array(array());
    $count = 0;
    // 각 조 시간이 빠른 순으로 정렬 후 1, 2등은 따로 저장
    foreach ($pass_player_groups as $group) {
        usort($group, "sortByTime");
        $first_group[] = $group[0];
        $second_group[] = $group[1];
        $other_group = array_merge($other_group, array_slice($group, 2));
    }
    // 따로 저장한 각 조의 1등, 2등 그리고 그 이외를 따로 정렬
    usort($first_group, "sortByTime");
    usort($second_group, "sortByTime");
    usort($other_group, "sortByTime");
    // Q, q 마킹처리 (일단 Q, q 처리하는 기준을 정확히 몰라서 1, 2등만 Q처리 나머진 q처리)
    qualify_marked($first_group, count($first_group), 'Q'); // 1등 Q처리
    qualify_marked($second_group, count($second_group), 'Q'); // 2등 Q처리
    qualify_marked($other_group, $limit_player_count - count($first_group) - count($second_group), 'q');    // 나머지 q 처리
    // 따로 정렬한 데이터를 하나로 합쳐 지그재그 재배치
    $merge_group = array_merge($first_group, $second_group, $other_group);
    for ($i = 0; $i < $total_by_group; $i += 2) {
        // 아래로 삽입
        for ($j = 0; $j < $depth; ++$j) {
            $next_round_group[$j][$i] = $merge_group[$count++] ?? null;
            if (($count == $limit_player_count) || ($next_round_group[$j][$i] == null)) break;
        }
        if ($count == $limit_player_count) break;
        // 위로 삽입
        for ($k = $depth - 1; $k > -1; --$k) {
            $next_round_group[$k][$i + 1] = $merge_group[$count++] ?? null;
            if (($count == $limit_player_count) || ($next_round_group[$k][$i + 1] == null)) break;
        }
        if ($count == $limit_player_count) break;
    }
    // 각 조를 랜덤 배치 후 return
    shuffle($next_round_group);
    return $next_round_group;
}

/**
 * Q또는 q 처리하는 함수
 * player data에 memo 영역에 마킹 처리
 * @param array $players_data 선수들 정보
 * @param int $total_player 전체 인원수
 * @param string $marking Q 또는 q
 * @return void
 */
function qualify_marked(array &$players_data, int $total_player, string $marking)
{
    for ($i = 0; $i < $total_player; $i++) {
        $players_data[$i]["record_memo"] = str_replace(array("Q, ", "q, ", "Q", "q"), '', $players_data[$i]["record_memo"]);  // 만약, 기록이 수정되어 Q나 q가 중복되서 들어갈 경우 제거 후 추가
        if ($players_data[$i]["record_memo"] == "" || $players_data[$i]["record_memo"] == " " || empty($players_data[$i]["record_memo"])) {
            // memo에 아무것도 없는 경우 -> marking을 붙힘
            $players_data[$i]["record_memo"] = $marking;
        } else {
            // memo에 무언가가 적혀있는 경우 -> Q q가 memo의 앞에 오게 marking을 붙힘
            $players_data[$i]["record_memo"] = $marking . ', ' . $players_data[$i]["record_memo"];
        }
    }
}

/**
 * 게임 종료 후, 선수가 통과했는지 확인<br>
 * list_record 테이블의 record_pass를 이용
 * @param array $athleteData 게임 종료 후 선수 정보
 * @return bool
 */
function isPass(array $athleteData): bool
{
    return $athleteData['record_pass'] == 'p';
}


# TEST
// 200m 여자 달리기
// https://www.worldathletics.org/results/world-athletics-championships/2022/world-athletics-championships-oregon-2022-7137279/women/200-metres/semi-final/result
/*
    61001 JACKSON (3, 4, 5, 6)
    63001 Shelly-Ann (3, 4, 5, 6)
    62001 Tamara CLARK (3, 4, 5, 6)
    62002 Dina ASHER (3, 4, 5, 6)
    62003 Aminatou (7, 8)
    61002 Abby STEINER (7, 8)
    61003 Elanie THOMPSON (1, 2)
    63002 Mujinga (1, 2)
 */
//$QUERY = "SELECT athlete_name, record_schedule_id, record_athlete_id, record_record, record_pass
//          FROM list_record JOIN list_athlete ON athlete_id = record_athlete_id
//          WHERE (record_schedule_id = 60001 or record_schedule_id = 60002 or record_schedule_id = 60003)";
//$conn = mysqli_connect('localhost', 'admin', 'anu123!', 'u20_test', 3306);
//$query = mysqli_query($conn, $QUERY);
//$result_data_by_groups = split_by_group($query);
//
//foreach ($result_data_by_groups as $group) {
//    foreach ($group as $player) {
//        echo $player['record_athlete_id'] . ' ' . $player['record_record'] . '&nbsp&nbsp&nbsp';
//    }
//    echo '<br>';
//}
//echo '<br>';
//
//$next_round_groups = getNextRoundGroup($result_data_by_groups, 24, 16, 1);
//for ($i = 0; $i < count($next_round_groups); $i++) {
//    $next_round_groups[$i] = set_line($next_round_groups[$i]);
//}
//
//foreach ($next_round_groups as $group) {
//    foreach ($group as $player) {
//        echo $player['athlete_name'] . '&nbsp&nbsp' . $player['record_order'] . '&nbsp&nbsp&nbsp<br>';
//    }
//    echo '<br>';
//}