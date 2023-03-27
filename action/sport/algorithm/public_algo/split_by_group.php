<?php

/**
 * 트랙경기 선수 데이터를 record_schedule_id 로 분리하여 배열에 저장 후 리턴
 * @param mysqli_result $result
 * @return array
 */
function split_by_group(mysqli_result $result): array
{
    $player_by_group = array();
    while ($player_data = mysqli_fetch_array($result)) {
        $player_by_group[$player_data['record_group']][] = $player_data;
    }
    return $player_by_group;
}