<?php
require_once __DIR__ . '/../../../module/record_worldrecord.php';
/**
 * 퀵 정렬 방식 함수 usort를 사용하여 해당 함수를 호출한다
 * @param array $player1_data 선수1 데이터
 * @param array $player2_data 선수2 데이터
 * @return int
 */
function sortByTime(array $player1_data, array $player2_data): int
{
    $player1_record = changeresult($player1_data["record_official_record"]);
    $player2_record = changeresult($player2_data["record_official_record"]);

    if ($player1_record > $player2_record) {
        return 1;
    } elseif ($player1_record < $player2_record) {
        return -1;
    } else {
        return 0;
    }
}
