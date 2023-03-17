<?php
// FIXME 규칙상 13번이 (3456)추첨에 들어가야하는 것이 맞는데 실제 경기 결과에선 (78)추첨에 들어감
error_reporting(E_ALL ^ E_WARNING);
include_once(__DIR__ . '/public_algo/sort_by_time.php');
/**
 * 1. 각 선수들 기록 끌고오기 (pass 확인, )
 * 2. 그룹 별 기록 합산 (그룹 당 4명)
 * 3. 그룹 별 기록 정렬
 * 4. 조 단위로 배열 저장
 * 5. 지그재그 조 편성
 * 6. 레인 배정 (같은 국적 프론트, 벡에서 처리)
 * 7. 결과 리턴
 */
// https://www.worldathletics.org/results/world-athletics-championships/2022/world-athletics-championships-oregon-2022-7137279/mixed/4x400-metres-relay/heats/result#resultheader
// 4 x 400M 혼성 릴레이 경기
// 1번
//$QUERY = "SELECT record_schedule_id, record_athlete_id, record_official_record, record_group, record_pass
//          FROM list_record
//          WHERE (record_schedule_id = 386 or record_schedule_id = 387 or record_schedule_id = 388 or record_schedule_id = 389)";
//$conn = mysqli_connect('220.69.240.140', 'jihoon', '1026baby', 'u20_db', 3306);
//$query = mysqli_query($conn, $QUERY);

// 2번
function create_ahtlete_data_by_teams($mysqli_result)
{
    $player_by_teams = null;
    while ($player_data = mysqli_fetch_array($mysqli_result)) {
        $player_by_teams[$player_data['record_schedule_id'] . '_' . $player_data['record_order']][] = $player_data;
    }
    return $player_by_teams;
}

// 3번
function create_group_by_record($player_by_teams)
{
    $group_by_record = null;
    foreach ($player_by_teams as $team_id => $team) {
        $group_by_record[$team_id] = array(
                                            'record_athlete_id' => $team_id,
                                            'record_official_record' => $team[0]['record_official_record'],
                                            'record_pass' => $team[0]['record_pass'],
                                            'record_judge' => $team[0]['record_judge'],
                                            'record_memo' => $team[0]['record_memo'],
                                            'record_order' => null
                                            );
    }
    return $group_by_record;
}

// 4번
function create_team_by_groups($group_by_record)
{
    $team_by_groups = null;
    foreach ($group_by_record as $key => $team_total_record) {
        $team_by_groups[explode('_', $key)[0]][] = $team_total_record;
    }
    return $team_by_groups;
}


//// 5번
//$next_round_team_groups = getNextRoundGroupSingle($team_by_groups, $team_total_count);
//
//// 6번
//for ($i = 0; $i < count($next_round_team_groups); $i++) {
//    $next_round_team_groups[$i] = set_line($next_round_team_groups[$i]);
//}
//
//foreach ($team_by_groups as $group) {
//    foreach ($group as $team) {
//        print_r($team);
//        echo '<br>';
//    }
//}
//echo '<br>';
//foreach ($next_round_team_groups as $group) {
//    foreach ($group as $player) {
//        print_r($player);
//        echo '<br>';
//    }
//    echo '<br>';
//}