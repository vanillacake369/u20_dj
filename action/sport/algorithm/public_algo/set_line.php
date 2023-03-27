<?php
include_once(__DIR__ . '/sort_by_time.php');

/**
 * 1. 지그재그로 정렬된 인원 시간 빠른순으로 정렬
 * 2. 현재 그룹 인원 체크
 * 3. 인원에 따라서 (case)
 * 4-2. 레인 배정에 쓰일 배열 셔플함
 * 4-3. 랜덤으로 섞인 값을 넣어준다. (1234 -> 3456) (56 -> 78) (78 -> 12) 왼:등수, 오:레인
 * 5. 자른 인원 배열 취합 (merge)
 * 6. 결과 값 리턴 (DB 컬럼 'record_order'에 배정시켜줌으로 나중에 값을 꺼내서 확인 및 사용)
 *******
 * 조편성 16명 이면 8-8 . 하지만 9~15명만 남았을때 어떤식으로? 17명읷때는? 9명조 허용?
 * - 9명 일때 5-4, 10명 일때 5-5, 11명 일때 6-5, 12명 일때 6-6, 13명 일때 7-6, 14명 일때 7-7, 15명 일때 8-7, 17명 일때 6-6-5
 * - 이런 경우 특별히 800m 의 경우 9명을 달리는데 8번 레인에서 2명이 달림.
 * - 10명 이상일 때 1500m, 3000m인 경우는 라인 개념이 없고, 랜덤 추첨을 통해 순서를 배정함
 ******
 */
function first_input(array $group): array
{
    $first_line = array(3, 4, 5, 6);
    shuffle($first_line);
    for ($i = 0; $i < 4; $i++) {
        $group[$i]['record_order'] = $first_line[$i];
    }
    return $group;
}

function second_input(array $group): array
{
    $second_line = array(7, 8);
    shuffle($second_line);
    for ($i = 0; $i < 2; $i++) {
        $group[$i]['record_order'] = $second_line[$i];
    }
    return $group;
}

function third_input(array $group): array
{
    $third_line = array(1, 2);
    shuffle($third_line);
    for ($i = 0; $i < 2; $i++) {
        $group[$i]['record_order'] = $third_line[$i];
    }
    return $group;
}

function set_line(array $group, string $sport_name, bool $sorting = true): array
{
    if ($sorting) {
        usort($group, 'sortByTime');
    }
    $player_count = count($group);
    if (!in_array($sport_name, ["1500m", "3000m", "5000m", "3000mSC", "10000m"])) { // 장거리 경기가 아닐 시 라인 편성
        switch ($player_count) {
            case 4:
                $group = first_input($group);
                break;
            case 5:
                $first_players = array_slice($group, 0, 4);
                $first_players = first_input($first_players);
                $group[4]['record_order'] = 7;
                $group = array_merge($first_players, array($group[4]));
                break;
            case 6:
                $first_players = array_slice($group, 0, 4);
                $second_players = array_slice($group, 4, 2);
                $first_players = first_input($first_players);
                $second_players = second_input($second_players);
                $group = array_merge($first_players, $second_players);
                break;
            case 7:
                $first_players = array_slice($group, 0, 4);
                $second_players = array_slice($group, 4, 2);
                $first_players = first_input($first_players);
                $second_players = second_input($second_players);
                $group[6]['record_order'] = 2;
                $group = array_merge($first_players, $second_players, array($group[6]));
                break;
            case 8:
                $first_players = array_slice($group, 0, 4);
                $second_players = array_slice($group, 4, 2);
                $third_players = array_slice($group, 6, 2);
                $first_players = first_input($first_players);
                $second_players = second_input($second_players);
                $third_players = third_input($third_players);
                $group = array_merge($first_players, $second_players, $third_players);
                break;
            case 9: // 800M 9명 일때만 해당
                $first_players = array_slice($group, 0, 4);
                $second_players = array_slice($group, 4, 2);
                $third_players = array_slice($group, 6, 2);
                $first_players = first_input($first_players);
                $second_players = second_input($second_players);
                $third_players = third_input($third_players);
                $group[8]['record_order'] = 8;
                $group = array_merge($first_players, $second_players, $third_players, array($group[8]));
                break;
            case 10: // 800M 10명 일때만 해당
                $first_players = array_slice($group, 0, 4);
                $second_players = array_slice($group, 4, 2);
                $third_players = array_slice($group, 6, 2);
                $first_players = first_input($first_players);
                $second_players = second_input($second_players);
                $third_players = third_input($third_players);
                $group[8]['record_order'] = 7;
                $group[9]['record_order'] = 8;
                $group = array_merge($first_players, $second_players, $third_players, array($group[8], $group[9]));
                break;
            default:
                shuffle($group);
                for ($i = 0; $i < $player_count; $i++) {
                    $group[$i]['record_order'] = $i + 1;
                }
        }
    } else { // 장거리 경기 조편성 (랜덤)
        shuffle($group);
        for ($i = 0; $i < $player_count; $i++) {
            $group[$i]['record_order'] = $i + 1;
        }
    }
    return $group;
}


// TEST
//foreach ($next_round_groups as $group) {
//    foreach ($group as $player) {
//        echo $player['record_athlete_id'] . ' ' . $player['record_record'] . '&nbsp&nbsp&nbsp';
//    }
//    echo '<br>';
//}
//
//$group_count = 1;
//foreach ($next_round_groups as $group) {
//    echo '======' . $group_count . '조======<br>';
//    foreach ($group as $player) {
//        print_r($player);
//        echo '<br>';
//    }
//    $group_count = $group_count + 1;
//    echo '<br>';
//}
//
//$group_count = 1;
//foreach ($new_next_round_groups as $group) {
//    echo '======' . $group_count . '조======<br>';
//    foreach ($group as $player) {
//        print_r($player);
//        echo '<br>';
//    }
//    $group_count = $group_count + 1;
//    echo '<br>';
//}
