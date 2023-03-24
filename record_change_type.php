<?php
// 접근 권한 체크 함수
include_once(__DIR__ . "/backheader.php");
// 수정 권한, 등록 권한 둘 다 있는 경우에만 접근 가능
if (!authCheck($db, "authRecordsUpdate") || !authCheck($db, "authRecordsCreate")) {
    exit("<script>
        alert('수정 권한이 없습니다.');
        history.back();
    </script>");
}

include_once(__DIR__ . "/database/dbconnect.php");
date_default_timezone_set('Asia/Seoul'); //timezone 설정
function gap_time($start_date, $end_date)
{
    //시간 차이 구하는 함수
    $start_time = strtotime($start_date);
    $end_time = strtotime($end_date);
    $diff = $end_time - $start_time;
    $hours = floor($diff / 3600);
    $diff = $diff - ($hours * 3600);
    $min = floor($diff / 60);
    $sec = $diff - ($min * 60);
    // return array($diff, $min, $sec); //시연용 바로 통과
    return array($hours, $min, $sec); //원래 꺼
}
$schedule_id=$_GET['id'];
$cnt=0;
$result1 = $db->query("SELECT * from list_schedule inner join list_record on schedule_id = record_schedule_id where schedule_id='$schedule_id'"); //live_result인 모든 스케쥴을 찾는 쿼리
$row = mysqli_fetch_array($result1);

if($row['schedule_result'] =='o'){
    echo "<script> alert('이미 변경된 경기입니다.');";
    echo "location.replace(document.referrer);";
    echo "</script>";
}else if($row['schedule_result'] =='n'){
    echo "<script> alert('종료된 경기가 아닙니다.');";
    echo "location.replace(document.referrer);";
    echo "</script>";
}
while ($row = mysqli_fetch_array($result1)) {
$check = gap_time(($row['schedule_end'] ?? date("Y-m-d H:i:s")), date("Y-m-d H:i:s"));
// echo $row[5].': '.date("Y-m-d H:i:s").'에 끝남 '.$check[0].'시간 '.$check[1].'분 '.$check[2].'초 경과<br>';
if ($check[0] >= 1 || ($check[1] >= 30 && $check[2] >= 0)) { //시간차이가 30분이상인지 판단
    $db->query("UPDATE list_schedule set schedule_result ='o' where schedule_id =" . $row['schedule_id'] . ""); // 스케쥴 official로 전화
    // echo 'UPDATE list_record set record_official_result =\'' . ($row['record_live_result'] ?? null) . '\',record_official_record=\'' . $row['record_live_record'] . '\',record_status=\'o\' where record_id ="' . $row['record_id'] . '"';
    $db->query('UPDATE list_record set record_official_result =\'' . ($row['record_live_result'] ?? null) . '\',record_official_record=\'' . $row['record_live_record'] . '\',record_status=\'o\' where record_id ="' . $row['record_id'] . '"'); //스케쥴에 맞는 기록을 official result로 변환
    $cnt++;
}
}
$result2 = $db->query("SELECT schedule_end,schedule_name,schedule_round,schedule_gender,schedule_id from list_schedule where schedule_result='l' and schedule_sports='".$row['schedule_sports']."' and schedule_division='b'");
$row1 = mysqli_fetch_array($result2);
$result3 = $db->query("SELECT * from list_schedule 
where schedule_name='" . $row1['schedule_name'] . "' and schedule_round='" . $row1['schedule_round'] . "' and schedule_gender='" . $row1['schedule_gender'] . "'
AND (schedule_result='l' OR schedule_result='n') AND schedule_division='s'");
//  echo "SELECT * from list_schedule 
//  where schedule_name='".$row1['schedule_name']."' and schedule_round='".$row1['schedule_round']."' and schedule_gender='".$row1['schedule_gender']."'
//   AND (schedule_result='l' OR schedule_result='n') AND schedule_division='s'"."<br>";
if (mysqli_num_rows($result3) === 0) {
    $check = gap_time(($row1[0] ?? date("Y-m-d H:i:s")), date("Y-m-d H:i:s"));
    if ($check[0] >= 1 || $check[1] >= 30 && $check[2] >= 0) {
        $db->query("UPDATE list_schedule set schedule_result ='o' where schedule_id =" . $row1['schedule_id'] . ""); // 스케쥴 official로 전화
    }
}

echo "<script>";
echo "alert('";
if ($cnt > 0) { //변경된 종목을 출력
    echo "변경되었습니다.";
} else {
    echo "30분이 경과하지 않았습니다.";
}
echo "')
location.replace(document.referrer) 
</script>";