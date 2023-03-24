<?php
require_once __DIR__ . "/../../includes/auth/config.php";
require_once __DIR__ . "/../../security/security.php";
require_once __DIR__ . "/../../console_log.php";
global $db;

console_log($_POST);

/**
 * @author 임지훈 @vanillacake369
 * 세 종류의 로직을 처리해야함
 *      1. 레인/순서만 변경한 경우
 *      2. 선수를 삭제한 경우
 *      3. 선수를 추가한 경우
 */

// try {
//     $athlete = $_POST["athlete_id"] ?? null;        // array: 참가하는 선수들 id
//     $lane = $_POST["order"] ?? null;                 // array: 선수별 순서 or 레인
//     $group = $_POST["group"] ?? null;               // array: 선수 별 그룹 배정
//     $sports = cleanInput($_POST["sport_code"] ?? null);         // string: 스포츠 종목 명 (cleanInput 사용 시 4mr만 남음)
//     $round = cleanInput($_POST["round"] ?? NULL);               // string: 라운드(영어)
//     $gender = cleanInput($_POST["gender"] ?? NULL);             // string: 경기 성별
//     $count = cleanInput($_POST["count"] ?? NULL);               // string: 조 개수
//     $category = cleanInput($_POST["sport_category"] ?? NULL);   // string: 스포츠 카테고리
//     if (($category == '트랙경기' && ($sports != '4x400mR' && $sports != '4x100mR')) || $sports == 'highjump' || $sports == 'polevault') {
//         //릴레이 경기가 아닌 트랙경기와 높이뛰기, 장대높이뛰기인 경우
//         for ($idx = 0; $idx < count($athlete); $idx++) {  //선수 수만큼
//             $athlete_data = [
//                 $athlete[$idx], // 0: record_athlete_id
//                 $lane[$idx],    // 1: record_order
//                 $group[$idx],   // 2: record_group
//                 $sports,        // 3: record_sports
//                 $round,         // 4: record_round
//                 $gender,        // 5: record_gender
//                 0,               // 6: record_medal
//                 $lane[$idx],    // 7: record_order
//                 $group[$idx]   // 8: record_group
//                 // $schedule_ids[$group[$idx] - 1]['schedule_id'], // 1: record_schedule_id
//             ];
//             // 중복키값인 record_id 가져오기
//             $select_record_id = 'SELECT record_id FROM list_record WHERE'
//                 . ' record_athlete_id = ' . $athlete[$idx];
//             $is_record_id = $db->query($select_record_id);
//             if ($result = mysqli_fetch_array($is_record_id)) {
//                 array_unshift($athlete_data, $result['record_id']);
//                 console_log($result['record_id']);
//             } else {
//                 array_unshift($athlete_data, null);
//             }
//             // 중복키값 존재 시, UPDATE문 실행, 존재하지 않는 경우 INSERT문 실행
//             $update_record_query = "INSERT INTO `list_record` 
//                                     (`record_id`,`record_athlete_id`, `record_order`, `record_group`, `record_sports`, `record_round`, `record_gender`, `record_medal`) 
//                                     VALUES (?,?,?,?,?,?,?,?)
//                                     ON DUPLICATE KEY UPDATE `record_order` = ?, `record_group` = ?;";
//             $stmt = $db->prepare($update_record_query);
//             $stmt->bind_param("iiiisssiii", ...$athlete_data);
//             $stmt->execute();
//             $stmt->close();
//         }
//     } else if ($category == '필드경기' && ($sports != 'highjump' || $sports != 'polevault')) {
//         //높이뛰기, 장대높이뛰기가 아닌 필드경기인 경우
//         $MAX_TRIAL = in_array($sports, ["decathlon", "heptathlon"]) ? 3 : 6;
//         for ($idx = 0; $idx < count($athlete); $idx++) {
//             for ($trial = 1; $trial <= $MAX_TRIAL; $trial++) {  //선수 수만큼
//                 $athlete_data = [
//                     $athlete[$idx], // 0: record_athlete_id
//                     $lane[$idx],    // 1: record_order
//                     $group[$idx],   // 2: record_group
//                     $sports,        // 3: record_sports
//                     $round,         // 4: record_round
//                     $gender,        // 5: record_gender
//                     0,              // 6: record_medal
//                     $trial         // 7: record_trial
//                     // $schedule_ids[$group[$idx] - 1]['schedule_id'], // 1: record_schedule_id
//                 ];
//                 // 중복키값인 record_id 가져오기
//                 $select_record_id = 'SELECT record_id FROM list_record WHERE'
//                     . ' record_athlete_id = ' . $athlete[$idx];
//                 $is_record_id = $db->query($select_record_id);
//                 if ($result = mysqli_fetch_array($is_record_id)) {
//                     array_unshift($athlete_data, $result['record_id']);
//                     console_log($result['record_id']);
//                 } else {
//                     array_unshift($athlete_data, null);
//                 }
//                 // 중복키값 존재 시, UPDATE문 실행, 존재하지 않는 경우 INSERT문 실행
//                 $update_record_query = "INSERT INTO `list_record` 
//                                     (`record_id`,`record_athlete_id`, `record_order`, `record_group`, `record_sports`, `record_round`, `record_gender`, `record_medal`) 
//                                     VALUES (?,?,?,?,?,?,?,?)
//                                     ON DUPLICATE KEY UPDATE `record_order` = ?, `record_group` = ?;";
//                 $stmt = $db->prepare($update_record_query);
//                 $stmt->bind_param("iiiisssiii", ...$athlete_data);
//                 $stmt->execute();
//                 $stmt->close();
//             }
//         }
//     } else if ($sports == '4x400mR' || $sports == '4x100mR') {
//         //릴레이 경기일 경우
//         $relay_lane = 0;
//         for ($idx = 0; $idx < count($athlete); $idx++) {  //선수 수만큼
//             if (((int)$order[$idx]) % 4 == 1) {
//                 $relay_lane = $lane[$idx];
//             }
//             $athlete_data = [
//                 $athlete[$idx],             // 0: record_athlete_id
//                 $relay_lane,                // 1: record_order
//                 $idx % 4 + 1,               // 2: record_team_order
//                 $group[$idx],               // 3: record_group
//                 $sports,                    // 4: record_sports
//                 $round,                     // 5: record_round
//                 $gender,                    // 6: record_gender
//                 0                           // 7: record_medal
//                 // $schedule_ids[$group[$idx] - 1]['schedule_id'], // 1: record_schedule_id
//             ];
//             // 중복키값인 record_id 가져오기
//             $select_record_id = 'SELECT record_id FROM list_record WHERE'
//                 . ' record_athlete_id = ' . $athlete[$idx];
//             $is_record_id = $db->query($select_record_id);
//             if ($result = mysqli_fetch_array($is_record_id)) {
//                 array_unshift($athlete_data, $result['record_id']);
//                 console_log($result['record_id']);
//             } else {
//                 array_unshift($athlete_data, null);
//             }
//             // 중복키값 존재 시, UPDATE문 실행, 존재하지 않는 경우 INSERT문 실행
//             $update_record_query = "INSERT INTO `list_record` 
//                                     (`record_id`,`record_athlete_id`, `record_order`, `record_group`, `record_sports`, `record_round`, `record_gender`, `record_medal`) 
//                                     VALUES (?,?,?,?,?,?,?,?)
//                                     ON DUPLICATE KEY UPDATE `record_order` = ?, `record_group` = ?;";
//             $stmt = $db->prepare($update_record_query);
//             $stmt->bind_param("iiiisssiii", ...$athlete_data);
//             $stmt->execute();
//             $stmt->close();
//         }
//     }
// } catch (Exception $e) {
//     $err_message = $e->getMessage();
//     $db->rollback();
//     echo "<script>alert('오류::$err_message'); history.back();</script>";
//     exit();
// }

// echo "<script>alert('수정되었습니다.'); window.close(); </script>";
