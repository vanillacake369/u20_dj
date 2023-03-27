<?php
include_once(__DIR__ . '/includes/auth/config.php');

echo '<script>alert("준비중입니다.");</script>';
echo '<script>window.close();</script>';
exit();

//$round = $_GET["round"];
//$group_count = $_GET["count"];
//$schedule_id = $_GET["id"];
//
//global $db;
//echo $round . '<br>';
//echo $group_count . '<br>';
//echo $schedule_id;
//
//
//
//function get_schedule_info($schedule_id)
//{
//    global $db;
//    $query = "SELECT * FROM list_schedule WHERE schedule_id = ?";
//    $stmt = $db->prepare($query);
//    $stmt->bind_param('i', $schedule_id);
//    $stmt->execute();
//    $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
//
//    if (count($data) == 0) {
//        echo '<script>alert("잘못된 접근입니다.");</script>';
//        echo '<script>history.back();</script>';
//    }
//    return $data;
//}
//
//function get_athletics_info($sport_name) {
//    $query = "";
//}