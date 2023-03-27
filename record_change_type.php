<?php
// 접근 권한 체크 함수
echo '<script>if (!window.confirm(\'30분이 경과한 Live Result를 Official Result로 바꾸시겠습니까?\')) {';
echo "location.replace(document.referrer) ;}</script>";
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
    return array($diff, $min, $sec); //시연용 바로 통과
    // return array($hours, $min, $sec); //원래 꺼
}
$sports = $_POST['sports'];
$round = $_POST['round'];
$gender = $_POST['gender'];
$group = $_POST['group'];
$cnt = 0;
$sql = "SELECT * from list_record  where record_sports='$sports' and record_round='$round' and record_gender ='$gender' and record_group='$group' "; //live_result인 모든 스케쥴을 찾는 쿼리
$result1 = $db->query($sql);
$row = mysqli_fetch_array($result1);

if ($row['record_status'] == 'o') {
    echo "<script> alert('이미 변경된 경기입니다.');";
    echo "location.replace(document.referrer);";
    echo "</script>";
    exit;
} else if ($row['record_status'] == 'n') {
    echo "<script> alert('종료된 경기가 아닙니다.');";
    echo "location.replace(document.referrer);";
    echo "</script>";
    exit;
}
$result1 = $db->query($sql);
while ($row = mysqli_fetch_array($result1)) {
    $check = gap_time(($row['record_end'] ?? date("Y-m-d H:i:s")), date("Y-m-d H:i:s"));
    // echo $row[5].': '.date("Y-m-d H:i:s").'에 끝남 '.$check[0].'시간 '.$check[1].'분 '.$check[2].'초 경과<br>';
    if ($check[0] >= 1 || ($check[1] >= 30 && $check[2] >= 0)) { //시간차이가 30분이상인지 판단
        // echo 'UPDATE list_record set record_official_result =\'' . ($row['record_live_result'] ?? null) . '\',record_official_record=\'' . $row['record_live_record'] . '\',record_status=\'o\' where record_id ="' . $row['record_id'] . '"';
        $db->query('UPDATE list_record set record_official_result =\'' . ($row['record_live_result'] ?? null) . '\',record_official_record=\'' . $row['record_live_record'] . '\',record_status=\'o\' where record_id ="' . $row['record_id'] . '"'); //스케쥴에 맞는 기록을 official result로 변환
        $cnt++;
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
